<!DOCTYPE html>
<html lang="es"><head><meta charset="utf-8"><style>
  *{font-family:DejaVu Sans,sans-serif;box-sizing:border-box}
  body{margin:0;color:#1f2937;font-size:10px}
  .box{border:1px solid #cbd5e1;border-radius:6px;padding:8px 10px;margin-bottom:8px}
  .hd{text-align:center}
  .emp{font-size:13px;font-weight:bold;color:#4c1d95}
  .tit{border:1.5px solid #4c1d95;border-radius:6px;padding:6px;text-align:center;color:#4c1d95}
  .tit b{font-size:12px;display:block}
  table{width:100%;border-collapse:collapse;font-size:9.5px}
  th{background:#4c1d95;color:#fff;padding:4px 6px;text-align:left}
  td{border-bottom:1px solid #e5e7eb;padding:4px 6px}
  .tot{text-align:right;margin-top:6px}
  .tot .l{display:flex;justify-content:space-between;padding:2px 0}
  .tot .g{font-size:13px;font-weight:bold;color:#4c1d95}
  .muted{color:#6b7280}
  .estado{display:inline-block;padding:2px 8px;border-radius:10px;font-size:9px;color:#fff}
</style></head><body>
  @php $e = $c->empresa; @endphp
  <table style="border:none"><tr>
    <td style="border:none;vertical-align:top;width:60%">
      <div class="emp">{{ $e->nombre }}</div>
      <div class="muted">{{ $c->empresa->facturacionConfig->razon_social ?? $e->nombre }}</div>
      <div class="muted">RUC: {{ $c->empresa->facturacionConfig->ruc ?? '—' }}</div>
      <div class="muted">{{ $c->empresa->facturacionConfig->direccion_fiscal ?? $e->direccion }}</div>
    </td>
    <td style="border:none;vertical-align:top;width:40%">
      <div class="tit"><b>{{ mb_strtoupper(\App\Models\Comprobante::TIPOS[$c->tipo] ?? $c->tipo) }} ELECTRÓNICA</b>{{ $c->numero }}</div>
    </td>
  </tr></table>

  <div class="box">
    <b>Cliente:</b> {{ $c->cliente_nombre }} &nbsp; · &nbsp;
    <b>Doc:</b> {{ ['1'=>'DNI','6'=>'RUC','0'=>'—'][$c->cliente_tipo_doc] ?? '' }} {{ $c->cliente_num_doc }}<br>
    <b>Fecha de emisión:</b> {{ $c->fecha_emision->format('d/m/Y') }} &nbsp; · &nbsp; <b>Moneda:</b> {{ $c->moneda }}
    @php $sc = ['pendiente'=>'#f59e0b','emitido'=>'#3b82f6','aceptado'=>'#22c55e','rechazado'=>'#ef4444','anulado'=>'#94a3b8'][$c->estado] ?? '#94a3b8'; @endphp
    &nbsp; · &nbsp; <span class="estado" style="background:{{ $sc }}">{{ mb_strtoupper($c->estado) }}</span>
  </div>

  <table>
    <thead><tr><th style="width:40px">Cant.</th><th>Descripción</th><th style="width:80px;text-align:right">Importe</th></tr></thead>
    <tbody>
    @forelse($c->items ?? [] as $it)
      <tr><td>{{ $it['cantidad'] ?? 1 }}</td><td>{{ $it['descripcion'] ?? 'Servicio' }}</td><td style="text-align:right">{{ number_format($it['total'] ?? 0,2) }}</td></tr>
    @empty
      <tr><td>1</td><td>Servicio</td><td style="text-align:right">{{ number_format($c->total,2) }}</td></tr>
    @endforelse
    </tbody>
  </table>

  <div class="tot">
    @if($c->gravado > 0)
      <div class="l"><span class="muted">Op. Gravada</span><span>{{ $c->moneda }} {{ number_format($c->gravado,2) }}</span></div>
      <div class="l"><span class="muted">IGV</span><span>{{ $c->moneda }} {{ number_format($c->igv,2) }}</span></div>
    @endif
    @if($c->exonerado > 0)
      <div class="l"><span class="muted">Op. Exonerada</span><span>{{ $c->moneda }} {{ number_format($c->exonerado,2) }}</span></div>
    @endif
    @if($c->inafecto > 0)
      <div class="l"><span class="muted">Op. Inafecta</span><span>{{ $c->moneda }} {{ number_format($c->inafecto,2) }}</span></div>
    @endif
    <div class="l g"><span>TOTAL</span><span>{{ $c->moneda }} {{ number_format($c->total,2) }}</span></div>
  </div>

  <table style="border:none;margin-top:10px"><tr>
    <td style="border:none;vertical-align:middle;width:80px">
      @if(!empty($qr))
        <img src="data:image/svg+xml;base64,{{ base64_encode($qr) }}" style="width:78px;height:78px">
      @endif
    </td>
    <td style="border:none;vertical-align:middle">
      <p class="muted" style="margin:0;font-size:8.5px">Representación impresa del comprobante de pago electrónico.
      @if($c->estado==='pendiente') <b>Pendiente de envío a SUNAT.</b>@endif</p>
      @if($c->hash)<p class="muted" style="margin:3px 0 0;font-size:8px">Hash / código de autorización: {{ $c->hash }}</p>@endif
    </td>
  </tr></table>
</body></html>
