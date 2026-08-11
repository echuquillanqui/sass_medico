<?php

namespace App\Http\Controllers;

use App\Models\Adjunto;
use App\Models\Paciente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdjuntoController extends Controller
{
    private function empresaId(): int
    {
        return (int) auth()->user()->empresa_id;
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'paciente_id' => ['required', 'exists:pacientes,id'],
            'consulta_id' => ['nullable', 'exists:consultas,id'],
            'categoria' => ['nullable', 'string', 'max:40'],
            'archivo' => ['required', 'file', 'max:10240', 'mimes:jpg,jpeg,png,webp,pdf,doc,docx'],
        ]);

        $paciente = Paciente::where('empresa_id', $this->empresaId())->findOrFail($data['paciente_id']);

        $file = $request->file('archivo');
        $path = $file->store('adjuntos/'.$this->empresaId(), 'public');

        Adjunto::create([
            'empresa_id' => $this->empresaId(),
            'paciente_id' => $paciente->id,
            'consulta_id' => $data['consulta_id'] ?? null,
            'user_id' => auth()->id(),
            'nombre' => $file->getClientOriginalName(),
            'archivo' => $path,
            'tipo' => $file->getClientMimeType(),
            'tamano' => $file->getSize(),
            'categoria' => $data['categoria'] ?? 'otro',
        ]);

        return back()->with('ok', 'Archivo adjuntado.');
    }

    public function download(Adjunto $adjunto)
    {
        abort_unless($adjunto->empresa_id === $this->empresaId(), 403);

        return Storage::disk('public')->download($adjunto->archivo, $adjunto->nombre);
    }

    public function destroy(Adjunto $adjunto)
    {
        abort_unless($adjunto->empresa_id === $this->empresaId(), 403);
        Storage::disk('public')->delete($adjunto->archivo);
        $adjunto->delete();

        return back()->with('ok', 'Archivo eliminado.');
    }
}
