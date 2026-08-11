<!DOCTYPE html><html lang="es"><head><meta charset="utf-8"><style>
    *{font-family:DejaVu Sans,sans-serif}
    body{margin:0;color:#1f2937;font-size:11px}
    .head{background:#1e1b4b;color:#fff;padding:14px 18px;text-align:center}
    .head h1{margin:0;font-size:15px}
    .head p{margin:2px 0 0;font-size:10px;color:#c7c1ef}
    .body{padding:14px 18px}
    table{width:100%;border-collapse:collapse;margin-top:8px}
    th{background:#fdf2fb;text-align:left;padding:6px;font-size:9px;text-transform:uppercase;color:#6b7280}
    td{padding:6px;border-bottom:1px solid #eee;font-size:10px}
    .tot{text-align:right;font-size:14px;font-weight:bold;margin-top:10px}
    .foot{margin-top:16px;font-size:9px;color:#9ca3af;text-align:center;border-top:1px dashed #ccc;padding-top:8px}
</style></head><body>
    <div class="head"><h1>{{ $empresa->nombre ?? 'Clínica' }} — Farmacia</h1><p>Comprobante de dispensación N° {{ str_pad($d->id,6,'0',STR_PAD_LEFT) }}</p></div>
    <div class="body">
        <div style="font-size:10px">
            <b>Paciente:</b> {{ $d->paciente->nombre_completo ?? 'Venta libre' }} ·
            <b>Fecha:</b> {{ $d->fecha->format('d/m/Y') }} ·
            <b>Atendió:</b> {{ $d->user->name ?? '—' }}
        </div>
        <table>
            <tr><th>Medicamento / insumo</th><th>Cant.</th><th>P. unit.</th><th>Subtotal</th></tr>
            @foreach($d->items as $it)
                <tr>
                    <td>{{ $it->nombre }}@if($it->indicaciones)<br><span style="color:#6b7280">{{ $it->indicaciones }}</span>@endif</td>
                    <td>{{ rtrim(rtrim(number_format($it->cantidad,2),'0'),'.') }}</td>
                    <td>{{ $empresa->moneda ?? 'S/' }} {{ number_format($it->precio,2) }}</td>
                    <td>{{ $empresa->moneda ?? 'S/' }} {{ number_format($it->precio * $it->cantidad,2) }}</td>
                </tr>
            @endforeach
        </table>
        <div class="tot">Total: {{ $empresa->moneda ?? 'S/' }} {{ number_format($d->total,2) }}</div>
        @if($d->observaciones)<p style="font-size:10px;color:#6b7280">{{ $d->observaciones }}</p>@endif
        <div class="foot">Gracias por su preferencia</div>
    </div>
</body></html>
