<?php

namespace App\Http\Controllers;

use App\Models\Cita;
use App\Models\Empresa;
use App\Models\Insumo;
use App\Models\Pago;
use App\Models\Paciente;
use App\Models\User;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if ($user->isSuperAdmin()) {
            return $this->superadmin();
        }

        return match ($user->role) {
            'medico' => $this->medico($user),
            'recepcion' => $this->recepcion($user),
            default => $this->admin($user),
        };
    }

    private function superadmin()
    {
        return view('dashboard.superadmin', [
            'totalEmpresas' => Empresa::count(),
            'empresasActivas' => Empresa::where('activo', true)->count(),
            'totalUsuarios' => User::count(),
            'totalPacientes' => Paciente::count(),
            'empresas' => Empresa::withCount(['usuarios', 'pacientes'])
                ->with('especialidadesActivas')->latest()->take(6)->get(),
        ]);
    }

    private function admin($user)
    {
        $empresaId = $user->empresa_id;
        $hoy = Carbon::today();

        $atendidas = Cita::where('empresa_id', $empresaId)->where('estado', 'atendida')->count();
        $totalCitas = max(Cita::where('empresa_id', $empresaId)->count(), 1);

        // 1) Citas por mes (6 meses) + 3) Ingresos por mes (6 meses)
        $mesLabels = [];
        $citasMesSerie = [];
        $ingresosMesSerie = [];
        for ($i = 5; $i >= 0; $i--) {
            $m = $hoy->copy()->subMonths($i);
            $mesLabels[] = $m->locale('es')->isoFormat('MMM');
            $citasMesSerie[] = Cita::where('empresa_id', $empresaId)
                ->whereYear('fecha', $m->year)->whereMonth('fecha', $m->month)->count();
            $ingresosMesSerie[] = (float) Pago::where('empresa_id', $empresaId)->where('estado', 'pagado')
                ->whereYear('fecha', $m->year)->whereMonth('fecha', $m->month)->sum('monto');
        }

        // 2) Citas por estado
        $estados = ['pendiente' => 'Pendiente', 'confirmada' => 'Confirmada', 'atendida' => 'Atendida', 'cancelada' => 'Cancelada', 'no_asistio' => 'No asistio'];
        $porEstado = [];
        foreach ($estados as $k => $label) {
            $porEstado[$label] = Cita::where('empresa_id', $empresaId)->where('estado', $k)->count();
        }

        // 4) Pacientes por especialidad
        $porEspecialidad = Paciente::where('pacientes.empresa_id', $empresaId)
            ->leftJoin('especialidades', 'especialidades.id', '=', 'pacientes.especialidad_id')
            ->selectRaw("COALESCE(especialidades.nombre, 'Sin asignar') nombre, COUNT(*) c")
            ->groupBy('nombre')->orderByDesc('c')->pluck('c', 'nombre');

        $ingresosMes = (float) Pago::where('empresa_id', $empresaId)->where('estado', 'pagado')
            ->whereMonth('fecha', $hoy->month)->whereYear('fecha', $hoy->year)->sum('monto');

        return view('dashboard.index', [
            'empresa' => $user->empresa,
            'citasHoy' => Cita::where('empresa_id', $empresaId)->whereDate('fecha', $hoy)->count(),
            'totalPacientes' => Paciente::where('empresa_id', $empresaId)->count(),
            'citasMes' => Cita::where('empresa_id', $empresaId)->whereMonth('fecha', $hoy->month)->whereYear('fecha', $hoy->year)->count(),
            'ingresosMes' => $ingresosMes,
            'porcentajeAtencion' => (int) round($atendidas / $totalCitas * 100),
            'mesLabels' => $mesLabels,
            'citasMesSerie' => $citasMesSerie,
            'ingresosMesSerie' => $ingresosMesSerie,
            'porEstado' => $porEstado,
            'porEspecialidad' => $porEspecialidad,
            'proximasCitas' => Cita::with(['paciente', 'especialidad', 'medico'])
                ->where('empresa_id', $empresaId)->whereDate('fecha', '>=', $hoy)
                ->orderBy('fecha')->orderBy('hora')->take(6)->get(),
            'especialidades' => $user->empresa?->especialidadesActivas()->get() ?? collect(),
        ]);
    }

    private function medico($user)
    {
        $hoy = Carbon::today();
        $base = Cita::where('empresa_id', $user->empresa_id)->where('medico_id', $user->id);

        return view('dashboard.medico', [
            'empresa' => $user->empresa,
            'citasHoy' => (clone $base)->whereDate('fecha', $hoy)->count(),
            'pendientesHoy' => (clone $base)->whereDate('fecha', $hoy)->whereIn('estado', ['pendiente', 'confirmada'])->count(),
            'atendidasMes' => (clone $base)->where('estado', 'atendida')->whereMonth('fecha', $hoy->month)->count(),
            'agendaHoy' => (clone $base)->with(['paciente', 'especialidad'])
                ->whereDate('fecha', $hoy)->orderBy('hora')->get(),
            'proximas' => (clone $base)->with(['paciente', 'especialidad'])
                ->whereDate('fecha', '>', $hoy)->orderBy('fecha')->orderBy('hora')->take(5)->get(),
        ]);
    }

    private function recepcion($user)
    {
        $empresaId = $user->empresa_id;
        $hoy = Carbon::today();

        return view('dashboard.recepcion', [
            'empresa' => $user->empresa,
            'citasHoy' => Cita::where('empresa_id', $empresaId)->whereDate('fecha', $hoy)->count(),
            'pendientes' => Cita::where('empresa_id', $empresaId)->whereDate('fecha', $hoy)->whereIn('estado', ['pendiente', 'confirmada'])->count(),
            'cobradoHoy' => Pago::where('empresa_id', $empresaId)->where('estado', 'pagado')->whereDate('fecha', $hoy)->sum('monto'),
            'bajoStock' => Insumo::where('empresa_id', $empresaId)->whereColumn('stock', '<=', 'stock_minimo')->count(),
            'agendaHoy' => Cita::where('empresa_id', $empresaId)->with(['paciente', 'especialidad', 'medico'])
                ->whereDate('fecha', $hoy)->orderBy('hora')->get(),
        ]);
    }
}
