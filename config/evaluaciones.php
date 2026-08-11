<?php

/*
 | Configuración de "procesos propios" por especialidad para el motor genérico
 | de evaluación (EvaluacionEspecialidadController). Cada especialidad define
 | sus campos clínicos, sus KPIs y un indicador para la curva de tendencia.
 |
 | tipo de campo: number | text | textarea | select
 | 'kpis'   => hasta 4 campos que se muestran como tarjetas resumen
 | 'grafico'=> ['campo' => 'clave_numerica', 'label' => 'Etiqueta']
 */

$n = fn ($name, $label, $extra = []) => array_merge(['name' => $name, 'label' => $label, 'tipo' => 'number'], $extra);
$t = fn ($name, $label, $extra = []) => array_merge(['name' => $name, 'label' => $label, 'tipo' => 'text'], $extra);
$a = fn ($name, $label) => ['name' => $name, 'label' => $label, 'tipo' => 'textarea', 'col' => 2];
$s = fn ($name, $label, $opciones) => ['name' => $name, 'label' => $label, 'tipo' => 'select', 'opciones' => $opciones];

return [

    'endocrinologia' => [
        'titulo' => 'Evaluación endocrinológica', 'icono' => 'fa-vials', 'color' => '#f59e0b',
        'intro' => 'Control metabólico: glucemia, HbA1c y perfil hormonal.',
        'campos' => [
            $n('glucosa', 'Glucosa (mg/dL)'), $n('hba1c', 'HbA1c (%)', ['step' => '0.1']),
            $n('tsh', 'TSH (µUI/mL)', ['step' => '0.01']), $n('t4', 'T4 libre (ng/dL)', ['step' => '0.01']),
            $n('insulina', 'Insulina (µU/mL)', ['step' => '0.1']), $n('colesterol', 'Colesterol total'),
            $t('diagnostico', 'Diagnóstico', ['col' => 2]),
        ],
        'kpis' => ['glucosa', 'hba1c', 'tsh', 'colesterol'],
        'grafico' => ['campo' => 'hba1c', 'label' => 'HbA1c (%)'],
    ],

    'gastroenterologia' => [
        'titulo' => 'Evaluación gastroenterológica', 'icono' => 'fa-stomach', 'color' => '#f97316',
        'intro' => 'Síntomas digestivos, endoscopía y estado nutricional.',
        'campos' => [
            $s('sintoma', 'Síntoma principal', ['Dolor abdominal', 'Reflujo', 'Diarrea', 'Estreñimiento', 'Náuseas', 'Sangrado', 'Otro']),
            $s('h_pylori', 'H. pylori', ['No evaluado', 'Positivo', 'Negativo']),
            $n('peso', 'Peso (kg)', ['step' => '0.1']),
            $a('endoscopia', 'Hallazgos endoscópicos'),
            $a('plan', 'Plan / dieta'),
            $t('diagnostico', 'Diagnóstico', ['col' => 2]),
        ],
        'kpis' => ['sintoma', 'h_pylori', 'peso', 'diagnostico'],
        'grafico' => ['campo' => 'peso', 'label' => 'Peso (kg)'],
    ],

    'neumologia' => [
        'titulo' => 'Evaluación neumológica', 'icono' => 'fa-lungs', 'color' => '#0ea5e9',
        'intro' => 'Espirometría, saturación y función respiratoria.',
        'campos' => [
            $n('fev1', 'FEV1 (L)', ['step' => '0.01']), $n('fvc', 'FVC (L)', ['step' => '0.01']),
            $n('fev1_fvc', 'FEV1/FVC (%)', ['step' => '0.1']), $n('sato2', 'SatO₂ (%)'),
            $n('pico_flujo', 'Pico flujo (L/min)'),
            $s('disnea', 'Disnea (mMRC)', ['0', '1', '2', '3', '4']),
            $t('diagnostico', 'Diagnóstico', ['col' => 2]),
        ],
        'kpis' => ['fev1', 'fvc', 'sato2', 'fev1_fvc'],
        'grafico' => ['campo' => 'fev1', 'label' => 'FEV1 (L)'],
    ],

    'neurologia' => [
        'titulo' => 'Evaluación neurológica', 'icono' => 'fa-brain', 'color' => '#8b5cf6',
        'intro' => 'Examen neurológico, escalas y estado de conciencia.',
        'campos' => [
            $n('glasgow', 'Glasgow (3-15)'),
            $s('fuerza', 'Fuerza muscular', ['5/5', '4/5', '3/5', '2/5', '1/5', '0/5']),
            $s('marcha', 'Marcha', ['Normal', 'Atáxica', 'Hemipléjica', 'Parkinsoniana', 'Antiálgica']),
            $t('reflejos', 'Reflejos'), $t('pares_craneales', 'Pares craneales'),
            $a('examen', 'Examen / observaciones'),
            $t('diagnostico', 'Diagnóstico', ['col' => 2]),
        ],
        'kpis' => ['glasgow', 'fuerza', 'marcha', 'diagnostico'],
        'grafico' => ['campo' => 'glasgow', 'label' => 'Glasgow'],
    ],

    'urologia' => [
        'titulo' => 'Evaluación urológica', 'icono' => 'fa-prescription-bottle-medical', 'color' => '#0891b2',
        'intro' => 'PSA, flujometría y síntomas del tracto urinario.',
        'campos' => [
            $n('psa', 'PSA (ng/mL)', ['step' => '0.01']), $n('ipss', 'IPSS (0-35)'),
            $n('flujo_max', 'Flujo máx. (mL/s)', ['step' => '0.1']), $n('residuo', 'Residuo postmiccional (mL)'),
            $a('sintomas', 'Síntomas'),
            $t('diagnostico', 'Diagnóstico', ['col' => 2]),
        ],
        'kpis' => ['psa', 'ipss', 'flujo_max', 'residuo'],
        'grafico' => ['campo' => 'psa', 'label' => 'PSA (ng/mL)'],
    ],

    'otorrinolaringologia' => [
        'titulo' => 'Evaluación otorrinolaringológica', 'icono' => 'fa-ear-listen', 'color' => '#14b8a6',
        'intro' => 'Audiometría y exploración de oído, nariz y garganta.',
        'campos' => [
            $n('audiometria_od', 'Audiometría OD (dB)'), $n('audiometria_os', 'Audiometría OS (dB)'),
            $t('otoscopia', 'Otoscopia'), $t('rinoscopia', 'Rinoscopia'), $t('faringe', 'Faringe / laringe'),
            $t('diagnostico', 'Diagnóstico', ['col' => 2]),
        ],
        'kpis' => ['audiometria_od', 'audiometria_os', 'diagnostico', 'otoscopia'],
        'grafico' => ['campo' => 'audiometria_od', 'label' => 'Audiometría OD (dB)'],
    ],

    'psiquiatria' => [
        'titulo' => 'Evaluación psiquiátrica', 'icono' => 'fa-user-doctor', 'color' => '#6366f1',
        'intro' => 'Examen mental, escalas y plan farmacológico.',
        'campos' => [
            $s('estado_animo', 'Estado de ánimo', ['Eutímico', 'Deprimido', 'Ansioso', 'Maníaco', 'Irritable']),
            $s('riesgo_suicida', 'Riesgo suicida', ['Bajo', 'Moderado', 'Alto']),
            $n('phq9', 'PHQ-9 (0-27)'),
            $a('examen_mental', 'Examen mental'), $a('medicacion', 'Medicación'),
            $t('diagnostico', 'Diagnóstico', ['col' => 2]),
        ],
        'kpis' => ['estado_animo', 'riesgo_suicida', 'phq9', 'diagnostico'],
        'grafico' => ['campo' => 'phq9', 'label' => 'PHQ-9'],
    ],

    'reumatologia' => [
        'titulo' => 'Evaluación reumatológica', 'icono' => 'fa-hand-holding-medical', 'color' => '#e11d48',
        'intro' => 'Actividad articular, reactantes y escalas.',
        'campos' => [
            $n('art_dolorosas', 'Articulaciones dolorosas'), $n('art_inflamadas', 'Articulaciones inflamadas'),
            $n('das28', 'DAS28', ['step' => '0.1']), $n('vsg', 'VSG (mm/h)'), $n('pcr', 'PCR (mg/L)', ['step' => '0.1']),
            $s('factor_reumatoide', 'Factor reumatoide', ['No evaluado', 'Positivo', 'Negativo']),
            $t('diagnostico', 'Diagnóstico', ['col' => 2]),
        ],
        'kpis' => ['das28', 'art_dolorosas', 'vsg', 'pcr'],
        'grafico' => ['campo' => 'das28', 'label' => 'DAS28'],
    ],

    'nefrologia' => [
        'titulo' => 'Evaluación nefrológica', 'icono' => 'fa-droplet', 'color' => '#2563eb',
        'intro' => 'Función renal, electrolitos y terapia de reemplazo.',
        'campos' => [
            $n('creatinina', 'Creatinina (mg/dL)', ['step' => '0.01']), $n('urea', 'Urea (mg/dL)'),
            $n('tfg', 'TFG (mL/min/1.73m²)', ['step' => '0.1']), $n('potasio', 'Potasio (mEq/L)', ['step' => '0.1']),
            $s('proteinuria', 'Proteinuria', ['Negativa', 'Trazas', '+', '++', '+++']),
            $s('dialisis', 'Diálisis', ['No', 'Hemodiálisis', 'Peritoneal']),
            $t('diagnostico', 'Diagnóstico', ['col' => 2]),
        ],
        'kpis' => ['creatinina', 'tfg', 'potasio', 'dialisis'],
        'grafico' => ['campo' => 'tfg', 'label' => 'TFG (mL/min)'],
    ],

    'geriatria' => [
        'titulo' => 'Valoración geriátrica integral', 'icono' => 'fa-person-cane', 'color' => '#78716c',
        'intro' => 'Funcionalidad, cognición y síndromes geriátricos.',
        'campos' => [
            $n('barthel', 'Índice de Barthel (0-100)'), $n('lawton', 'Lawton (0-8)'),
            $n('mmse', 'Mini-Mental (0-30)'), $n('caidas', 'Caídas (últimos 6 meses)'),
            $n('farmacos', 'N.º de fármacos'),
            $s('fragilidad', 'Fragilidad', ['Robusto', 'Prefrágil', 'Frágil']),
            $t('diagnostico', 'Diagnóstico / plan', ['col' => 2]),
        ],
        'kpis' => ['barthel', 'mmse', 'fragilidad', 'caidas'],
        'grafico' => ['campo' => 'barthel', 'label' => 'Barthel'],
    ],

    'infectologia' => [
        'titulo' => 'Evaluación infectológica', 'icono' => 'fa-bacterium', 'color' => '#16a34a',
        'intro' => 'Foco infeccioso, microbiología y esquema antibiótico.',
        'campos' => [
            $t('agente', 'Agente sospechado/aislado'), $t('foco', 'Foco infeccioso'),
            $s('cultivo', 'Cultivo', ['Pendiente', 'Positivo', 'Negativo']),
            $n('temperatura', 'Temperatura (°C)', ['step' => '0.1']),
            $a('antibiograma', 'Antibiograma'), $a('esquema_atb', 'Esquema antibiótico'),
            $t('diagnostico', 'Diagnóstico', ['col' => 2]),
        ],
        'kpis' => ['agente', 'cultivo', 'temperatura', 'diagnostico'],
        'grafico' => ['campo' => 'temperatura', 'label' => 'Temperatura (°C)'],
    ],

    'oncologia' => [
        'titulo' => 'Evaluación oncológica', 'icono' => 'fa-ribbon', 'color' => '#db2777',
        'intro' => 'Estadificación, tratamiento y estado funcional.',
        'campos' => [
            $t('tumor', 'Tumor primario'),
            $s('estadio', 'Estadio', ['0', 'I', 'II', 'III', 'IV']),
            $t('tnm', 'TNM'),
            $s('tratamiento', 'Línea de tratamiento', ['Cirugía', 'Quimioterapia', 'Radioterapia', 'Inmunoterapia', 'Hormonal', 'Paliativo']),
            $n('ciclo', 'Ciclo n.º'),
            $s('ecog', 'ECOG', ['0', '1', '2', '3', '4']),
            $t('diagnostico', 'Diagnóstico', ['col' => 2]),
        ],
        'kpis' => ['tumor', 'estadio', 'tratamiento', 'ecog'],
        'grafico' => ['campo' => 'ciclo', 'label' => 'Ciclo'],
    ],

    'fisioterapia' => [
        'titulo' => 'Evaluación de fisioterapia', 'icono' => 'fa-person-walking', 'color' => '#0d9488',
        'intro' => 'Dolor, rango de movimiento y progreso de rehabilitación.',
        'campos' => [
            $n('dolor_eva', 'Dolor EVA (0-10)'),
            $s('fuerza', 'Fuerza muscular', ['5/5', '4/5', '3/5', '2/5', '1/5', '0/5']),
            $t('rango', 'Rango de movimiento'),
            $n('sesion', 'Sesión n.º'), $n('total_sesiones', 'Total de sesiones'),
            $a('objetivo', 'Objetivos / plan'),
        ],
        'kpis' => ['dolor_eva', 'fuerza', 'sesion', 'total_sesiones'],
        'grafico' => ['campo' => 'dolor_eva', 'label' => 'Dolor EVA'],
    ],

    'medicina-general' => [
        'titulo' => 'Evaluación de medicina general', 'icono' => 'fa-stethoscope', 'color' => '#a855f7',
        'intro' => 'Signos vitales, motivo de consulta y plan.',
        'campos' => [
            $t('motivo', 'Motivo de consulta', ['col' => 2]),
            $t('pa', 'Presión arterial'), $n('fc', 'Frec. cardíaca'), $n('temperatura', 'Temperatura (°C)', ['step' => '0.1']),
            $n('sato2', 'SatO₂ (%)'),
            $t('diagnostico', 'Diagnóstico'), $a('plan', 'Plan'),
        ],
        'kpis' => ['pa', 'fc', 'temperatura', 'sato2'],
        'grafico' => ['campo' => 'fc', 'label' => 'Frec. cardíaca'],
    ],

    'medicina-interna' => [
        'titulo' => 'Evaluación de medicina interna', 'icono' => 'fa-notes-medical', 'color' => '#7c3aed',
        'intro' => 'Problemas activos, comorbilidades y signos vitales.',
        'campos' => [
            $a('problemas', 'Problemas activos'),
            $t('pa', 'Presión arterial'), $n('fc', 'Frec. cardíaca'), $n('temperatura', 'Temperatura (°C)', ['step' => '0.1']),
            $n('sato2', 'SatO₂ (%)'), $n('glucosa', 'Glucosa (mg/dL)'),
            $a('comorbilidades', 'Comorbilidades'),
            $t('diagnostico', 'Diagnóstico', ['col' => 2]),
        ],
        'kpis' => ['pa', 'fc', 'temperatura', 'glucosa'],
        'grafico' => ['campo' => 'fc', 'label' => 'Frec. cardíaca'],
    ],

];
