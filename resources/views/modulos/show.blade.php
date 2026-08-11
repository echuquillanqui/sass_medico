@extends('layouts.app')
@section('title', $especialidad->nombre)

@section('content')
    <div class="page-head">
        <div class="flex gap">
            <div style="width:56px;height:56px;border-radius:16px;background:{{ $especialidad->color }};color:#fff;display:grid;place-items:center;font-size:24px">
                <i class="fa-solid {{ $especialidad->icono }}"></i>
            </div>
            <div><h1>{{ $especialidad->nombre }}</h1><p>{{ $especialidad->descripcion }}</p></div>
        </div>
        <a href="{{ route('pacientes.create') }}" class="btn btn-primary"><i class="fa-solid fa-user-plus"></i> Nuevo paciente</a>
    </div>

    @php
        // Procesos propios de cada especialidad. Formato: [icono, etiqueta, ruta|null, params].
        // Cuando la ruta es null, la tarjeta se muestra informativa (sin enlace).
        $procesos = [
            'odontologia' => [
                ['fa-tooth','Odontograma','odontograma.index',[]],
                ['fa-teeth','Plan de tratamiento','odontograma.index',[]],
                ['fa-x-ray','Radiografías','imagenes.index',[]],
                ['fa-file-invoice-dollar','Presupuestos','pagos.index',[]],
            ],
            'pediatria' => [
                ['fa-child-reaching','Control de crecimiento','crecimiento.index',[]],
                ['fa-syringe','Esquema de vacunas','pacientes.index',[]],
                ['fa-chart-line','Percentiles OMS','crecimiento.index',[]],
                ['fa-baby','Desarrollo psicomotor',null,[]],
            ],
            'ginecologia' => [
                ['fa-baby-carriage','Control prenatal','prenatal.index',[]],
                ['fa-venus','Ciclo menstrual',null,[]],
                ['fa-notes-medical','Papanicolaou','laboratorio.index',[]],
                ['fa-hand-holding-medical','Obstetricia','prenatal.index',[]],
            ],
            'obstetricia' => [
                ['fa-baby-carriage','Control prenatal','prenatal.index',[]],
                ['fa-heart-pulse','Monitoreo fetal',null,[]],
                ['fa-calendar-days','Fecha probable de parto','prenatal.index',[]],
                ['fa-notes-medical','Ecografías','imagenes.index',[]],
            ],
            'cardiologia' => [
                ['fa-heart-pulse','Evaluación cardiovascular','cardio.index',[]],
                ['fa-wave-square','ECG / Ecocardiograma','imagenes.index',[]],
                ['fa-droplet','Perfil lipídico','laboratorio.index',[]],
                ['fa-heart-crack','Riesgo cardiovascular','cardio.index',[]],
            ],
            'dermatologia' => [
                ['fa-hand-dots','Mapa de lesiones','dermatograma.index',[]],
                ['fa-magnifying-glass','Dermatoscopía',null,[]],
                ['fa-vial','Biopsias','laboratorio.index',[]],
                ['fa-camera','Fotografía clínica','imagenes.index',[]],
            ],
            'psicologia' => [
                ['fa-brain','Sesiones y seguimiento','psicologia.index',[]],
                ['fa-clipboard-list','Evaluaciones','psicologia.index',[]],
                ['fa-face-smile','Escala de ánimo','psicologia.index',[]],
                ['fa-chart-simple','Progreso terapéutico','psicologia.index',[]],
            ],
            'oftalmologia' => [
                ['fa-eye','Agudeza visual','oftalmo.index',[]],
                ['fa-glasses','Refracción','oftalmo.index',[]],
                ['fa-gauge-high','Presión intraocular','oftalmo.index',[]],
                ['fa-x-ray','Fondo de ojo','imagenes.index',[]],
            ],
            'nutricion' => [
                ['fa-weight-scale','Antropometría','nutricion.index',[]],
                ['fa-calculator','IMC y composición','nutricion.index',[]],
                ['fa-utensils','Plan alimentario','nutricion.index',[]],
                ['fa-chart-line','Evolución de peso','nutricion.index',[]],
            ],
            'traumatologia' => [
                ['fa-bone','Mapa de lesiones','traumatograma.index',[]],
                ['fa-x-ray','Radiografías','imagenes.index',[]],
                ['fa-person-walking','Rehabilitación',null,[]],
                ['fa-hospital','Cirugías',null,[]],
            ],
        ][$especialidad->slug] ?? null;

        // Si la especialidad usa el motor genérico de evaluación, generamos su tarjeta.
        $evalCfg = config('evaluaciones.'.$especialidad->slug);
        if (! $procesos && $evalCfg) {
            $procesos = [
                [$evalCfg['icono'], $evalCfg['titulo'], 'evaluacion.index', $especialidad->slug],
                ['fa-flask-vial','Laboratorio','laboratorio.index',[]],
                ['fa-x-ray','Imágenes','imagenes.index',[]],
                ['fa-file-lines','Historial','evaluacion.index',$especialidad->slug],
            ];
        }
        $procesos = $procesos ?? [
            ['fa-notes-medical','Historia clínica',null,[]],
            ['fa-flask-vial','Laboratorio','laboratorio.index',[]],
            ['fa-x-ray','Imágenes','imagenes.index',[]],
            ['fa-chart-line','Reportes','reportes.index',[]],
        ];
    @endphp

    <div class="grid g-4 mb">
        @foreach($procesos as $ex)
            @php $url = $ex[2] ? route($ex[2], $ex[3]) : null; @endphp
            @if($url)
                <a href="{{ $url }}" class="card proc-card" style="text-align:center;text-decoration:none;color:inherit;display:block">
                    <div style="font-size:26px;color:{{ $especialidad->color }};margin-bottom:8px"><i class="fa-solid {{ $ex[0] }}"></i></div>
                    <div style="font-weight:600;font-size:13px">{{ $ex[1] }}</div>
                    <div class="cap" style="margin-top:4px;color:var(--violet)"><i class="fa-solid fa-arrow-right"></i> Abrir</div>
                </a>
            @else
                <div class="card" style="text-align:center">
                    <div style="font-size:26px;color:{{ $especialidad->color }};margin-bottom:8px"><i class="fa-solid {{ $ex[0] }}"></i></div>
                    <div style="font-weight:600;font-size:13px">{{ $ex[1] }}</div>
                </div>
            @endif
        @endforeach
    </div>

    <h3 class="mb">Pacientes de {{ $especialidad->nombre }}</h3>
    <div class="table-wrap">
        <table>
            <thead><tr><th>Paciente</th><th>Documento</th><th>Edad</th><th>Teléfono</th><th></th></tr></thead>
            <tbody>
            @forelse($pacientes as $p)
                <tr>
                    <td><span class="avatar-sm">{{ mb_substr($p->nombres,0,1) }}{{ mb_substr($p->apellidos,0,1) }}</span>{{ $p->nombre_completo }}</td>
                    <td>{{ $p->documento ?? '—' }}</td>
                    <td>{{ $p->edad !== null ? $p->edad.' años' : '—' }}</td>
                    <td>{{ $p->telefono ?? '—' }}</td>
                    <td style="text-align:right">
                        @switch($especialidad->slug)
                            @case('odontologia')
                                <a href="{{ route('odontograma.edit',$p) }}" class="btn btn-light btn-sm"><i class="fa-solid fa-tooth"></i> Odontograma</a>
                                @break
                            @case('pediatria')
                                <a href="{{ route('pacientes.crecimiento',$p) }}" class="btn btn-light btn-sm"><i class="fa-solid fa-chart-line"></i> Crecimiento</a>
                                @break
                            @case('ginecologia')
                            @case('obstetricia')
                                <a href="{{ route('prenatal.show',$p) }}" class="btn btn-light btn-sm"><i class="fa-solid fa-baby-carriage"></i> Prenatal</a>
                                @break
                            @case('cardiologia')
                                <a href="{{ route('cardio.show',$p) }}" class="btn btn-light btn-sm"><i class="fa-solid fa-heart-pulse"></i> Cardiovascular</a>
                                @break
                            @case('dermatologia')
                                <a href="{{ route('dermatograma.edit',$p) }}" class="btn btn-light btn-sm"><i class="fa-solid fa-hand-dots"></i> Lesiones</a>
                                @break
                            @case('psicologia')
                                <a href="{{ route('psicologia.show',$p) }}" class="btn btn-light btn-sm"><i class="fa-solid fa-brain"></i> Sesiones</a>
                                @break
                            @case('oftalmologia')
                                <a href="{{ route('oftalmo.show',$p) }}" class="btn btn-light btn-sm"><i class="fa-solid fa-eye"></i> Visión</a>
                                @break
                            @case('nutricion')
                                <a href="{{ route('nutricion.show',$p) }}" class="btn btn-light btn-sm"><i class="fa-solid fa-apple-whole"></i> Nutrición</a>
                                @break
                            @case('traumatologia')
                                <a href="{{ route('traumatograma.edit',$p) }}" class="btn btn-light btn-sm"><i class="fa-solid fa-bone"></i> Lesiones</a>
                                @break
                            @default
                                @if(config('evaluaciones.'.$especialidad->slug))
                                    <a href="{{ route('evaluacion.show', [$especialidad->slug, $p]) }}" class="btn btn-light btn-sm"><i class="fa-solid {{ config('evaluaciones.'.$especialidad->slug.'.icono') }}"></i> Evaluar</a>
                                @endif
                        @endswitch
                        <a href="{{ route('pacientes.show',$p) }}" class="btn btn-light btn-sm"><i class="fa-solid fa-eye"></i> Ver</a>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5"><div class="empty"><i class="fa-solid {{ $especialidad->icono }}"></i><p>Aún no hay pacientes asignados a esta especialidad.</p></div></td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    {{ $pacientes->links() }}

    <style>.proc-card{transition:.15s}.proc-card:hover{transform:translateY(-3px);box-shadow:0 10px 24px rgba(80,60,160,.14)}</style>
@endsection
