<?php

namespace App\Support;

/**
 * Cálculo de percentiles según los Patrones de Crecimiento Infantil de la OMS
 * (método LMS). Valores de referencia aproximados 0-60 meses.
 *
 *   z = ((valor / M)^L - 1) / (L * S)     (L != 0)
 *   percentil = Φ(z) * 100
 */
class WhoLms
{
    // [mes => [L, M, S]]
    private const PESO_NINO = [
        0 => [0.3487, 3.3464, 0.14602], 2 => [0.2297, 5.5675, 0.13395],
        4 => [0.1738, 7.0023, 0.12988], 6 => [0.1257, 7.9340, 0.12730],
        9 => [0.0917, 8.9014, 0.12486], 12 => [0.0402, 9.6479, 0.12261],
        18 => [-0.0158, 10.9385, 0.12164], 24 => [-0.0567, 12.1515, 0.12183],
        36 => [-0.1000, 14.3000, 0.12300], 48 => [-0.1300, 16.3000, 0.12500],
        60 => [-0.1500, 18.3000, 0.12700],
    ];
    private const PESO_NINA = [
        0 => [0.3809, 3.2322, 0.14171], 2 => [0.2380, 5.1282, 0.13257],
        4 => [0.1714, 6.4237, 0.12816], 6 => [0.1181, 7.2970, 0.12619],
        9 => [0.0688, 8.2254, 0.12600], 12 => [0.0248, 8.9462, 0.12440],
        18 => [-0.0200, 10.2315, 0.12400], 24 => [-0.0600, 11.4775, 0.12400],
        36 => [-0.1000, 13.9000, 0.12500], 48 => [-0.1300, 16.0000, 0.12600],
        60 => [-0.1500, 18.2000, 0.12700],
    ];
    private const TALLA_NINO = [
        0 => [1, 49.8842, 0.03795], 2 => [1, 58.4249, 0.03421],
        4 => [1, 63.8000, 0.03280], 6 => [1, 67.6236, 0.03165],
        9 => [1, 72.0000, 0.03100], 12 => [1, 75.7488, 0.03030],
        18 => [1, 82.2000, 0.02990], 24 => [1, 87.1161, 0.02980],
        36 => [1, 96.1000, 0.03000], 48 => [1, 103.3000, 0.03100],
        60 => [1, 110.0000, 0.03200],
    ];
    private const TALLA_NINA = [
        0 => [1, 49.1477, 0.03790], 2 => [1, 57.0673, 0.03502],
        4 => [1, 62.1000, 0.03340], 6 => [1, 65.7311, 0.03227],
        9 => [1, 70.1000, 0.03160], 12 => [1, 74.0000, 0.03090],
        18 => [1, 80.7000, 0.03050], 24 => [1, 85.7153, 0.03050],
        36 => [1, 95.1000, 0.03100], 48 => [1, 102.7000, 0.03200],
        60 => [1, 109.4000, 0.03300],
    ];

    private static function tabla(string $medida, string $sexo): array
    {
        if ($medida === 'peso') {
            return $sexo === 'F' ? self::PESO_NINA : self::PESO_NINO;
        }
        return $sexo === 'F' ? self::TALLA_NINA : self::TALLA_NINO;
    }

    /** Interpola L, M, S para una edad en meses. */
    private static function lms(string $medida, string $sexo, float $mes): array
    {
        $t = self::tabla($medida, $sexo);
        $meses = array_keys($t);
        $mes = max(min($mes, max($meses)), min($meses));

        $prev = $meses[0];
        foreach ($meses as $m) {
            if ($m == $mes) return $t[$m];
            if ($m > $mes) {
                [$l1, $m1, $s1] = $t[$prev];
                [$l2, $m2, $s2] = $t[$m];
                $f = ($mes - $prev) / ($m - $prev);
                return [$l1 + ($l2 - $l1) * $f, $m1 + ($m2 - $m1) * $f, $s1 + ($s2 - $s1) * $f];
            }
            $prev = $m;
        }
        return $t[$prev];
    }

