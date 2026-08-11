<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;

/**
 * Copia de seguridad, restauración y reinicio de datos POR EMPRESA (multi-tenant).
 *
 * Todo se filtra por empresa_id (o por su tabla padre), de modo que exportar,
 * restaurar o reiniciar una empresa NUNCA afecta a los datos de otras empresas.
 * NO se tocan las tablas compartidas: empresas, users, especialidades ni la
 * asignación de especialidades de la empresa.
 *
 * Definición de cada tabla:
 *   'scope'  => 'empresa'  (tiene columna empresa_id)
 *            => ['parent' => 'tabla_padre', 'fk' => 'columna']  (se acota por el padre)
 *   'remap'  => ['columna_fk' => 'tabla_tenant_referenciada', ...]  (FKs a remapear al restaurar)
 *
 * Las FKs hacia tablas compartidas (users: medico_id/user_id/radiologo_id,
 * especialidades: especialidad_id) se conservan tal cual.
 */
class TenantData
{
    /** Orden de dependencias: padres primero. */
    public static function tables(): array
    {
        return [
            'pacientes'                => ['scope' => 'empresa', 'remap' => []],
            'servicios'                => ['scope' => 'empresa', 'remap' => []],
            'insumos'                  => ['scope' => 'empresa', 'remap' => []],
            'lab_examenes'             => ['scope' => 'empresa', 'remap' => ['padre_id' => 'lab_examenes']],
            'camas'                    => ['scope' => 'empresa', 'remap' => []],
            'horarios_medico'          => ['scope' => 'empresa', 'remap' => []],
            'donantes'                 => ['scope' => 'empresa', 'remap' => []],
            'citas'                    => ['scope' => 'empresa', 'remap' => ['paciente_id' => 'pacientes']],
            'consultas'                => ['scope' => 'empresa', 'remap' => ['paciente_id' => 'pacientes', 'cita_id' => 'citas']],
            'pagos'                    => ['scope' => 'empresa', 'remap' => ['paciente_id' => 'pacientes', 'cita_id' => 'citas', 'consulta_id' => 'consultas']],
            'receta_items'             => ['scope' => ['parent' => 'consultas', 'fk' => 'consulta_id'], 'remap' => ['consulta_id' => 'consultas']],
            'adjuntos'                 => ['scope' => 'empresa', 'remap' => ['paciente_id' => 'pacientes', 'consulta_id' => 'consultas']],
            'encuestas'                => ['scope' => 'empresa', 'remap' => ['paciente_id' => 'pacientes', 'cita_id' => 'citas']],
            'vacunas'                  => ['scope' => 'empresa', 'remap' => ['paciente_id' => 'pacientes']],
            'lab_ordenes'              => ['scope' => 'empresa', 'remap' => ['paciente_id' => 'pacientes', 'consulta_id' => 'consultas']],
            'lab_orden_items'          => ['scope' => ['parent' => 'lab_ordenes', 'fk' => 'lab_orden_id'], 'remap' => ['lab_orden_id' => 'lab_ordenes', 'lab_examen_id' => 'lab_examenes']],
            'hospitalizaciones'        => ['scope' => 'empresa', 'remap' => ['paciente_id' => 'pacientes', 'cama_id' => 'camas']],
            'evoluciones'              => ['scope' => ['parent' => 'hospitalizaciones', 'fk' => 'hospitalizacion_id'], 'remap' => ['hospitalizacion_id' => 'hospitalizaciones']],
            'imagen_estudios'          => ['scope' => 'empresa', 'remap' => ['paciente_id' => 'pacientes']],
            'triajes'                  => ['scope' => 'empresa', 'remap' => ['paciente_id' => 'pacientes']],
            'dispensaciones'           => ['scope' => 'empresa', 'remap' => ['paciente_id' => 'pacientes', 'consulta_id' => 'consultas']],
            'dispensacion_items'       => ['scope' => ['parent' => 'dispensaciones', 'fk' => 'dispensacion_id'], 'remap' => ['dispensacion_id' => 'dispensaciones', 'insumo_id' => 'insumos']],
            'movimientos_insumo'       => ['scope' => 'empresa', 'remap' => ['insumo_id' => 'insumos']],
            'unidades_sangre'          => ['scope' => 'empresa', 'remap' => ['donante_id' => 'donantes']],
            'solicitudes_sangre'       => ['scope' => 'empresa', 'remap' => ['paciente_id' => 'pacientes']],
            'odontogramas'             => ['scope' => 'empresa', 'remap' => ['paciente_id' => 'pacientes']],
            'embarazos'                => ['scope' => 'empresa', 'remap' => ['paciente_id' => 'pacientes']],
            'controles_prenatales'     => ['scope' => ['parent' => 'embarazos', 'fk' => 'embarazo_id'], 'remap' => ['embarazo_id' => 'embarazos']],
            'evaluaciones_cardio'      => ['scope' => 'empresa', 'remap' => ['paciente_id' => 'pacientes']],
            'dermatogramas'            => ['scope' => 'empresa', 'remap' => ['paciente_id' => 'pacientes']],
            'sesiones_psicologicas'    => ['scope' => 'empresa', 'remap' => ['paciente_id' => 'pacientes']],
            'evaluaciones_oftalmo'     => ['scope' => 'empresa', 'remap' => ['paciente_id' => 'pacientes']],
            'evaluaciones_nutricion'   => ['scope' => 'empresa', 'remap' => ['paciente_id' => 'pacientes']],
            'traumatogramas'           => ['scope' => 'empresa', 'remap' => ['paciente_id' => 'pacientes']],
            'evaluaciones_especialidad'=> ['scope' => 'empresa', 'remap' => ['paciente_id' => 'pacientes']],
            'notificaciones'           => ['scope' => 'empresa', 'remap' => []],
            'auditorias'               => ['scope' => 'empresa', 'remap' => []],
        ];
    }

