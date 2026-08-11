@extends('layouts.app')
@section('title', 'Copia de seguridad y mantenimiento')

@section('content')
    @php
        $labels = [
            'pacientes'=>'Pacientes','citas'=>'Citas','consultas'=>'Consultas','pagos'=>'Pagos','receta_items'=>'Ítems de receta',
            'adjuntos'=>'Adjuntos','encuestas'=>'Encuestas','vacunas'=>'Vacunas','servicios'=>'Servicios','insumos'=>'Insumos',
            'movimientos_insumo'=>'Mov. de inventario','lab_examenes'=>'Exámenes lab.','lab_ordenes'=>'Órdenes lab.','lab_orden_items'=>'Ítems de orden lab.',
            'camas'=>'Camas','hospitalizaciones'=>'Hospitalizaciones','evoluciones'=>'Evoluciones','imagen_estudios'=>'Estudios de imagen',
            'triajes'=>'Triajes','dispensaciones'=>'Dispensaciones','dispensacion_items'=>'Ítems dispensación','donantes'=>'Donantes',
            'unidades_sangre'=>'Unidades de sangre','solicitudes_sangre'=>'Solicitudes de sangre','horarios_medico'=>'Horarios',
            'odontogramas'=>'Odontogramas','embarazos'=>'Embarazos','controles_prenatales'=>'Controles prenatales','evaluaciones_cardio'=>'Eval. cardiovasculares',
            'dermatogramas'=>'Mapas dermatológicos','sesiones_psicologicas'=>'Sesiones psicología','evaluaciones_oftalmo'=>'Eval. oftalmológicas',
            'evaluaciones_nutricion'=>'Eval. nutricionales','traumatogramas'=>'Mapas traumatológicos','evaluaciones_especialidad'=>'Eval. por especialidad',
            'notificaciones'=>'Notificaciones','auditorias'=>'Auditoría',
        ];
        $conDatos = collect($resumen)->filter(fn($v)=>$v>0);
    @endphp

    <div class="page-head">
        <div class="flex gap">
            <div style="width:56px;height:56px;border-radius:16px;background:#0f766e;color:#fff;display:grid;place-items:center;font-size:24px"><i class="fa-solid fa-database"></i></div>
            <div><h1>Copia de seguridad y mantenimiento</h1><p>Respaldo, restauración y reinicio de los datos de {{ $empresa->nombre }}.</p></div>
        </div>
    </div>

    @if(session('ok'))<div class="alert ok mb">{{ session('ok') }}</div>@endif
    @if(session('error'))<div class="alert mb" style="background:#fef2f2;border-left:4px solid #ef4444;color:#991b1b">{{ session('error') }}</div>@endif

    <div class="note mb" style="background:#eff6ff;border-left:4px solid #3b82f6;color:#1e3a8a;padding:10px 14px;border-radius:0 8px 8px 0">
        <i class="fa-solid fa-shield-halved"></i> Estas acciones afectan <b>únicamente los datos de tu empresa</b> ({{ $empresa->nombre }}). No influyen en ninguna otra empresa que use el sistema.
    </div>

    {{-- Resumen --}}
    <div class="card mb">
        <div class="flex between" style="flex-wrap:wrap;gap:8px">
            <h3 style="margin:0"><i class="fa-solid fa-chart-simple" style="color:var(--violet)"></i> Datos actuales de la empresa</h3>
            <span class="pill violet" style="font-size:14px">{{ number_format($total) }} registros en total</span>
        </div>
        @if($conDatos->isEmpty())
            <p class="muted" style="margin-top:10px">La empresa aún no tiene datos operativos registrados.</p>
        @else
            <div class="grid g-4" style="margin-top:12px">
                @foreach($conDatos->sortDesc() as $t => $n)
                    <div class="metric" style="text-align:center"><div class="big" style="color:#0f766e">{{ number_format($n) }}</div><div class="cap">{{ $labels[$t] ?? $t }}</div></div>
                @endforeach
            </div>
        @endif
    </div>

    <div class="grid g-2e">
        {{-- Copia de seguridad --}}
        <div class="card mb" style="border-top:3px solid #3b82f6">
            <h3 class="mb"><i class="fa-solid fa-cloud-arrow-down" style="color:#3b82f6"></i> Crear copia de seguridad</h3>
            <p class="muted">Descarga un archivo con todos los datos de tu empresa (pacientes, citas, historia, pagos, exámenes, procesos por especialidad, etc.). Guárdalo en un lugar seguro.</p>
            <a href="{{ route('admin.mantenimiento.backup') }}" class="btn btn-primary" style="margin-top:8px"><i class="fa-solid fa-download"></i> Descargar copia (.json)</a>
            <p class="small muted" style="margin-top:8px">Recomendación: realiza copias periódicas (por ejemplo, semanalmente).</p>
        </div>

        {{-- Restaurar --}}
        <div class="card mb" style="border-top:3px solid #8b5cf6">
            <h3 class="mb"><i class="fa-solid fa-rotate-left" style="color:#8b5cf6"></i> Restaurar copia</h3>
            <p class="muted">Sube un archivo de copia de esta empresa. <b>Se reemplazarán los datos operativos actuales</b> por los del respaldo.</p>
            <form method="POST" action="{{ route('admin.mantenimiento.restore') }}" enctype="multipart/form-data" onsubmit="return confirm('Esto reemplazará los datos actuales de la empresa por los de la copia. ¿Continuar?')">
                @csrf
                <div class="field mb"><label>Archivo de copia (.json)</label><input type="file" name="archivo" accept=".json,application/json" required></div>
                <label class="flex gap" style="align-items:center;font-size:13px;cursor:pointer;margin-bottom:10px"><input type="checkbox" name="confirmar" value="1" style="width:auto" required> Entiendo que se reemplazarán los datos actuales.</label>
                <button class="btn btn-light" style="border-color:#8b5cf6;color:#6d28d9"><i class="fa-solid fa-upload"></i> Restaurar copia</button>
            </form>
        </div>
    </div>

    {{-- Zona de peligro: reinicio --}}
    <div class="card" style="border:1.5px solid #fecaca;background:#fef2f2">
        <h3 class="mb" style="color:#b91c1c"><i class="fa-solid fa-triangle-exclamation"></i> Reiniciar sistema (empresa nueva)</h3>
        <p class="muted">Elimina <b>todos los datos operativos</b> de la empresa (pacientes, citas, historia, pagos, etc.) para empezar de cero. <b>Se conservan</b> la configuración de la empresa, sus usuarios y las especialidades habilitadas. Esta acción <b>no se puede deshacer</b> — descarga primero una copia de seguridad.</p>
        <form method="POST" action="{{ route('admin.mantenimiento.reset') }}" onsubmit="return confirm('ÚLTIMA CONFIRMACIÓN: se borrarán todos los datos operativos de la empresa. ¿Continuar?')">
            @csrf
            <div class="field mb" style="max-width:360px"><label>Para confirmar, escribe el nombre exacto de la empresa:</label>
                <input name="confirmacion" placeholder="{{ $empresa->nombre }}" autocomplete="off" required></div>
            <button class="btn btn-danger"><i class="fa-solid fa-trash-can"></i> Reiniciar sistema de la empresa</button>
        </form>
    </div>
@endsection
