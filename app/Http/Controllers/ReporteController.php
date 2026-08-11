<?php

namespace App\Http\Controllers;

use App\Models\Cita;
use App\Models\Pago;
use App\Models\Paciente;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class ReporteController extends Controller
{
    private function empresaId(): int
    {
        return (int) auth()->user()->empresa_id;
    }

    private function datos(Request $request): array
    {
        $desde = Carbon::parse($request->get('desde', now()->startOfMonth()->toDateString()));
        $hasta = Carbon::parse($request->get('hasta', now()->endOfMonth()->toDateString()));
        $eid = $this->empresaId();

        $citas = Cita::where('empresa_id', $eid)->whereBetween('fecha', [$desde, $hasta]);
        $porEstado = (clone $citas)->selectRaw('estado, count(*) c')->groupBy('estado')->pluck('c', 'estado');

        $ingresos = Pago::where('empresa_id', $eid)->where('estado', 'pagado')
            ->whereBetween('fecha', [$desde, $hasta])->sum('monto');

        // Ingresos por especialidad (vía citas de pagos)
        $porEspecialidad = Cita::where('citas.empresa_id', $eid)
            ->whereBetween('citas.fecha', [$desde, $hasta])
            ->join('especialidades', 'especialidades.id', '=', 'citas.especialidad_id')
            ->selectRaw('especialidades.nombre, count(*) c')
            ->groupBy('especialidades.nombre')->pluck('c', 'especialidades.nombre');

        return [
            'desde' => $desde, 'hasta' => $hasta,
            'totalCitas' => (clone $citas)->count(),
            'totalPacientes' => Paciente::where('empresa_id', $eid)->count(),
            'nuevosPacientes' => Paciente::where('empresa_id', $eid)->whereBetween('created_at', [$desde, $hasta->copy()->endOfDay()])->count(),
            'ingresos' => $ingresos,
            'porEstado' => $porEstado,
            'porEspecialidad' => $porEspecialidad,
            'empresa' => auth()->user()->empresa,
        ];
    }

    public function index(Request $request)
    {
        return view('reportes.index', $this->datos($request));
    }

    public function pdf(Request $request)
    {
        $pdf = Pdf::loadView('reportes.pdf', $this->datos($request))->setPaper('a4');
        return $pdf->stream('reporte-'.now()->format('Ymd').'.pdf');
    }

    public function excel(Request $request)
    {
        $eid = $this->empresaId();
        $desde = $request->get('desde', now()->startOfMonth()->toDateString());
        $hasta = $request->get('hasta', now()->endOfMonth()->toDateString());

        $citas = Cita::with(['paciente', 'medico', 'especialidad'])
            ->where('empresa_id', $eid)->whereBetween('fecha', [$desde, $hasta])
            ->orderBy('fecha')->get();

        $filename = 'citas-'.$desde.'-a-'.$hasta.'.csv';
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        return response()->stream(function () use ($citas) {
            $out = fopen('php://output', 'w');
            fprintf($out, chr(0xEF).chr(0xBB).chr(0xBF)); // BOM UTF-8 para Excel
            fputcsv($out, ['Fecha', 'Hora', 'Paciente', 'Documento', 'Especialidad', 'Medico', 'Estado', 'Motivo']);
            foreach ($citas as $c) {
                fputcsv($out, [
                    $c->fecha->format('d/m/Y'),
                    substr((string) $c->hora, 0, 5),
                    $c->paciente->nombre_completo ?? '',
                    $c->paciente->documento ?? '',
                    $c->especialidad->nombre ?? '',
                    $c->medico->name ?? '',
                    $c->estado,
                    $c->motivo ?? '',
                ]);
            }
            fclose($out);
        }, 200, $headers);
    }

    public function financiero(\Illuminate\Http\Request $request)
    {
        $eid = $this->empresaId();
        $desde = \Illuminate\Support\Carbon::parse($request->get('desde', now()->startOfMonth()->toDateString()));
        $hasta = \Illuminate\Support\Carbon::parse($request->get('hasta', now()->endOfMonth()->toDateString()));

        $base = \App\Models\Pago::where('empresa_id', $eid)->where('estado', 'pagado');

        $porMetodo = (clone $base)->whereBetween('fecha', [$desde, $hasta])
            ->selectRaw('metodo, SUM(monto) total, COUNT(*) c')->groupBy('metodo')->get();

        $total = (clone $base)->whereBetween('fecha', [$desde, $hasta])->sum('monto');
        $numPagos = (clone $base)->whereBetween('fecha', [$desde, $hasta])->count();

        // Serie mensual (ultimos 6 meses)
        $labels = []; $serie = [];
        for ($i = 5; $i >= 0; $i--) {
            $m = now()->subMonths($i);
            $labels[] = $m->locale('es')->isoFormat('MMM YY');
            $serie[] = (float) \App\Models\Pago::where('empresa_id', $eid)->where('estado', 'pagado')
                ->whereYear('fecha', $m->year)->whereMonth('fecha', $m->month)->sum('monto');
        }

        // Top pacientes por gasto
        $topPacientes = (clone $base)->whereBetween('fecha', [$desde, $hasta])
            ->selectRaw('paciente_id, SUM(monto) total')->groupBy('paciente_id')
            ->orderByDesc('total')->limit(8)->with('paciente')->get();

        return view('reportes.financiero', [
            'empresa' => auth()->user()->empresa,
            'desde' => $desde, 'hasta' => $hasta,
            'porMetodo' => $porMetodo,
            'total' => $total,
            'ticket' => $numPagos ? $total / $numPagos : 0,
            'numPagos' => $numPagos,
            'labels' => $labels, 'serie' => $serie,
            'topPacientes' => $topPacientes,
        ]);
    }


    public function clinico()
    {
        $eid = $this->empresaId();

        $diagnosticos = \App\Models\Consulta::where('empresa_id', $eid)
            ->whereNotNull('diagnostico')->where('diagnostico', '!=', '')
            ->selectRaw('diagnostico, COUNT(*) c')->groupBy('diagnostico')
            ->orderByDesc('c')->limit(8)->pluck('c', 'diagnostico');

        $porEspecialidad = \App\Models\Paciente::where('pacientes.empresa_id', $eid)
            ->leftJoin('especialidades', 'especialidades.id', '=', 'pacientes.especialidad_id')
            ->selectRaw('COALESCE(especialidades.nombre, \'Sin asignar\') nombre, COUNT(*) c')
            ->groupBy('nombre')->pluck('c', 'nombre');

        $pacientes = \App\Models\Paciente::where('empresa_id', $eid)->get();
        $sexo = ['Masculino' => 0, 'Femenino' => 0, 'Otro' => 0];
        $edades = ['0-12' => 0, '13-18' => 0, '19-40' => 0, '41-65' => 0, '65+' => 0];
        foreach ($pacientes as $p) {
            $sexo[['M' => 'Masculino', 'F' => 'Femenino', 'O' => 'Otro'][$p->sexo] ?? 'Otro']++;
            $e = $p->edad;
            if ($e === null) continue;
            if ($e <= 12) $edades['0-12']++;
            elseif ($e <= 18) $edades['13-18']++;
            elseif ($e <= 40) $edades['19-40']++;
            elseif ($e <= 65) $edades['41-65']++;
            else $edades['65+']++;
        }

        $labels = []; $serie = [];
        for ($i = 5; $i >= 0; $i--) {
            $m = now()->subMonths($i);
            $labels[] = $m->locale('es')->isoFormat('MMM YY');
            $serie[] = \App\Models\Consulta::where('empresa_id', $eid)
                ->whereYear('fecha', $m->year)->whereMonth('fecha', $m->month)->count();
        }

        $satisfaccion = \App\Models\Encuesta::where('empresa_id', $eid)->avg('puntuacion');
        $totalEncuestas = \App\Models\Encuesta::where('empresa_id', $eid)->count();

        return view('reportes.clinico', [
            'diagnosticos' => $diagnosticos,
            'porEspecialidad' => $porEspecialidad,
            'sexo' => $sexo,
            'edades' => $edades,
            'labels' => $labels,
            'serie' => $serie,
            'totalConsultas' => \App\Models\Consulta::where('empresa_id', $eid)->count(),
            'totalPacientes' => $pacientes->count(),
            'satisfaccion' => round($satisfaccion ?? 0, 1),
            'totalEncuestas' => $totalEncuestas,
        ]);
    }

}
