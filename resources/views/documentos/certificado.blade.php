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
        <p>{{ $empresa->direccion ?? '' }} @if($empresa->telefono) · {{ $empresa->telefono }} @endif @if($empresa->ruc) · RUC {{ $empresa->ruc }} @endif</p></div>
    <div class="body">
        <div class="title">Certificado de Atención Médica</div>
        <p class="p">Por medio del presente se deja constancia que el/la paciente
            <b class="u">{{ $consulta->paciente->nombre_completo }}</b>,
            identificado(a) con {{ $consulta->paciente->tipo_documento }}
            <b class="u">{{ $consulta->paciente->documento ?? '—' }}</b>,
            fue atendido(a) en nuestro establecimiento el día
            <b class="u">{{ $consulta->fecha->locale('es')->isoFormat('D [de] MMMM [de] YYYY') }}</b>
            en el área de <b>{{ $consulta->especialidad->nombre ?? 'Medicina General' }}</b>.
        </p>
        @if($consulta->diagnostico)
        <p class="p">Diagnóstico: <b>{{ $consulta->diagnostico }}</b>.</p>
        @endif
        <p class="p">Se expide el presente certificado a solicitud del interesado para los fines que estime conveniente.</p>
        <p style="text-align:right;margin-top:26px">{{ $empresa->direccion ? explode(',', $empresa->direccion)[0].', ' : '' }}{{ now()->locale('es')->isoFormat('D [de] MMMM [de] YYYY') }}</p>
        <div class="foot">
            @if($consulta->medico && $consulta->medico->firma)<img src="{{ $consulta->medico->firma }}" style="max-height:70px;display:block;margin:0 auto -6px">@endif
            <div class="sign"></div>
            <b>{{ $consulta->medico->titulo_profesional ? $consulta->medico->titulo_profesional.' ' : 'Dr(a). ' }}{{ $consulta->medico->name ?? '' }}</b><br>
            <span style="font-size:11px;color:#6b7280">{{ $consulta->especialidad->nombre ?? '' }}@if($consulta->medico && $consulta->medico->cmp) · CMP {{ $consulta->medico->cmp }}@endif</span>
        </div>
    </div>
</body></html>
