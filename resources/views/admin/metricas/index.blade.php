@extends('layouts.app')
@section('title', 'Métricas SaaS')

@section('content')
    <div class="page-head"><div><h1>Métricas del negocio</h1><p>Salud financiera de la plataforma (planes y suscripciones).</p></div></div>

    <div class="grid g-4 mb">
        <div class="card pink"><div class="cap" style="font-size:12px;color:var(--ink-soft);text-transform:uppercase">MRR estimado</div><div style="font-size:26px;font-weight:700;margin-top:6px;background:var(--grad);-webkit-background-clip:text;background-clip:text;-webkit-text-fill-color:transparent">S/ {{ number_format($mrr,0) }}</div></div>
        <div class="card"><div class="cap" style="font-size:12px;color:var(--ink-soft);text-transform:uppercase">ARR estimado</div><div style="font-size:26px;font-weight:700;margin-top:6px">S/ {{ number_format($arr,0) }}</div></div>
        <div class="card"><div class="cap" style="font-size:12px;color:var(--ink-soft);text-transform:uppercase">Empresas activas</div><div style="font-size:26px;font-weight:700;margin-top:6px">{{ $totalActivas }}</div></div>
        <div class="card"><div class="cap" style="font-size:12px;color:var(--ink-soft);text-transform:uppercase">Ingreso medio/empresa</div><div style="font-size:26px;font-weight:700;margin-top:6px">S/ {{ $totalActivas ? number_format($mrr/$totalActivas,0) : 0 }}</div></div>
    </div>

    <div class="grid g-2 mb">
        <div class="card"><h3 class="mb">Altas de empresas (6 meses)</h3><div class="chart-box" style="height:240px"><canvas id="chAltas"></canvas></div></div>
        <div class="card"><h3 class="mb">Distribución por plan</h3><div class="chart-box" style="height:240px"><canvas id="chPlan"></canvas></div></div>
    </div>

    @if($porVencer->isNotEmpty())
    <div class="card mb" style="border-left:4px solid #f59e0b">
        <h3 class="mb"><i class="fa-solid fa-triangle-exclamation" style="color:#d97706"></i> Suscripciones por vencer / vencidas</h3>
        <div class="grid g-3" style="gap:10px">
            @foreach($porVencer as $e)
                <div class="metric" style="text-align:left;border-style:solid">
                    <b style="font-size:13.5px">{{ $e->nombre }}</b>
                    <div class="muted" style="font-size:12px">{{ $e->planRef->nombre ?? ucfirst($e->plan) }} · vence {{ $e->vence_suscripcion->format('d/m/Y') }}</div>
                    @if($e->estado_suscripcion==='vencida')<span class="pill red">Vencida hace {{ abs($e->dias_restantes) }} d</span>
                    @else<span class="pill amber">Vence en {{ $e->dias_restantes }} d</span>@endif
                    <a href="{{ route('admin.suscripcion.show',$e) }}" class="btn btn-light btn-sm" style="margin-top:8px"><i class="fa-solid fa-receipt"></i> Renovar</a>
                </div>
            @endforeach
        </div>
    </div>
    @endif

    <div class="card" style="padding:0">
        <div style="padding:18px 22px 8px"><h3 style="margin:0">Empresas y suscripciones</h3></div>
        <div class="table-wrap" style="box-shadow:none;border-radius:0;overflow-x:auto">
            <table style="min-width:820px">
                <thead><tr><th>Empresa</th><th>Plan</th><th>MRR</th><th>Vence</th><th>Facturado (pagos)</th><th>Usuarios</th><th>Estado</th></tr></thead>
                <tbody>
                @foreach($empresas as $e)
                    <tr>
                        <td><span class="avatar-sm">{{ mb_substr($e->nombre,0,2) }}</span>{{ $e->nombre }}</td>
                        <td>@if($e->planRef)<span class="pill violet">{{ $e->planRef->nombre }}</span>@else<span class="pill gray">{{ ucfirst($e->plan) }}</span>@endif</td>
                        <td>S/ {{ number_format($mrrEmpresa[$e->id] ?? 0,0) }}</td>
                        <td>
                            @if($e->vence_suscripcion)
                                {{ $e->vence_suscripcion->format('d/m/Y') }}
                                @if($e->estado_suscripcion==='vencida')<span class="pill red">Vencida</span>
                                @elseif($e->estado_suscripcion==='por_vencer')<span class="pill amber">Por vencer</span>@endif
                            @else — @endif
                        </td>
                        <td><b>S/ {{ number_format($ingresosPorEmpresa[$e->id] ?? 0,2) }}</b></td>
                        <td>{{ $e->usuarios_count }}</td>
                        <td>@if($e->activo)<span class="pill green">Activa</span>@else<span class="pill red">Inactiva</span>@endif</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>

    @push('scripts')
    <script>
    window.addEventListener('load', function(){
        if(!window.Chart) return;
        new Chart(document.getElementById('chAltas'), {
            type:'line',
            data:{ labels:@json($labels), datasets:[{ data:@json($altas), borderColor:'#a855f7', backgroundColor:'rgba(168,85,247,.15)', fill:true, tension:.3, pointRadius:4 }] },
            options:{ responsive:true, maintainAspectRatio:false, plugins:{legend:{display:false}}, scales:{y:{beginAtZero:true,ticks:{precision:0}}} }
        });
        var planLabels=@json(array_keys($porPlan)), planData=@json(array_values($porPlan));
        var paleta=['#7c3aed','#ec4899','#14b8a6','#f59e0b','#2563eb','#0ea5e9','#c4b5fd'];
        new Chart(document.getElementById('chPlan'), {
            type:'doughnut',
            data:{ labels:planLabels.length?planLabels:['Sin datos'], datasets:[{ data:planData.length?planData:[1], backgroundColor:paleta, borderWidth:0, hoverOffset:8 }] },
            options:{ responsive:true, maintainAspectRatio:false, cutout:'60%', plugins:{legend:{position:'bottom',labels:{boxWidth:12,padding:14,font:{size:11}}}} }
        });
    });
    </script>
    @endpush
@endsection
