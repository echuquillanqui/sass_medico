@extends('layouts.app')
@section('title', 'Evaluación cardiovascular')

@section('content')
    <div class="page-head">
        <div class="flex gap">
            <div style="width:56px;height:56px;border-radius:16px;background:#ef4444;color:#fff;display:grid;place-items:center;font-size:24px">
                <i class="fa-solid fa-heart-pulse"></i>
            </div>
            <div>
                <h1>Evaluación cardiovascular</h1>
                <p>Presión, perfil lipídico, ECG y riesgo cardiovascular.</p>
            </div>
        </div>
        <a href="{{ route('pacientes.create') }}" class="btn btn-primary"><i class="fa-solid fa-user-plus"></i> Nuevo paciente</a>
    </div>

    <div class="card mb">
        <form method="GET" class="flex gap" style="flex-wrap:wrap">
            <input name="q" value="{{ $buscar }}" placeholder="Buscar paciente por nombre o documento…" style="flex:1;min-width:220px">
            <button class="btn btn-light"><i class="fa-solid fa-magnifying-glass"></i> Buscar</button>
        </form>
    </div>

    <div class="table-wrap">
        <table>
            <thead><tr><th>Paciente</th><th>Edad</th><th>Última evaluación</th><th>Riesgo CV</th><th>Registros</th><th></th></tr></thead>
            <tbody>
            @forelse($pacientes as $p)
                @php $u = $p->evaluacionesCardio->first();
                     $col = ['bajo'=>'green','moderado'=>'orange','alto'=>'red','muy alto'=>'red'][$u->riesgo_nivel ?? ''] ?? 'gray'; @endphp
                <tr>
                    <td><span class="avatar-sm">{{ mb_substr($p->nombres,0,1) }}{{ mb_substr($p->apellidos,0,1) }}</span>{{ $p->nombre_completo }}</td>
                    <td>{{ $p->edad !== null ? $p->edad.' años' : '—' }}</td>
                    <td>{{ $u ? $u->fecha->locale('es')->isoFormat('D MMM YYYY') : '—' }}</td>
                    <td>@if($u)<span class="pill {{ $col }}">{{ ucfirst($u->riesgo_nivel) }} · {{ $u->riesgo_pct }}%</span>@else<span class="pill gray">Sin evaluar</span>@endif</td>
                    <td>{{ $p->evaluaciones_cardio_count }}</td>
                    <td style="text-align:right">
                        <a href="{{ route('cardio.show',$p) }}" class="btn btn-primary btn-sm"><i class="fa-solid fa-heart-pulse"></i> Abrir</a>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6"><div class="empty"><i class="fa-solid fa-heart-pulse"></i><p>No hay pacientes cardiológicos todavía.</p></div></td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    {{ $pacientes->links() }}
@endsection
