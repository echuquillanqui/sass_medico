<?php

namespace Database\Seeders;

use App\Models\Cita;
use App\Models\Empresa;
use App\Models\Pago;
use App\Models\Paciente;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class HistorialDemoSeeder extends Seeder
{
    // Marca para identificar (y poder regenerar) los datos demo históricos
    private const TAG = '[demo-hist]';

    public function run(): void
    {
        $empresa = Empresa::where('slug', 'clinica-vida')->first();
        if (! $empresa) {
            $this->command?->warn('No existe la empresa demo (clinica-vida). Ejecuta primero db:seed.');
            return;
        }

        $medicos = User::where('empresa_id', $empresa->id)->where('role', 'medico')->get();
        $medicoIds = $medicos->pluck('id')->all() ?: [null];

        // Limpiar datos demo previos (idempotente)
        Cita::where('empresa_id', $empresa->id)->where('notas', self::TAG)->delete();
        Pago::where('empresa_id', $empresa->id)->where('notas', self::TAG)->delete();
        Paciente::where('empresa_id', $empresa->id)->where('antecedentes', self::TAG)->delete();

        // Pacientes demo repartidos por especialidad (para el grafico de especialidades)
        $espIds = $empresa->especialidadesActivas()->pluck('especialidades.id', 'especialidades.slug');
        $nombres = ['Lucas', 'Emma', 'Santiago', 'Mia', 'Thiago', 'Isabella', 'Benjamin', 'Camila', 'Martin', 'Renata', 'Joaquin', 'Antonella', 'Gael', 'Luciana', 'Dylan'];
        $apellidos = ['Vargas', 'Flores', 'Castro', 'Rojas', 'Mendoza', 'Aguilar', 'Herrera', 'Cordova', 'Paredes', 'Quispe'];

        $repartoEsp = [];
        foreach ($espIds as $slug => $id) {
            $cuantos = $slug === 'pediatria' ? 6 : ($slug === 'ginecologia' ? 4 : 3);
            $repartoEsp = array_merge($repartoEsp, array_fill(0, $cuantos, $id));
        }

        foreach ($repartoEsp as $k => $espId) {
            Paciente::create([
                'empresa_id' => $empresa->id,
                'especialidad_id' => $espId,
                'nombres' => $nombres[$k % count($nombres)],
                'apellidos' => $apellidos[$k % count($apellidos)].' '.$apellidos[($k + 3) % count($apellidos)],
                'tipo_documento' => 'DNI',
                'documento' => (string) (70000000 + random_int(1000000, 9999999)),
                'fecha_nacimiento' => now()->subYears(random_int(2, 60))->subDays(random_int(0, 300))->toDateString(),
                'sexo' => ['M', 'F'][array_rand(['M', 'F'])],
                'telefono' => '+51 9'.random_int(10000000, 99999999),
                'antecedentes' => self::TAG,
                'activo' => true,
            ]);
        }

        $pacientes = Paciente::where('empresa_id', $empresa->id)->get();

        // Cantidad de citas por mes (mes -5 .. mes 0) para curvas variadas
        $citasPorMes = [9, 14, 7, 18, 12, 22];
        $montos = [60, 80, 90, 120, 150, 180, 200];
        $estadosPasados = ['atendida', 'atendida', 'atendida', 'no_asistio', 'cancelada'];
        $metodosPago = ['efectivo', 'tarjeta', 'yape_plin', 'transferencia'];

        $hoy = Carbon::today();
        $totalCitas = 0;
        $totalPagos = 0;

        for ($offset = 5; $offset >= 0; $offset--) {
            $mesBase = $hoy->copy()->subMonths($offset);
            $diasEnMes = (int) $mesBase->daysInMonth;
            $n = $citasPorMes[5 - $offset];

            for ($i = 0; $i < $n; $i++) {
                $topDia = $offset === 0 ? max(1, min($hoy->day, $diasEnMes)) : $diasEnMes;
                $fecha = $mesBase->copy()->day(random_int(1, $topDia));
                $pac = $pacientes->random();
                $estado = $offset === 0 ? 'confirmada' : $estadosPasados[array_rand($estadosPasados)];

                $cita = Cita::create([
                    'empresa_id' => $empresa->id,
                    'paciente_id' => $pac->id,
                    'medico_id' => $medicoIds[array_rand($medicoIds)],
                    'especialidad_id' => $pac->especialidad_id,
                    'fecha' => $fecha->toDateString(),
                    'hora' => sprintf('%02d:%02d:00', random_int(8, 18), [0, 30][array_rand([0, 30])]),
                    'duracion' => 30,
                    'estado' => $estado,
                    'motivo' => 'Consulta',
                    'notas' => self::TAG,
                ]);
                $totalCitas++;

                if ($estado === 'atendida') {
                    Pago::create([
                        'empresa_id' => $empresa->id,
                        'paciente_id' => $pac->id,
                        'cita_id' => $cita->id,
                        'concepto' => 'Consulta medica',
                        'monto' => $montos[array_rand($montos)],
                        'metodo' => $metodosPago[array_rand($metodosPago)],
                        'estado' => 'pagado',
                        'fecha' => $fecha->toDateString(),
                        'notas' => self::TAG,
                    ]);
                    $totalPagos++;
                }
            }
        }

        $this->command?->info("Historial demo: {$totalCitas} citas, {$totalPagos} pagos y ".count($repartoEsp)." pacientes en 6 meses.");
    }
}