    /** Exporta todos los datos de una empresa a un arreglo serializable. */
    public static function export(int $empresaId): array
    {
        $ids = [];              // ids exportados por tabla (para acotar hijos)
        $out = [];

        foreach (self::tables() as $tabla => $def) {
            if (! self::existe($tabla)) {
                continue;
            }
            $q = DB::table($tabla);
            if ($def['scope'] === 'empresa') {
                $q->where('empresa_id', $empresaId);
            } else {
                $parent = $def['scope']['parent'];
                $q->whereIn($def['scope']['fk'], $ids[$parent] ?? [0]);
            }
            $rows = $q->get()->map(fn ($r) => (array) $r)->all();
            $out[$tabla] = $rows;
            $ids[$tabla] = array_column($rows, 'id');
        }

        return [
            'meta' => [
                'app' => 'suite-salud-modular',
                'tipo' => 'backup-empresa',
                'version' => 1,
                'empresa_id_origen' => $empresaId,
                'fecha' => now()->toIso8601String(),
            ],
            'tablas' => $out,
        ];
    }

    /** Cuenta de registros por tabla, para mostrar en la interfaz. */
    public static function resumen(int $empresaId): array
    {
        $ids = [];
        $res = [];
        foreach (self::tables() as $tabla => $def) {
            if (! self::existe($tabla)) {
                continue;
            }
            $q = DB::table($tabla);
            if ($def['scope'] === 'empresa') {
                $q->where('empresa_id', $empresaId);
            } else {
                $q->whereIn($def['scope']['fk'], $ids[$def['scope']['parent']] ?? [0]);
            }
            $rowIds = $q->pluck('id')->all();
            $ids[$tabla] = $rowIds;
            $res[$tabla] = count($rowIds);
        }

        return $res;
    }

    /** Elimina TODOS los datos operativos de una empresa (no toca empresa, usuarios ni especialidades). */
    public static function reset(int $empresaId): void
    {
        // ids vivos por tabla (para borrar hijos por padre)
        $ids = [];
        foreach (self::tables() as $tabla => $def) {
            if (! self::existe($tabla)) {
                continue;
            }
            $q = DB::table($tabla);
            if ($def['scope'] === 'empresa') {
                $q->where('empresa_id', $empresaId);
            } else {
                $q->whereIn($def['scope']['fk'], $ids[$def['scope']['parent']] ?? [0]);
            }
            $ids[$tabla] = $q->pluck('id')->all();
        }

        DB::transaction(function () use ($empresaId, $ids) {
            DB::statement('SET FOREIGN_KEY_CHECKS=0');
            foreach (array_reverse(self::tables(), true) as $tabla => $def) {
                if (! self::existe($tabla)) {
                    continue;
                }
                if ($def['scope'] === 'empresa') {
                    DB::table($tabla)->where('empresa_id', $empresaId)->delete();
                } else {
                    DB::table($tabla)->whereIn($def['scope']['fk'], $ids[$tabla] ?: [0])->delete();
                }
            }
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
        });
    }

    /**
     * Restaura un backup dentro de la empresa indicada. Primero reinicia los
     * datos operativos y luego inserta remapeando los identificadores.
     * Devuelve el conteo de registros restaurados por tabla.
     */
    public static function import(int $empresaId, array $data): array
    {
        $tablas = $data['tablas'] ?? [];
        $map = [];        // map[tabla][idViejo] = idNuevo
        $conteo = [];

        self::reset($empresaId);

        DB::transaction(function () use ($empresaId, $tablas, &$map, &$conteo) {
            DB::statement('SET FOREIGN_KEY_CHECKS=0');
            foreach (self::tables() as $tabla => $def) {
                if (! self::existe($tabla) || empty($tablas[$tabla])) {
                    continue;
                }
                $map[$tabla] = [];
                $n = 0;
                foreach ($tablas[$tabla] as $row) {
                    $row = (array) $row;
                    $viejoId = $row['id'] ?? null;
                    unset($row['id']);

                    if (array_key_exists('empresa_id', $row)) {
                        $row['empresa_id'] = $empresaId;
                    }
                    foreach ($def['remap'] as $col => $ref) {
                        if (! empty($row[$col]) && isset($map[$ref][$row[$col]])) {
                            $row[$col] = $map[$ref][$row[$col]];
                        } elseif (array_key_exists($col, $row)) {
                            $row[$col] = null; // padre no restaurado -> deja nulo
                        }
                    }

                    $nuevoId = DB::table($tabla)->insertGetId($row);
                    if ($viejoId !== null) {
                        $map[$tabla][$viejoId] = $nuevoId;
                    }
                    $n++;
                }
                $conteo[$tabla] = $n;
            }
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
        });

        return $conteo;
    }

    private static function existe(string $tabla): bool
    {
        static $cache = [];
        if (! array_key_exists($tabla, $cache)) {
            $cache[$tabla] = \Illuminate\Support\Facades\Schema::hasTable($tabla);
        }

        return $cache[$tabla];
    }
}
