<?php

namespace App\Support;

use App\Models\Empresa;

/**
 * Formateo de moneda y números respetando la configuración de cada empresa
 * (símbolo, separador de miles/decimales y n.º de decimales). Multi-tenant:
 * usa la empresa del usuario autenticado; si no hay, aplica valores por defecto.
 */
class Money
{
    protected static function empresa(?Empresa $empresa = null): ?Empresa
    {
        return $empresa ?? auth()->user()?->empresa;
    }

    public static function monto($valor, ?Empresa $empresa = null, ?int $decimales = null): string
    {
        $e = static::empresa($empresa);
        if ($e) {
            return $e->formatoMonto($valor, $decimales);
        }

        return 'S/ '.number_format((float) $valor, $decimales ?? 2, '.', ',');
    }

    public static function numero($valor, ?Empresa $empresa = null, ?int $decimales = null): string
    {
        $e = static::empresa($empresa);
        if ($e) {
            return $e->formatoNumero($valor, $decimales);
        }

        return number_format((float) $valor, $decimales ?? 2, '.', ',');
    }
}
