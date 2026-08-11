<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Empresa;
use App\Models\Plan;
use App\Models\Suscripcion;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class SuscripcionController extends Controller
{
    public function show(Empresa $empresa)
    {
        return view('admin.suscripciones.show', [
            'empresa' => $empresa,
            'planes' => Plan::where('activo', true)->orderBy('orden')->orderBy('precio')->get(),
            'historial' => $empresa->suscripciones()->with('plan')->get(),
        ]);
    }

    public function store(Request $request, Empresa $empresa)
    {
        $data = $request->validate([
            'plan_id' => ['required', 'exists:planes,id'],
            'duracion' => ['required', 'integer', 'min:1', 'max:120'],
            'unidad' => ['required', 'in:meses,anios'],
            'descuento' => ['nullable', 'numeric', 'min:0'],
            'tipo_descuento' => ['required', 'in:monto,porcentaje'],
            'metodo' => ['nullable', 'string', 'max:60'],
            'nota' => ['nullable', 'string', 'max:160'],
        ]);

        $plan = Plan::findOrFail($data['plan_id']);
        $meses = $data['unidad'] === 'anios' ? $data['duracion'] * 12 : $data['duracion'];

        // Precio mensual normalizado (si el plan es anual, se divide entre 12).
        $mensual = $plan->ciclo === 'anual' ? ((float) $plan->precio) / 12 : (float) $plan->precio;
        $subtotal = round($mensual * $meses, 2);

        $desc = (float) ($data['descuento'] ?? 0);
        $descMonto = $data['tipo_descuento'] === 'porcentaje' ? round($subtotal * $desc / 100, 2) : round($desc, 2);
        $total = max(0, round($subtotal - $descMonto, 2));

        // La nueva vigencia extiende desde el vencimiento actual si aún no caduca.
        $hoy = Carbon::today();
        $base = ($empresa->vence_suscripcion && $empresa->vence_suscripcion->gte($hoy))
            ? $empresa->vence_suscripcion->copy() : $hoy->copy();
        $inicio = $base->copy();
        $fin = $base->copy()->addMonths($meses);

        $sub = Suscripcion::create([
            'empresa_id' => $empresa->id,
            'plan_id' => $plan->id,
            'user_id' => auth()->id(),
            'plan_nombre' => $plan->nombre,
            'plan_precio' => $plan->precio,
            'ciclo' => $plan->ciclo,
            'duracion' => $data['duracion'],
            'unidad' => $data['unidad'],
            'descuento' => $desc,
            'tipo_descuento' => $data['tipo_descuento'],
            'subtotal' => $subtotal,
            'total' => $total,
            'fecha_inicio' => $inicio->toDateString(),
            'fecha_fin' => $fin->toDateString(),
            'metodo' => $data['metodo'] ?? 'Ticket / manual',
            'nota' => $data['nota'] ?? null,
        ]);
        $sub->update(['ticket' => 'SUS-'.str_pad((string) $sub->id, 6, '0', STR_PAD_LEFT)]);

        $empresa->update([
            'plan_id' => $plan->id,
            'vence_suscripcion' => $fin->toDateString(),
        ]);

        return redirect()->route('admin.suscripcion.show', $empresa)
            ->with('ok', 'Suscripción generada. Ticket '.$sub->ticket.' · Vence '.$fin->format('d/m/Y').'.');
    }

    public function ticket(Suscripcion $suscripcion)
    {
        return view('admin.suscripciones.ticket', [
            'sub' => $suscripcion->load('empresa', 'plan'),
        ]);
    }
}
