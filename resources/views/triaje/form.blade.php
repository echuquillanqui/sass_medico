@extends('layouts.app')
@section('title', 'Registrar en emergencias')

@section('content')
    <div class="page-head">
        <div><h1>Registrar paciente en emergencias</h1><p>Clasificación de prioridad (Manchester).</p></div>
        <a href="{{ route('triaje.index') }}" class="btn btn-ghost"><i class="fa-solid fa-arrow-left"></i> Volver</a>
    </div>

    @if($errors->any())<div class="alert error"><i class="fa-solid fa-circle-exclamation"></i> {{ $errors->first() }}</div>@endif

    <form method="POST" action="{{ route('triaje.store') }}" class="card">
        @csrf
        <div class="form-grid mb">
            <div class="field"><label>Paciente *</label>
                <select name="paciente_id" required>
                    <option value="">— Selecciona —</option>
                    @foreach($pacientes as $p)<option value="{{ $p->id }}" @selected(old('paciente_id')==$p->id)>{{ $p->nombre_completo }}</option>@endforeach
                </select></div>
            <div class="field full"><label>Motivo de consulta *</label><input name="motivo" value="{{ old('motivo') }}" required></div>
        </div>

        <h3 class="mb">Nivel de prioridad *</h3>
        <div class="grid g-3 mb" style="gap:10px">
            @foreach($niveles as $n => $info)
                <label class="card" style="cursor:pointer;padding:12px;border:2px solid {{ $info['color'] }}33;display:flex;align-items:center;gap:10px">
                    <input type="radio" name="nivel" value="{{ $n }}" @checked(old('nivel')==$n) required>
                    <span style="width:34px;height:34px;border-radius:10px;background:{{ $info['color'] }};color:#fff;display:grid;place-items:center;font-weight:700">{{ $n }}</span>
                    <span><b style="font-size:13px">{{ $info['nombre'] }}</b><br><small class="muted">{{ $info['label'] }} · {{ $info['espera'] }}</small></span>
                </label>
            @endforeach
        </div>

        <h3 class="mb">Signos vitales</h3>
        <div class="form-grid mb">
            <div class="field"><label>Presión arterial</label><input name="presion_arterial" placeholder="120/80"></div>
            <div class="field"><label>Frec. cardíaca</label><input type="number" name="frecuencia_cardiaca"></div>
            <div class="field"><label>Frec. respiratoria</label><input type="number" name="frecuencia_respiratoria"></div>
            <div class="field"><label>Temperatura</label><input type="number" step="0.1" name="temperatura"></div>
            <div class="field"><label>Saturación O₂</label><input name="saturacion" placeholder="98%"></div>
            <div class="field"><label>Dolor (0-10)</label><input type="number" min="0" max="10" name="dolor"></div>
            <div class="field full"><label>Observaciones</label><textarea name="observaciones">{{ old('observaciones') }}</textarea></div>
        </div>

        <button class="btn btn-primary"><i class="fa-solid fa-truck-medical"></i> Registrar en cola</button>
    </form>
@endsection
