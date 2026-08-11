<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="utf-8">
<style>
    * { font-family: DejaVu Sans, sans-serif; }
    body { color:#1f2937; font-size:12px; margin:0; }
    .head { background:#1e1b4b; color:#fff; padding:18px 22px; }
    .head h1 { margin:0; font-size:18px; }
    .head p { margin:2px 0 0; font-size:11px; color:#c7c1ef; }
    .rx { font-size:40px; color:#a855f7; font-weight:bold; }
    .body { padding:18px 24px; }
    .lbl { color:#6b7280; font-size:10px; text-transform:uppercase; letter-spacing:.5px; }
    .box { border:1px solid #e5e7eb; border-radius:8px; padding:10px 12px; margin-top:6px; white-space:pre-wrap; }
    table { width:100%; }
    table.rxt { border-collapse:collapse; margin-top:8px; }
    table.rxt th { background:#fdf2fb; text-align:left; padding:6px 8px; font-size:9px; text-transform:uppercase; color:#6b7280; }
    table.rxt td { padding:6px 8px; border-bottom:1px solid #eee; font-size:11px; vertical-align:top; }
    .foot { margin-top:30px; text-align:center; }
    .sign { border-top:1px solid #9ca3af; width:230px; margin:36px auto 4px; }
</style>
</head>
<body>
    <div class="head">
        <table>
            <tr>
                <td>
                    <h1>{{ $empresa->nombre ?? 'Clínica' }}</h1>
                    <p>{{ $empresa->direccion ?? '' }} @if($empresa->telefono) · {{ $empresa->telefono }} @endif</p>
                    <p>{{ $empresa->ruc ? 'RUC: '.$empresa->ruc : '' }}</p>
                </td>
                <td style="text-align:right"><span class="rx">℞</span></td>
            </tr>
        </table>
    </div>

    <div class="body">
        <table style="margin-bottom:12px">
            <tr>
                <td><span class="lbl">Paciente</span><br><b>{{ $consulta->paciente->nombre_completo }}</b></td>
                <td style="text-align:right"><span class="lbl">Fecha</span><br>{{ $consulta->fecha->format('d/m/Y') }}</td>
            </tr>
            <tr>
                <td style="padding-top:6px"><span class="lbl">Edad</span> {{ $consulta->paciente->edad !== null ? $consulta->paciente->edad.' años' : '—' }}</td>
                <td style="text-align:right;padding-top:6px"><span class="lbl">Especialidad</span> {{ $consulta->especialidad->nombre ?? 'Medicina General' }}</td>
            </tr>
        </table>

        @if($consulta->diagnostico)
            <span class="lbl">Diagnóstico</span>
            <div class="box" style="margin-bottom:10px">{{ $consulta->diagnostico }}</div>
        @endif

        @if($consulta->recetaItems->isNotEmpty())
            <span class="lbl">Medicamentos</span>
            <table class="rxt">
                <tr><th>Medicamento</th><th>Dosis</th><th>Frecuencia</th><th>Duración</th></tr>
                @foreach($consulta->recetaItems as $it)
                    <tr>
                        <td><b>{{ $it->medicamento }}</b>@if($it->presentacion)<br><span style="color:#6b7280">{{ $it->presentacion }}</span>@endif @if($it->indicaciones)<br><span style="color:#6b7280;font-size:10px">{{ $it->indicaciones }}</span>@endif</td>
                        <td>{{ $it->dosis ?? '—' }}</td>
                        <td>{{ $it->frecuencia ?? '—' }}</td>
                        <td>{{ $it->duracion ?? '—' }}</td>
                    </tr>
                @endforeach
            </table>
        @endif

        @if($consulta->tratamiento)
            <div style="margin-top:12px"><span class="lbl">Indicaciones adicionales</span>
                <div class="box">{{ $consulta->tratamiento }}</div>
            </div>
        @endif

        <div class="foot">
            @if($consulta->medico && $consulta->medico->firma)<img src="{{ $consulta->medico->firma }}" style="max-height:70px;display:block;margin:0 auto -6px">@endif
            <div class="sign"></div>
            <b>{{ $consulta->medico->titulo_profesional ? $consulta->medico->titulo_profesional.' ' : 'Dr(a). ' }}{{ $consulta->medico->name ?? '' }}</b><br>
            <span style="font-size:10px;color:#6b7280">
                {{ $consulta->especialidad->nombre ?? '' }}
                @if($consulta->medico && $consulta->medico->cmp) · CMP {{ $consulta->medico->cmp }} @endif
            </span>
        </div>
    </div>
</body>
</html>
