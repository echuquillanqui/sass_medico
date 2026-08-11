@extends('layouts.app')
@section('title', 'Dashboard')
@section('content')
    <div class="page-head">
        <div><h1>Hola, {{ explode(' ', auth()->user()->name)[0] }} 👨‍⚕️</h1>
            <p>Tu jornada · {{ now()->locale('es')->isoFormat('dddd D [de] MMMM') }}</p></div>
        <a href="{{ route('agenda.index') }}" class="btn btn-primary"><i class="fa-regular fa-calendar-days"></i> Ver agenda</a>
    </div>

    <div class="grid g-3 mb">
        <div class="stat"><div class="ring" style="background:conic-gradient(#ec4899 0% {{ min(100,$citasHoy*15) }}%, #f3d9f0 0)"><b>{{ $citasHoy }}</b></div>
            <div class="info"><h4><span class="icn"><i class="fa-solid fa-calendar-day"></i></span> Citas hoy</h4><small>Pacientes agendados contigo hoy.</small></div></div>
        <div class="stat"><div class="ring" style="background:conic-gradient(#a855f7 0% {{ min(100,$pendientesHoy*20) }}%, #f3d9f0 0)"><b>{{ $pendientesHoy }}</b></div>
            <div class="info"><h4><span class="icn"><i class="fa-solid fa-clock"></i></span> Por atender</h4><small>Citas pendientes de hoy.</small></div></div>
        <div class="stat"><div class="ring" style="background:conic-gradient(#22c55e 0% {{ min(100,$atendidasMes*5) }}%, #f3d9f0 0)"><b>{{ $atendidasMes }}</b></div>
            <div class="info"><h4><span class="icn"><i class="fa-solid fa-user-check"></i></span> Atendidas (mes)</h4><small>Total de consultas este mes.</small></div></div>
    </div>

    <div class="card" style="padding:0">
        <div style="padding:18px 22px 8px"><h3 style="margin:0">Agenda de hoy</h3></div>
        <div class="table-wrap" style="box-shadow:none;border-radius:0">
            <table>
                <thead><tr><th>Hora</th><th>Paciente</th><th>Especialidad</th><th>Estado</th><th></th></tr></thead>
                <tbody>
                @forelse($agendaHoy as $c)
                    <tr>
                        <td><b>{{ \Illuminate\Support\Str::of($c->hora)->substr(0,5) }}</b></td>
                        <td>{{ $c->paciente->nombre_completo }}</td>
                        <td>{{ $c->especialidad->nombre ?? '—' }}</td>
                        <td>@include('citas.estado', ['estado' => $c->estado])</td>
                        <td style="text-align:right">@if(in_array($c->estado,['pendiente','confirmada']))<a href="{{ route('consultas.create',['paciente_id'=>$c->paciente_id,'cita_id'=>$c->id]) }}" class="btn btn-primary btn-sm"><i class="fa-solid fa-stethoscope"></i> Atender</a>@endif</td>
                    </tr>
                @empty
                    <tr><td colspan="5"><div class="empty"><i class="fa-regular fa-calendar-check"></i><p>No tienes citas hoy.</p></div></td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
