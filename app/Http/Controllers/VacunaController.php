<?php

namespace App\Http\Controllers;

use App\Models\Paciente;
use App\Models\Vacuna;
use Illuminate\Http\Request;

class VacunaController extends Controller
{
    private function empresaId(): int
    {
        return (int) auth()->user()->empresa_id;
    }

    private function paciente(int $id): Paciente
    {
        $p = Paciente::where('empresa_id', $this->empresaId())->findOrFail($id);
        return $p;
    }

    public function index(Paciente $paciente)
    {
        abort_unless($paciente->empresa_id === $this->empresaId(), 403);
        $vacunas = $paciente->vacunas()->orderByRaw("estado = 'aplicada'")->orderBy('fecha_programada')->orderBy('id')->get();

        return view('vacunas.index', [
            'paciente' => $paciente,
            'vacunas' => $vacunas,
            'esquema' => Vacuna::ESQUEMA,
            'aplicadas' => $vacunas->where('estado', 'aplicada')->count(),
        ]);
    }

    public function generarEsquema(Paciente $paciente)
    {
        abort_unless($paciente->empresa_id === $this->empresaId(), 403);

        foreach (Vacuna::ESQUEMA as $nombre => $edad) {
            $existe = $paciente->vacunas()->where('nombre', $nombre)->exists();
            if (! $existe) {
                $paciente->vacunas()->create([
                    'empresa_id' => $this->empresaId(),
                    'nombre' => $nombre,
                    'dosis' => $edad,
                    'estado' => 'pendiente',
                ]);
            }
        }

        return back()->with('ok', 'Esquema de vacunación generado.');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'paciente_id' => ['required', 'exists:pacientes,id'],
            'nombre' => ['required', 'string', 'max:120'],
            'dosis' => ['nullable', 'string', 'max:60'],
            'fecha_programada' => ['nullable', 'date'],
        ]);
        $paciente = $this->paciente((int) $data['paciente_id']);

        $paciente->vacunas()->create([
            'empresa_id' => $this->empresaId(),
            'nombre' => $data['nombre'],
            'dosis' => $data['dosis'] ?? null,
            'fecha_programada' => $data['fecha_programada'] ?? null,
            'estado' => 'pendiente',
        ]);

        return back()->with('ok', 'Vacuna agregada.');
    }

    public function aplicar(Request $request, Vacuna $vacuna)
    {
        abort_unless($vacuna->empresa_id === $this->empresaId(), 403);
        $vacuna->update([
            'estado' => 'aplicada',
            'fecha_aplicada' => now()->toDateString(),
            'user_id' => auth()->id(),
            'lote' => $request->get('lote'),
        ]);

        return back()->with('ok', 'Vacuna registrada como aplicada.');
    }

    public function destroy(Vacuna $vacuna)
    {
        abort_unless($vacuna->empresa_id === $this->empresaId(), 403);
        $vacuna->delete();

        return back()->with('ok', 'Vacuna eliminada.');
    }
}
