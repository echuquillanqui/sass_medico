@extends('layouts.app')
@section('title', 'Resumen diario de boletas')

@section('content')
    <div class="page-head">
        <div class="flex gap">
            <div style="width:56px;height:56px;border-radius:16px;background:#0ea5e9;color:#fff;display:grid;place-items:center;font-size:24px"><i class="fa-solid fa-layer-group"></i></div>
            <div><h1>Resumen diario de boletas</h1><p>Reporta a SUNAT en lote las boletas del día (RC) y comunica sus bajas.</p></div>
        </div>
        <a href="{{ route('comprobantes.index') }}" class="btn btn-light"><i class="fa-solid fa-file-invoice"></i> Comprobantes</a>
    </div>

    @if(session('ok'))<div class="alert ok mb">{{ session('ok') }}</div>@endif
    @if(session('error'))<div class="alert mb" style="background:#fef2f2;border-left:4px solid #ef4444;color:#991b1b">{{ session('error') }}</div>@endif

    {{-- Boletas pendientes de reportar, por fecha --}}
    <div class="card mb">
        <h3 style="margin:0 0 12px"><i class="fa-solid fa-clock" style="color:#f59e0b"></i> Boletas pendientes de reportar</h3>
        @if($pendientes->isEmpty())
            <div class="empty"><i class="fa-solid fa-circle-check" style="color:#22c55e"></i><p>No hay boletas pendientes. Todo lo emitido ya fue reportado a SUNAT.</p></div>
        @else
            <div class="table-wrap">
                <table>
                    <thead><tr><th>Fecha de emisión</th><th>Altas</th><th>Bajas</th><th>Documentos</th><th>Importe</th><th></th></tr></thead>
                    <tbody>
                    @foreach($pendientes as $p)
                        <tr>
                            <td><b>{{ \Carbon\Carbon::parse($p->fecha)->format('d/m/Y') }}</b></td>
                            <td><span class="pill green">{{ $p->altas }}</span></td>
                            <td>@if($p->bajas)<span class="pill red">{{ $p->bajas }}</span>@else <span class="muted">0</span>@endif</td>
                            <td>{{ $p->documentos }}</td>
                            <td><b>S/ {{ number_format($p->importe, 2) }}</b></td>
                            <td style="text-align:right">
                                <form method="POST" action="{{ route('resumenes.generar') }}" style="display:inline" onsubmit="return confirm('¿Generar y enviar a SUNAT el resumen de las boletas del {{ \Carbon\Carbon::parse($p->fecha)->format('d/m/Y') }}?')">
                                    @csrf<input type="hidden" name="fecha" value="{{ $p->fecha }}">
                                    <button class="btn btn-primary btn-sm"><i class="fa-solid fa-paper-plane"></i> Generar y enviar</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    {{-- Historial de resúmenes enviados --}}
    <div class="card">
        <h3 style="margin:0 0 12px"><i class="fa-solid fa-list-check"></i> Resúmenes generados</h3>
        <div class="table-wrap">
            <table>
                <thead><tr><th>Identificador</th><th>Fecha boletas</th><th>Docs.</th><th>Importe</th><th>Ticket</th><th>Estado</th><th></th></tr></thead>
                <tbody>
                @forelse($resumenes as $r)
                    @php $col = ['pendiente'=>'amber','enviado'=>'blue','aceptado'=>'green','rechazado'=>'red'][$r->estado] ?? 'gray'; @endphp
                    <tr>
                        <td><b>{{ $r->identificador }}</b></td>
                        <td>{{ $r->fecha_generacion->format('d/m/Y') }}</td>
                        <td>{{ $r->total_documentos }}</td>
                        <td><b>S/ {{ number_format($r->total_importe, 2) }}</b></td>
                        <td class="muted" style="font-size:11px">{{ $r->sunat_ticket ?: '—' }}</td>
                        <td>
                            <span class="pill {{ $col }}">{{ $estados[$r->estado] ?? ucfirst($r->estado) }}</span>
                            @if($r->sunat_respuesta)<div class="muted" style="font-size:11px;max-width:280px">{{ \Illuminate\Support\Str::limit($r->sunat_respuesta, 120) }}</div>@endif
                        </td>
                        <td style="text-align:right;white-space:nowrap">
                            @if($r->estado === 'enviado')
                                <form method="POST" action="{{ route('resumenes.consultar', $r) }}" style="display:inline">
                                    @csrf<button class="btn btn-primary btn-sm"><i class="fa-solid fa-rotate"></i> Consultar</button>
                                </form>
                            @elseif($r->estado === 'pendiente')
                                <form method="POST" action="{{ route('resumenes.reenviar', $r) }}" style="display:inline">
                                    @csrf<button class="btn btn-light btn-sm"><i class="fa-solid fa-paper-plane"></i> Reenviar</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7"><div class="empty"><i class="fa-solid fa-layer-group"></i><p>Todavía no has generado ningún resumen diario.</p></div></td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        {{ $resumenes->links() }}
    </div>
@endsection
