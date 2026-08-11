<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HorarioMedico;
use App\Models\User;
use Illuminate\Http\Request;

class HorarioController extends Controller
{
    private function empresaId(): int
    {
        return (int) auth()->user()->empresa_id;
    }

    public function index()
    {
        $medicos = User::where('empresa_id', $this->empresaId())
            ->where('role', 'medico')
            ->with(['horarios' => fn ($q) => $q->orderBy('dia_semana')->orderBy('hora_inicio')])
            ->orderBy('name')->get();

        return view('admin.horarios.index', [
            'medicos' => $medicos,
            'dias' => HorarioMedico::DIAS,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'dia_semana' => ['required', 'integer', 'between:0,6'],
            'hora_inicio' => ['required', 'string'],
            'hora_fin' => ['required', 'string', 'after:hora_inicio'],
        ]);

        // Verificar que el médico pertenece a la empresa
        User::where('empresa_id', $this->empresaId())->where('role', 'medico')->findOrFail($data['user_id']);

        HorarioMedico::create([
            'empresa_id' => $this->empresaId(),
            'user_id' => $data['user_id'],
            'dia_semana' => $data['dia_semana'],
            'hora_inicio' => $data['hora_inicio'],
            'hora_fin' => $data['hora_fin'],
        ]);

        return back()->with('ok', 'Horario agregado.');
    }

    public function destroy(HorarioMedico $horario)
    {
        abort_unless($horario->empresa_id === $this->empresaId(), 403);
        $horario->delete();

        return back()->with('ok', 'Horario eliminado.');
    }
}
