<?php

namespace App\Http\Controllers;

use App\Models\ImagenEstudio;
use App\Models\Paciente;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ImagenController extends Controller
{
    private function empresaId(): int
    {
        return (int) auth()->user()->empresa_id;
    }

    public function index(Request $request)
    {
        $estado = $request->get('estado');
        $estudios = ImagenEstudio::with(['paciente', 'medico'])
            ->where('empresa_id', $this->empresaId())
            ->when($estado, fn ($q) => $q->where('estado', $estado))
            ->orderByDesc('fecha')->orderByDesc('id')
            ->paginate(12)->withQueryString();

        return view('imagenes.index', compact('estudios', 'estado'));
    }

    public function create(Request $request)
    {
        return view('imagenes.form', $this->opciones() + ['pacienteSel' => $request->get('paciente_id')]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'paciente_id' => ['required', 'exists:pacientes,id'],
            'medico_id' => ['nullable', 'exists:users,id'],
            'modalidad' => ['required', 'string', 'max:60'],
            'region' => ['nullable', 'string', 'max:80'],
            'tipo_estudio' => ['required', 'string', 'max:160'],
            'fecha' => ['required', 'date'],
            'indicacion' => ['nullable', 'string'],
        ]);
        $data['empresa_id'] = $this->empresaId();
        $data['medico_id'] = $data['medico_id'] ?? auth()->id();
        $data['estado'] = 'solicitado';
        $estudio = ImagenEstudio::create($data);

        return redirect()->route('imagenes.show', $estudio)->with('ok', 'Estudio solicitado.');
    }

    public function show(ImagenEstudio $imagen)
    {
        abort_unless($imagen->empresa_id === $this->empresaId(), 403);
        $imagen->load(['paciente', 'medico', 'radiologo']);
        $radiologos = User::where('empresa_id', $this->empresaId())->where('role', 'medico')->get();

        return view('imagenes.show', compact('imagen', 'radiologos'));
    }

    public function guardarInforme(Request $request, ImagenEstudio $imagen)
    {
        abort_unless($imagen->empresa_id === $this->empresaId(), 403);
        $data = $request->validate([
            'radiologo_id' => ['nullable', 'exists:users,id'],
            'hallazgos' => ['nullable', 'string'],
            'conclusion' => ['nullable', 'string'],
        ]);

        $informado = filled($data['hallazgos'] ?? null) || filled($data['conclusion'] ?? null);
        $imagen->update($data + ['estado' => $informado ? 'informado' : $imagen->estado]);

        return back()->with('ok', 'Informe guardado.');
    }

    public function subirArchivo(Request $request, ImagenEstudio $imagen)
    {
        abort_unless($imagen->empresa_id === $this->empresaId(), 403);
        $request->validate(['archivo' => ['required', 'file', 'max:15360', 'mimes:jpg,jpeg,png,webp,pdf,dcm']]);

        if ($imagen->archivo) {
            Storage::disk('public')->delete($imagen->archivo);
        }
        $file = $request->file('archivo');
        $path = $file->store('imagenes/'.$this->empresaId(), 'public');

        $imagen->update([
            'archivo' => $path,
            'archivo_nombre' => $file->getClientOriginalName(),
            'estado' => $imagen->estado === 'solicitado' ? 'realizado' : $imagen->estado,
        ]);

        return back()->with('ok', 'Archivo cargado.');
    }

    public function subirOrden(Request $request, ImagenEstudio $imagen)
    {
        abort_unless($imagen->empresa_id === $this->empresaId(), 403);
        $request->validate(['orden' => ['required', 'file', 'max:15360', 'mimes:jpg,jpeg,png,webp,pdf']]);

        if ($imagen->orden_archivo) {
            Storage::disk('public')->delete($imagen->orden_archivo);
        }

        $file = $request->file('orden');
        $imagen->update([
            'orden_archivo' => $file->store('ordenes/'.$this->empresaId(), 'public'),
            'orden_nombre' => $file->getClientOriginalName(),
        ]);

        return back()->with('ok', 'Orden médica cargada.');
    }

    public function destroy(ImagenEstudio $imagen)
    {
        abort_unless($imagen->empresa_id === $this->empresaId(), 403);
        if ($imagen->archivo) {
            Storage::disk('public')->delete($imagen->archivo);
        }
        if ($imagen->orden_archivo) {
            Storage::disk('public')->delete($imagen->orden_archivo);
        }
        $imagen->delete();

        return redirect()->route('imagenes.index')->with('ok', 'Estudio eliminado.');
    }

    public function pdf(ImagenEstudio $imagen)
    {
        abort_unless($imagen->empresa_id === $this->empresaId(), 403);
        $imagen->load(['paciente', 'medico', 'radiologo']);

        $pdf = Pdf::loadView('imagenes.pdf', [
            'imagen' => $imagen,
            'empresa' => auth()->user()->empresa,
        ])->setPaper('a4');

        return $pdf->stream('imagen-'.$imagen->id.'.pdf');
    }

    private function opciones(): array
    {
        return [
            'imagen' => new ImagenEstudio(['fecha' => now()->toDateString()]),
            'pacientes' => Paciente::where('empresa_id', $this->empresaId())->orderBy('apellidos')->get(),
            'medicos' => User::where('empresa_id', $this->empresaId())->where('role', 'medico')->get(),
            'modalidades' => ImagenEstudio::MODALIDADES,
        ];
    }
}
