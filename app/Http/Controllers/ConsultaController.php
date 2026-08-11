<?php

namespace App\Http\Controllers;

use App\Models\Cita;
use App\Models\Consulta;
use App\Models\Paciente;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class ConsultaController extends Controller
{
    private function empresaId(): int
    {
        return (int) auth()->user()->empresa_id;
    }

    public function create(Request $request)
    {
        $paciente = Paciente::where('empresa_id', $this->empresaId())
            ->findOrFail($request->get('paciente_id'));

        $cita = null;
        if ($request->filled('cita_id')) {
            $cita = Cita::where('empresa_id', $this->empresaId())->find($request->get('cita_id'));
        }

        return view('consultas.form', [
            'consulta' => new Consulta(['fecha' => now()->toDateString()]),
            'paciente' => $paciente,
            'cita' => $cita,
            'especialidad' => $paciente->especialidad,
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $paciente = Paciente::where('empresa_id', $this->empresaId())->findOrFail($data['paciente_id']);

        $data['empresa_id'] = $this->empresaId();
        $data['medico_id'] = auth()->id();
        $data['especialidad_id'] = $paciente->especialidad_id;
        $data['datos_especialidad'] = $request->input('datos', []);

        $consulta = Consulta::create($data);
        $this->guardarReceta($consulta, $request);

        if ($request->filled('cita_id')) {
            Cita::where('empresa_id', $this->empresaId())
                ->where('id', $request->cita_id)
                ->update(['estado' => 'atendida']);
        }

        return redirect()->route('pacientes.show', $consulta->paciente_id)
            ->with('ok', 'Consulta registrada en la historia clínica.');
    }

    public function show(Consulta $consulta)
    {
        $this->authorize($consulta);
        $consulta->load(['paciente', 'medico', 'especialidad', 'recetaItems']);
        return view('consultas.show', compact('consulta'));
    }

    public function edit(Consulta $consulta)
    {
        $this->authorize($consulta);
        $consulta->load('recetaItems');
        return view('consultas.form', [
            'consulta' => $consulta,
            'paciente' => $consulta->paciente,
            'cita' => null,
            'especialidad' => $consulta->especialidad,
        ]);
    }

    public function update(Request $request, Consulta $consulta)
    {
        $this->authorize($consulta);
        $data = $this->validated($request);
        $data['datos_especialidad'] = $request->input('datos', []);
        $consulta->update($data);
        $this->guardarReceta($consulta, $request);

        return redirect()->route('pacientes.show', $consulta->paciente_id)
            ->with('ok', 'Consulta actualizada.');
    }

    public function receta(Consulta $consulta)
    {
        $this->authorize($consulta);
        $consulta->load(['paciente', 'medico', 'especialidad', 'recetaItems']);
        $empresa = auth()->user()->empresa;

        $pdf = Pdf::loadView('consultas.receta', compact('consulta', 'empresa'))->setPaper('a5');

        return $pdf->stream('receta-'.$consulta->id.'.pdf');
    }

    private function guardarReceta(Consulta $consulta, Request $request): void
    {
        $consulta->recetaItems()->delete();
        foreach ((array) $request->input('receta', []) as $item) {
            if (! empty($item['medicamento'])) {
                $consulta->recetaItems()->create([
                    'medicamento' => $item['medicamento'],
                    'presentacion' => $item['presentacion'] ?? null,
                    'dosis' => $item['dosis'] ?? null,
                    'frecuencia' => $item['frecuencia'] ?? null,
                    'duracion' => $item['duracion'] ?? null,
                    'indicaciones' => $item['indicaciones'] ?? null,
                ]);
            }
        }
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'paciente_id' => ['required', 'exists:pacientes,id'],
            'fecha' => ['required', 'date'],
            'motivo' => ['nullable', 'string'],
            'diagnostico' => ['nullable', 'string'],
            'tratamiento' => ['nullable', 'string'],
            'peso' => ['nullable', 'numeric', 'min:0', 'max:500'],
            'talla' => ['nullable', 'numeric', 'min:0', 'max:300'],
            'presion_arterial' => ['nullable', 'string', 'max:20'],
            'frecuencia_cardiaca' => ['nullable', 'integer', 'min:0', 'max:400'],
            'temperatura' => ['nullable', 'numeric', 'min:25', 'max:45'],
            'observaciones' => ['nullable', 'string'],
        ]);
    }

    private function authorize(Consulta $consulta): void
    {
        abort_unless($consulta->empresa_id === $this->empresaId(), 403);
    }
}
