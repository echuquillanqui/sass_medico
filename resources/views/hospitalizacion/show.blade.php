@extends('layouts.app')
@section('title', 'Hospitalización · '.$hospitalizacion->paciente->nombre_completo)

@section('content')
    @php $h = $hospitalizacion; @endphp
    <div class="page-head">
        <div>
            <h1>{{ $h->paciente->nombre_completo }}</h1>
            <p>Cama {{ $h->cama->nombre ?? '—' }} · Ingreso {{ $h->fecha_ingreso->format('d/m/Y H:i') }} · {{ $h->dias_estancia }} día(s)
            @if($h->estado === 'activa')<span class="pill red">Internado</span>@else<span class="pill green">Alta {{ optional($h->fecha_alta)->format('d/m/Y') }}</span>@endif</p>
        </div>
        <a href="{{ route('hospitalizacion.index') }}" class="btn btn-ghost"><i class="fa-solid fa-arrow-left"></i> Volver</a>
    </div>

    <div class="grid g-2 mb">
        <div class="card">
            <h3 class="mb">Datos del ingreso</h3>
            <p class="muted"><b>Médico:</b> {{ $h->medico->name ?? '—' }}</p>
            <p class="muted"><b>Especialidad:</b> {{ $h->especialidad->nombre ?? '—' }}</p>
            <p class="muted"><b>Motivo:</b> {{ $h->motivo_ingreso ?? '—' }}</p>
            <p class="muted"><b>Diagnóstico:</b> {{ $h->diagnostico_ingreso ?? '—' }}</p>
            @if($h->resumen_alta)<p class="muted"><b>Resumen de alta:</b> {{ $h->resumen_alta }}</p>@endif
        </div>
        <div>
            @if($h->estado === 'activa')
                <div class="card pink">
                    <h3 class="mb"><i class="fa-solid fa-right-from-bracket" style="color:var(--violet)"></i> Dar de alta</h3>
                    <form method="POST" action="{{ route('hospitalizacion.alta', $h) }}" onsubmit="return confirm('¿Dar de alta al paciente? Se liberará la cama.')">
                        @csrf
                        <div class="field mb"><label>Resumen de alta</label><textarea name="resumen_alta" placeholder="Evolución favorable, indicaciones..."></textarea></div>
                        <button class="btn btn-primary"><i class="fa-solid fa-check"></i> Registrar alta</button>
                    </form>
                </div>
            @endif
        </div>
    </div>

    @if($h->estado === 'activa')
        <div class="card mb">
            <h3 class="mb"><i class="fa-solid fa-notes-medical" style="color:var(--pink)"></i> Nueva evolución</h3>
            <form method="POST" action="{{ route('hospitalizacion.evolucion', $h) }}">
                @csrf
                <div class="form-grid mb">
                    <div class="field"><label>Presión arterial</label><input name="presion_arterial" placeholder="120/80"></div>
                    <div class="field"><label>Frec. cardíaca</label><input type="number" name="frecuencia_cardiaca"></div>
                    <div class="field"><label>Temperatura</label><input type="number" step="0.1" name="temperatura"></div>
                    <div class="field"><label>Saturación O₂</label><input name="saturacion" placeholder="98%"></div>
                </div>
                <div class="field mb"><label>Nota de evolución *</label><textarea name="nota" required></textarea></div>
                <button class="btn btn-primary"><i class="fa-solid fa-plus"></i> Registrar evolución</button>
            </form>
        </div>
    @endif

    <div class="card">
        <h3 class="mb">Evolución diaria ({{ $h->evoluciones->count() }})</h3>
        @forelse($h->evoluciones->sortByDesc('fecha') as $ev)
            <div style="border-left:3px solid var(--violet-2);padding:4px 0 12px 16px;margin-bottom:6px">
                <div class="flex between">
                    <b>{{ $ev->fecha->format('d/m/Y H:i') }}</b>
                    <span class="muted" style="font-size:12px">{{ $ev->user->name ?? '' }}</span>
                </div>
                <div style="margin:4px 0">
                    @if($ev->presion_arterial)<span class="pill violet">PA {{ $ev->presion_arterial }}</span>@endif
                    @if($ev->frecuencia_cardiaca)<span class="pill violet">FC {{ $ev->frecuencia_cardiaca }}</span>@endif
                    @if($ev->temperatura)<span class="pill violet">T° {{ $ev->temperatura }}</span>@endif
                    @if($ev->saturacion)<span class="pill violet">SatO₂ {{ $ev->saturacion }}</span>@endif
                </div>
                <div class="muted">{{ $ev->nota }}</div>
            </div>
        @empty
            <div class="empty"><i class="fa-solid fa-notes-medical"></i><p>Sin notas de evolución.</p></div>
        @endforelse
    </div>
@endsection
