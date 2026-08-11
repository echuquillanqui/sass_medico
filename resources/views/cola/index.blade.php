@extends('layouts.app')
@section('title', 'Sala de espera')

@section('content')
    <div class="page-head">
        <div>
            <h1>Sala de espera</h1>
            <p>Turnos de hoy · {{ now()->locale('es')->isoFormat('dddd D [de] MMMM') }} · <span class="sw-live"><span class="sw-dot"></span>en vivo</span></p>
        </div>
        <a href="{{ route('cola.index') }}" class="btn btn-light"><i class="fa-solid fa-rotate"></i> Actualizar</a>
    </div>

    @php
        $cols = [
            ['Por llegar', $porLlegar, '#64748b', '#f1f5f9', 'fa-clock', 'llegada', 'Marcar llegada', 'fa-right-to-bracket'],
            ['En espera', $esperando, '#d97706', '#fef3c7', 'fa-chair', 'iniciar', 'Pasar a atención', 'fa-play'],
            ['En atención', $enAtencion, '#2563eb', '#dbeafe', 'fa-user-doctor', 'finalizar', 'Finalizar', 'fa-check'],
            ['Atendidos', $atendidos, '#16a34a', '#dcfce7', 'fa-circle-check', null, null, null],
        ];
        $totalHoy = $porLlegar->count() + $esperando->count() + $enAtencion->count() + $atendidos->count();
    @endphp

    <div class="sw-board">
        @foreach($cols as $col)
            <div class="sw-col" style="border-top:4px solid {{ $col[2] }}">
                <div class="sw-col-head" style="background:{{ $col[3] }}">
                    <span style="color:{{ $col[2] }};font-weight:700;font-size:14px"><i class="fa-solid {{ $col[4] }}"></i> {{ $col[0] }}</span>
                    <span class="sw-count" style="background:{{ $col[2] }}">{{ $col[1]->count() }}</span>
                </div>
                <div class="sw-col-body">
                @forelse($col[1] as $c)
                    @php
                        $ini = mb_substr($c->paciente->nombres,0,1).mb_substr($c->paciente->apellidos,0,1);
                        $espMin = ($c->estado_sala==='esperando' && $c->hora_llegada) ? (int) $c->hora_llegada->diffInMinutes(now()) : null;
                        $espCol = $espMin===null ? '' : ($espMin>=30 ? '#ef4444' : ($espMin>=15 ? '#d97706' : '#16a34a'));
                    @endphp
                    <div class="sw-card">
                        <div class="sw-card-top">
                            <span class="sw-ava" style="background:{{ $col[2] }}">{{ strtoupper($ini) }}</span>
                            <div style="flex:1;min-width:0">
                                <b class="sw-name">{{ $c->paciente->nombre_completo }}</b>
                                <div class="sw-meta">{{ $c->especialidad->nombre ?? 'General' }} · {{ $c->medico->name ?? 'Sin médico' }}</div>
                            </div>
                            <span class="sw-time"><i class="fa-regular fa-clock"></i> {{ \Illuminate\Support\Str::of($c->hora)->substr(0,5) }}</span>
                        </div>
                        @if($espMin !== null)
                            <div class="sw-wait" style="color:{{ $espCol }}"><i class="fa-solid fa-hourglass-half"></i> Esperando {{ $espMin }} min</div>
                        @elseif($c->estado_sala==='en_atencion' && $c->hora_atencion)
                            <div class="sw-wait" style="color:#2563eb"><i class="fa-solid fa-stethoscope"></i> En atención desde {{ $c->hora_atencion->format('H:i') }}</div>
                        @endif
                        @if($col[5])
                            <form method="POST" action="{{ route('cola.'.$col[5], $c) }}">@csrf
                                <button class="sw-btn" style="background:{{ $col[2] }}"><i class="fa-solid {{ $col[7] }}"></i> {{ $col[6] }}</button>
                            </form>
                        @endif
                    </div>
                @empty
                    <div class="sw-empty"><i class="fa-solid {{ $col[4] }}"></i><span>Sin pacientes</span></div>
                @endforelse
                </div>
            </div>
        @endforeach
    </div>

    <p class="muted" style="font-size:12px;text-align:center;margin-top:14px">
        <i class="fa-solid fa-users"></i> {{ $totalHoy }} paciente(s) hoy · La página se actualiza automáticamente cada 30&nbsp;segundos.
    </p>

    <style>
    .sw-live{display:inline-flex;align-items:center;gap:5px;color:#16a34a;font-weight:600}
    .sw-dot{width:8px;height:8px;border-radius:50%;background:#22c55e;display:inline-block;box-shadow:0 0 0 0 rgba(34,197,94,.5);animation:swpulse 1.8s infinite}
    @keyframes swpulse{0%{box-shadow:0 0 0 0 rgba(34,197,94,.5)}70%{box-shadow:0 0 0 7px rgba(34,197,94,0)}100%{box-shadow:0 0 0 0 rgba(34,197,94,0)}}
    .sw-board{display:grid;grid-template-columns:repeat(4,1fr);gap:16px;align-items:start}
    .sw-col{background:#fff;border:1px solid var(--line);border-radius:16px;overflow:hidden;box-shadow:0 4px 16px rgba(90,70,160,.05)}
    .sw-col-head{display:flex;align-items:center;justify-content:space-between;padding:12px 14px}
    .sw-count{color:#fff;font-size:12px;font-weight:800;min-width:24px;height:24px;padding:0 7px;border-radius:12px;display:inline-flex;align-items:center;justify-content:center}
    .sw-col-body{padding:12px;display:flex;flex-direction:column;gap:10px;min-height:80px}
    .sw-card{border:1px solid var(--line);border-radius:12px;padding:12px;background:#fff;transition:.14s}
    .sw-card:hover{box-shadow:0 6px 16px rgba(90,70,160,.10);transform:translateY(-2px)}
    .sw-card-top{display:flex;align-items:center;gap:10px}
    .sw-ava{width:38px;height:38px;border-radius:11px;color:#fff;display:grid;place-items:center;font-weight:700;font-size:13px;flex:0 0 38px}
    .sw-name{font-size:13.5px;color:var(--ink);display:block;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
    .sw-meta{font-size:11.5px;color:var(--ink-soft);margin-top:2px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
    .sw-time{font-size:11.5px;font-weight:700;color:var(--violet);background:var(--bg-pink);padding:4px 8px;border-radius:8px;white-space:nowrap;flex:0 0 auto}
    .sw-wait{font-size:11.5px;font-weight:600;margin-top:8px}
    .sw-btn{width:100%;margin-top:10px;border:none;color:#fff;font-weight:600;font-size:12.5px;padding:9px;border-radius:10px;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:6px;transition:.12s}
    .sw-btn:hover{filter:brightness(1.06)}
    .sw-empty{display:flex;flex-direction:column;align-items:center;gap:6px;color:#c4b5d4;font-size:12px;padding:22px 0}
    .sw-empty i{font-size:22px;opacity:.5}
    [data-theme="dark"] .sw-col,[data-theme="dark"] .sw-card{background:#161428}
    @media(max-width:900px){ .sw-board{grid-template-columns:repeat(2,1fr)} }
    @media(max-width:560px){ .sw-board{grid-template-columns:1fr} }
    </style>

    @push('scripts')
    <script>setTimeout(function(){ location.reload(); }, 30000);</script>
    @endpush
@endsection
