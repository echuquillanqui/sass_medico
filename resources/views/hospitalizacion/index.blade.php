@extends('layouts.app')
@section('title', 'Hospitalización')

@section('content')
    <div class="page-head">
        <div><h1>Hospitalización</h1><p>Pacientes internados y ocupación de camas.</p></div>
        <a href="{{ route('hospitalizacion.create') }}" class="btn btn-primary"><i class="fa-solid fa-bed-pulse"></i> Nuevo ingreso</a>
    </div>

    <div class="grid g-4 mb">
        <div class="kpi k2"><div class="kpi-top"><span class="kpi-cap">Internados</span><span class="kpi-ic"><i class="fa-solid fa-user-injured"></i></span></div><div><div class="kpi-val">{{ $activas->count() }}</div></div></div>
        <div class="kpi k1"><div class="kpi-top"><span class="kpi-cap">Camas</span><span class="kpi-ic"><i class="fa-solid fa-bed"></i></span></div><div><div class="kpi-val">{{ $totalCamas }}</div></div></div>
        <div class="kpi k4"><div class="kpi-top"><span class="kpi-cap">Ocupadas</span><span class="kpi-ic"><i class="fa-solid fa-bed-pulse"></i></span></div><div><div class="kpi-val">{{ $ocupadas }}</div></div></div>
        <div class="kpi k3"><div class="kpi-top"><span class="kpi-cap">Libres</span><span class="kpi-ic"><i class="fa-solid fa-check"></i></span></div><div><div class="kpi-val">{{ $totalCamas - $ocupadas }}</div></div></div>
    </div>

    <div class="card mb">
        <h3 class="mb">Mapa de camas</h3>
        <div class="grid g-4" style="gap:12px">
            @forelse($camas as $c)
                @php $h = $c->hospitalizacionActiva(); @endphp
                <div class="card" style="padding:14px;box-shadow:none;border:1px solid var(--line);background:{{ $c->ocupada_count ? '#fef2f2' : '#f0fdf4' }}">
                    <div class="flex between"><b>{{ $c->nombre }}</b>@if($c->ocupada_count)<span class="pill red">Ocupada</span>@else<span class="pill green">Libre</span>@endif</div>
                    <div class="muted" style="font-size:12px">{{ $c->area ?? 'General' }}</div>
                    @if($h)<div style="margin-top:6px;font-size:13px"><i class="fa-solid fa-user"></i> {{ $h->paciente->nombre_completo }}</div>@endif
                </div>
            @empty
                <p class="muted">No hay camas registradas. Créalas en Administración → Camas.</p>
            @endforelse
        </div>
    </div>

    <div class="card" style="padding:0">
        <div style="padding:18px 22px 8px"><h3 style="margin:0">Pacientes internados</h3></div>
        <div class="table-wrap" style="box-shadow:none;border-radius:0">
            <table>
                <thead><tr><th>Paciente</th><th>Cama</th><th>Médico</th><th>Ingreso</th><th>Días</th><th></th></tr></thead>
                <tbody>
                @forelse($activas as $h)
                    <tr>
                        <td><span class="avatar-sm">{{ mb_substr($h->paciente->nombres,0,1) }}{{ mb_substr($h->paciente->apellidos,0,1) }}</span>{{ $h->paciente->nombre_completo }}</td>
                        <td>{{ $h->cama->nombre ?? '—' }}</td>
                        <td>{{ $h->medico->name ?? '—' }}</td>
                        <td>{{ $h->fecha_ingreso->format('d/m/Y H:i') }}</td>
                        <td>{{ $h->dias_estancia }}</td>
                        <td style="text-align:right"><a href="{{ route('hospitalizacion.show',$h) }}" class="btn btn-primary btn-sm"><i class="fa-solid fa-notes-medical"></i> Ver / Evolución</a></td>
                    </tr>
                @empty
                    <tr><td colspan="6"><div class="empty"><i class="fa-solid fa-bed"></i><p>No hay pacientes internados.</p></div></td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
