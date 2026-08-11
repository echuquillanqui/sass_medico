<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Empresa;
use App\Models\Pago;
use Illuminate\Support\Carbon;

class MetricasController extends Controller
{
    // Precios de referencia para empresas sin plan asignado (esquema antiguo).
    private const LEGACY = ['basico' => 99, 'profesional' => 199, 'enterprise' => 399];

    public function index()
    {
        $empresas = Empresa::withCount(['usuarios', 'pacientes'])->with('planRef')->get();

        $mrr = 0;
        $porPlan = [];        // nombre_plan => cantidad de empresas activas
        $mrrEmpresa = [];     // empresa_id => MRR mensual

        foreach ($empresas as $e) {
            // Precio mensual: plan real si existe (normalizado a mes), si no, esquema antiguo.
            if ($e->planRef) {
                $precio = (float) $e->planRef->precio;
                if ($e->planRef->ciclo === 'anual') {
                    $precio = $precio / 12;
                }
                $nombre = $e->planRef->nombre;
            } else {
                $precio = self::LEGACY[$e->plan] ?? 0;
                $nombre = ucfirst($e->plan);
            }
            $mrrEmpresa[$e->id] = $precio;

            if ($e->activo) {
                $mrr += $precio;
                $porPlan[$nombre] = ($porPlan[$nombre] ?? 0) + 1;
            }
        }
        arsort($porPlan);

        // Ingresos reales facturados por empresa (pagos).
        $ingresosPorEmpresa = Pago::where('estado', 'pagado')
            ->selectRaw('empresa_id, SUM(monto) total')
            ->groupBy('empresa_id')->pluck('total', 'empresa_id');

        // Suscripciones próximas a vencer (30 días) o vencidas.
        $porVencer = $empresas->filter(function ($e) {
            return $e->vence_suscripcion && $e->dias_restantes !== null && $e->dias_restantes <= 30;
        })->sortBy('vence_suscripcion')->values();

        // Crecimiento de empresas (últimos 6 meses).
        $labels = [];
        $altas = [];
        for ($i = 5; $i >= 0; $i--) {
            $mes = Carbon::now()->subMonths($i);
            $labels[] = $mes->locale('es')->isoFormat('MMM YY');
            $altas[] = Empresa::whereYear('created_at', $mes->year)->whereMonth('created_at', $mes->month)->count();
        }

        return view('admin.metricas.index', [
            'empresas' => $empresas,
            'ingresosPorEmpresa' => $ingresosPorEmpresa,
            'mrrEmpresa' => $mrrEmpresa,
            'mrr' => $mrr,
            'arr' => $mrr * 12,
            'porPlan' => $porPlan,
            'porVencer' => $porVencer,
            'totalActivas' => $empresas->where('activo', true)->count(),
            'labels' => $labels,
            'altas' => $altas,
        ]);
    }
}
