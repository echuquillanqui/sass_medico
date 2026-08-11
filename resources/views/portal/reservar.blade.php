@extends('portal.layout')
@section('title','Reservar cita')
@section('content')
    <div class="flex between mb"><h1 style="margin:0">Reservar una cita</h1><a href="{{ route('portal.dashboard') }}" class="btn btn-ghost btn-sm">Volver</a></div>

    @if($errors->any())<div class="alert error"><i class="fa-solid fa-circle-exclamation"></i> {{ $errors->first() }}</div>@endif

    <form method="POST" action="{{ route('portal.reservar.store') }}" class="card">
        @csrf
        <div class="form-grid">
            <div class="field"><label>Especialidad</label>
                <select name="especialidad_id">
                    <option value="">— Indiferente —</option>
                    @foreach($especialidades as $e)<option value="{{ $e->id }}" @selected(old('especialidad_id')==$e->id)>{{ $e->nombre }}</option>@endforeach
                </select></div>
            <div class="field"><label>Médico</label>
                <select name="medico_id">
                    <option value="">— Cualquiera disponible —</option>
                    @foreach($medicos as $m)<option value="{{ $m->id }}" @selected(old('medico_id')==$m->id)>{{ $m->titulo_profesional ? $m->titulo_profesional.' ' : '' }}{{ $m->name }}</option>@endforeach
                </select></div>
            <div class="field"><label>Fecha *</label><input type="date" name="fecha" min="{{ now()->toDateString() }}" value="{{ old('fecha', now()->toDateString()) }}" required></div>
            <div class="field"><label>Hora *</label>
                <select name="hora" required>
                    @foreach($franjas as $h)<option value="{{ $h }}" @selected(old('hora')==$h)>{{ $h }}</option>@endforeach
                </select></div>
            <div class="field full"><label>Motivo (opcional)</label><input name="motivo" value="{{ old('motivo') }}" placeholder="Ej. control, dolor..."></div>
        </div>
        <div class="mt"><button class="btn btn-primary"><i class="fa-solid fa-calendar-check"></i> Solicitar cita</button></div>
        <p class="muted mt" style="font-size:12px">Horario de atención: {{ $empresa->horario_inicio ?? '08:00' }} - {{ $empresa->horario_fin ?? '18:00' }} · {{ $empresa->dias_atencion ?? '' }}</p>
    </form>
@endsection
