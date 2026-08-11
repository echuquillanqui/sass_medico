@extends('layouts.app')
@section('title', 'Notificaciones')

@section('content')
    <div class="page-head">
        <div><h1>Notificaciones</h1><p>Avisos recientes de tu clínica.</p></div>
        <form method="POST" action="{{ route('notificaciones.leer') }}">@csrf<button class="btn btn-light"><i class="fa-solid fa-check-double"></i> Marcar todas</button></form>
    </div>

    <div class="card" style="padding:0">
        @forelse($notificaciones as $n)
            <a href="{{ $n->url ?? '#' }}" style="display:flex;gap:14px;align-items:center;padding:16px 20px;border-bottom:1px solid var(--line)">
                @php $col=['exito'=>'#22c55e','alerta'=>'#ef4444','pago'=>'#a855f7','cita'=>'#3b82f6'][$n->tipo]??'#7c3aed'; @endphp
                <span style="width:42px;height:42px;border-radius:12px;background:{{ $col }}22;color:{{ $col }};display:grid;place-items:center;font-size:16px;flex-shrink:0"><i class="fa-solid {{ $n->icono }}"></i></span>
                <div style="flex:1">
                    <b style="font-size:14px">{{ $n->titulo }}</b>
                    @if($n->mensaje)<div class="muted" style="font-size:13px">{{ $n->mensaje }}</div>@endif
                </div>
                <span class="muted" style="font-size:12px;white-space:nowrap">{{ $n->created_at->diffForHumans() }}</span>
            </a>
        @empty
            <div class="empty" style="padding:50px"><i class="fa-regular fa-bell"></i><p>No tienes notificaciones.</p></div>
        @endforelse
    </div>
    <div class="mt">{{ $notificaciones->links() }}</div>
@endsection
