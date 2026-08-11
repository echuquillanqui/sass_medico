<?php

namespace Database\Seeders;

use App\Models\Cita;
use App\Models\Consulta;
use App\Models\Empresa;
use App\Models\Encuesta;
use App\Models\Especialidad;
use App\Models\Cama;
use App\Models\Hospitalizacion;
use App\Models\ImagenEstudio;
use App\Models\Dispensacion;
use App\Models\Donante;
use App\Models\UnidadSangre;
use App\Models\SolicitudSangre;
use App\Models\Insumo;
use App\Models\Triaje;
use App\Models\LabOrden;
use App\Models\LabExamen;
use App\Models\Pago;
use App\Models\Servicio;
use App\Models\HorarioMedico;
use App\Models\Paciente;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1) Especialidades disponibles en la suite
        $especialidades = [
            ['nombre' => 'Medicina General',       'slug' => 'medicina-general',       'icono' => 'fa-stethoscope',      'color' => '#7c3aed', 'descripcion' => 'Consulta general y atencion primaria.'],
            ['nombre' => 'Pediatria',              'slug' => 'pediatria',              'icono' => 'fa-baby',             'color' => '#ec4899', 'descripcion' => 'Control del nino sano, crecimiento y vacunas.'],
            ['nombre' => 'Ginecologia',            'slug' => 'ginecologia',            'icono' => 'fa-venus',            'color' => '#a855f7', 'descripcion' => 'Salud femenina y control ginecologico.'],
            ['nombre' => 'Obstetricia',            'slug' => 'obstetricia',            'icono' => 'fa-baby-carriage',    'color' => '#d946ef', 'descripcion' => 'Control prenatal y atencion del embarazo.'],
            ['nombre' => 'Odontologia',            'slug' => 'odontologia',            'icono' => 'fa-tooth',            'color' => '#06b6d4', 'descripcion' => 'Odontograma, tratamientos y salud bucal.'],
            ['nombre' => 'Cardiologia',            'slug' => 'cardiologia',            'icono' => 'fa-heart-pulse',      'color' => '#ef4444', 'descripcion' => 'Evaluacion cardiovascular, ECG y presion.'],
            ['nombre' => 'Neumologia',             'slug' => 'neumologia',             'icono' => 'fa-lungs',            'color' => '#0891b2', 'descripcion' => 'Aparato respiratorio y funcion pulmonar.'],
            ['nombre' => 'Gastroenterologia',      'slug' => 'gastroenterologia',      'icono' => 'fa-disease',          'color' => '#d97706', 'descripcion' => 'Sistema digestivo y endoscopia.'],
            ['nombre' => 'Endocrinologia',         'slug' => 'endocrinologia',         'icono' => 'fa-dna',              'color' => '#db2777', 'descripcion' => 'Hormonas, tiroides y diabetes.'],
            ['nombre' => 'Neurologia',             'slug' => 'neurologia',             'icono' => 'fa-brain',            'color' => '#6d28d9', 'descripcion' => 'Sistema nervioso y neurologia clinica.'],
            ['nombre' => 'Psicologia',             'slug' => 'psicologia',             'icono' => 'fa-brain',            'color' => '#8b5cf6', 'descripcion' => 'Sesiones, evaluaciones y seguimiento.'],
            ['nombre' => 'Psiquiatria',            'slug' => 'psiquiatria',            'icono' => 'fa-head-side-virus',  'color' => '#6366f1', 'descripcion' => 'Salud mental y tratamiento psiquiatrico.'],
            ['nombre' => 'Dermatologia',           'slug' => 'dermatologia',           'icono' => 'fa-hand-dots',        'color' => '#f59e0b', 'descripcion' => 'Piel, cabello y unas.'],
            ['nombre' => 'Oftalmologia',           'slug' => 'oftalmologia',           'icono' => 'fa-eye',              'color' => '#0ea5e9', 'descripcion' => 'Salud visual y evaluacion ocular.'],
            ['nombre' => 'Otorrinolaringologia',   'slug' => 'otorrinolaringologia',   'icono' => 'fa-ear-listen',       'color' => '#14b8a6', 'descripcion' => 'Oido, nariz y garganta.'],
            ['nombre' => 'Traumatologia',          'slug' => 'traumatologia',          'icono' => 'fa-bone',             'color' => '#64748b', 'descripcion' => 'Huesos, articulaciones y lesiones.'],
            ['nombre' => 'Reumatologia',           'slug' => 'reumatologia',           'icono' => 'fa-bone',             'color' => '#94a3b8', 'descripcion' => 'Enfermedades articulares y autoinmunes.'],
            ['nombre' => 'Urologia',               'slug' => 'urologia',               'icono' => 'fa-droplet',          'color' => '#2563eb', 'descripcion' => 'Vias urinarias y salud masculina.'],
            ['nombre' => 'Nefrologia',             'slug' => 'nefrologia',             'icono' => 'fa-hand-holding-droplet', 'color' => '#0d9488', 'descripcion' => 'Rinon y funcion renal.'],
            ['nombre' => 'Nutricion',              'slug' => 'nutricion',              'icono' => 'fa-apple-whole',      'color' => '#22c55e', 'descripcion' => 'Evaluacion nutricional y dietas.'],
            ['nombre' => 'Oncologia',              'slug' => 'oncologia',              'icono' => 'fa-ribbon',           'color' => '#e11d48', 'descripcion' => 'Diagnostico y tratamiento del cancer.'],
            ['nombre' => 'Fisioterapia',           'slug' => 'fisioterapia',           'icono' => 'fa-person-walking',   'color' => '#16a34a', 'descripcion' => 'Rehabilitacion y terapia fisica.'],
            ['nombre' => 'Medicina Interna',       'slug' => 'medicina-interna',       'icono' => 'fa-user-doctor',      'color' => '#4f46e5', 'descripcion' => 'Adulto: diagnostico integral.'],
            ['nombre' => 'Geriatria',              'slug' => 'geriatria',              'icono' => 'fa-person-cane',      'color' => '#a16207', 'descripcion' => 'Salud del adulto mayor.'],
            ['nombre' => 'Infectologia',           'slug' => 'infectologia',           'icono' => 'fa-virus-covid',      'color' => '#dc2626', 'descripcion' => 'Enfermedades infecciosas.'],
        ];
        foreach ($especialidades as $e) {
            Especialidad::updateOrCreate(['slug' => $e['slug']], $e);
        }

        // 2) SuperAdmin de la plataforma (dueno del SaaS)
        User::updateOrCreate(
            ['email' => 'superadmin@suitesalud.test'],
            [
                'name' => 'Super Administrador',
                'password' => Hash::make('password'),
                'role' => 'superadmin',
                'activo' => true,
            ]
        );

        // 3) Empresa demo (cliente) con 3 especialidades habilitadas
        // Planes de suscripción
        $planesData = [
            ['nombre' => 'Básico', 'slug' => 'basico', 'precio' => 49, 'ciclo' => 'mensual', 'descripcion' => 'Ideal para consultorios pequeños.', 'limite_especialidades' => 3, 'limite_usuarios' => 5, 'orden' => 1],
            ['nombre' => 'Profesional', 'slug' => 'profesional', 'precio' => 99, 'ciclo' => 'mensual', 'descripcion' => 'Para clínicas en crecimiento.', 'limite_especialidades' => 10, 'limite_usuarios' => 20, 'destacado' => true, 'orden' => 2],
            ['nombre' => 'Premium', 'slug' => 'premium', 'precio' => 199, 'ciclo' => 'mensual', 'descripcion' => 'Todas las especialidades y módulos.', 'limite_especialidades' => null, 'limite_usuarios' => null, 'orden' => 3],
        ];
        foreach ($planesData as $p) {
            \App\Models\Plan::updateOrCreate(['slug' => $p['slug']], $p);
        }
        $planPro = \App\Models\Plan::where('slug', 'profesional')->first();

        $empresa = Empresa::updateOrCreate(
            ['slug' => 'clinica-vida'],
            [
                'nombre' => 'Clinica Vida Sana',
                'ruc' => '20481234567',
                'email' => 'contacto@clinicavida.test',
                'telefono' => '+51 987 654 321',
                'direccion' => 'Av. Salud 123, Lima',
                'plan' => 'profesional',
                'plan_id' => $planPro?->id,
                'vence_suscripcion' => now()->addMonths(5)->toDateString(),
                'moneda' => 'S/',
                'color_primario' => '#7c3aed',
                'horario_inicio' => '08:00',
                'horario_fin' => '20:00',
                'dias_atencion' => 'Lun a Sab',
                'activo' => true,
            ]
        );

        // Suscripción inicial demo con su ticket
        if ($planPro && class_exists(\App\Models\Suscripcion::class) && ! \App\Models\Suscripcion::where('empresa_id', $empresa->id)->exists()) {
            $sub = \App\Models\Suscripcion::create([
                'empresa_id' => $empresa->id,
                'plan_id' => $planPro->id,
                'plan_nombre' => $planPro->nombre,
                'plan_precio' => $planPro->precio,
                'ciclo' => 'mensual',
                'duracion' => 6, 'unidad' => 'meses',
                'descuento' => 0, 'tipo_descuento' => 'monto',
                'subtotal' => $planPro->precio * 6,
                'total' => $planPro->precio * 6,
                'fecha_inicio' => now()->subMonth()->toDateString(),
                'fecha_fin' => now()->addMonths(5)->toDateString(),
                'metodo' => 'Ticket / manual',
                'nota' => 'Suscripción inicial (demo).',
            ]);
            $sub->update(['ticket' => 'SUS-'.str_pad((string) $sub->id, 6, '0', STR_PAD_LEFT)]);
        }

        $ids = Especialidad::pluck('id', 'slug');
        $sync = [];
        foreach ($ids as $id) { $sync[$id] = ['activo' => true]; }
        $empresa->especialidades()->sync($sync);

        // 4) Usuarios de la empresa
        User::updateOrCreate(
            ['email' => 'admin@clinicavida.test'],
            ['name' => 'Ana Torres', 'empresa_id' => $empresa->id, 'role' => 'admin',
             'password' => Hash::make('password'), 'telefono' => '+51 900 111 222', 'activo' => true]
        );
        $medico = User::updateOrCreate(
            ['email' => 'medico@clinicavida.test'],
            ['name' => 'Dr. Carlos Ramirez', 'empresa_id' => $empresa->id, 'role' => 'medico',
             'especialidad_id' => $ids['pediatria'] ?? null, 'cmp' => '45821', 'titulo_profesional' => 'Dr.',
             'password' => Hash::make('password'), 'telefono' => '+51 900 333 444', 'activo' => true]
        );
        User::updateOrCreate(
            ['email' => 'recepcion@clinicavida.test'],
            ['name' => 'Lucia Fernandez', 'empresa_id' => $empresa->id, 'role' => 'recepcion',
             'password' => Hash::make('password'), 'telefono' => '+51 900 555 666', 'activo' => true]
        );

        // 5) Pacientes demo
        $pacientesData = [
            ['nombres' => 'Mateo', 'apellidos' => 'Gomez Ruiz', 'documento' => '75319852', 'fecha_nacimiento' => '2019-04-12', 'sexo' => 'M', 'especialidad_id' => $ids['pediatria'] ?? null, 'telefono' => '+51 911 222 333'],
            ['nombres' => 'Valentina', 'apellidos' => 'Salazar Diaz', 'documento' => '44125879', 'fecha_nacimiento' => '1992-09-30', 'sexo' => 'F', 'especialidad_id' => $ids['ginecologia'] ?? null, 'telefono' => '+51 933 444 555', 'email' => 'valentina@paciente.test'],
            ['nombres' => 'Sofia', 'apellidos' => 'Peralta Leon', 'documento' => '48896231', 'fecha_nacimiento' => '1988-01-15', 'sexo' => 'F', 'especialidad_id' => $ids['odontologia'] ?? null, 'telefono' => '+51 955 666 777'],
            ['nombres' => 'Diego', 'apellidos' => 'Rios Campos', 'documento' => '70012345', 'fecha_nacimiento' => '2015-11-05', 'sexo' => 'M', 'especialidad_id' => $ids['pediatria'] ?? null, 'telefono' => '+51 977 888 999'],
        ];
        foreach ($pacientesData as $p) {
            $p['empresa_id'] = $empresa->id;
            Paciente::updateOrCreate(
                ['empresa_id' => $empresa->id, 'documento' => $p['documento']],
                $p
            );
        }

        // 6) Citas demo
        $pacientes = Paciente::where('empresa_id', $empresa->id)->get();
        $estados = ['pendiente', 'confirmada', 'atendida'];
        foreach ($pacientes as $i => $pac) {
            Cita::updateOrCreate(
                ['empresa_id' => $empresa->id, 'paciente_id' => $pac->id, 'fecha' => now()->addDays($i)->toDateString()],
                [
                    'medico_id' => $medico->id,
                    'especialidad_id' => $pac->especialidad_id,
                    'hora' => sprintf('%02d:00:00', 9 + $i),
                    'duracion' => 30,
                    'estado' => $estados[$i % count($estados)],
                    'motivo' => 'Consulta de control',
                ]
            );
        }

        // 7) Consultas demo (historia clinica)
        foreach ($pacientes->take(3) as $i => $pac) {
            Consulta::updateOrCreate(
                ['empresa_id' => $empresa->id, 'paciente_id' => $pac->id, 'fecha' => now()->subDays(($i + 1) * 5)->toDateString()],
                [
                    'medico_id' => $medico->id,
                    'especialidad_id' => $pac->especialidad_id,
                    'motivo' => 'Control de rutina',
                    'diagnostico' => 'Paciente estable, sin hallazgos patologicos.',
                    'tratamiento' => "Paracetamol 500mg cada 8h por 3 dias. Control en 2 semanas.",
                    'peso' => 12 + $i * 20,
                    'talla' => 90 + $i * 30,
                    'presion_arterial' => '120/80',
                    'frecuencia_cardiaca' => 78,
                    'temperatura' => 36.6,
                ]
            );
        }

        // 8) Pagos demo
        $conceptos = ['Consulta medica', 'Control', 'Tratamiento'];
        $metodos = ['efectivo', 'tarjeta', 'yape_plin'];
        foreach ($pacientes as $i => $pac) {
            Pago::updateOrCreate(
                ['empresa_id' => $empresa->id, 'paciente_id' => $pac->id, 'concepto' => $conceptos[$i % 3]],
                [
                    'monto' => [80, 120, 150, 60][$i % 4],
                    'metodo' => $metodos[$i % 3],
                    'estado' => $i % 4 === 0 ? 'pendiente' : 'pagado',
                    'fecha' => now()->subDays($i * 3)->toDateString(),
                ]
            );
        }
    
        // 9) Acceso al portal del paciente (demo)
        Paciente::where('empresa_id', $empresa->id)->where('email', 'valentina@paciente.test')
            ->update(['password' => Hash::make('password'), 'acceso_portal' => true]);

        // 10) Insumos demo
        $insumos = [
            ['nombre' => 'Guantes de nitrilo', 'categoria' => 'Material', 'unidad' => 'caja', 'stock' => 25, 'stock_minimo' => 10, 'precio' => 18.00],
            ['nombre' => 'Paracetamol 500mg', 'categoria' => 'Medicamento', 'unidad' => 'blister', 'stock' => 8, 'stock_minimo' => 15, 'precio' => 4.50],
            ['nombre' => 'Alcohol 70%', 'categoria' => 'Material', 'unidad' => 'litro', 'stock' => 12, 'stock_minimo' => 5, 'precio' => 9.00],
            ['nombre' => 'Jeringas 5ml', 'categoria' => 'Material', 'unidad' => 'unidad', 'stock' => 120, 'stock_minimo' => 50, 'precio' => 0.60],
            ['nombre' => 'Anestesia dental', 'categoria' => 'Medicamento', 'unidad' => 'cartucho', 'stock' => 6, 'stock_minimo' => 20, 'precio' => 2.80],
        ];
        foreach ($insumos as $ins) {
            $ins['empresa_id'] = $empresa->id;
            Insumo::updateOrCreate(['empresa_id' => $empresa->id, 'nombre' => $ins['nombre']], $ins);
        }

    
        // 11) Servicios demo
        $serviciosDemo = [
            ['nombre' => 'Consulta pediatrica', 'precio' => 80, 'especialidad_id' => $ids['pediatria'] ?? null],
            ['nombre' => 'Control ginecologico', 'precio' => 120, 'especialidad_id' => $ids['ginecologia'] ?? null],
            ['nombre' => 'Limpieza dental', 'precio' => 90, 'especialidad_id' => $ids['odontologia'] ?? null],
            ['nombre' => 'Consulta general', 'precio' => 60, 'especialidad_id' => null],
        ];
        foreach ($serviciosDemo as $sv) {
            $sv['empresa_id'] = $empresa->id;
            Servicio::updateOrCreate(['empresa_id' => $empresa->id, 'nombre' => $sv['nombre']], $sv);
        }

        // 12) Horarios del medico demo (Lun-Vie 09:00-13:00)
        foreach ([1, 2, 3, 4, 5] as $dia) {
            HorarioMedico::updateOrCreate(
                ['empresa_id' => $empresa->id, 'user_id' => $medico->id, 'dia_semana' => $dia, 'hora_inicio' => '09:00'],
                ['hora_fin' => '13:00', 'activo' => true]
            );
        }

    
        // 13) Encuesta demo (sobre una cita atendida)
        $citaAtendida = Cita::where('empresa_id', $empresa->id)->where('estado', 'atendida')->first();
        if ($citaAtendida) {
            Encuesta::updateOrCreate(
                ['empresa_id' => $empresa->id, 'cita_id' => $citaAtendida->id],
                ['paciente_id' => $citaAtendida->paciente_id, 'puntuacion' => 5, 'comentario' => 'Excelente atencion, muy amables.']
            );
        }

    
        // 14) Historial demo (6 meses) para graficos poblados
        $this->call(HistorialDemoSeeder::class);
    
        // 15) Laboratorio: catalogo + una orden demo
        $labCatalogo = [
            ['nombre' => 'Hemoglobina', 'categoria' => 'Hematologia', 'unidad' => 'g/dL', 'valor_referencia' => '12-16', 'precio' => 15],
            ['nombre' => 'Glucosa', 'categoria' => 'Bioquimica', 'unidad' => 'mg/dL', 'valor_referencia' => '70-110', 'precio' => 12],
            ['nombre' => 'Colesterol total', 'categoria' => 'Bioquimica', 'unidad' => 'mg/dL', 'valor_referencia' => '<200', 'precio' => 18],
            ['nombre' => 'Trigliceridos', 'categoria' => 'Bioquimica', 'unidad' => 'mg/dL', 'valor_referencia' => '<150', 'precio' => 18],
            ['nombre' => 'Creatinina', 'categoria' => 'Bioquimica', 'unidad' => 'mg/dL', 'valor_referencia' => '0.6-1.2', 'precio' => 14],
            ['nombre' => 'Examen de orina', 'categoria' => 'Uroanalisis', 'unidad' => '', 'valor_referencia' => 'Normal', 'precio' => 20],
        ];
        foreach ($labCatalogo as $ex) {
            $ex['empresa_id'] = $empresa->id;
            LabExamen::updateOrCreate(['empresa_id' => $empresa->id, 'nombre' => $ex['nombre']], $ex);
        }

        $pacLab = Paciente::where('empresa_id', $empresa->id)->first();
        if ($pacLab) {
            $orden = LabOrden::updateOrCreate(
                ['empresa_id' => $empresa->id, 'paciente_id' => $pacLab->id, 'fecha' => now()->subDays(2)->toDateString()],
                ['medico_id' => $medico->id, 'estado' => 'completada', 'observaciones' => 'Chequeo de rutina']
            );
            $orden->items()->delete();
            $orden->items()->createMany([
                ['nombre' => 'Hemoglobina', 'unidad' => 'g/dL', 'valor_referencia' => '12-16', 'resultado' => '13.5', 'fuera_rango' => false],
                ['nombre' => 'Glucosa', 'unidad' => 'mg/dL', 'valor_referencia' => '70-110', 'resultado' => '128', 'fuera_rango' => true, 'notas' => 'Repetir en ayunas'],
                ['nombre' => 'Colesterol total', 'unidad' => 'mg/dL', 'valor_referencia' => '<200', 'resultado' => '185', 'fuera_rango' => false],
            ]);
        }

    
        // 16) Hospitalizacion: camas + un ingreso demo con evolucion
        $camas = [];
        foreach (['101', '102', '103', 'UCI-1', 'Ped-1'] as $n) {
            $area = str_starts_with($n, 'UCI') ? 'UCI' : (str_starts_with($n, 'Ped') ? 'Pediatria' : 'Hospitalizacion');
            $camas[] = Cama::updateOrCreate(
                ['empresa_id' => $empresa->id, 'nombre' => 'Cama '.$n],
                ['area' => $area, 'activo' => true]
            );
        }
        $pacHosp = Paciente::where('empresa_id', $empresa->id)->first();
        if ($pacHosp && ! Hospitalizacion::where('empresa_id', $empresa->id)->where('estado', 'activa')->exists()) {
            $hosp = Hospitalizacion::create([
                'empresa_id' => $empresa->id,
                'paciente_id' => $pacHosp->id,
                'cama_id' => $camas[0]->id,
                'medico_id' => $medico->id,
                'especialidad_id' => $pacHosp->especialidad_id,
                'fecha_ingreso' => now()->subDays(1),
                'estado' => 'activa',
                'motivo_ingreso' => 'Observacion por cuadro febril.',
                'diagnostico_ingreso' => 'Sindrome febril en estudio.',
            ]);
            $hosp->evoluciones()->create([
                'user_id' => $medico->id,
                'fecha' => now()->subHours(6),
                'nota' => 'Paciente estable, afebril. Continua tratamiento.',
                'presion_arterial' => '110/70',
                'frecuencia_cardiaca' => 76,
                'temperatura' => 36.8,
                'saturacion' => '98%',
            ]);
        }

    
        // 17) Imagenes: un estudio demo informado
        $pacImg = Paciente::where('empresa_id', $empresa->id)->first();
        if ($pacImg) {
            ImagenEstudio::updateOrCreate(
                ['empresa_id' => $empresa->id, 'paciente_id' => $pacImg->id, 'modalidad' => 'Radiografia', 'fecha' => now()->subDays(3)->toDateString()],
                [
                    'medico_id' => $medico->id, 'radiologo_id' => $medico->id,
                    'region' => 'Torax', 'estado' => 'informado',
                    'indicacion' => 'Tos persistente.',
                    'hallazgos' => 'Campos pulmonares sin consolidaciones. Silueta cardiaca normal.',
                    'conclusion' => 'Estudio dentro de limites normales.',
                ]
            );
        }

    
        // 18) Triaje: dos pacientes en cola de emergencias
        $pacsTri = Paciente::where('empresa_id', $empresa->id)->take(2)->get();
        $nivelesDemo = [2, 4];
        $motivosDemo = ['Dolor toracico agudo', 'Dolor de garganta'];
        foreach ($pacsTri as $i => $pac) {
            Triaje::updateOrCreate(
                ['empresa_id' => $empresa->id, 'paciente_id' => $pac->id, 'estado' => 'en_espera'],
                [
                    'user_id' => $medico->id,
                    'nivel' => $nivelesDemo[$i] ?? 4,
                    'motivo' => $motivosDemo[$i] ?? 'Malestar general',
                    'presion_arterial' => '130/85',
                    'frecuencia_cardiaca' => 88,
                    'saturacion' => '97%',
                    'dolor' => $i === 0 ? 8 : 3,
                    'hora_llegada' => now()->subMinutes(($i + 1) * 10),
                ]
            );
        }

    
        // 19) Farmacia: una dispensacion demo (descuenta stock una vez)
        if (! Dispensacion::where('empresa_id', $empresa->id)->exists()) {
            $insumosFarm = Insumo::where('empresa_id', $empresa->id)->take(2)->get();
            if ($insumosFarm->isNotEmpty()) {
                $pacFarm = Paciente::where('empresa_id', $empresa->id)->first();
                $disp = Dispensacion::create([
                    'empresa_id' => $empresa->id,
                    'paciente_id' => $pacFarm?->id,
                    'user_id' => $medico->id,
                    'fecha' => now()->subDays(1)->toDateString(),
                    'observaciones' => 'Entrega segun receta.',
                    'total' => 0,
                ]);
                $total = 0;
                foreach ($insumosFarm as $ins) {
                    $cant = 2;
                    $total += (float) $ins->precio * $cant;
                    $disp->items()->create([
                        'insumo_id' => $ins->id,
                        'nombre' => $ins->nombre,
                        'cantidad' => $cant,
                        'precio' => $ins->precio,
                        'indicaciones' => '1 cada 8 horas',
                    ]);
                    $ins->decrement('stock', $cant);
                }
                $disp->update(['total' => $total]);
            }
        }

    
        // 20) Banco de sangre: donantes, unidades y una solicitud
        $donantesDemo = [
            ['nombres' => 'Pedro', 'apellidos' => 'Gutierrez', 'grupo' => 'O+'],
            ['nombres' => 'Maria', 'apellidos' => 'Lopez', 'grupo' => 'A+'],
            ['nombres' => 'Jose', 'apellidos' => 'Ramos', 'grupo' => 'O-'],
            ['nombres' => 'Carmen', 'apellidos' => 'Diaz', 'grupo' => 'B+'],
        ];
        foreach ($donantesDemo as $i => $dn) {
            $dn['empresa_id'] = $empresa->id;
            $dn['documento'] = '4010'.str_pad((string) $i, 4, '0', STR_PAD_LEFT);
            Donante::updateOrCreate(['empresa_id' => $empresa->id, 'documento' => $dn['documento']], $dn);
        }
        if (! UnidadSangre::where('empresa_id', $empresa->id)->exists()) {
            $stockDemo = ['O+' => 5, 'A+' => 3, 'O-' => 2, 'B+' => 1, 'AB+' => 0];
            foreach ($stockDemo as $g => $cant) {
                for ($k = 0; $k < $cant; $k++) {
                    UnidadSangre::create([
                        'empresa_id' => $empresa->id,
                        'codigo' => 'U'.strtoupper(substr(md5($g.$k.$empresa->id), 0, 6)),
                        'grupo' => $g,
                        'volumen' => 450,
                        'fecha_extraccion' => now()->subDays(10)->toDateString(),
                        'fecha_vencimiento' => now()->addDays(32)->toDateString(),
                        'estado' => 'disponible',
                    ]);
                }
            }
        }
        $pacSangre = Paciente::where('empresa_id', $empresa->id)->first();
        if ($pacSangre && ! SolicitudSangre::where('empresa_id', $empresa->id)->exists()) {
            SolicitudSangre::create([
                'empresa_id' => $empresa->id,
                'paciente_id' => $pacSangre->id,
                'medico_id' => $medico->id,
                'grupo' => 'A+',
                'cantidad' => 2,
                'fecha' => now()->toDateString(),
                'estado' => 'pendiente',
                'motivo' => 'Anemia severa',
            ]);
        }

        // --- Odontograma demo (paciente de odontologia) ---
        $pacOdo = Paciente::where('empresa_id', $empresa->id)
            ->where('especialidad_id', $ids['odontologia'] ?? 0)->first()
            ?? Paciente::where('empresa_id', $empresa->id)->first();
        if ($pacOdo && class_exists(\App\Models\Odontograma::class)) {
            \App\Models\Odontograma::updateOrCreate(
                ['paciente_id' => $pacOdo->id],
                [
                    'empresa_id' => $empresa->id,
                    'user_id' => $medico->id,
                    'dientes' => [
                        '16' => ['s' => ['o' => 'caries', 'm' => 'caries'], 'w' => null],
                        '26' => ['s' => ['o' => 'obturado'], 'w' => null],
                        '36' => ['s' => [], 'w' => 'corona'],
                        '46' => ['s' => [], 'w' => 'endodoncia'],
                        '18' => ['s' => [], 'w' => 'ausente'],
                        '38' => ['s' => [], 'w' => 'extraccion'],
                        '11' => ['s' => ['v' => 'obturado'], 'w' => null],
                        '21' => ['s' => ['v' => 'caries'], 'w' => null],
                        '47' => ['s' => [], 'w' => 'implante'],
                    ],
                    'plan' => [
                        ['pieza' => '16', 'procedimiento' => 'Obturación con resina', 'estado' => 'pendiente', 'costo' => 90],
                        ['pieza' => '46', 'procedimiento' => 'Tratamiento de conducto', 'estado' => 'en_proceso', 'costo' => 380],
                        ['pieza' => '38', 'procedimiento' => 'Exodoncia', 'estado' => 'pendiente', 'costo' => 120],
                        ['pieza' => '36', 'procedimiento' => 'Corona de porcelana', 'estado' => 'realizado', 'costo' => 450],
                    ],
                    'notas' => 'Paciente con buena higiene. Se recomienda control cada 6 meses y profilaxis.',
                ]
            );
        }

        // --- Control prenatal demo (gestante) ---
        $pacGine = Paciente::where('empresa_id', $empresa->id)->where('sexo', 'F')
            ->where('especialidad_id', $ids['ginecologia'] ?? 0)->first()
            ?? Paciente::where('empresa_id', $empresa->id)->where('sexo', 'F')->first();
        if ($pacGine && class_exists(\App\Models\Embarazo::class) && ! \App\Models\Embarazo::where('paciente_id', $pacGine->id)->exists()) {
            $fum = now()->subWeeks(26);
            $emb = \App\Models\Embarazo::create([
                'empresa_id' => $empresa->id,
                'paciente_id' => $pacGine->id,
                'user_id' => $medico->id,
                'fum' => $fum->toDateString(),
                'fpp' => $fum->copy()->addDays(280)->toDateString(),
                'gestas' => 2, 'partos' => 1, 'abortos' => 0, 'cesareas' => 0,
                'grupo_sanguineo' => 'O+', 'riesgo_alto' => false, 'estado' => 'activo',
                'antecedentes' => 'Primer embarazo sin complicaciones. Sin antecedentes patológicos.',
            ]);
            foreach ([[12, 62.0, '110/70', 14, 150], [20, 65.0, '112/72', 21, 145], [26, 67.5, '115/75', 27, 142]] as $c) {
                $emb->controles()->create([
                    'user_id' => $medico->id,
                    'fecha' => $fum->copy()->addWeeks($c[0])->toDateString(),
                    'semanas' => $c[0],
                    'peso' => $c[1],
                    'presion_arterial' => $c[2],
                    'altura_uterina' => $c[3],
                    'fcf' => $c[4],
                    'presentacion' => 'Cefálica',
                    'movimientos_fetales' => true,
                ]);
            }
        }

        // --- Evaluacion cardiovascular demo ---
        $pacCardio = Paciente::where('empresa_id', $empresa->id)
            ->where('especialidad_id', $ids['cardiologia'] ?? 0)->first()
            ?? Paciente::where('empresa_id', $empresa->id)->orderByDesc('id')->first();
        if ($pacCardio && class_exists(\App\Models\EvaluacionCardio::class) && ! \App\Models\EvaluacionCardio::where('paciente_id', $pacCardio->id)->exists()) {
            foreach ([[90, 145, 92, 78, 240, 38, 160, 180, 110, true], [20, 138, 88, 74, 220, 42, 140, 165, 102, true]] as $e) {
                $data = [
                    'pa_sistolica' => $e[1], 'pa_diastolica' => $e[2], 'fc' => $e[3],
                    'colesterol_total' => $e[4], 'hdl' => $e[5], 'ldl' => $e[6],
                    'trigliceridos' => $e[7], 'glucosa' => $e[8], 'fumador' => $e[9], 'diabetes' => false,
                ];
                $riesgo = \App\Models\EvaluacionCardio::estimarRiesgo($data, $pacCardio->edad, $pacCardio->sexo);
                \App\Models\EvaluacionCardio::create(array_merge($data, [
                    'empresa_id' => $empresa->id,
                    'paciente_id' => $pacCardio->id,
                    'user_id' => $medico->id,
                    'fecha' => now()->subDays($e[0])->toDateString(),
                    'ecg_ritmo' => 'Sinusal',
                    'ecg_hallazgos' => 'Sin alteraciones agudas del ST.',
                    'riesgo_pct' => $riesgo['pct'],
                    'riesgo_nivel' => $riesgo['nivel'],
                    'observaciones' => 'Se indica dieta hiposódica y control en 1 mes.',
                ]));
            }
        }

        // --- Mapa de lesiones (dermatograma) demo ---
        $pacDerma = Paciente::where('empresa_id', $empresa->id)
            ->where('especialidad_id', $ids['dermatologia'] ?? 0)->first()
            ?? Paciente::where('empresa_id', $empresa->id)->orderByDesc('id')->skip(1)->first()
            ?? Paciente::where('empresa_id', $empresa->id)->first();
        if ($pacDerma && class_exists(\App\Models\Dermatograma::class)) {
            \App\Models\Dermatograma::updateOrCreate(
                ['paciente_id' => $pacDerma->id],
                [
                    'empresa_id' => $empresa->id,
                    'user_id' => $medico->id,
                    'lesiones' => [
                        ['vista' => 'frente', 'x' => 42, 'y' => 30, 'tipo' => 'nevo', 'descripcion' => 'Nevo de 4 mm, bordes regulares.'],
                        ['vista' => 'frente', 'x' => 58, 'y' => 48, 'tipo' => 'macula', 'descripcion' => 'Mácula hiperpigmentada.'],
                        ['vista' => 'espalda', 'x' => 50, 'y' => 40, 'tipo' => 'papula', 'descripcion' => 'Pápula eritematosa pruriginosa.'],
                    ],
                    'notas' => 'Fototipo III. Se recomienda fotoprotección y control anual de nevos.',
                ]
            );
        }

        // --- Sesiones de psicologia demo ---
        $pacPsico = Paciente::where('empresa_id', $empresa->id)->where('especialidad_id', $ids['psicologia'] ?? 0)->first()
            ?? Paciente::where('empresa_id', $empresa->id)->first();
        if ($pacPsico && class_exists(\App\Models\SesionPsicologica::class) && ! \App\Models\SesionPsicologica::where('paciente_id', $pacPsico->id)->exists()) {
            foreach ([[1, 60, 4, 20, 'Ansiedad generalizada'], [2, 45, 5, 40, 'Manejo de ansiedad'], [3, 30, 7, 65, 'Reestructuración cognitiva']] as $s) {
                \App\Models\SesionPsicologica::create([
                    'empresa_id' => $empresa->id, 'paciente_id' => $pacPsico->id, 'user_id' => $medico->id,
                    'fecha' => now()->subDays($s[1])->toDateString(), 'numero' => $s[0], 'enfoque' => 'TCC',
                    'motivo' => $s[4], 'desarrollo' => 'Se trabajó con técnicas de respiración y registro de pensamientos.',
                    'tareas' => 'Registro diario de emociones.', 'estado_animo' => $s[2], 'progreso' => $s[3],
                    'proxima_cita' => now()->addDays(7)->toDateString(),
                ]);
            }
        }

        // --- Evaluacion oftalmologica demo ---
        $pacOft = Paciente::where('empresa_id', $empresa->id)->where('especialidad_id', $ids['oftalmologia'] ?? 0)->first()
            ?? Paciente::where('empresa_id', $empresa->id)->orderByDesc('id')->first();
        if ($pacOft && class_exists(\App\Models\EvaluacionOftalmo::class) && ! \App\Models\EvaluacionOftalmo::where('paciente_id', $pacOft->id)->exists()) {
            \App\Models\EvaluacionOftalmo::create([
                'empresa_id' => $empresa->id, 'paciente_id' => $pacOft->id, 'user_id' => $medico->id,
                'fecha' => now()->subDays(15)->toDateString(),
                'od_av' => '20/25', 'od_esfera' => -1.25, 'od_cilindro' => -0.50, 'od_eje' => 90, 'od_pio' => 15.0,
                'os_av' => '20/30', 'os_esfera' => -1.50, 'os_cilindro' => -0.75, 'os_eje' => 85, 'os_pio' => 16.0,
                'diagnostico' => 'Miopía con astigmatismo leve', 'observaciones' => 'Se prescribe corrección óptica; control anual.',
            ]);
        }

        // --- Evaluacion nutricional demo ---
        $pacNut = Paciente::where('empresa_id', $empresa->id)->where('especialidad_id', $ids['nutricion'] ?? 0)->first()
            ?? Paciente::where('empresa_id', $empresa->id)->orderByDesc('id')->first();
        if ($pacNut && class_exists(\App\Models\EvaluacionNutricion::class) && ! \App\Models\EvaluacionNutricion::where('paciente_id', $pacNut->id)->exists()) {
            foreach ([[60, 82.0, 170, 30.0, 95, 102], [30, 79.5, 170, 28.0, 92, 101], [1, 77.0, 170, 26.0, 90, 100]] as $n) {
                $m = $n[2] / 100;
                \App\Models\EvaluacionNutricion::create([
                    'empresa_id' => $empresa->id, 'paciente_id' => $pacNut->id, 'user_id' => $medico->id,
                    'fecha' => now()->subDays($n[0])->toDateString(),
                    'peso' => $n[1], 'talla' => $n[2], 'imc' => round($n[1] / ($m * $m), 2),
                    'grasa' => $n[3], 'cintura' => $n[4], 'cadera' => $n[5], 'musculo' => 34.0,
                    'objetivo_kcal' => 1800, 'peso_objetivo' => 72.0,
                    'plan' => 'Déficit calórico moderado, 5 comidas, aumento de proteína y fibra.',
                    'observaciones' => 'Buena adherencia; continuar plan.',
                ]);
            }
        }

        // --- Mapa de lesiones oseas (traumatograma) demo ---
        $pacTrauma = Paciente::where('empresa_id', $empresa->id)->where('especialidad_id', $ids['traumatologia'] ?? 0)->first()
            ?? Paciente::where('empresa_id', $empresa->id)->first();
        if ($pacTrauma && class_exists(\App\Models\Traumatograma::class)) {
            \App\Models\Traumatograma::updateOrCreate(
                ['paciente_id' => $pacTrauma->id],
                [
                    'empresa_id' => $empresa->id, 'user_id' => $medico->id,
                    'lesiones' => [
                        ['vista' => 'frente', 'x' => 40, 'y' => 74, 'tipo' => 'fractura', 'descripcion' => 'Fractura de muñeca izquierda.'],
                        ['vista' => 'frente', 'x' => 44, 'y' => 88, 'tipo' => 'esguince', 'descripcion' => 'Esguince de tobillo grado II.'],
                        ['vista' => 'espalda', 'x' => 50, 'y' => 42, 'tipo' => 'contusion', 'descripcion' => 'Contusión lumbar.'],
                    ],
                    'notas' => 'Fractura inmovilizada con yeso. Control en 4 semanas y fisioterapia.',
                ]
            );
        }

        // --- Evaluaciones por especialidad (motor genérico) demo ---
        if (class_exists(\App\Models\EvaluacionEspecialidad::class)) {
            $demoEval = [
                'endocrinologia' => [
                    [60, ['glucosa' => 165, 'hba1c' => 8.2, 'tsh' => 2.1, 'colesterol' => 220, 'diagnostico' => 'Diabetes tipo 2']],
                    [1,  ['glucosa' => 128, 'hba1c' => 7.1, 'tsh' => 2.0, 'colesterol' => 190, 'diagnostico' => 'Diabetes tipo 2 controlada']],
                ],
                'neumologia' => [
                    [10, ['fev1' => 2.4, 'fvc' => 3.1, 'fev1_fvc' => 77, 'sato2' => 95, 'disnea' => '1', 'diagnostico' => 'Asma leve persistente']],
                ],
                'urologia' => [
                    [20, ['psa' => 3.8, 'ipss' => 12, 'flujo_max' => 14.5, 'residuo' => 40, 'diagnostico' => 'Hiperplasia prostática benigna']],
                ],
            ];
            foreach ($demoEval as $slug => $regs) {
                $espId = $ids[$slug] ?? 0;
                $pac = Paciente::where('empresa_id', $empresa->id)->where('especialidad_id', $espId)->first()
                    ?? Paciente::where('empresa_id', $empresa->id)->inRandomOrder()->first();
                if (! $pac || \App\Models\EvaluacionEspecialidad::where('paciente_id', $pac->id)->where('especialidad_slug', $slug)->exists()) {
                    continue;
                }
                foreach ($regs as $r) {
                    \App\Models\EvaluacionEspecialidad::create([
                        'empresa_id' => $empresa->id,
                        'paciente_id' => $pac->id,
                        'user_id' => $medico->id,
                        'especialidad_slug' => $slug,
                        'fecha' => now()->subDays($r[0])->toDateString(),
                        'datos' => $r[1],
                        'notas' => 'Registro de demostración.',
                    ]);
                }
            }
        }

    }
}
