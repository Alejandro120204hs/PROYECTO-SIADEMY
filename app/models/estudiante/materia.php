<?php

/**
 * MODELO PARA GESTIONAR MATERIAS DEL ESTUDIANTE
 * Maneja consultas relacionadas con las asignaturas, actividades y calificaciones del estudiante
 */

require_once __DIR__ . '/../../../config/database.php';

class MateriaEstudiante
{
    private $conexion;

    public function __construct()
    {
        $db = new Conexion();
        $this->conexion = $db->getConexion();
    }

    /**
     * Obtener todas las materias del estudiante con información del docente y estadísticas
     * 
     * @param int $id_estudiante ID del estudiante
     * @param int $id_institucion ID de la institución
     * @param int $anio Año académico
     * @return array Lista de materias con estadísticas
     */
    public function obtenerMateriasConEstadisticas($id_estudiante, $id_institucion, $anio)
    {
        // Fecha de PHP (timezone America/Bogota configurado en config.php).
        // Las actividades vencidas sin entrega cuentan como nota 0 en el promedio.
        $fechaHoy = date('Y-m-d');

        try {
            $sql = "SELECT
                        a.id AS id_asignatura,
                        a.nombre AS materia,
                        a.descripcion,
                        ac.id AS id_asignatura_curso,
                        c.id AS id_curso,
                        c.grado,
                        c.curso,
                        d.id AS id_docente,
                        d.nombres AS docente_nombres,
                        d.apellidos AS docente_apellidos,
                        CONCAT(LEFT(d.nombres, 1), LEFT(d.apellidos, 1)) AS iniciales_docente,
                        u.correo AS docente_correo,
                        d.foto AS docente_foto,
                        COUNT(DISTINCT act.id) AS total_actividades,
                        -- Pendiente = sin entrega, dentro de plazo (fecha PHP)
                        SUM(CASE WHEN ea.id IS NULL AND DATE(act.fecha_entrega) >= :fecha_pend THEN 1 ELSE 0 END) AS actividades_pendientes,
                        -- Promedio PONDERADO: SUM(nota×ponderacion) / SUM(ponderacion usada)
                        -- Las vencidas sin entrega cuentan como nota 0 con su ponderación completa.
                        -- Las pendientes (dentro de plazo, sin entrega) quedan excluidas del denominador.
                        -- Fórmula canónica usada también en el boletín (BoletinEstudiante::obtenerMateriasPorPeriodo).
                        ROUND(
                            SUM(CASE
                                WHEN cal.nota IS NOT NULL                                                    THEN cal.nota * act.ponderacion
                                WHEN ea.id IS NULL AND DATE(act.fecha_entrega) < :fecha_prom_num             THEN 0
                                ELSE NULL
                            END)
                            /
                            NULLIF(
                                SUM(CASE
                                    WHEN cal.nota IS NOT NULL                                                THEN act.ponderacion
                                    WHEN ea.id IS NULL AND DATE(act.fecha_entrega) < :fecha_prom_den         THEN act.ponderacion
                                    ELSE 0
                                END),
                                0
                            ),
                            1
                        ) AS promedio
                    FROM estudiante e
                    INNER JOIN matricula m ON m.id_estudiante = e.id
                    INNER JOIN curso c ON m.id_curso = c.id
                    INNER JOIN asignatura_curso ac ON ac.id_curso = c.id
                    INNER JOIN asignatura a ON ac.id_asignatura = a.id
                    INNER JOIN docente_asignatura_curso dac ON dac.id_asignatura_curso = ac.id
                    INNER JOIN docente d ON dac.id_docente = d.id
                    INNER JOIN usuario u ON d.id_usuario = u.id
                    LEFT JOIN actividad act ON act.id_asignatura_curso = ac.id
                    LEFT JOIN entrega_actividad ea  ON ea.id_actividad  = act.id AND ea.id_estudiante = e.id
                    LEFT JOIN calificacion cal ON cal.id_actividad = act.id AND cal.id_estudiante = e.id
                    WHERE e.id = :id_estudiante
                    AND e.id_institucion = :id_institucion
                    AND m.anio = :anio
                    AND c.estado = 'Activo'
                    GROUP BY a.id, a.nombre, a.descripcion, ac.id, c.id, c.grado, c.curso,
                             d.id, d.nombres, d.apellidos, u.correo, d.foto
                    ORDER BY a.nombre ASC";

            $stmt = $this->conexion->prepare($sql);
            $stmt->bindParam(':id_estudiante',  $id_estudiante,  PDO::PARAM_INT);
            $stmt->bindParam(':id_institucion', $id_institucion, PDO::PARAM_INT);
            $stmt->bindParam(':anio',           $anio,           PDO::PARAM_INT);
            $stmt->bindParam(':fecha_pend',     $fechaHoy,       PDO::PARAM_STR);
            $stmt->bindParam(':fecha_prom_num', $fechaHoy,       PDO::PARAM_STR);
            $stmt->bindParam(':fecha_prom_den', $fechaHoy,       PDO::PARAM_STR);
            $stmt->execute();

            $materias = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Procesar datos adicionales
            foreach ($materias as &$materia) {
                $materia['estado_nota'] = $this->calcularEstadoNota($materia['promedio']);
                $materia['color_icono'] = $this->asignarColorIcono($materia['id_asignatura']);
                $materia['icono'] = $this->asignarIcono($materia['materia']);
            }

            return $materias;

        } catch (PDOException $e) {
            error_log("Error en obtenerMateriasConEstadisticas: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Obtener estadísticas generales del estudiante
     * 
     * @param int $id_estudiante ID del estudiante
     * @param int $id_institucion ID de la institución
     * @param int $anio Año académico
     * @return array Estadísticas generales
     */
    public function obtenerEstadisticasGenerales($id_estudiante, $id_institucion, $anio)
    {
        // Fecha PHP (America/Bogota). Actividades vencidas sin entrega cuentan como 0.
        $fechaHoy = date('Y-m-d');

        try {
            $sql = "SELECT
                        COUNT(DISTINCT a.id) AS total_materias,
                        -- Promedio PONDERADO: SUM(nota×ponderacion) / SUM(ponderacion usada)
                        -- Las vencidas sin entrega cuentan como nota 0 con su ponderación completa.
                        -- Fórmula canónica idéntica a BoletinEstudiante::obtenerMateriasPorPeriodo.
                        ROUND(
                            SUM(CASE
                                WHEN cal.nota IS NOT NULL                                                THEN cal.nota * act.ponderacion
                                WHEN ea.id IS NULL AND DATE(act.fecha_entrega) < :fecha_prom_gen_num    THEN 0
                                ELSE NULL
                            END)
                            /
                            NULLIF(
                                SUM(CASE
                                    WHEN cal.nota IS NOT NULL                                           THEN act.ponderacion
                                    WHEN ea.id IS NULL AND DATE(act.fecha_entrega) < :fecha_prom_gen_den THEN act.ponderacion
                                    ELSE 0
                                END),
                                0
                            ),
                            1
                        ) AS promedio_general,
                        -- Pendiente = sin entrega, dentro de plazo (fecha PHP)
                        SUM(CASE WHEN ea.id IS NULL AND DATE(act.fecha_entrega) >= :fecha_pend_gen THEN 1 ELSE 0 END) AS actividades_pendientes
                    FROM estudiante e
                    INNER JOIN matricula m ON m.id_estudiante = e.id
                    INNER JOIN curso c ON m.id_curso = c.id
                    INNER JOIN asignatura_curso ac ON ac.id_curso = c.id
                    INNER JOIN asignatura a ON ac.id_asignatura = a.id
                    LEFT JOIN actividad act ON act.id_asignatura_curso = ac.id
                    LEFT JOIN entrega_actividad ea  ON ea.id_actividad  = act.id AND ea.id_estudiante = e.id
                    LEFT JOIN calificacion cal ON cal.id_actividad = act.id AND cal.id_estudiante = e.id
                    WHERE e.id = :id_estudiante
                    AND e.id_institucion = :id_institucion
                    AND m.anio = :anio
                    AND c.estado = 'Activo'";

            $stmt = $this->conexion->prepare($sql);
            $stmt->bindParam(':id_estudiante',       $id_estudiante,  PDO::PARAM_INT);
            $stmt->bindParam(':id_institucion',      $id_institucion, PDO::PARAM_INT);
            $stmt->bindParam(':anio',                $anio,           PDO::PARAM_INT);
            $stmt->bindParam(':fecha_prom_gen_num',  $fechaHoy,       PDO::PARAM_STR);
            $stmt->bindParam(':fecha_prom_gen_den',  $fechaHoy,       PDO::PARAM_STR);
            $stmt->bindParam(':fecha_pend_gen',      $fechaHoy,       PDO::PARAM_STR);
            $stmt->execute();

            $stats = $stmt->fetch(PDO::FETCH_ASSOC);

            // Contar materias con bajo rendimiento (promedio <= 3.0).
            // Incluye vencidas sin entrega como 0, igual que el promedio general.
            // Subquery de materias en riesgo — usa la misma fórmula ponderada canónica.
            $sqlRiesgo = "SELECT COUNT(*) AS en_riesgo
                          FROM (
                              SELECT a.id,
                                     ROUND(
                                         SUM(CASE
                                             WHEN cal.nota IS NOT NULL                                              THEN cal.nota * act.ponderacion
                                             WHEN ea.id IS NULL AND DATE(act.fecha_entrega) < :fecha_riesgo_num    THEN 0
                                             ELSE NULL
                                         END)
                                         /
                                         NULLIF(
                                             SUM(CASE
                                                 WHEN cal.nota IS NOT NULL                                          THEN act.ponderacion
                                                 WHEN ea.id IS NULL AND DATE(act.fecha_entrega) < :fecha_riesgo_den THEN act.ponderacion
                                                 ELSE 0
                                             END),
                                             0
                                         ),
                                         1
                                     ) AS promedio
                              FROM estudiante e
                              INNER JOIN matricula m  ON m.id_estudiante = e.id
                              INNER JOIN curso c      ON m.id_curso = c.id
                              INNER JOIN asignatura_curso ac ON ac.id_curso = c.id
                              INNER JOIN asignatura a  ON ac.id_asignatura = a.id
                              LEFT JOIN actividad act  ON act.id_asignatura_curso = ac.id
                              LEFT JOIN entrega_actividad ea ON ea.id_actividad = act.id AND ea.id_estudiante = e.id
                              LEFT JOIN calificacion cal ON cal.id_actividad = act.id AND cal.id_estudiante = e.id
                              WHERE e.id = :id_estudiante
                              AND e.id_institucion = :id_institucion
                              AND m.anio = :anio
                              AND c.estado = 'Activo'
                              GROUP BY a.id
                              HAVING promedio <= 3.0 AND promedio IS NOT NULL
                          ) AS materias_riesgo";

            $stmtRiesgo = $this->conexion->prepare($sqlRiesgo);
            $stmtRiesgo->bindParam(':id_estudiante',    $id_estudiante,  PDO::PARAM_INT);
            $stmtRiesgo->bindParam(':id_institucion',   $id_institucion, PDO::PARAM_INT);
            $stmtRiesgo->bindParam(':anio',             $anio,           PDO::PARAM_INT);
            $stmtRiesgo->bindParam(':fecha_riesgo_num', $fechaHoy,       PDO::PARAM_STR);
            $stmtRiesgo->bindParam(':fecha_riesgo_den', $fechaHoy,       PDO::PARAM_STR);
            $stmtRiesgo->execute();

            $riesgo = $stmtRiesgo->fetch(PDO::FETCH_ASSOC);
            $stats['en_riesgo'] = $riesgo['en_riesgo'] ?? 0;

            // Valores por defecto
            $stats['total_materias']         = $stats['total_materias']         ?? 0;
            $stats['promedio_general']        = $stats['promedio_general']        ?? 0;
            $stats['actividades_pendientes']  = $stats['actividades_pendientes']  ?? 0;

            return $stats;

        } catch (PDOException $e) {
            error_log("Error en obtenerEstadisticasGenerales: " . $e->getMessage());
            return [
                'total_materias' => 0,
                'promedio_general' => 0,
                'actividades_pendientes' => 0,
                'en_riesgo' => 0
            ];
        }
    }

    /**
     * Obtener actividades próximas a vencer del estudiante
     * 
     * @param int $id_estudiante ID del estudiante
     * @param int $id_institucion ID de la institución
     * @param int $anio Año académico
     * @param int $limite Número máximo de resultados
     * @return array Lista de actividades próximas
     */
    public function obtenerActividadesProximas($id_estudiante, $id_institucion, $anio, $limite = 5)
    {
        // Usar fecha PHP (timezone America/Bogota) en lugar de CURDATE() de MySQL.
        $fechaHoy = date('Y-m-d');

        try {
            $sql = "SELECT
                        act.id,
                        act.titulo,
                        act.descripcion,
                        act.fecha_entrega,
                        act.tipo,
                        a.nombre AS materia,
                        d.nombres AS docente_nombres,
                        d.apellidos AS docente_apellidos,
                        DATEDIFF(act.fecha_entrega, :fecha_prox) AS dias_restantes
                    FROM estudiante e
                    INNER JOIN matricula m ON m.id_estudiante = e.id
                    INNER JOIN curso c ON m.id_curso = c.id
                    INNER JOIN asignatura_curso ac ON ac.id_curso = c.id
                    INNER JOIN actividad act ON act.id_asignatura_curso = ac.id
                    INNER JOIN asignatura a ON ac.id_asignatura = a.id
                    INNER JOIN docente d ON act.id_docente = d.id
                    LEFT JOIN entrega_actividad ea ON ea.id_actividad = act.id AND ea.id_estudiante = e.id
                    WHERE e.id = :id_estudiante
                    AND e.id_institucion = :id_institucion
                    AND m.anio = :anio
                    AND act.estado = 'activa'
                    AND DATE(act.fecha_entrega) >= :fecha_prox2
                    AND ea.id IS NULL
                    AND c.estado = 'Activo'
                    ORDER BY act.fecha_entrega ASC
                    LIMIT :limite";

            $stmt = $this->conexion->prepare($sql);
            $stmt->bindParam(':id_estudiante',  $id_estudiante,  PDO::PARAM_INT);
            $stmt->bindParam(':id_institucion', $id_institucion, PDO::PARAM_INT);
            $stmt->bindParam(':anio',           $anio,           PDO::PARAM_INT);
            $stmt->bindParam(':fecha_prox',     $fechaHoy,       PDO::PARAM_STR);
            $stmt->bindParam(':fecha_prox2',    $fechaHoy,       PDO::PARAM_STR);
            $stmt->bindParam(':limite',         $limite,         PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            error_log("Error en obtenerActividadesProximas: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Obtener resumen general para panel de calificaciones.
     *
     * @param int $id_estudiante ID del estudiante
     * @param int $id_institucion ID de la institución
     * @param int $anio Año académico
     * @return array
     */
    public function obtenerResumenCalificaciones($id_estudiante, $id_institucion, $anio)
    {
        // Usar fecha PHP (timezone America/Bogota).
        // Actividades vencidas sin entrega cuentan como 0 (consistente con el dashboard).
        $fechaHoy = date('Y-m-d');

        try {
            $sql = "SELECT
                        COUNT(DISTINCT a.id) AS total_materias,
                        -- Promedio PONDERADO: SUM(nota×ponderacion) / SUM(ponderacion usada)
                        -- Fórmula canónica idéntica a BoletinEstudiante::obtenerMateriasPorPeriodo.
                        ROUND(
                            SUM(CASE
                                WHEN cal.nota IS NOT NULL                                                  THEN cal.nota * act.ponderacion
                                WHEN ea.id IS NULL AND DATE(act.fecha_entrega) < :fecha_res_prom_num       THEN 0
                                ELSE NULL
                            END)
                            /
                            NULLIF(
                                SUM(CASE
                                    WHEN cal.nota IS NOT NULL                                              THEN act.ponderacion
                                    WHEN ea.id IS NULL AND DATE(act.fecha_entrega) < :fecha_res_prom_den   THEN act.ponderacion
                                    ELSE 0
                                END),
                                0
                            ),
                            1
                        ) AS promedio_general,
                        COUNT(DISTINCT act.id) AS total_evaluaciones,
                        -- Pendiente = sin entrega, dentro de plazo (fecha PHP)
                        SUM(CASE WHEN ea.id IS NULL AND DATE(act.fecha_entrega) >= :fecha_res_pend THEN 1 ELSE 0 END) AS pendientes
                    FROM estudiante e
                    INNER JOIN matricula m ON m.id_estudiante = e.id
                    INNER JOIN curso c ON m.id_curso = c.id
                    INNER JOIN asignatura_curso ac ON ac.id_curso = c.id
                    INNER JOIN asignatura a ON ac.id_asignatura = a.id
                    LEFT JOIN actividad act ON act.id_asignatura_curso = ac.id
                    LEFT JOIN entrega_actividad ea  ON ea.id_actividad  = act.id AND ea.id_estudiante = e.id
                    LEFT JOIN calificacion cal ON cal.id_actividad = act.id AND cal.id_estudiante = e.id
                    WHERE e.id = :id_estudiante
                    AND e.id_institucion = :id_institucion
                    AND m.anio = :anio
                    AND c.estado = 'Activo'";

            $stmt = $this->conexion->prepare($sql);
            $stmt->bindParam(':id_estudiante',      $id_estudiante,  PDO::PARAM_INT);
            $stmt->bindParam(':id_institucion',     $id_institucion, PDO::PARAM_INT);
            $stmt->bindParam(':anio',               $anio,           PDO::PARAM_INT);
            $stmt->bindParam(':fecha_res_prom_num', $fechaHoy,       PDO::PARAM_STR);
            $stmt->bindParam(':fecha_res_prom_den', $fechaHoy,       PDO::PARAM_STR);
            $stmt->bindParam(':fecha_res_pend',     $fechaHoy,       PDO::PARAM_STR);
            $stmt->execute();

            $resumen = $stmt->fetch(PDO::FETCH_ASSOC) ?: [];

            return [
                'total_materias' => (int)($resumen['total_materias'] ?? 0),
                'promedio_general' => (float)($resumen['promedio_general'] ?? 0),
                'total_evaluaciones' => (int)($resumen['total_evaluaciones'] ?? 0),
                'pendientes' => (int)($resumen['pendientes'] ?? 0),
            ];
        } catch (PDOException $e) {
            error_log('Error en obtenerResumenCalificaciones: ' . $e->getMessage());
            return [
                'total_materias' => 0,
                'promedio_general' => 0,
                'total_evaluaciones' => 0,
                'pendientes' => 0,
            ];
        }
    }

    /**
     * Obtener el número del periodo académico activo.
     *
     * @param int $id_institucion ID de la institución
     * @param int $anio Año académico
     * @return int
     */
    public function obtenerPeriodoActualNumero($id_institucion, $anio)
    {
        try {
            $sql = "SELECT numero_periodo
                    FROM periodos_academicos
                    WHERE institucion_id = :id_institucion
                    AND ano_lectivo = :anio
                    AND activo = 1
                    LIMIT 1";

            $stmt = $this->conexion->prepare($sql);
            $stmt->bindParam(':id_institucion', $id_institucion, PDO::PARAM_INT);
            $stmt->bindParam(':anio', $anio, PDO::PARAM_INT);
            $stmt->execute();

            $fila = $stmt->fetch(PDO::FETCH_ASSOC);
            $periodo = (int)($fila['numero_periodo'] ?? 1);

            return ($periodo >= 1 && $periodo <= 4) ? $periodo : 1;
        } catch (PDOException $e) {
            error_log('Error en obtenerPeriodoActualNumero: ' . $e->getMessage());
            return 1;
        }
    }

    /**
     * Obtener evaluaciones del estudiante agrupables por materia y periodo.
     *
     * @param int $id_estudiante ID del estudiante
     * @param int $id_institucion ID de la institución
     * @param int $anio Año académico
     * @return array
     */
    public function obtenerEvaluacionesPorMateriaYPeriodo($id_estudiante, $id_institucion, $anio)
    {
        try {
            $sql = "SELECT
                        ac.id AS id_asignatura_curso,
                        a.nombre AS materia,
                        CONCAT(COALESCE(d.nombres, ''), ' ', COALESCE(d.apellidos, '')) AS docente_nombre,
                        COALESCE(pa.numero_periodo, 1) AS numero_periodo,
                        act.titulo AS evaluacion,
                        act.fecha_entrega,
                        act.ponderacion,
                        cal.nota
                    FROM estudiante e
                    INNER JOIN matricula m ON m.id_estudiante = e.id
                    INNER JOIN curso c ON m.id_curso = c.id
                    INNER JOIN asignatura_curso ac ON ac.id_curso = c.id
                    INNER JOIN asignatura a ON ac.id_asignatura = a.id
                    LEFT JOIN docente_asignatura_curso dac ON dac.id_asignatura_curso = ac.id
                    LEFT JOIN docente d ON dac.id_docente = d.id
                    LEFT JOIN actividad act ON act.id_asignatura_curso = ac.id
                    LEFT JOIN calificacion cal ON cal.id_actividad = act.id AND cal.id_estudiante = e.id
                    LEFT JOIN periodos_academicos pa
                        ON pa.institucion_id = e.id_institucion
                        AND pa.ano_lectivo = m.anio
                        AND act.fecha_entrega BETWEEN pa.fecha_inicio AND pa.fecha_fin
                    WHERE e.id = :id_estudiante
                    AND e.id_institucion = :id_institucion
                    AND m.anio = :anio
                    AND c.estado = 'Activo'
                    ORDER BY a.nombre ASC, numero_periodo ASC, act.fecha_entrega ASC";

            $stmt = $this->conexion->prepare($sql);
            $stmt->bindParam(':id_estudiante', $id_estudiante, PDO::PARAM_INT);
            $stmt->bindParam(':id_institucion', $id_institucion, PDO::PARAM_INT);
            $stmt->bindParam(':anio', $anio, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('Error en obtenerEvaluacionesPorMateriaYPeriodo: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Obtener todas las actividades del estudiante para el calendario del año académico.
     * Incluye actividades pasadas, presentes y futuras — sin filtro de entrega.
     *
     * @param int $id_estudiante  ID del estudiante
     * @param int $id_institucion ID de la institución
     * @param int $anio           Año académico
     * @return array
     */
    public function obtenerActividadesParaCalendario($id_estudiante, $id_institucion, $anio)
    {
        try {
            $sql = "SELECT
                        act.id,
                        act.titulo,
                        COALESCE(act.descripcion, '') AS descripcion,
                        act.fecha_entrega,
                        COALESCE(NULLIF(TRIM(act.tipo), ''), 'Actividad') AS tipo,
                        a.nombre AS materia
                    FROM estudiante e
                    INNER JOIN matricula m    ON m.id_estudiante = e.id
                    INNER JOIN curso c        ON m.id_curso = c.id
                    INNER JOIN asignatura_curso ac ON ac.id_curso = c.id
                    INNER JOIN actividad act  ON act.id_asignatura_curso = ac.id
                    INNER JOIN asignatura a   ON act.id_asignatura = a.id
                    WHERE e.id              = :id_estudiante
                    AND e.id_institucion    = :id_institucion
                    AND m.anio              = :anio
                    AND c.estado            = 'Activo'
                    AND YEAR(act.fecha_entrega) = :anio_cal
                    ORDER BY act.fecha_entrega ASC";

            $stmt = $this->conexion->prepare($sql);
            $stmt->bindParam(':id_estudiante',  $id_estudiante,  PDO::PARAM_INT);
            $stmt->bindParam(':id_institucion', $id_institucion, PDO::PARAM_INT);
            $stmt->bindParam(':anio',           $anio,           PDO::PARAM_INT);
            $stmt->bindParam(':anio_cal',       $anio,           PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            error_log("Error en obtenerActividadesParaCalendario: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Calcular el estado de la nota según el promedio
     *
     * @param float|null $promedio Promedio de la materia
     * @return string Estado de la nota (excelente, bien, riesgo, critico)
     */
    private function calcularEstadoNota($promedio)
    {
        if ($promedio === null) {
            return 'sin-nota';
        }
        $p = (float)$promedio;
        // Escala académica definida:
        //   Superior     : 4.5 – 5.0
        //   Alto         : 4.0 – 4.4
        //   Básico       : 3.1 – 3.9
        //   Bajo         : 0.0 – 3.0  (incluye exactamente 3.0)
        if ($p >= 4.5) {
            return 'superior';
        }
        if ($p >= 4.0) {
            return 'alto';
        }
        if ($p > 3.0) {
            return 'basico';
        }
        return 'bajo';
    }

    /**
     * Asignar color de icono según el ID de la asignatura
     * 
     * @param int $id_asignatura ID de la asignatura
     * @return string Gradiente CSS para el icono
     */
    private function asignarColorIcono($id_asignatura)
    {
        $colores = [
            'linear-gradient(135deg, #f59e0b 0%, #d97706 100%)', // Amarillo/Naranja
            'linear-gradient(135deg, #ef4444 0%, #dc2626 100%)', // Rojo
            'linear-gradient(135deg, #06b6d4 0%, #0891b2 100%)', // Cyan
            'linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%)', // Púrpura
            'linear-gradient(135deg, #10b981 0%, #059669 100%)', // Verde
            'linear-gradient(135deg, #6366f1 0%, #4f46e5 100%)', // Indigo
            'linear-gradient(135deg, #ec4899 0%, #db2777 100%)', // Rosa
            'linear-gradient(135deg, #3b82f6 0%, #2563eb 100%)', // Azul
        ];

        return $colores[$id_asignatura % count($colores)];
    }

    /**
     * Asignar icono según el nombre de la materia
     * 
     * @param string $nombreMateria Nombre de la materia
     * @return string Clase del icono RemixIcon
     */
    private function asignarIcono($nombreMateria)
    {
        $nombre = strtolower($nombreMateria);

        $iconos = [
            'matemat' => 'ri-calculator-line',
            'fisica' => 'ri-flask-line',
            'quimica' => 'ri-test-tube-line',
            'biolog' => 'ri-microscope-line',
            'ingles' => 'ri-english-input',
            'español' => 'ri-book-open-line',
            'lenguaje' => 'ri-book-open-line',
            'historia' => 'ri-book-2-line',
            'geografia' => 'ri-earth-line',
            'sociales' => 'ri-group-line',
            'filosofia' => 'ri-lightbulb-line',
            'educacion fisica' => 'ri-run-line',
            'deporte' => 'ri-basketball-line',
            'arte' => 'ri-palette-line',
            'musica' => 'ri-music-line',
            'tecnologia' => 'ri-computer-line',
            'informatica' => 'ri-code-line',
            'programacion' => 'ri-code-s-slash-line',
            'religion' => 'ri-heart-line',
            'etica' => 'ri-scales-line',
        ];

        foreach ($iconos as $key => $icono) {
            if (strpos($nombre, $key) !== false) {
                return $icono;
            }
        }

        return 'ri-book-line'; // Icono por defecto
    }
}

