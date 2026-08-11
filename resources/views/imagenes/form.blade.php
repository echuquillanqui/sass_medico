@extends('layouts.app')
@section('title', 'Nuevo estudio de imágenes')

@section('content')
    <div class="page-head">
        <div><h1>Nuevo estudio de imágenes</h1><p>Solicitud de estudio radiológico o ecográfico.</p></div>
        <a href="{{ route('imagenes.index') }}" class="btn btn-ghost"><i class="fa-solid fa-arrow-left"></i> Volver</a>
    </div>

    @if($errors->any())<div class="alert error"><i class="fa-solid fa-circle-exclamation"></i> {{ $errors->first() }}</div>@endif

    <form method="POST" action="{{ route('imagenes.store') }}" class="card">
        @csrf
        <div class="form-grid">
            <div class="field"><label>Paciente *</label>
                <select name="paciente_id" required>
                    <option value="">— Selecciona —</option>
                    @foreach($pacientes as $p)<option value="{{ $p->id }}" @selected(old('paciente_id', $pacienteSel ?? null)==$p->id)>{{ $p->nombre_completo }}</option>@endforeach
                </select></div>
            <div class="field"><label>Médico solicitante</label>
                <select name="medico_id">
                    <option value="">— Yo —</option>
                    @foreach($medicos as $m)<option value="{{ $m->id }}" @selected(old('medico_id')==$m->id)>{{ $m->name }}</option>@endforeach
                </select></div>
            <div class="field"><label>Modalidad *</label>
                <select name="modalidad" required>
                    @foreach($modalidades as $mo)<option value="{{ $mo }}" @selected(old('modalidad')==$mo)>{{ $mo }}</option>@endforeach
                </select></div>
            <div class="field"><label>Región / Zona</label><input name="region" value="{{ old('region') }}" placeholder="Tórax, Abdomen, Rodilla..."></div>
            <div class="field"><label>Fecha *</label><input type="date" name="fecha" value="{{ old('fecha', now()->toDateString()) }}" required></div>
            <div class="field full"><label>Indicación / motivo del estudio</label><textarea name="indicacion">{{ old('indicacion') }}</textarea></div>
        </div>
        <div class="mt"><button class="btn btn-primary"><i class="fa-solid fa-x-ray"></i> Solicitar estudio</button></div>
    </form>
@endsection
