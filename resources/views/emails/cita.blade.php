<!DOCTYPE html>
<html lang="es"><head><meta charset="utf-8"><style>
    body{font-family:Arial,Helvetica,sans-serif;background:#f6f4fb;margin:0;padding:24px;color:#1f2937}
    .card{max-width:520px;margin:0 auto;background:#fff;border-radius:16px;overflow:hidden;box-shadow:0 8px 30px rgba(124,58,237,.1)}
    .head{background:#1e1b4b;color:#fff;padding:22px 26px}
    .head h1{margin:0;font-size:18px}
    .body{padding:24px 26px}
    .badge{display:inline-block;background:linear-gradient(135deg,#a855f7,#ec4899);color:#fff;padding:6px 14px;border-radius:20px;font-size:12px;font-weight:bold}
    table{width:100%;margin-top:16px;font-size:14px}
    td{padding:8px 0;border-bottom:1px solid #eee}
    .lbl{color:#6b7280}
    .foot{padding:16px 26px;font-size:12px;color:#9ca3af;text-align:center}
</style></head><body>
    <div class="card">
        <div class="head"><h1>{{ $empresa->nombre ?? 'Clínica' }}</h1></div>
        <div class="body">
            <span class="badge">{{ $tipo === 'recordatorio' ? 'Recordatorio' : 'Cita confirmada' }}</span>
            <p style="margin-top:16px">Hola <b>{{ $cita->paciente->nombres }}</b>,</p>
            <p>{{ $tipo === 'recordatorio'
                ? 'Te recordamos que tienes una cita programada:'
                : 'Tu cita ha sido registrada con los siguientes datos:' }}</p>
            <table>
                <tr><td class="lbl">Fecha</td><td style="text-align:right"><b>{{ $cita->fecha->locale('es')->isoFormat('dddd D [de] MMMM') }}</b></td></tr>
                <tr><td class="lbl">Hora</td><td style="text-align:right">{{ \Illuminate\Support\Str::of($cita->hora)->substr(0,5) }}</td></tr>
                <tr><td class="lbl">Especialidad</td><td style="text-align:right">{{ $cita->especialidad->nombre ?? 'Consulta general' }}</td></tr>
                <tr><td class="lbl">Médico</td><td style="text-align:right">{{ $cita->medico->name ?? 'Por asignar' }}</td></tr>
            </table>
            <p style="margin-top:18px;font-size:13px;color:#6b7280">Si no puedes asistir, comunícate con nosotros @if($empresa->telefono) al {{ $empresa->telefono }} @endif.</p>
        </div>
        <div class="foot">{{ $empresa->nombre ?? '' }} · {{ $empresa->direccion ?? '' }}</div>
    </div>
</body></html>
