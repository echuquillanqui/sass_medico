@extends('portal.layout')
@section('title','Mi historia')
@section('content')
    <h1 style="margin:0 0 16px">Mi historia clínica</h1>
    @forelse($consultas as $c)
        <div class="card mb">
            <div class="flex between mb">
                <b>{{ $c->fecha->locale('es')->isoFormat('D MMMM YYYY') }}</b>
                <span class="pill violet">{{ $c->especialidad->nombre ?? 'General' }}</span>
            </div>
            <p class="muted"><b>Motivo:</b> {{ $c->motivo ?? '—' }}</p>
            <p class="muted"><b>Diagnóstico:</b> {{ $c->diagnostico ?? '—' }}</p>
            <p class="muted"><b>Indicaciones:</b> {{ $c->tratamiento ?? '—' }}</p>
            <p class="muted" style="font-size:12px">Atendido por {{ $c->medico->name ?? '—' }}</p>
        </div>
    @empty
        <div class="card"><div class="empty"><i class="fa-solid fa-notes-medical"></i><p>Aún no tienes consultas registradas.</p></div></div>
    @endforelse
@endsection
