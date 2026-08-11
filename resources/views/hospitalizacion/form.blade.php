@extends('layouts.app')
@section('title', 'Nuevo ingreso')

@section('content')
    <div class="page-head">
        <div><h1>Nuevo ingreso hospitalario</h1><p>Registra el internamiento del paciente.</p></div>
        <a href="{{ route('hospitalizacion.index') }}" class="btn btn-ghost"><i class="fa-solid fa-arrow-left"></i> Volver</a>
    </div>

    @if($errors->any())<div class="alert error"><i class="fa-solid fa-circle-exclamation"></i> {{ $errors->first() }}</div>@endif

    <form method="POST" action="{{ route('hospitalizacion.store') }}" class="card">
        @csrf
        <div class="form-grid">
            <div class="field"><label>Paciente *</label>
                <select name="paciente_id" required>
                    <option value="">— Selecciona —</option>
                    @foreach($pacientes as $p)<option value="{{ $p->id }}" @selected(old('paciente_id')==$p->id)>{{ $p->nombre_completo }}</option>@endforeach
                </select></div>
            <div class="field"><label>Cama</label>
                <select name="cama_id">
                    <option value="">— Sin asignar —</option>
                    @foreach($camasLibres as $c)<option value="{{ $c->id }}" @selected(old('cama_id')==$c->id)>{{ $c->nombre }} ({{ $c->area ?? 'General' }})</option>@endforeach
                </select>@error('cama_id')<span class="err">{{ $message }}</span>@enderror</div>
            <div class="field"><label>Médico tratante</label>
                <select name="medico_id">
                    <option value="">— Yo —</option>
                    @foreach($medicos as $m)<option value="{{ $m->id }}" @selected(old('medico_id')==$m->id)>{{ $m->name }}</option>@endforeach
                </select></div>
            <div class="field"><label>Especialidad</label>
                <select name="especialidad_id">
                    <option value="">—</option>
                    @foreach($especialidades as $e)<option value="{{ $e->id }}" @selected(old('especialidad_id')==$e->id)>{{ $e->nombre }}</option>@endforeach
                </select></div>
            <div class="field"><label>Fecha/hora de ingreso *</label><input type="datetime-local" name="fecha_ingreso" value="{{ old('fecha_ingreso', now()->format('Y-m-d\TH:i')) }}" required></div>
            <div class="field full"><label>Motivo de ingreso</label><textarea name="motivo_ingreso">{{ old('motivo_ingreso') }}</textarea></div>
            <div class="field full"><label>Diagnóstico de ingreso</label><textarea name="diagnostico_ingreso">{{ old('diagnostico_ingreso') }}</textarea></div>
        </div>
        <div class="mt"><button class="btn btn-primary"><i class="fa-solid fa-bed-pulse"></i> Registrar ingreso</button></div>
    </form>
@endsection
