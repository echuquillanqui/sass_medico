@extends('layouts.app')
@section('title', $especialidad->exists ? 'Editar especialidad' : 'Nueva especialidad')

@section('content')
    <div class="page-head">
        <div><h1>{{ $especialidad->exists ? 'Editar especialidad' : 'Nueva especialidad' }}</h1><p>Define el módulo clínico.</p></div>
        <a href="{{ route('admin.especialidades.index') }}" class="btn btn-ghost"><i class="fa-solid fa-arrow-left"></i> Volver</a>
    </div>

    <form method="POST" action="{{ $especialidad->exists ? route('admin.especialidades.update',$especialidad) : route('admin.especialidades.store') }}" class="card">
        @csrf
        @if($especialidad->exists) @method('PUT') @endif
        <div class="form-grid">
            <div class="field"><label>Nombre *</label><input name="nombre" value="{{ old('nombre',$especialidad->nombre) }}" required>@error('nombre')<span class="err">{{ $message }}</span>@enderror</div>
            <div class="field"><label>Ícono (Font Awesome)</label><input name="icono" value="{{ old('icono',$especialidad->icono) }}" placeholder="fa-stethoscope"></div>
            <div class="field"><label>Color</label><input type="color" name="color" value="{{ old('color',$especialidad->color ?? '#7c3aed') }}" style="height:44px"></div>
            <div class="field"><label>Estado</label>
                <select name="activo"><option value="1" @selected(old('activo',$especialidad->activo ?? 1))>Activa</option><option value="0" @selected(!old('activo',$especialidad->activo ?? 1))>Inactiva</option></select></div>
            <div class="field full"><label>Descripción</label><input name="descripcion" value="{{ old('descripcion',$especialidad->descripcion) }}"></div>
        </div>
        <div class="mt"><button class="btn btn-primary"><i class="fa-solid fa-floppy-disk"></i> Guardar</button></div>
        <p class="muted mt" style="font-size:12px">Íconos disponibles en fontawesome.com/icons (usa el nombre completo, ej. <b>fa-tooth</b>).</p>
    </form>
@endsection
