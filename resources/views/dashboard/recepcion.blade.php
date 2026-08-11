@extends('layouts.app')
@section('title', 'Dashboard')
@section('content')
    @php $mon = $empresa->moneda ?? 'S/'; @endphp
    <div class="page-head">
        <div><h1>Recepción 🗂️</h1><p>{{ now()->locale('es')->isoFormat('dddd D [de] MMMM') }}</p></div>
        <div class="flex gap">
            <a href="{{ route('citas.create') }}" class="btn btn-primary"><i class="fa-solid fa-calendar-plus"></i> Nueva cita</a>
            <a href="{{ route('pagos.create') }}" class="btn btn-light"><i class="fa-solid fa-money-bill"></i> Cobrar</a>
        </div>
    </div>

    <div class="grid g-4 mb">
        <div class="card"><div class="cap" style="font-size:12px;color:var(--ink-soft);text-transform:uppercase">Citas hoy</div><div style="font-size:28px;font-weight:700;margin-top:6px">{{ $citasHoy }}</div></div>
        <div class="card"><div class="cap" style="font-size:12px;color:var(--ink-soft);text-transform:uppercase">Por confirmar</div><div style="font-size:28px;font-weight:700;margin-top:6px">{{ $pendientes }}</div></div>
        <div class="card pink"><div class="cap" style="font-size:12px;color:var(--ink-soft);text-transform:uppercase">Cobrado hoy</div><div style="font-size:24px;font-weight:700;margin-top:6px;color:#15803d">@money($cobradoHoy, null, 2)</div></div>
        <div class="card" style="{{ $bajoStock ? 'background:#fef2f2' : '' }}"><div class="cap" style="font-size:12px;color:var(--ink-soft);text-transform:uppercase">Bajo stock</div><div style="font-size:28px;font-weight:700;margin-top:6px;color:{{ $bajoStock ? '#dc2626':'inherit' }}">{{ $bajoStock }}</div></div>
    </div>

    <div class="card" style="padding:0">
        <div style="padding:18px 22px 8px"><h3 style="margin:0">Agenda de hoy</h3></div>
        <div class="table-wrap" style="box-shadow:none;border-radius:0">
            <table>
                <thead><tr><th>Hora</th><th>Paciente</th><th>Especialidad</th><th>Médico</th><th>Estado</th></tr></thead>
                <tbody>
                @forelse($agendaHoy as $c)
                    <tr>
                        <td><b>{{ \Illuminate\Support\Str::of($c->hora)->substr(0,5) }}</b></td>
                        <td>{{ $c->paciente->nombre_completo }}</td>
                        <td>{{ $c->especialidad->nombre ?? '—' }}</td>
                        <td>{{ $c->medico->name ?? '—' }}</td>
                        <td>@include('citas.estado', ['estado' => $c->estado])</td>
                    </tr>
                @empty
                    <tr><td colspan="5"><div class="empty"><i class="fa-regular fa-calendar"></i><p>No hay citas para hoy.</p></div></td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
