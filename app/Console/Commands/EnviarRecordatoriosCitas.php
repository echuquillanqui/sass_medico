<?php

namespace App\Console\Commands;

use App\Mail\CitaMail;
use App\Models\Cita;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class EnviarRecordatoriosCitas extends Command
{
    protected $signature = 'citas:recordatorios {--dias=1 : Días de anticipación}';

    protected $description = 'Envía recordatorios por correo de las citas próximas';

    public function handle(): int
    {
        $fecha = now()->addDays((int) $this->option('dias'))->toDateString();

        $citas = Cita::with(['paciente', 'especialidad', 'medico', 'empresa'])
            ->whereDate('fecha', $fecha)
            ->whereIn('estado', ['pendiente', 'confirmada'])
            ->get();

        $enviados = 0;
        foreach ($citas as $cita) {
            if ($cita->paciente && $cita->paciente->email) {
                Mail::to($cita->paciente->email)->send(new CitaMail($cita, 'recordatorio'));
                $enviados++;
            }
        }

        $this->info("Recordatorios enviados: {$enviados} (citas del {$fecha}).");

        return self::SUCCESS;
    }
}
