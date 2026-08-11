<!DOCTYPE html><html lang="es"><head><meta charset="utf-8"><style>
    *{font-family:DejaVu Sans,sans-serif}
    body{margin:0;color:#1f2937;font-size:12px}
    .head{background:#1e1b4b;color:#fff;padding:16px 22px}
    .head h1{margin:0;font-size:18px}
    .head p{margin:2px 0 0;font-size:11px;color:#c7c1ef}
    .body{padding:20px 24px}
    .pdata{background:#fdf2fb;border-radius:8px;padding:10px 14px;margin-bottom:14px;font-size:11px}
    h3{font-size:13px;border-bottom:2px solid #ec4899;padding-bottom:4px;margin-top:16px}
    .box{white-space:pre-wrap;font-size:12px;line-height:1.5}
    .foot{margin-top:40px;text-align:center}
    .sign{border-top:1px solid #444;width:240px;margin:26px auto 4px}
</style></head><body>
    <div class="head"><h1>{{ $empresa->nombre ?? 'Clínica' }} — Imagenología</h1>
        <p>Informe de estudio · #{{ str_pad($imagen->id,5,'0',STR_PAD_LEFT) }} · {{ $imagen->fecha->format('d/m/Y') }}</p></div>
    <div class="body">
        <div class="pdata">
            <b style="font-size:13px">{{ $imagen->paciente->nombre_completo }}</b> ·
            {{ $imagen->paciente->tipo_documento }} {{ $imagen->paciente->documento ?? '—' }} ·
            {{ $imagen->paciente->edad !== null ? $imagen->paciente->edad.' años' : '' }}<br>
            <b>Estudio:</b> {{ $imagen->modalidad }} {{ $imagen->region ? '· '.$imagen->region : '' }} ·
            <b>Solicita:</b> {{ $imagen->medico->name ?? '—' }}
        </div>

        @if($imagen->indicacion)<h3>Indicación</h3><div class="box">{{ $imagen->indicacion }}</div>@endif
        <h3>Hallazgos</h3><div class="box">{{ $imagen->hallazgos ?: 'Pendiente de informe.' }}</div>
        <h3>Conclusión</h3><div class="box">{{ $imagen->conclusion ?: '—' }}</div>

        <div class="foot"><div class="sign"></div>
            {{ $imagen->radiologo->titulo_profesional ?? 'Dr(a).' }} {{ $imagen->radiologo->name ?? '' }}<br>
            <span style="font-size:10px;color:#6b7280">Médico radiólogo</span>
        </div>
    </div>
</body></html>
