@extends('layouts.app')
@section('title', $cita->exists ? 'Editar cita' : 'Nueva cita')

@section('content')
    <div class="page-head">
        <div><h1>{{ $cita->exists ? 'Editar cita' : 'Nueva cita' }}</h1><p>Programa la atención del paciente.</p></div>
        <a href="{{ route('citas.index') }}" class="btn btn-ghost"><i class="fa-solid fa-arrow-left"></i> Volver</a>
    </div>

    <form method="POST" action="{{ $cita->exists ? route('citas.update',$cita) : route('citas.store') }}" class="card">
        @csrf
        @if($cita->exists) @method('PUT') @endif
        <div class="form-grid">
            <div class="field"><label>Paciente *</label>
                <select name="paciente_id" required>
                    <option value="">— Selecciona —</option>
                    @foreach($pacientes as $p)
                        <option value="{{ $p->id }}" @selected(old('paciente_id',$cita->paciente_id)==$p->id)>{{ $p->nombre_completo }}</option>
                    @endforeach
                </select>@error('paciente_id')<span class="err">{{ $message }}</span>@enderror</div>
            <div class="field"><label>Especialidad</label>
                <select name="especialidad_id">
                    <option value="">—</option>
                    @foreach($especialidades as $e)
                        <option value="{{ $e->id }}" @selected(old('especialidad_id',$cita->especialidad_id)==$e->id)>{{ $e->nombre }}</option>
                    @endforeach
                </select></div>
            <div class="field"><label>Médico</label>
                <select name="medico_id">
                    <option value="">— Sin asignar —</option>
                    @foreach($medicos as $m)
                        <option value="{{ $m->id }}" @selected(old('medico_id',$cita->medico_id)==$m->id)>{{ $m->name }}</option>
                    @endforeach
                </select></div>
            <div class="field"><label>Estado</label>
                <select name="estado" required>
                    @foreach(['pendiente'=>'Pendiente','confirmada'=>'Confirmada','atendida'=>'Atendida','cancelada'=>'Cancelada','no_asistio'=>'No asistió'] as $k=>$v)
                        <option value="{{ $k }}" @selected(old('estado',$cita->estado ?? 'pendiente')==$k)>{{ $v }}</option>
                    @endforeach
                </select></div>
            <div class="field"><label>Fecha *</label><input type="date" name="fecha" value="{{ old('fecha', optional($cita->fecha)->format('Y-m-d') ?? $cita->fecha) }}" required></div>
            <div class="field"><label>Hora *</label><input type="time" name="hora" value="{{ old('hora', \Illuminate\Support\Str::of($cita->hora)->substr(0,5)) }}" required></div>
            <div class="field"><label>Duración (min)</label><input type="number" name="duracion" value="{{ old('duracion',$cita->duracion ?? 30) }}"></div>
            <div class="field"><label>Modalidad</label>
                <label style="display:flex;align-items:center;gap:8px;font-weight:400;margin-top:6px">
                    <input type="checkbox" name="es_teleconsulta" value="1" @checked(old('es_teleconsulta',$cita->es_teleconsulta ?? false))>
                    <span>Teleconsulta (videollamada)</span>
                </label>
            </div>
            <div class="field full"><label>Motivo</label><input name="motivo" value="{{ old('motivo',$cita->motivo) }}"></div>
            <div class="field full"><label>Notas</label><textarea name="notas">{{ old('notas',$cita->notas) }}</textarea></div>
        </div>
        <div class="mt"><button class="btn btn-primary"><i class="fa-solid fa-floppy-disk"></i> Guardar cita</button></div>
    </form>
@endsection
