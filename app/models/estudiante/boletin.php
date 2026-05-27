<?php

/**
 * Modelo: BoletinEstudiante
 * Genera los datos necesarios para los boletines académicos por período.
 *
 * ARQUITECTURA:
 *   - Los períodos vienen de `periodos_academicos` (institucion_id, ano_lectivo, numero_periodo).
 *   - Las actividades se asignan a un período por rango de fechas:
 *       act.fecha_entrega BETWEEN pa.fecha_inicio AND pa.fecha_fin
 *   - El promedio es PONDERADO: SUM(nota×ponderacion) / SUM(ponderacion usada),
 *     donde las actividades vencidas sin entrega cuentan como nota 0.
 *   - La asistencia del período se obtiene filtrando las fechas del calendario
 *     dentro del rango fecha_inicio–fecha_fin del período.
 *   - Todo está aislado por id_institucion (multi-tenant).
 */

require_once __DIR__ . '/../../../config/database.php';

class BoletinEstudiante
{
    private $conexion;

    public function __construct()
    {
        $db = new Conexion();
        $this->conexion = $db->getConexion();
    }

    // ─────────────────────────────────────────────────────────────────────────
    // 1. PERÍODOS DEL AÑO
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Devuelve todos los períodos académicos de la institución para el año dado,
     * ordenados por número de período.
     *
     * @param int $id_institucion
     * @param int $anio
     * @return array  [ { id, nombre, numero_periodo, tipo_periodo, fecha_inicio, fecha_fin, activo, estado }, … ]
     */
    public function obtenerPeriodos(int $id_institucion, int $anio): array
    {
        try {
            $stmt = $this->conexion->prepare(
                "SELECT id, nombre, numero_periodo, tipo_periodo,
                        fecha_inicio, fecha_fin, activo, estado
                 FROM periodos_academicos
                 WHERE institucion_id = :id_institucion
                   AND ano_lectivo    = :anio
                 ORDER BY numero_periodo ASC"
            );
            $stmt->bindParam(':id_institucion', $id_institucion, PDO::PARAM_INT);
            $stmt->bindParam(':anio',           $anio,           PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (PDOException $e) {
            error_log('BoletinEstudiante::obtenerPeriodos -> ' . $e->getMessage());
            return [];
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    // 2. DATOS DEL ESTUDIANTE (cabecera del boletín)
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Devuelve los datos personales y académicos del estudiante para la cabecera del boletín.
     *
     * @param int $id_estudiante
     * @param int $id_institucion
     * @param int $anio
     * @return array|null
     */
    public function obtenerDatosEstudiante(int $id_estudiante, int $id_institucion, int $anio): ?array
    {
        try {
            $stmt = $this->conexion->prepare(
                "SELECT
                     e.id,
                     e.nombres,
                     e.apellidos,
                     e.documento,
                     e.foto,
                     c.grado,
                     c.curso,
                     c.jornada,
                     m.anio,
                     u.correo
                 FROM estudiante e
                 INNER JOIN matricula m ON m.id_estudiante = e.id
                 INNER JOIN curso c     ON m.id_curso      = c.id
                 INNER JOIN usuario u   ON e.id_usuario    = u.id
                 WHERE e.id             = :id_estudiante
                   AND e.id_institucion = :id_institucion
                   AND m.anio           = :anio
                   AND c.estado         = 'Activo'
                 LIMIT 1"
            );
            $stmt->bindParam(':id_estudiante',  $id_estudiante,  PDO::PARAM_INT);
            $stmt->bindParam(':id_institucion', $id_institucion, PDO::PARAM_INT);
            $stmt->bindParam(':anio',           $anio,           PDO::PARAM_INT);
            $stmt->execute();

            $fila = $stmt->fetch(PDO::FETCH_ASSOC);
            return $fila ?: null;
        } catch (PDOException $e) {
            error_log('BoletinEstudiante::obtenerDatosEstudiante -> ' . $e->getMessage());
            return null;
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    // 3. MATERIAS CON PROMEDIO PONDERADO PARA UN PERÍODO
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Devuelve todas las materias del estudiante con sus estadísticas para un período dado.
     *
     * Fórmula del promedio ponderado:
     *   SUM(nota_o_cero × ponderacion) / SUM(ponderacion usada)
     *   donde:
     *     - nota_o_cero  = cal.nota si está calificada; 0 si está vencida sin entrega
     *     - ponderacion usada = ponderacion de actividades calificadas + vencidas (no las pendientes)
     *
     * Solo se consideran actividades cuya fecha_entrega cae dentro del rango del período.
     *
     * @param int $id_estudiante
     * @param int $id_institucion
     * @param int $anio
     * @param int $numero_periodo   1 | 2 | 3 | 4
     * @return array
     */
    public function obtenerMateriasPorPeriodo(
        int $id_estudiante,
        int $id_institucion,
        int $anio,
        int $numero_periodo
    ): array {
        // Usar fecha PHP (timezone America/Bogota).
        $fechaHoy = date('Y-m-d');

        try {
            $sql = "SELECT
                        ac.id    AS id_asignatura_curso,
                        a.nombre AS materia,
                        TRIM(CONCAT(COALESCE(d.nombres,''), ' ', COALESCE(d.apellidos,''))) AS docente_nombre,
                        COUNT(DISTINCT act.id) AS total_actividades,
                        SUM(CASE WHEN cal.nota IS NOT NULL THEN 1 ELSE 0 END) AS actividades_calificadas,
                        SUM(CASE WHEN ea.id IS NULL AND DATE(act.fecha_entrega) < :fecha_venc  THEN 1 ELSE 0 END) AS actividades_vencidas,
                        SUM(CASE WHEN ea.id IS NULL AND DATE(act.fecha_entrega) >= :fecha_pend THEN 1 ELSE 0 END) AS actividades_pendientes,
                        -- Promedio ponderado:
                        --   numerador   = suma(nota×ponderacion) para calificadas + suma(0) para vencidas
                        --   denominador = suma(ponderacion) de calificadas + vencidas (pendientes = 0, no afectan)
                        ROUND(
                            SUM(CASE
                                WHEN cal.nota IS NOT NULL                                              THEN cal.nota * act.ponderacion
                                WHEN ea.id IS NULL AND DATE(act.fecha_entrega) < :fecha_prom_num       THEN 0
                                ELSE NULL
                            END)
                            /
                            NULLIF(
                                SUM(CASE
                                    WHEN cal.nota IS NOT NULL                                          THEN act.ponderacion
                                    WHEN ea.id IS NULL AND DATE(act.fecha_entrega) < :fecha_prom_den   THEN act.ponderacion
                                    ELSE 0
                                END),
                                0
                            ),
                            1
                        ) AS promedio
                    FROM estudiante e
                    INNER JOIN matricula m              ON m.id_estudiante         = e.id
                    INNER JOIN curso c                  ON m.id_curso              = c.id
                    INNER JOIN asignatura_curso ac      ON ac.id_curso             = c.id
                    INNER JOIN asignatura a             ON ac.id_asignatura        = a.id
                    -- El período es el filtro central: si no existe, el query devuelve vacío
                    INNER JOIN periodos_academicos pa   ON pa.institucion_id       = e.id_institucion
                                                       AND pa.ano_lectivo          = m.anio
                                                       AND pa.numero_periodo       = :numero_periodo
                    LEFT JOIN docente_asignatura_curso dac ON dac.id_asignatura_curso = ac.id
                    LEFT JOIN docente d                 ON dac.id_docente           = d.id
                    -- Solo actividades cuya fecha_entrega cae dentro del período
                    LEFT JOIN actividad act             ON act.id_asignatura_curso  = ac.id
                                                       AND DATE(act.fecha_entrega) BETWEEN pa.fecha_inicio AND pa.fecha_fin
                    LEFT JOIN entrega_actividad ea      ON ea.id_actividad          = act.id
                                                       AND ea.id_estudiante         = e.id
                    LEFT JOIN calificacion cal          ON cal.id_actividad         = act.id
                                                       AND cal.id_estudiante        = e.id
                    WHERE e.id             = :id_estudiante
                      AND e.id_institucion = :id_institucion
                      AND m.anio           = :anio
                      AND c.estado         = 'Activo'
                    GROUP BY ac.id, a.nombre, d.id, d.nombres, d.apellidos
                    ORDER BY a.nombre ASC";

            $stmt = $this->conexion->prepare($sql);
            $stmt->bindParam(':id_estudiante',  $id_estudiante,  PDO::PARAM_INT);
            $stmt->bindParam(':id_institucion', $id_institucion, PDO::PARAM_INT);
            $stmt->bindParam(':anio',           $anio,           PDO::PARAM_INT);
            $stmt->bindParam(':numero_periodo', $numero_periodo,  PDO::PARAM_INT);
            $stmt->bindParam(':fecha_venc',     $fechaHoy,        PDO::PARAM_STR);
            $stmt->bindParam(':fecha_pend',     $fechaHoy,        PDO::PARAM_STR);
            $stmt->bindParam(':fecha_prom_num', $fechaHoy,        PDO::PARAM_STR);
            $stmt->bindParam(':fecha_prom_den', $fechaHoy,        PDO::PARAM_STR);
            $stmt->execute();

            $materias = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];

            // Agregar estado textual a cada materia
            foreach ($materias as &$m) {
                $promedio         = $m['promedio'] !== null ? (float)$m['promedio'] : null;
                $m['estado_nota'] = $this->calcularEstadoNota($promedio);
                $m['estado_label']= $this->estadoLabel($m['estado_nota']);
            }

            return $materias;
        } catch (PDOException $e) {
            error_log('BoletinEstudiante::obtenerMateriasPorPeriodo -> ' . $e->getMessage());
            return [];
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    // 4. ASISTENCIA POR PERÍODO
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Devuelve el resumen de asistencia del estudiante para un período dado.
     *
     * La asistencia se filtra por las fechas de la tabla `calendario` que caen
     * dentro del rango fecha_inicio–fecha_fin del período académico.
     *
     * @param int $id_estudiante
     * @param int $id_institucion
     * @param int $anio
     * @param int $numero_periodo
     * @return array { total_registros, presentes, ausentes, justificados, tardes, porcentaje_asistencia }
     */
    public function obtenerAsistenciaPorPeriodo(
        int $id_estudiante,
        int $id_institucion,
        int $anio,
        int $numero_periodo
    ): array {
        $defecto = [
            'total_registros'       => 0,
            'presentes'             => 0,
            'ausentes'              => 0,
            'justificados'          => 0,
            'tardes'                => 0,
            'porcentaje_asistencia' => null,
        ];

        try {
            $stmt = $this->conexion->prepare(
                "SELECT
                     COUNT(ast.id)                                                  AS total_registros,
                     SUM(CASE WHEN ast.estado = 'Presente'    THEN 1 ELSE 0 END)   AS presentes,
                     SUM(CASE WHEN ast.estado = 'Ausente'     THEN 1 ELSE 0 END)   AS ausentes,
                     SUM(CASE WHEN ast.estado = 'Justificado' THEN 1 ELSE 0 END)   AS justificados,
                     SUM(CASE WHEN ast.estado = 'Tarde'       THEN 1 ELSE 0 END)   AS tardes
                 FROM asistencia ast
                 INNER JOIN calendario cal
                         ON cal.id             = ast.id_calendario
                        AND cal.id_institucion = ast.id_institucion
                 INNER JOIN periodos_academicos pa
                         ON pa.institucion_id  = ast.id_institucion
                        AND pa.ano_lectivo     = :anio
                        AND pa.numero_periodo  = :numero_periodo
                        AND cal.fecha BETWEEN pa.fecha_inicio AND pa.fecha_fin
                 WHERE ast.id_estudiante  = :id_estudiante
                   AND ast.id_institucion = :id_institucion"
            );
            $stmt->bindParam(':id_estudiante',  $id_estudiante,  PDO::PARAM_INT);
            $stmt->bindParam(':id_institucion', $id_institucion, PDO::PARAM_INT);
            $stmt->bindParam(':anio',           $anio,           PDO::PARAM_INT);
            $stmt->bindParam(':numero_periodo', $numero_periodo,  PDO::PARAM_INT);
            $stmt->execute();

            $fila = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$fila) {
                return $defecto;
            }

            $total = (int)($fila['total_registros'] ?? 0);

            return [
                'total_registros'       => $total,
                'presentes'             => (int)($fila['presentes']    ?? 0),
                'ausentes'              => (int)($fila['ausentes']     ?? 0),
                'justificados'          => (int)($fila['justificados'] ?? 0),
                'tardes'                => (int)($fila['tardes']       ?? 0),
                'porcentaje_asistencia' => $total > 0
                    ? round(((int)$fila['presentes'] / $total) * 100, 1)
                    : null,
            ];
        } catch (PDOException $e) {
            error_log('BoletinEstudiante::obtenerAsistenciaPorPeriodo -> ' . $e->getMessage());
            return $defecto;
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    // 5. RESUMEN ANUAL — TODOS LOS PERÍODOS
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Devuelve todos los datos necesarios para el boletín completo del año:
     *   - datos del estudiante
     *   - lista de períodos configurados
     *   - por cada período: materias con promedios ponderados + resumen de asistencia
     *
     * El "promedio del período" es el promedio simple de los promedios por materia
     * (cada materia tiene el mismo peso, que es el estándar en boletines colombianos).
     *
     * @param int $id_estudiante
     * @param int $id_institucion
     * @param int $anio
     * @return array { estudiante, periodos, por_periodo }
     */
    public function obtenerResumenAnual(int $id_estudiante, int $id_institucion, int $anio): array
    {
        $periodos   = $this->obtenerPeriodos($id_institucion, $anio);
        $estudiante = $this->obtenerDatosEstudiante($id_estudiante, $id_institucion, $anio);
        $porPeriodo = [];

        foreach ($periodos as $periodo) {
            $numPeriodo = (int)$periodo['numero_periodo'];
            $materias   = $this->obtenerMateriasPorPeriodo($id_estudiante, $id_institucion, $anio, $numPeriodo);
            $asistencia = $this->obtenerAsistenciaPorPeriodo($id_estudiante, $id_institucion, $anio, $numPeriodo);

            // Promedio general del período = promedio de los promedios por materia
            $promediosValidos = array_filter(
                array_column($materias, 'promedio'),
                fn($p) => $p !== null
            );
            $promedioPeriodo = count($promediosValidos) > 0
                ? round(array_sum($promediosValidos) / count($promediosValidos), 1)
                : null;

            $porPeriodo[] = [
                'periodo'          => $periodo,
                'materias'         => $materias,
                'asistencia'       => $asistencia,
                'promedio_periodo' => $promedioPeriodo,
            ];
        }

        return [
            'estudiante'  => $estudiante,
            'periodos'    => $periodos,
            'por_periodo' => $porPeriodo,
        ];
    }

    // ─────────────────────────────────────────────────────────────────────────
    // HELPERS PRIVADOS
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Escala académica definida por la institución:
     *   Superior : 4.5 – 5.0
     *   Alto     : 4.0 – 4.4
     *   Básico   : 3.1 – 3.9
     *   Bajo     : 0.0 – 3.0  (incluye exactamente 3.0)
     */
    private function calcularEstadoNota(?float $promedio): string
    {
        if ($promedio === null) return 'sin-nota';
        if ($promedio >= 4.5)  return 'superior';
        if ($promedio >= 4.0)  return 'alto';
        if ($promedio > 3.0)   return 'basico';
        return 'bajo';
    }

    private function estadoLabel(string $estado): string
    {
        return match ($estado) {
            'superior' => 'Superior',
            'alto'     => 'Alto',
            'basico'   => 'Básico',
            'bajo'     => 'Bajo',
            default    => 'Sin Nota',
        };
    }
}
