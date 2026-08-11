@extends('portal.layout')
@section('title','Inicio')
@section('content')
    <div class="flex between mb">
        <div>
            <h1 style="margin:0 0 4px">Hola, {{ explode(' ', $paciente->nombres)[0] }} 👋</h1>
            <p class="muted" style="margin:0">Bienvenido a tu portal de {{ $empresa->nombre ?? 'la clínica' }}.</p>
        </div>
        <a href="{{ route('portal.reservar') }}" class="btn btn-primary"><i class="fa-solid fa-calendar-plus"></i> Reservar cita</a>
    </div>

    <div class="grid g-3 mb">
        <div class="card pink"><div class="cap" style="font-size:12px;color:var(--ink-soft);text-transform:uppercase">Próximas citas</div><div style="font-size:28px;font-weight:700">{{ $proximas->count() }}</div></div>
        <div class="card"><div class="cap" style="font-size:12px;color:var(--ink-soft);text-transform:uppercase">Consultas</div><div style="font-size:28px;font-weight:700">{{ $paciente->consultas()->count() }}</div></div>
        <div class="card"><div class="cap" style="font-size:12px;color:var(--ink-soft);text-transform:uppercase">Documento</div><div style="font-size:20px;font-weight:600;margin-top:6px">{{ $paciente->tipo_documento }} {{ $paciente->documento }}</div></div>
    </div>

    @if($porCalificar)
        <div class="card mb" style="background:linear-gradient(135deg,#fdf2fb,#f5d0fe);border:1px solid #f0abfc">
            <div class="flex between" style="flex-wrap:wrap;gap:10px">
                <div>
                    <b style="font-size:15px"><i class="fa-solid fa-star" style="color:#f59e0b"></i> ¿Cómo fue tu última atención?</b>
                    <div class="muted">{{ $porCalificar->fecha->locale('es')->isoFormat('D MMM') }} · {{ $porCalificar->especialidad->nombre ?? 'General' }} · {{ $porCalificar->medico->name ?? '' }}</div>
                </div>
                <a href="{{ route('portal.cita.encuesta', $porCalificar) }}" class="btn btn-primary"><i class="fa-solid fa-comment-dots"></i> Calificar</a>
            </div>
        </div>
    @endif

    <div class="card" style="padding:0">
        <div style="padding:18px 20px 8px"><h3 style="margin:0">Mis próximas citas</h3></div>
        <div class="table-wrap" style="box-shadow:none;border-radius:0">
            <table>
                <thead><tr><th>Fecha</th><th>Hora</th><th>Especialidad</th><th>Médico</th><th>Estado</th><th></th></tr></thead>
                <tbody>
                @forelse($proximas as $c)
                    <tr>
                        <td>{{ $c->fecha->locale('es')->isoFormat('D MMM YYYY') }}</td>
                        <td>{{ \Illuminate\Support\Str::of($c->hora)->substr(0,5) }}</td>
                        <td>{{ $c->especialidad->nombre ?? '—' }}</td>
                        <td>{{ $c->medico->name ?? '—' }}</td>
                        <td>@include('citas.estado', ['estado' => $c->estado])</td>
                        <td style="text-align:right;white-space:nowrap">
                            @if($c->es_teleconsulta)
                                <a href="{{ $c->sala_video_url }}" target="_blank" class="btn btn-primary btn-sm"><i class="fa-solid fa-video"></i> Unirme</a>
                            @endif
                            @if($c->estado === 'pendiente')
                                <form method="POST" action="{{ route('portal.cita.confirmar', $c) }}" style="display:inline">@csrf<button class="btn btn-light btn-sm" style="color:#15803d"><i class="fa-solid fa-check"></i> Confirmar</button></form>
                                <a href="{{ route('portal.cita.editar', $c) }}" class="btn btn-light btn-sm"><i class="fa-solid fa-pen"></i></a>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6"><div class="empty"><i class="fa-regular fa-calendar"></i><p>No tienes citas próximas.</p></div></td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
