@extends('layouts.app')
@section('title', 'Emergencias')

@section('content')
    <div class="page-head">
        <div><h1>Emergencias / Triaje</h1><p>Cola de atención por prioridad · {{ now()->locale('es')->isoFormat('dddd D MMM') }}</p></div>
        <a href="{{ route('triaje.create') }}" class="btn btn-primary"><i class="fa-solid fa-truck-medical"></i> Registrar paciente</a>
    </div>

    <div class="grid g-4 mb">
        <div class="kpi k4"><div class="kpi-top"><span class="kpi-cap">En espera</span><span class="kpi-ic"><i class="fa-solid fa-hourglass-half"></i></span></div><div><div class="kpi-val">{{ $enEspera }}</div></div></div>
        <div class="kpi k3"><div class="kpi-top"><span class="kpi-cap">En atención</span><span class="kpi-ic"><i class="fa-solid fa-user-doctor"></i></span></div><div><div class="kpi-val">{{ $enAtencion }}</div></div></div>
        <div class="kpi k2"><div class="kpi-top"><span class="kpi-cap">Atendidos hoy</span><span class="kpi-ic"><i class="fa-solid fa-circle-check"></i></span></div><div><div class="kpi-val">{{ $atendidosHoy }}</div></div></div>
        <div class="kpi k1"><div class="kpi-top"><span class="kpi-cap">Total en cola</span><span class="kpi-ic"><i class="fa-solid fa-list-ol"></i></span></div><div><div class="kpi-val">{{ $cola->count() }}</div></div></div>
    </div>

    <div class="card mb">
        <div class="flex gap" style="flex-wrap:wrap">
            @foreach($niveles as $n => $info)
                <span class="pill" style="background:{{ $info['color'] }}22;color:{{ $info['color'] }}"><b>{{ $n }}</b> {{ $info['nombre'] }} · {{ $info['label'] }} ({{ $info['espera'] }})</span>
            @endforeach
        </div>
    </div>

    @forelse($cola as $t)
        @php $info = $t->nivel_info; @endphp
        <div class="card mb" style="border-left:6px solid {{ $info['color'] }};display:flex;gap:16px;align-items:center;flex-wrap:wrap">
            <div style="width:52px;height:52px;border-radius:14px;background:{{ $info['color'] }};color:#fff;display:grid;place-items:center;font-size:22px;font-weight:700">{{ $t->nivel }}</div>
            <div style="flex:1;min-width:180px">
                <b style="font-size:15px">{{ $t->paciente->nombre_completo }}</b>
                <span class="pill" style="background:{{ $info['color'] }}22;color:{{ $info['color'] }};margin-left:6px">{{ $info['nombre'] }}</span>
                <div class="muted" style="font-size:13px;margin-top:2px">{{ $t->motivo }}</div>
                <div class="muted" style="font-size:12px;margin-top:2px">
                    Llegó {{ $t->hora_llegada->diffForHumans() }}
                    @if($t->presion_arterial) · PA {{ $t->presion_arterial }}@endif
                    @if($t->frecuencia_cardiaca) · FC {{ $t->frecuencia_cardiaca }}@endif
                    @if($t->saturacion) · SatO₂ {{ $t->saturacion }}@endif
                    @if($t->dolor !== null) · Dolor {{ $t->dolor }}/10 @endif
                </div>
            </div>
            <div class="flex gap">
                @if($t->estado === 'en_espera')
                    <form method="POST" action="{{ route('triaje.atender', $t) }}">@csrf<button class="btn btn-primary btn-sm"><i class="fa-solid fa-user-doctor"></i> Atender</button></form>
                @else
                    <span class="pill blue"><i class="fa-solid fa-user-doctor"></i> En atención</span>
                    <form method="POST" action="{{ route('triaje.finalizar', $t) }}">@csrf<button class="btn btn-primary btn-sm"><i class="fa-solid fa-check"></i> Finalizar</button></form>
                @endif
                <form method="POST" action="{{ route('triaje.destroy', $t) }}" onsubmit="return confirm('¿Eliminar registro?')">@csrf @method('DELETE')<button class="btn btn-danger btn-sm"><i class="fa-solid fa-xmark"></i></button></form>
            </div>
        </div>
    @empty
        <div class="card"><div class="empty"><i class="fa-solid fa-truck-medical"></i><p>No hay pacientes en la cola de emergencias.</p></div></div>
    @endforelse

    @push('scripts')<script>setTimeout(()=>location.reload(), 30000);</script>@endpush
@endsection
