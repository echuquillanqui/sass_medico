<?php

namespace App\Http\Controllers;

use App\Models\Donante;
use App\Models\Paciente;
use App\Models\SolicitudSangre;
use App\Models\UnidadSangre;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BancoSangreController extends Controller
{
    private function empresaId(): int
    {
        return (int) auth()->user()->empresa_id;
    }

    public function index()
    {
        $eid = $this->empresaId();

        // Stock disponible por grupo
        $stock = [];
        foreach (Donante::GRUPOS as $g) {
            $stock[$g] = UnidadSangre::where('empresa_id', $eid)->where('grupo', $g)
                ->where('estado', 'disponible')->whereDate('fecha_vencimiento', '>=', today())->count();
        }

        $porVencer = UnidadSangre::where('empresa_id', $eid)->where('estado', 'disponible')
            ->whereBetween('fecha_vencimiento', [today(), today()->addDays(7)])
            ->orderBy('fecha_vencimiento')->get();

        $solicitudes = SolicitudSangre::with(['paciente', 'medico'])
            ->where('empresa_id', $eid)->where('estado', 'pendiente')
            ->orderBy('fecha')->get();

        return view('bancosangre.index', [
            'stock' => $stock,
            'grupos' => Donante::GRUPOS,
            'totalDisponible' => array_sum($stock),
            'totalDonantes' => Donante::where('empresa_id', $eid)->where('activo', true)->count(),
            'porVencer' => $porVencer,
            'solicitudes' => $solicitudes,
            'donantes' => Donante::where('empresa_id', $eid)->where('activo', true)->orderBy('apellidos')->get(),
            'pacientes' => Paciente::where('empresa_id', $eid)->orderBy('apellidos')->get(),
            'medicos' => User::where('empresa_id', $eid)->where('role', 'medico')->get(),
        ]);
    }

    public function donantes()
    {
        $donantes = Donante::where('empresa_id', $this->empresaId())
            ->withCount(['unidades as unidades_count'])
            ->orderBy('apellidos')->get();

        return view('bancosangre.donantes', [
            'donantes' => $donantes,
            'grupos' => Donante::GRUPOS,
        ]);
    }

    public function donanteStore(Request $request)
    {
        $data = $request->validate([
            'nombres' => ['required', 'string', 'max:120'],
            'apellidos' => ['required', 'string', 'max:120'],
            'documento' => ['nullable', 'string', 'max:30'],
            'grupo' => ['required', 'in:'.implode(',', Donante::GRUPOS)],
            'telefono' => ['nullable', 'string', 'max:30'],
        ]);
        $data['empresa_id'] = $this->empresaId();
        Donante::create($data);

        return back()->with('ok', 'Donante registrado.');
    }

    public function unidadStore(Request $request)
    {
        $data = $request->validate([
            'donante_id' => ['nullable', 'exists:donantes,id'],
            'grupo' => ['required', 'in:'.implode(',', Donante::GRUPOS)],
            'volumen' => ['nullable', 'integer', 'min:100', 'max:600'],
            'fecha_extraccion' => ['required', 'date'],
        ]);

        $extraccion = \Illuminate\Support\Carbon::parse($data['fecha_extraccion']);
        UnidadSangre::create([
            'empresa_id' => $this->empresaId(),
            'donante_id' => $data['donante_id'] ?? null,
            'codigo' => 'U'.strtoupper(\Illuminate\Support\Str::random(6)),
            'grupo' => $data['grupo'],
            'volumen' => $data['volumen'] ?? 450,
            'fecha_extraccion' => $data['fecha_extraccion'],
            'fecha_vencimiento' => $extraccion->copy()->addDays(42)->toDateString(),
            'estado' => 'disponible',
        ]);

        if (! empty($data['donante_id'])) {
            Donante::where('id', $data['donante_id'])->update(['fecha_ultima_donacion' => $data['fecha_extraccion']]);
        }

        return back()->with('ok', 'Unidad registrada e ingresada al stock.');
    }

    public function unidadDescartar(UnidadSangre $unidad)
    {
        abort_unless($unidad->empresa_id === $this->empresaId(), 403);
        $unidad->update(['estado' => 'descartada']);

        return back()->with('ok', 'Unidad descartada.');
    }

    public function solicitudStore(Request $request)
    {
        $data = $request->validate([
            'paciente_id' => ['required', 'exists:pacientes,id'],
            'medico_id' => ['nullable', 'exists:users,id'],
            'grupo' => ['required', 'in:'.implode(',', Donante::GRUPOS)],
            'cantidad' => ['required', 'integer', 'min:1', 'max:20'],
            'fecha' => ['required', 'date'],
            'motivo' => ['nullable', 'string', 'max:200'],
        ]);
        $data['empresa_id'] = $this->empresaId();
        $data['medico_id'] = $data['medico_id'] ?? auth()->id();
        $data['estado'] = 'pendiente';
        SolicitudSangre::create($data);

        return back()->with('ok', 'Solicitud de sangre registrada.');
    }

    public function despachar(SolicitudSangre $solicitud)
    {
        abort_unless($solicitud->empresa_id === $this->empresaId(), 403);
        abort_unless($solicitud->estado === 'pendiente', 403);

        $disponibles = UnidadSangre::where('empresa_id', $this->empresaId())
            ->where('grupo', $solicitud->grupo)->where('estado', 'disponible')
            ->whereDate('fecha_vencimiento', '>=', today())
            ->orderBy('fecha_vencimiento')->limit($solicitud->cantidad)->get();

        if ($disponibles->count() < $solicitud->cantidad) {
            return back()->with('ok', 'Stock insuficiente del grupo '.$solicitud->grupo.'. Disponibles: '.$disponibles->count());
        }

        DB::transaction(function () use ($disponibles, $solicitud) {
            UnidadSangre::whereIn('id', $disponibles->pluck('id'))->update(['estado' => 'transfundida']);
            $solicitud->update(['estado' => 'atendida']);
        });

        return back()->with('ok', 'Solicitud despachada. Unidades descontadas del stock.');
    }

    public function solicitudCancelar(SolicitudSangre $solicitud)
    {
        abort_unless($solicitud->empresa_id === $this->empresaId(), 403);
        $solicitud->update(['estado' => 'cancelada']);

        return back()->with('ok', 'Solicitud cancelada.');
    }
}
