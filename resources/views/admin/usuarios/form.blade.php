@extends('layouts.app')
@section('title', $usuario->exists ? 'Editar usuario' : 'Nuevo usuario')

@section('content')
    <div class="page-head">
        <div><h1>{{ $usuario->exists ? 'Editar usuario' : 'Nuevo usuario' }}</h1><p>Datos de acceso y rol del miembro del equipo.</p></div>
        <a href="{{ route('admin.usuarios.index') }}" class="btn btn-ghost"><i class="fa-solid fa-arrow-left"></i> Volver</a>
    </div>

    <form method="POST" action="{{ $usuario->exists ? route('admin.usuarios.update',$usuario) : route('admin.usuarios.store') }}" class="card">
        @csrf
        @if($usuario->exists) @method('PUT') @endif
        <div class="form-grid">
            <div class="field"><label>Nombre completo *</label><input name="name" value="{{ old('name',$usuario->name) }}" required>@error('name')<span class="err">{{ $message }}</span>@enderror</div>
            <div class="field"><label>Correo *</label><input type="email" name="email" value="{{ old('email',$usuario->email) }}" required>@error('email')<span class="err">{{ $message }}</span>@enderror</div>
            <div class="field"><label>Contraseña {{ $usuario->exists ? '(dejar en blanco para no cambiar)' : '*' }}</label><input type="password" name="password">@error('password')<span class="err">{{ $message }}</span>@enderror</div>
            <div class="field"><label>Rol *</label>
                <select name="role" required>
                    @foreach(['admin'=>'Administrador','medico'=>'Médico','recepcion'=>'Recepción'] as $k=>$v)
                        <option value="{{ $k }}" @selected(old('role',$usuario->role)==$k)>{{ $v }}</option>
                    @endforeach
                </select></div>
            <div class="field"><label>Especialidad (para médicos)</label>
                <select name="especialidad_id">
                    <option value="">— Ninguna —</option>
                    @foreach($especialidades as $e)
                        <option value="{{ $e->id }}" @selected(old('especialidad_id',$usuario->especialidad_id)==$e->id)>{{ $e->nombre }}</option>
                    @endforeach
                </select></div>
            <div class="field"><label>Teléfono</label><input name="telefono" value="{{ old('telefono',$usuario->telefono) }}"></div>
            <div class="field"><label>Título profesional</label><input name="titulo_profesional" value="{{ old('titulo_profesional',$usuario->titulo_profesional) }}" placeholder="Dr., Dra., Lic."></div>
            <div class="field"><label>CMP / Colegiatura</label><input name="cmp" value="{{ old('cmp',$usuario->cmp) }}"></div>
            @if($usuario->exists)
            <div class="field"><label>Estado</label>
                <select name="activo">
                    <option value="1" @selected($usuario->activo)>Activo</option>
                    <option value="0" @selected(!$usuario->activo)>Inactivo</option>
                </select></div>
            @endif
        </div>
        <div class="mt"><button class="btn btn-primary"><i class="fa-solid fa-floppy-disk"></i> Guardar usuario</button></div>
    </form>
@endsection
