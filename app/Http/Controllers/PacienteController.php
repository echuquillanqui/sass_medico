<?php

namespace App\Http\Controllers;

use App\Models\Especialidad;
use App\Models\Paciente;
use App\Support\WhoLms;
use Illuminate\Http\Request;

class PacienteController extends Controller
{
    private function empresaId(): int
    {
        return (int) auth()->user()->empresa_id;
    }

    public function index(Request $request)
    {
        $q = $request->get('q');
        $pacientes = Paciente::where('empresa_id', $this->empresaId())
            ->when($q, fn ($query) => $query->where(function ($sub) use ($q) {
                $sub->where('nombres', 'like', "%{$q}%")
                    ->orWhere('apellidos', 'like', "%{$q}%")
                    ->orWhere('documento', 'like', "%{$q}%");
            }))
            ->with('especialidad')
            ->orderBy('apellidos')
            ->paginate(10)->withQueryString();

        return view('pacientes.index', compact('pacientes', 'q'));
    }

    public function create()
    {
        return view('pacientes.form', [
            'paciente' => new Paciente(),
            'especialidades' => $this->especialidades(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['empresa_id'] = $this->empresaId();
        Paciente::create($data);

        return redirect()->route('pacientes.index')->with('ok', 'Paciente registrado correctamente.');
    }

    public function show(Paciente $paciente)
    {
        $this->authorizeEmpresa($paciente);
        $paciente->load(['especialidad', 'consultas.medico', 'pagos', 'adjuntos.user', 'citas' => fn ($q) => $q->latest('fecha')]);

        return view('pacientes.show', compact('paciente'));
    }

    public function edit(Paciente $paciente)
    {
        $this->authorizeEmpresa($paciente);
        return view('pacientes.form', [
            'paciente' => $paciente,
            'especialidades' => $this->especialidades(),
        ]);
    }

    public function update(Request $request, Paciente $paciente)
    {
        $this->authorizeEmpresa($paciente);
        $paciente->update($this->validated($request));

        return redirect()->route('pacientes.index')->with('ok', 'Paciente actualizado.');
    }

    public function destroy(Paciente $paciente)
    {
        $this->authorizeEmpresa($paciente);
        $paciente->delete();

        return redirect()->route('pacientes.index')->with('ok', 'Paciente eliminado.');
    }


    public function exportar(Request $request)
    {
        $q = $request->get('q');
        $pacientes = Paciente::where('empresa_id', $this->empresaId())
            ->when($q, fn ($query) => $query->where(function ($sub) use ($q) {
                $sub->where('nombres', 'like', "%{$q}%")
                    ->orWhere('apellidos', 'like', "%{$q}%")
                    ->orWhere('documento', 'like', "%{$q}%");
            }))
            ->with('especialidad')->orderBy('apellidos')->get();

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="pacientes-'.now()->format('Ymd').'.csv"',
        ];

        return response()->stream(function () use ($pacientes) {
            $out = fopen('php://output', 'w');
            fprintf($out, chr(0xEF).chr(0xBB).chr(0xBF));
            fputcsv($out, ['Nombres', 'Apellidos', 'Documento', 'Fecha nac.', 'Sexo', 'Especialidad', 'Telefono', 'Email', 'Direccion']);
            foreach ($pacientes as $p) {
                fputcsv($out, [
                    $p->nombres, $p->apellidos, $p->documento,
                    optional($p->fecha_nacimiento)->format('d/m/Y'),
                    ['M' => 'Masculino', 'F' => 'Femenino', 'O' => 'Otro'][$p->sexo] ?? '',
                    $p->especialidad->nombre ?? '', $p->telefono, $p->email, $p->direccion,
                ]);
            }
            fclose($out);
        }, 200, $headers);
    }

    /** Curvas de crecimiento OMS (peso/talla por edad). */
    public function crecimiento(Paciente $paciente)
    {
        $this->authorizeEmpresa($paciente);
        abort_unless($paciente->fecha_nacimiento && in_array($paciente->sexo, ['M', 'F']),
            404, 'Se requiere fecha de nacimiento y sexo del paciente.');

        $sexo = $paciente->sexo;
        $puntos = ['peso' => [], 'talla' => []];

        foreach ($paciente->consultas()->orderBy('fecha')->get() as $c) {
            $mes = (int) $paciente->fecha_nacimiento->diffInMonths($c->fecha);
            if ($c->peso) {
                $puntos['peso'][] = ['x' => $mes, 'y' => (float) $c->peso,
                    'p' => WhoLms::percentil('peso', $sexo, $mes, (float) $c->peso)['percentil']];
            }
            if ($c->talla) {
                $puntos['talla'][] = ['x' => $mes, 'y' => (float) $c->talla,
                    'p' => WhoLms::percentil('talla', $sexo, $mes, (float) $c->talla)['percentil']];
            }
        }

        return view('pacientes.crecimiento', [
            'paciente' => $paciente,
            'puntos' => $puntos,
            'curvasPeso' => WhoLms::curvas('peso', $sexo),
            'curvasTalla' => WhoLms::curvas('talla', $sexo),
        ]);
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'nombres' => ['required', 'string', 'max:120'],
            'apellidos' => ['required', 'string', 'max:120'],
            'tipo_documento' => ['nullable', 'string', 'max:20'],
            'documento' => ['nullable', 'string', 'max:30'],
            'fecha_nacimiento' => ['nullable', 'date'],
            'sexo' => ['nullable', 'in:M,F,O'],
            'telefono' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:120'],
            'direccion' => ['nullable', 'string', 'max:200'],
            'grupo_sanguineo' => ['nullable', 'string', 'max:5'],
            'alergias' => ['nullable', 'string'],
            'antecedentes' => ['nullable', 'string'],
            'especialidad_id' => ['nullable', 'exists:especialidades,id'],
        ]);
    }

    private function especialidades()
    {
        return auth()->user()->empresa?->especialidadesActivas()->get() ?? collect();
    }

    private function authorizeEmpresa(Paciente $paciente): void
    {
        abort_unless($paciente->empresa_id === $this->empresaId(), 403);
    }
}
