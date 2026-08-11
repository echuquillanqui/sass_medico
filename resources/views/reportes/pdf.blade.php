<!DOCTYPE html>
<html lang="es"><head><meta charset="utf-8">
<style>
    *{font-family:DejaVu Sans,sans-serif;}
    body{color:#1f2937;font-size:12px;margin:0}
    .head{background:#1e1b4b;color:#fff;padding:16px 22px}
    .head h1{margin:0;font-size:18px}
    .head p{margin:2px 0 0;font-size:11px;color:#c7c1ef}
    .body{padding:20px 24px}
    .kpis{width:100%;margin-bottom:18px}
    .kpis td{border:1px solid #e5e7eb;padding:10px;text-align:center;width:25%}
    .kpis .n{font-size:20px;font-weight:bold;color:#7c3aed}
    .kpis .l{font-size:9px;color:#6b7280;text-transform:uppercase}
    h3{font-size:13px;border-bottom:2px solid #ec4899;padding-bottom:4px}
    table.data{width:100%;border-collapse:collapse;margin-top:6px}
    table.data th{background:#fdf2fb;text-align:left;padding:7px;font-size:10px;text-transform:uppercase;color:#6b7280}
    table.data td{padding:7px;border-bottom:1px solid #eee}
</style></head><body>
    <div class="head">
        <h1>{{ $empresa->nombre ?? 'Clínica' }} — Reporte</h1>
        <p>Periodo: {{ $desde->format('d/m/Y') }} al {{ $hasta->format('d/m/Y') }} · Generado {{ now()->format('d/m/Y H:i') }}</p>
    </div>
    <div class="body">
        <table class="kpis"><tr>
            <td><div class="n">{{ $totalCitas }}</div><div class="l">Citas</div></td>
            <td><div class="n">{{ $totalPacientes }}</div><div class="l">Pacientes</div></td>
            <td><div class="n">{{ $nuevosPacientes }}</div><div class="l">Nuevos</div></td>
            <td><div class="n">{{ $empresa->moneda ?? 'S/' }} {{ number_format($ingresos,2) }}</div><div class="l">Ingresos</div></td>
        </tr></table>

        <h3>Citas por estado</h3>
        <table class="data"><tr><th>Estado</th><th>Cantidad</th></tr>
            @foreach($porEstado as $e => $c)<tr><td>{{ ucfirst($e) }}</td><td>{{ $c }}</td></tr>@endforeach
        </table>

        <h3 style="margin-top:18px">Citas por especialidad</h3>
        <table class="data"><tr><th>Especialidad</th><th>Cantidad</th></tr>
            @forelse($porEspecialidad as $e => $c)<tr><td>{{ $e }}</td><td>{{ $c }}</td></tr>
            @empty <tr><td colspan="2">Sin datos</td></tr>@endforelse
        </table>
    </div>
</body></html>