    /** Percentil (0-100) y z-score para un valor medido. */
    public static function percentil(string $medida, string $sexo, float $mes, float $valor): array
    {
        [$L, $M, $S] = self::lms($medida, $sexo, $mes);
        $z = $L != 0 ? ((($valor / $M) ** $L) - 1) / ($L * $S) : log($valor / $M) / $S;
        $p = self::cdf($z) * 100;

        return ['z' => round($z, 2), 'percentil' => round($p, 1)];
    }

    /** Valor esperado para un percentil dado (para dibujar curvas P3/P50/P97). */
    public static function valorEnPercentil(string $medida, string $sexo, float $mes, float $percentil): float
    {
        [$L, $M, $S] = self::lms($medida, $sexo, $mes);
        $z = self::invCdf($percentil / 100);
        $valor = $L != 0 ? $M * ((1 + $L * $S * $z) ** (1 / $L)) : $M * exp($S * $z);

        return round($valor, 2);
    }

    /** Curvas de referencia P3, P50, P97 en el rango 0-60 meses. */
    public static function curvas(string $medida, string $sexo): array
    {
        $out = ['labels' => [], 'p3' => [], 'p50' => [], 'p97' => []];
        for ($m = 0; $m <= 60; $m += 3) {
            $out['labels'][] = $m;
            $out['p3'][] = self::valorEnPercentil($medida, $sexo, $m, 3);
            $out['p50'][] = self::valorEnPercentil($medida, $sexo, $m, 50);
            $out['p97'][] = self::valorEnPercentil($medida, $sexo, $m, 97);
        }
        return $out;
    }

    private static function cdf(float $z): float
    {
        return 0.5 * (1 + self::erf($z / sqrt(2)));
    }

    private static function invCdf(float $p): float
    {
        // Aproximación de Acklam para la inversa de la normal estándar
        $a = [-3.969683028665376e+01, 2.209460984245205e+02, -2.759285104469687e+02, 1.383577518672690e+02, -3.066479806614716e+01, 2.506628277459239e+00];
        $b = [-5.447609879822406e+01, 1.615858368580409e+02, -1.556989798598866e+02, 6.680131188771972e+01, -1.328068155288572e+01];
        $c = [-7.784894002430293e-03, -3.223964580411365e-01, -2.400758277161838e+00, -2.549732539343734e+00, 4.374664141464968e+00, 2.938163982698783e+00];
        $d = [7.784695709041462e-03, 3.224671290700398e-01, 2.445134137142996e+00, 3.754408661907416e+00];
        $pl = 0.02425;
        if ($p < $pl) {
            $q = sqrt(-2 * log($p));
            return ((((($c[0] * $q + $c[1]) * $q + $c[2]) * $q + $c[3]) * $q + $c[4]) * $q + $c[5]) /
                   (((($d[0] * $q + $d[1]) * $q + $d[2]) * $q + $d[3]) * $q + 1);
        }
        if ($p <= 1 - $pl) {
            $q = $p - 0.5; $r = $q * $q;
            return ((((($a[0] * $r + $a[1]) * $r + $a[2]) * $r + $a[3]) * $r + $a[4]) * $r + $a[5]) * $q /
                   ((((($b[0] * $r + $b[1]) * $r + $b[2]) * $r + $b[3]) * $r + $b[4]) * $r + 1);
        }
        $q = sqrt(-2 * log(1 - $p));
        return -((((($c[0] * $q + $c[1]) * $q + $c[2]) * $q + $c[3]) * $q + $c[4]) * $q + $c[5]) /
                (((($d[0] * $q + $d[1]) * $q + $d[2]) * $q + $d[3]) * $q + 1);
    }

    private static function erf(float $x): float
    {
        $t = 1 / (1 + 0.3275911 * abs($x));
        $y = 1 - ((((1.061405429 * $t - 1.453152027) * $t + 1.421413741) * $t - 0.284496736) * $t + 0.254829592) * $t * exp(-$x * $x);
        return $x >= 0 ? $y : -$y;
    }
}
