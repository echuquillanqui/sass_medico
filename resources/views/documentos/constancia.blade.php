<!DOCTYPE html><html lang="es"><head><meta charset="utf-8"><style>
    *{font-family:DejaVu Sans,sans-serif}
    body{margin:0;color:#1f2937;font-size:13px}
    .head{background:#1e1b4b;color:#fff;padding:22px 30px}
    .head h1{margin:0;font-size:20px}
    .head p{margin:3px 0 0;font-size:11px;color:#c7c1ef}
    .body{padding:36px 42px;line-height:1.9}
    .title{text-align:center;font-size:17px;font-weight:bold;text-transform:uppercase;letter-spacing:2px;margin-bottom:30px;color:#7c3aed}
    .p{text-align:justify;margin-bottom:16px}
    b.u{border-bottom:1px solid #999;padding:0 6px}
    .foot{margin-top:70px;text-align:center}
    .sign{border-top:1px solid #444;width:260px;margin:0 auto 6px}
</style></head><body>
    <div class="head"><h1>{{ $empresa->nombre ?? 'Clínica' }}</h1>
        <p>{{ $empresa->direccion ?? '' }} @if($empresa->telefono) · {{ $empresa->telefono }} @endif</p></div>
    <div class="body">
        <div class="title">Constancia Médica</div>
        <p class="p">Quien suscribe deja constancia que el/la paciente
            <b class="u">{{ $paciente->nombre_completo }}</b>
            ({{ $paciente->tipo_documento }} <b class="u">{{ $paciente->documento ?? '—' }}</b>)
            requiere reposo/atención por motivo de <b>{{ $motivo }}</b>
            @if($dias > 0) por un periodo de <b class="u">{{ $dias }} día(s)</b>@endif,
            a partir de la fecha.
        </p>
        <p class="p">Se expide la presente para los fines que el interesado estime conveniente.</p>
        <p style="text-align:right;margin-top:26px">{{ now()->locale('es')->isoFormat('D [de] MMMM [de] YYYY') }}</p>
        <div class="foot">
            @if($medico->firma)<img src="{{ $medico->firma }}" style="max-height:70px;display:block;margin:0 auto -6px">@endif
            <div class="sign"></div>
            <b>{{ $medico->titulo_profesional ? $medico->titulo_profesional.' ' : 'Dr(a). ' }}{{ $medico->name }}</b><br>
            <span style="font-size:11px;color:#6b7280">@if($medico->cmp) CMP {{ $medico->cmp }} @endif</span>
        </div>
    </div>
</body></html>
