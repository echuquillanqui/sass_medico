<!DOCTYPE html><html lang="es"><head><meta charset="utf-8"><style>
    *{font-family:DejaVu Sans,sans-serif}
    body{margin:0;color:#1f2937;font-size:12px}
    .head{background:#1e1b4b;color:#fff;padding:18px 24px}
    .head h1{margin:0;font-size:18px}
    .head p{margin:2px 0 0;font-size:11px;color:#c7c1ef}
    .body{padding:20px 24px}
    .pdata{background:#fdf2fb;border-radius:8px;padding:12px 14px;margin-bottom:16px;font-size:11px}
    .cons{border:1px solid #e5e7eb;border-radius:8px;padding:12px 14px;margin-bottom:12px}
    .cons .top{border-bottom:1px solid #eee;padding-bottom:6px;margin-bottom:6px}
    .lbl{color:#6b7280}
    .vit{display:inline-block;background:#f3e8ff;color:#6d28d9;border-radius:6px;padding:2px 8px;margin:2px;font-size:10px}
    table.rx{width:100%;border-collapse:collapse;margin-top:6px}
    table.rx th{background:#fafafa;text-align:left;padding:4px 6px;font-size:9px;color:#6b7280;text-transform:uppercase}
    table.rx td{padding:4px 6px;border-bottom:1px solid #f0f0f0;font-size:10px}
</style></head><body>
    <div class="head"><h1>{{ $empresa->nombre ?? 'Clínica' }}</h1><p>Historia Clínica · generada {{ now()->format('d/m/Y H:i') }}</p></div>
    <div class="body">
        <div class="pdata">
            <b style="font-size:14px">{{ $paciente->nombre_completo }}</b><br>
            {{ $paciente->tipo_documento }} {{ $paciente->documento ?? '—' }} ·
            {{ $paciente->edad !== null ? $paciente->edad.' años' : 'Edad N/D' }} ·
            {{ ['M'=>'Masculino','F'=>'Femenino','O'=>'Otro'][$paciente->sexo] ?? '—' }}<br>
            Tel: {{ $paciente->telefono ?? '—' }} · Grupo: {{ $paciente->grupo_sanguineo ?? '—' }}<br>
            <b>Alergias:</b> {{ $paciente->alergias ?? 'Ninguna' }} · <b>Antecedentes:</b> {{ $paciente->antecedentes ?? 'Ninguno' }}
        </div>

        <h3 style="font-size:13px;border-bottom:2px solid #ec4899;padding-bottom:4px">Consultas ({{ $paciente->consultas->count() }})</h3>
        @forelse($paciente->consultas as $c)
            <div class="cons">
                <div class="top">
                    <b>{{ $c->fecha->format('d/m/Y') }}</b> ·
                    {{ $c->especialidad->nombre ?? 'General' }} ·
                    {{ $c->medico->titulo_profesional ?? 'Dr(a).' }} {{ $c->medico->name ?? '' }}
                </div>
                <div><span class="lbl">Motivo:</span> {{ $c->motivo ?? '—' }}</div>
                <div><span class="lbl">Diagnóstico:</span> {{ $c->diagnostico ?? '—' }}</div>
                <div><span class="lbl">Tratamiento:</span> {{ $c->tratamiento ?? '—' }}</div>
                <div style="margin-top:4px">
                    @if($c->peso)<span class="vit">Peso {{ $c->peso }}kg</span>@endif
                    @if($c->talla)<span class="vit">Talla {{ $c->talla }}cm</span>@endif
                    @if($c->imc)<span class="vit">IMC {{ $c->imc }}</span>@endif
                    @if($c->presion_arterial)<span class="vit">PA {{ $c->presion_arterial }}</span>@endif
                    @if($c->frecuencia_cardiaca)<span class="vit">FC {{ $c->frecuencia_cardiaca }}</span>@endif
                    @if($c->temperatura)<span class="vit">T° {{ $c->temperatura }}</span>@endif
                </div>
                @if($c->recetaItems->isNotEmpty())
                    <table class="rx"><tr><th>Medicamento</th><th>Dosis</th><th>Frecuencia</th><th>Duración</th></tr>
                        @foreach($c->recetaItems as $it)
                            <tr><td>{{ $it->medicamento }}</td><td>{{ $it->dosis ?? '—' }}</td><td>{{ $it->frecuencia ?? '—' }}</td><td>{{ $it->duracion ?? '—' }}</td></tr>
                        @endforeach
                    </table>
                @endif
            </div>
        @empty
            <p>Sin consultas registradas.</p>
        @endforelse
    </div>
</body></html>
