<!DOCTYPE html><html lang="es"><head><meta charset="utf-8"><style>
    *{font-family:DejaVu Sans,sans-serif}
    body{margin:0;color:#1f2937;font-size:12px}
    .head{background:#1e1b4b;color:#fff;padding:16px 22px}
    .head h1{margin:0;font-size:18px}
    .head p{margin:2px 0 0;font-size:11px;color:#c7c1ef}
    .body{padding:20px 24px}
    .pdata{background:#fdf2fb;border-radius:8px;padding:10px 14px;margin-bottom:14px;font-size:11px}
    table.res{width:100%;border-collapse:collapse;margin-top:8px}
    table.res th{background:#fafafa;text-align:left;padding:7px;font-size:9px;text-transform:uppercase;color:#6b7280;border-bottom:1px solid #eee}
    table.res td{padding:7px;border-bottom:1px solid #f0f0f0;font-size:11px}
    .alto{color:#dc2626;font-weight:bold}
    .foot{margin-top:36px;text-align:center}
    .sign{border-top:1px solid #444;width:240px;margin:30px auto 4px}
</style></head><body>
    <div class="head"><h1>{{ $empresa->nombre ?? 'Clínica' }} — Laboratorio</h1>
        <p>Informe de resultados · Orden #{{ str_pad($orden->id,5,'0',STR_PAD_LEFT) }} · {{ $orden->fecha->format('d/m/Y') }}</p></div>
    <div class="body">
        <div class="pdata">
            <b style="font-size:13px">{{ $orden->paciente->nombre_completo }}</b> ·
            {{ $orden->paciente->tipo_documento }} {{ $orden->paciente->documento ?? '—' }} ·
            {{ $orden->paciente->edad !== null ? $orden->paciente->edad.' años' : '' }}<br>
            Médico solicitante: {{ $orden->medico->name ?? '—' }}
        </div>

        <table class="res">
            <tr><th>Examen</th><th>Resultado</th><th>Unidad</th><th>Valor de referencia</th></tr>
        @php $grupoActual = null; @endphp
        @foreach($orden->items as $it)
            @if($it->grupo && $it->grupo !== $grupoActual)
                <tr><td colspan="4" style="background:#edf2f7"><strong>{{ $it->grupo }}</strong></td></tr>
                @php $grupoActual = $it->grupo; @endphp
            @elseif(!$it->grupo)
                @php $grupoActual = null; @endphp
            @endif
                <tr>
                    <td>{{ $it->nombre }}</td>
                    <td class="{{ $it->fuera_rango ? 'alto' : '' }}">{{ $it->resultado ?? '—' }}{!! $it->fuera_rango ? ' *' : '' !!}</td>
                    <td>{{ $it->unidad ?? '—' }}</td>
                    <td>{{ $it->valor_referencia ?? '—' }}</td>
                </tr>
            @endforeach
        </table>
        <p style="font-size:10px;color:#6b7280;margin-top:8px">* Valor fuera del rango de referencia.</p>

        <div class="foot"><div class="sign"></div>Responsable de laboratorio</div>
    </div>
</body></html>
