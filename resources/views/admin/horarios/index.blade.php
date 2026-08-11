@extends('layouts.app')
@section('title', 'Horarios')

@section('content')
    <div class="page-head"><div><h1>Horarios de atención</h1><p>Disponibilidad semanal de cada médico. La reserva online la respeta.</p></div></div>

    @forelse($medicos as $m)
        <div class="card mb">
            <div class="flex between mb">
                <h3 style="margin:0"><span class="avatar-sm">{{ $m->initials() }}</span>{{ $m->titulo_profesional }} {{ $m->name }}</h3>
            </div>

            <div class="flex gap mb" style="flex-wrap:wrap">
                @forelse($m->horarios as $h)
                    <span class="pill violet" style="display:inline-flex;align-items:center;gap:8px">
                        {{ $dias[$h->dia_semana] }} {{ \Illuminate\Support\Str::of($h->hora_inicio)->substr(0,5) }}–{{ \Illuminate\Support\Str::of($h->hora_fin)->substr(0,5) }}
                        <form method="POST" action="{{ route('admin.horarios.destroy',$h) }}" style="display:inline">@csrf @method('DELETE')<button style="border:none;background:none;cursor:pointer;color:#be185d"><i class="fa-solid fa-xmark"></i></button></form>
                    </span>
                @empty
                    <span class="muted">Sin horarios definidos (se usa el horario general de la clínica).</span>
                @endforelse
            </div>

            <form method="POST" action="{{ route('admin.horarios.store') }}" class="flex gap" style="flex-wrap:wrap;align-items:flex-end">
                @csrf
                <input type="hidden" name="user_id" value="{{ $m->id }}">
                <div class="field"><label>Día</label>
                    <select name="dia_semana">
                        @foreach($dias as $i => $d)<option value="{{ $i }}">{{ $d }}</option>@endforeach
                    </select></div>
                <div class="field"><label>Desde</label><input type="time" name="hora_inicio" value="09:00" required></div>
                <div class="field"><label>Hasta</label><input type="time" name="hora_fin" value="13:00" required></div>
                <button class="btn btn-primary btn-sm"><i class="fa-solid fa-plus"></i> Agregar</button>
            </form>
        </div>
    @empty
        <div class="card"><div class="empty"><i class="fa-solid fa-user-doctor"></i><p>No hay médicos registrados. Créalos en Usuarios.</p></div></div>
    @endforelse
@endsection
