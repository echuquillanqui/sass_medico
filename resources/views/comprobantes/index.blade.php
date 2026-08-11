@extends('layouts.app')
@section('title', 'Comprobantes electrónicos')

@section('content')
    <div class="page-head">
        <div class="flex gap">
            <div style="width:56px;height:56px;border-radius:16px;background:#7c3aed;color:#fff;display:grid;place-items:center;font-size:24px"><i class="fa-solid fa-file-invoice"></i></div>
            <div><h1>Comprobantes electrónicos</h1><p>Boletas, facturas y notas emitidas ante SUNAT.</p></div>
        </div>
        <a href="{{ route('admin.facturacion.configuracion') }}" class="btn btn-light"><i class="fa-solid fa-gear"></i> Configuración</a>
    </div>

    @if(session('ok'))<div class="alert ok mb">{{ session('ok') }}</div>@endif
    @if(session('error'))<div class="alert mb" style="background:#fef2f2;border-left:4px solid #ef4444;color:#991b1b">{{ session('error') }}</div>@endif

    @php
        $chips = ['pendiente'=>['Pendientes','#f59e0b'],'aceptado'=>['Aceptados','#22c55e'],'rechazado'=>['Rechazados','#ef4444'],'emitido'=>['Emitidos','#3b82f6']];
    @endphp
    <div class="grid g-4 mb">
        @foreach($chips as $k=>$meta)
            @php $r = $resumen[$k] ?? null; @endphp
            <div class="metric" style="text-align:left;border-style:solid;border-left:4px solid {{ $meta[1] }}">
                <div class="big" style="color:{{ $meta[1] }}">{{ $r->n ?? 0 }}</div>
                <div class="cap">{{ $meta[0] }}</div>
            </div>
        @endforeach
    </div>

    <div class="card mb">
        <form method="GET" class="flex gap" style="flex-wrap:wrap">
            <select name="tipo" onchange="this.form.submit()">
                <option value="">Todos los tipos</option>
                @foreach($tipos as $k=>$v)<option value="{{ $k }}" @selected(request('tipo')==$k)>{{ $v }}</option>@endforeach
            </select>
            <select name="estado" onchange="this.form.submit()">
                <option value="">Todos los estados</option>
                @foreach(['pendiente','emitido','aceptado','rechazado','anulado'] as $e)<option value="{{ $e }}" @selected(request('estado')==$e)>{{ ucfirst($e) }}</option>@endforeach
            </select>
        </form>
    </div>

    <div class="table-wrap">
        <table>
            <thead><tr><th>Número</th><th>Tipo</th><th>Cliente</th><th>Fecha</th><th>Total</th><th>Estado</th><th></th></tr></thead>
            <tbody>
            @forelse($comprobantes as $c)
                @php $col = ['pendiente'=>'amber','emitido'=>'blue','aceptado'=>'green','rechazado'=>'red','anulado'=>'gray'][$c->estado] ?? 'gray'; @endphp
                <tr>
                    <td><b>{{ $c->numero }}</b></td>
                    <td>{{ \App\Models\Comprobante::TIPOS[$c->tipo] ?? $c->tipo }}</td>
                    <td>{{ $c->cliente_nombre }}<div class="muted" style="font-size:11px">{{ $c->cliente_num_doc }}</div></td>
                    <td>{{ $c->fecha_emision->format('d/m/Y') }}</td>
                    <td><b>{{ $c->moneda }} {{ number_format($c->total,2) }}</b></td>
                    <td><span class="pill {{ $col }}">{{ ucfirst($c->estado) }}</span></td>
                    <td style="text-align:right;white-space:nowrap">
                        <a href="{{ route('comprobantes.pdf',$c) }}" target="_blank" class="btn btn-light btn-sm" title="Imprimir"><i class="fa-solid fa-print"></i></a>
                        @if($c->xml_path)<a href="{{ route('comprobantes.xml',$c) }}" class="btn btn-light btn-sm" title="Descargar XML"><i class="fa-solid fa-file-code"></i></a>@endif
                        @if($c->cdr_path)<a href="{{ route('comprobantes.cdr',$c) }}" class="btn btn-light btn-sm" title="Descargar CDR"><i class="fa-solid fa-file-zipper"></i></a>@endif
                        @if(in_array($c->estado,['pendiente','rechazado']))
                            <form method="POST" action="{{ route('comprobantes.emitir',$c) }}" style="display:inline">
                                @csrf<button class="btn btn-primary btn-sm"><i class="fa-solid fa-paper-plane"></i> Emitir</button>
                            </form>
                        @endif
                        @if($c->baja_pendiente)
                            <form method="POST" action="{{ route('comprobantes.consultar-baja',$c) }}" style="display:inline">
                                @csrf<button class="btn btn-primary btn-sm" title="Consultar baja en SUNAT"><i class="fa-solid fa-rotate"></i> Consultar baja</button>
                            </form>
                        @elseif($c->tipo !== 'nota_credito' && in_array($c->estado,['emitido','aceptado']))
                            @php $ncData = ['url' => route('comprobantes.nota', $c), 'num' => $c->numero, 'total' => (float) $c->total]; @endphp
                            <button type="button" class="btn btn-light btn-sm" title="Nota de crédito" onclick='notaModal(@json($ncData))'><i class="fa-solid fa-file-circle-minus" style="color:#d97706"></i></button>
                            <form method="POST" action="{{ route('comprobantes.anular',$c) }}" style="display:inline" onsubmit="return anularConf()">
                                @csrf<input type="hidden" name="motivo" value="Anulación de la operación"><button class="btn btn-danger btn-sm" title="Anular"><i class="fa-solid fa-ban"></i></button>
                            </form>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="7"><div class="empty"><i class="fa-solid fa-file-invoice"></i><p>Aún no hay comprobantes. Se generan al registrar pagos con la facturación habilitada.</p></div></td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    {{ $comprobantes->links() }}

    {{-- Modal nota de crédito --}}
    <div id="ncOverlay" class="nc-overlay" onclick="if(event.target===this)ncClose()">
        <div class="nc-modal">
            <div class="flex between mb"><h3 style="margin:0"><i class="fa-solid fa-file-circle-minus" style="color:#d97706"></i> Nota de crédito</h3><button type="button" class="btn btn-light btn-sm" onclick="ncClose()"><i class="fa-solid fa-xmark"></i></button></div>
            <p class="muted" id="ncNum"></p>
            <form method="POST" id="ncForm">
                @csrf
                <div class="field mb"><label>Motivo (catálogo SUNAT 09)</label>
                    <select name="tipo_nota">
                        @foreach(\App\Models\Comprobante::MOTIVOS_NOTA as $k=>$v)<option value="{{ $k }}">{{ $k }} · {{ $v }}</option>@endforeach
                    </select></div>
                <div class="field mb"><label>Detalle (opcional)</label><input name="motivo" placeholder="Descripción del motivo"></div>
                <div class="field mb"><label>Monto (vacío = total del comprobante)</label><input type="number" step="0.01" name="total" id="ncTotal"></div>
                <div class="flex gap" style="justify-content:flex-end"><button type="button" class="btn btn-light" onclick="ncClose()">Cancelar</button><button class="btn btn-primary"><i class="fa-solid fa-check"></i> Generar nota</button></div>
            </form>
        </div>
    </div>

    <style>
    .nc-overlay{display:none;position:fixed;inset:0;background:rgba(30,27,75,.45);z-index:999;align-items:flex-start;justify-content:center;padding:50px 16px;overflow:auto}
    .nc-overlay.open{display:flex}
    .nc-modal{background:#fff;border-radius:16px;padding:22px;width:100%;max-width:460px;box-shadow:0 20px 50px rgba(0,0,0,.25)}
    [data-theme="dark"] .nc-modal{background:#161428}
    </style>

    @push('scripts')
    <script>
    function notaModal(d){
        var f=document.getElementById('ncForm'); f.action=d.url;
        document.getElementById('ncNum').textContent='Sobre el comprobante '+d.num;
        document.getElementById('ncTotal').placeholder=(d.total).toFixed(2);
        document.getElementById('ncOverlay').classList.add('open');
    }
    function ncClose(){ document.getElementById('ncOverlay').classList.remove('open'); }
    function anularConf(){ return confirm('¿Anular este comprobante? Si es una factura aceptada, se enviará la comunicación de baja a SUNAT.'); }
    document.addEventListener('keydown',function(e){ if(e.key==='Escape')ncClose(); });
    </script>
    @endpush
@endsection
