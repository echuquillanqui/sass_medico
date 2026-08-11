<!DOCTYPE html>
<html lang="es"><head><meta charset="utf-8">
<title>Ticket {{ $sub->ticket }}</title>
<style>
  *{box-sizing:border-box;font-family:'Segoe UI',Arial,sans-serif}
  body{background:#f3f0fb;margin:0;padding:24px;color:#2b2b3a}
  .ticket{max-width:380px;margin:0 auto;background:#fff;border-radius:16px;overflow:hidden;box-shadow:0 10px 30px rgba(90,70,160,.15)}
  .th{background:linear-gradient(135deg,#7c3aed,#ec4899);color:#fff;padding:20px 22px}
  .th h1{margin:0;font-size:17px}
  .th p{margin:4px 0 0;font-size:12px;opacity:.9}
  .body{padding:20px 22px}
  .row{display:flex;justify-content:space-between;padding:7px 0;font-size:13px;border-bottom:1px dashed #ece9f7}
  .row .k{color:#6b7280}
  .row .v{font-weight:600;text-align:right}
  .total{background:#faf5ff;border-radius:12px;padding:12px 14px;margin-top:14px;display:flex;justify-content:space-between;align-items:center}
  .total .k{font-weight:600}
  .total .v{font-size:22px;font-weight:800;color:#6d28d9}
  .tk{text-align:center;margin-top:14px;font-size:12px;color:#8b8b9a}
  .tk b{display:block;font-size:16px;letter-spacing:2px;color:#2b2b3a;margin-top:4px}
  .foot{text-align:center;font-size:11px;color:#9ca3af;padding:12px}
  .noprint{text-align:center;margin:18px 0}
  .btn{background:linear-gradient(135deg,#a855f7,#ec4899);color:#fff;border:none;padding:10px 20px;border-radius:10px;font-size:13px;font-weight:600;cursor:pointer}
  @media print{.noprint{display:none}body{background:#fff;padding:0}.ticket{box-shadow:none}}
</style></head>
<body>
  @php $mon = $sub->empresa->moneda ?: 'S/'; @endphp
  <div class="ticket">
    <div class="th">
      <h1>Comprobante de suscripción</h1>
      <p>{{ config('app.name', 'Suite Salud Modular') }} · SaaS Médico</p>
    </div>
    <div class="body">
      <div class="row"><span class="k">Empresa</span><span class="v">{{ $sub->empresa->nombre }}</span></div>
      <div class="row"><span class="k">RUC</span><span class="v">{{ $sub->empresa->ruc ?? '—' }}</span></div>
      <div class="row"><span class="k">Plan</span><span class="v">{{ $sub->plan_nombre }}</span></div>
      <div class="row"><span class="k">Precio</span><span class="v">{{ $mon }} {{ number_format($sub->plan_precio,2) }} / {{ $sub->ciclo }}</span></div>
      <div class="row"><span class="k">Duración</span><span class="v">{{ $sub->duracion }} {{ $sub->unidad }}</span></div>
      <div class="row"><span class="k">Vigencia</span><span class="v">{{ $sub->fecha_inicio->format('d/m/Y') }} → {{ $sub->fecha_fin->format('d/m/Y') }}</span></div>
      <div class="row"><span class="k">Subtotal</span><span class="v">{{ $mon }} {{ number_format($sub->subtotal,2) }}</span></div>
      <div class="row"><span class="k">Descuento</span><span class="v">{{ $sub->descuento>0 ? ($sub->tipo_descuento==='porcentaje' ? $sub->descuento.'%' : $mon.' '.number_format($sub->descuento,2)) : '—' }}</span></div>
      <div class="row"><span class="k">Método</span><span class="v">{{ $sub->metodo ?? '—' }}</span></div>
      @if($sub->nota)<div class="row"><span class="k">Nota</span><span class="v">{{ $sub->nota }}</span></div>@endif
      <div class="total"><span class="k">Total pagado</span><span class="v">{{ $mon }} {{ number_format($sub->total,2) }}</span></div>
      <div class="tk">Ticket de suscripción<b>{{ $sub->ticket }}</b></div>
    </div>
    <div class="foot">Emitido el {{ $sub->created_at->format('d/m/Y H:i') }} · Documento de control interno</div>
  </div>
  <div class="noprint"><button class="btn" onclick="window.print()">🖨 Imprimir ticket</button></div>
</body></html>
