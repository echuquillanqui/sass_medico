@extends('portal.layout')
@section('title','Reprogramar cita')
@section('content')
    <div class="flex between mb"><h1 style="margin:0">Reprogramar cita</h1><a href="{{ route('portal.dashboard') }}" class="btn btn-ghost btn-sm">Volver</a></div>

    @if($errors->any())<div class="alert error"><i class="fa-solid fa-circle-exclamation"></i> {{ $errors->first() }}</div>@endif

    <div class="card mb">
        <p class="muted" style="margin:0"><b>Cita actual:</b> {{ $cita->fecha->locale('es')->isoFormat('D MMM YYYY') }} · {{ \Illuminate\Support\Str::of($cita->hora)->substr(0,5) }}
        · {{ $cita->especialidad->nombre ?? 'General' }} · {{ $cita->medico->name ?? 'Sin médico' }}</p>
    </div>

    <form method="POST" action="{{ route('portal.cita.actualizar', $cita) }}" class="card mb">
        @csrf @method('PUT')
        <div class="form-grid">
            <div class="field"><label>Nueva fecha *</label><input type="date" name="fecha" min="{{ now()->toDateString() }}" value="{{ old('fecha', $cita->fecha->format('Y-m-d')) }}" required></div>
            <div class="field"><label>Nueva hora *</label>
                <select name="hora" required>
                    @foreach($franjas as $h)<option value="{{ $h }}" @selected(\Illuminate\Support\Str::of($cita->hora)->substr(0,5)==$h)>{{ $h }}</option>@endforeach
                </select></div>
        </div>
        <div class="mt"><button class="btn btn-primary"><i class="fa-solid fa-calendar-check"></i> Guardar cambios</button></div>
    </form>

    <form method="POST" action="{{ route('portal.cita.cancelar', $cita) }}" onsubmit="return confirm('¿Cancelar esta cita?')">
        @csrf
        <button class="btn btn-danger"><i class="fa-solid fa-ban"></i> Cancelar esta cita</button>
    </form>
@endsection
