<!DOCTYPE html><html lang="es"><head><meta charset="utf-8">
<style>
    *{font-family:DejaVu Sans,sans-serif}
    body{margin:0;color:#1f2937;font-size:12px}
    .head{background:#1e1b4b;color:#fff;padding:14px 18px;text-align:center}
    .head h1{margin:0;font-size:16px}
    .head p{margin:2px 0 0;font-size:10px;color:#c7c1ef}
    .body{padding:16px 18px}
    .amt{font-size:26px;font-weight:bold;color:#7c3aed;text-align:center;margin:10px 0}
    table{width:100%;font-size:11px}
    td{padding:4px 0}
    .lbl{color:#6b7280}
    .foot{margin-top:16px;text-align:center;font-size:9px;color:#9ca3af;border-top:1px dashed #ccc;padding-top:8px}
</style></head><body>
    <div class="head"><h1>{{ $empresa->nombre ?? 'Clínica' }}</h1><p>RECIBO DE PAGO N° {{ str_pad($pago->id,6,'0',STR_PAD_LEFT) }}</p></div>
    <div class="body">
        <div class="amt">{{ $empresa->moneda ?? 'S/' }} {{ number_format($pago->monto,2) }}</div>
        <table>
            <tr><td class="lbl">Paciente</td><td style="text-align:right"><b>{{ $pago->paciente->nombre_completo }}</b></td></tr>
            <tr><td class="lbl">Concepto</td><td style="text-align:right">{{ $pago->concepto }}</td></tr>
            <tr><td class="lbl">Método</td><td style="text-align:right">{{ $pago->metodo_label }}</td></tr>
            <tr><td class="lbl">Estado</td><td style="text-align:right">{{ ucfirst($pago->estado) }}</td></tr>
            <tr><td class="lbl">Fecha</td><td style="text-align:right">{{ $pago->fecha->format('d/m/Y') }}</td></tr>
        </table>
        <div class="foot">Gracias por su preferencia · {{ $empresa->telefono ?? '' }}</div>
    </div>
</body></html>
