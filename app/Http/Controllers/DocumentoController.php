<?php

namespace App\Http\Controllers;

use App\Models\Consulta;
use App\Models\Paciente;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class DocumentoController extends Controller
{
    private function empresaId(): int
    {
        return (int) auth()->user()->empresa_id;
    }

    public function certificado(Consulta $consulta)
    {
        abort_unless($consulta->empresa_id === $this->empresaId(), 403);
        $consulta->load(['paciente', 'medico', 'especialidad']);

        $pdf = Pdf::loadView('documentos.certificado', [
            'consulta' => $consulta,
            'empresa' => auth()->user()->empresa,
        ])->setPaper('a4');

        return $pdf->stream('certificado-'.$consulta->id.'.pdf');
    }

    public function constancia(Request $request, Paciente $paciente)
    {
        abort_unless($paciente->empresa_id === $this->empresaId(), 403);

        $pdf = Pdf::loadView('documentos.constancia', [
            'paciente' => $paciente,
            'empresa' => auth()->user()->empresa,
            'medico' => auth()->user(),
            'motivo' => $request->get('motivo', 'atención médica'),
            'dias' => (int) $request->get('dias', 1),
        ])->setPaper('a4');

        return $pdf->stream('constancia-'.$paciente->id.'.pdf');
    }

    public function historia(Paciente $paciente)
    {
        abort_unless($paciente->empresa_id === $this->empresaId(), 403);
        $paciente->load(['consultas' => fn ($q) => $q->orderBy('fecha'), 'consultas.medico', 'consultas.especialidad', 'consultas.recetaItems', 'empresa']);

        $pdf = Pdf::loadView('documentos.historia', [
            'paciente' => $paciente,
            'empresa' => auth()->user()->empresa,
        ])->setPaper('a4');

        return $pdf->stream('historia-'.$paciente->id.'.pdf');
    }

}
