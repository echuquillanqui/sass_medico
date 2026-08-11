@extends('layouts.app')
@section('title', $paciente->exists ? 'Editar paciente' : 'Nuevo paciente')

@section('content')
    <div class="page-head">
        <div><h1>{{ $paciente->exists ? 'Editar paciente' : 'Nuevo paciente' }}</h1>
            <p>Completa la ficha del paciente.</p></div>
        <a href="{{ route('pacientes.index') }}" class="btn btn-ghost"><i class="fa-solid fa-arrow-left"></i> Volver</a>
    </div>

    <form method="POST" action="{{ $paciente->exists ? route('pacientes.update',$paciente) : route('pacientes.store') }}" class="card">
        @csrf
        @if($paciente->exists) @method('PUT') @endif
        <div class="form-grid">
            <div class="field"><label>Nombres *</label><input name="nombres" value="{{ old('nombres',$paciente->nombres) }}" required>@error('nombres')<span class="err">{{ $message }}</span>@enderror</div>
            <div class="field"><label>Apellidos *</label><input name="apellidos" value="{{ old('apellidos',$paciente->apellidos) }}" required>@error('apellidos')<span class="err">{{ $message }}</span>@enderror</div>
            <div class="field"><label>Tipo doc.</label>
                <select name="tipo_documento">
                    @foreach(['DNI','CE','Pasaporte'] as $td)
                        <option value="{{ $td }}" @selected(old('tipo_documento',$paciente->tipo_documento)==$td)>{{ $td }}</option>
                    @endforeach
                </select></div>
            <div class="field"><label>N° documento</label><input name="documento" value="{{ old('documento',$paciente->documento) }}"></div>
            <div class="field"><label>Fecha de nacimiento</label><input type="date" name="fecha_nacimiento" value="{{ old('fecha_nacimiento', optional($paciente->fecha_nacimiento)->format('Y-m-d')) }}"></div>
            <div class="field"><label>Sexo</label>
                <select name="sexo">
                    <option value="">—</option>
                    @foreach(['M'=>'Masculino','F'=>'Femenino','O'=>'Otro'] as $k=>$v)
                        <option value="{{ $k }}" @selected(old('sexo',$paciente->sexo)==$k)>{{ $v }}</option>
                    @endforeach
                </select></div>
            <div class="field"><label>Teléfono</label><input name="telefono" value="{{ old('telefono',$paciente->telefono) }}"></div>
            <div class="field"><label>Email</label><input type="email" name="email" value="{{ old('email',$paciente->email) }}"></div>
            <div class="field"><label>Grupo sanguíneo</label><input name="grupo_sanguineo" value="{{ old('grupo_sanguineo',$paciente->grupo_sanguineo) }}" placeholder="O+"></div>
            <div class="field"><label>Especialidad principal</label>
                <select name="especialidad_id">
                    <option value="">— Sin asignar —</option>
                    @foreach($especialidades as $e)
                        <option value="{{ $e->id }}" @selected(old('especialidad_id',$paciente->especialidad_id)==$e->id)>{{ $e->nombre }}</option>
                    @endforeach
                </select></div>
            <div class="field full"><label>Dirección</label><input name="direccion" value="{{ old('direccion',$paciente->direccion) }}"></div>
            <div class="field full"><label>Alergias</label><textarea name="alergias">{{ old('alergias',$paciente->alergias) }}</textarea></div>
            <div class="field full"><label>Antecedentes</label><textarea name="antecedentes">{{ old('antecedentes',$paciente->antecedentes) }}</textarea></div>
        </div>
        <div class="mt"><button class="btn btn-primary"><i class="fa-solid fa-floppy-disk"></i> Guardar paciente</button></div>
    </form>
@endsection
