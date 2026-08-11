@extends('layouts.app')
@section('title', 'Estudio #'.$imagen->id)

@section('content')
    <div class="page-head">
        <div>
            <h1>{{ $imagen->modalidad }} · {{ $imagen->region ?? '—' }}</h1>
            <p>{{ $imagen->paciente->nombre_completo }} · {{ $imagen->fecha->format('d/m/Y') }} · Solicita: {{ $imagen->medico->name ?? '—' }}
            @php $c=['solicitado'=>'amber','realizado'=>'blue','informado'=>'green'][$imagen->estado]??'gray'; @endphp
            · <span class="pill {{ $c }}">{{ $imagen->estado_label }}</span></p>
        </div>
        <div class="flex gap">
            <a href="{{ route('imagenes.pdf',$imagen) }}" target="_blank" class="btn btn-light"><i class="fa-solid fa-file-pdf"></i> Informe PDF</a>
            <a href="{{ route('imagenes.index') }}" class="btn btn-ghost">Volver</a>
        </div>
    </div>

    @if($imagen->indicacion)<div class="card mb"><p class="muted" style="margin:0"><b>Indicación:</b> {{ $imagen->indicacion }}</p></div>@endif

    <div class="grid g-2">
        <div>
            <div class="card mb">
                <h3 class="mb"><i class="fa-solid fa-image" style="color:var(--violet)"></i> Archivo del estudio</h3>
                @if($imagen->archivo)
                    @if($imagen->es_imagen)
                        <img src="{{ Storage::url($imagen->archivo) }}" alt="estudio" style="width:100%;border-radius:12px;border:1px solid var(--line)">
                    @else
                        <p class="muted"><i class="fa-solid fa-file"></i> {{ $imagen->archivo_nombre }}</p>
                    @endif
                    <a href="{{ Storage::url($imagen->archivo) }}" target="_blank" class="btn btn-light btn-sm mt"><i class="fa-solid fa-download"></i> Abrir archivo</a>
                @else
                    <p class="muted">Sin archivo cargado.</p>
                @endif
                <form method="POST" action="{{ route('imagenes.archivo',$imagen) }}" enctype="multipart/form-data" class="mt">
                    @csrf
                    <div class="flex gap" style="flex-wrap:wrap;align-items:center">
                        <input type="file" name="archivo" accept="image/*,application/pdf" required style="flex:1;min-width:150px">
                        <button class="btn btn-primary btn-sm"><i class="fa-solid fa-upload"></i> Subir</button>
                    </div>
                    @error('archivo')<span class="err">{{ $message }}</span>@enderror
                </form>
            </div>
        </div>
        <div class="card">
            <h3 class="mb"><i class="fa-solid fa-file-medical" style="color:var(--pink)"></i> Informe radiológico</h3>
            <form method="POST" action="{{ route('imagenes.informe',$imagen) }}">
                @csrf @method('PUT')
                <div class="field mb"><label>Radiólogo</label>
                    <select name="radiologo_id">
                        <option value="">—</option>
                        @foreach($radiologos as $r)<option value="{{ $r->id }}" @selected($imagen->radiologo_id==$r->id)>{{ $r->name }}</option>@endforeach
                    </select></div>
                <div class="field mb"><label>Hallazgos</label><textarea name="hallazgos" style="min-height:120px">{{ $imagen->hallazgos }}</textarea></div>
                <div class="field mb"><label>Conclusión</label><textarea name="conclusion">{{ $imagen->conclusion }}</textarea></div>
                <button class="btn btn-primary"><i class="fa-solid fa-floppy-disk"></i> Guardar informe</button>
            </form>
        </div>
    </div>
@endsection
