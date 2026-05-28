<?php

/**
 * Modelo: ProfesorEstudiante
 * Obtiene los docentes que enseñan al estudiante, con su materia,
 * promedio ponderado del estudiante y porcentaje de asistencia.
 */

require_once __DIR__ . '/../../../config/database.php';

class ProfesorEstudiante
{
    private $conexion;

    public function __construct()
    {
        $db = new Conexion();
        $this->conexion = $db->getConexion();
    }

    /**
     * Obtener todos los docentes vinculados al estudiante este año,
     * junto con estadísticas por materia.
     *
     * @param int $id_estudiante  ID de la tabla `estudiante`
     * @param int $id_institucion ID de la institución (aislamiento multi-tenant)
     * @param int $anio           Año académico
     * @return array
     */
    public function obtenerProfesoresPorEstudiante(int $id_estudiante, int $id_institucion, int $anio): array
    {
        $fechaHoy = date('Y-m-d');

        try {
            $sql = "SELECT
                        d.id                                          AS id_docente,
                        d.nombres,
                        d.apellidos,
                        d.foto,
                        u.correo,
                        asig.id                                       AS id_asignatura,
                        asig.nombre                                   AS nombre_asignatura,
                        asig.descripcion                              AS descripcion_asignatura,
                        ac.id                                         AS id_asignatura_curso,

                        -- Promedio ponderado del estudiante en esta materia
                        -- Vencidas sin entrega = nota 0 (fórmula canónica)
                        ROUND(
                            SUM(CASE
                                WHEN cal.nota IS NOT NULL
                                    THEN cal.nota * act.ponderacion
                                WHEN ea.id IS NULL AND DATE(act.fecha_entrega) < :fecha_num
                                    THEN 0
                                ELSE NULL
                            END)
                            /
                            NULLIF(
                                SUM(CASE
                                    WHEN cal.nota IS NOT NULL
                                        THEN act.ponderacion
                                    WHEN ea.id IS NULL AND DATE(act.fecha_entrega) < :fecha_den
                                        THEN act.ponderacion
                                    ELSE 0
                                END),
                                0
                            ),
                            1
                        ) AS promedio_estudiante,

                        -- Total actividades calificadas
                        COUNT(DISTINCT CASE WHEN cal.nota IS NOT NULL THEN cal.id END) AS total_calificadas,

                        -- Asistencia: porcentaje de clases 'Presente' o 'Tarde'
                        ROUND(
                            SUM(CASE WHEN asist.estado IN ('Presente','Tarde') THEN 1 ELSE 0 END)
                            * 100.0
                            / NULLIF(COUNT(DISTINCT asist.id), 0),
                            0
                        ) AS porcentaje_asistencia,

                        COUNT(DISTINCT asist.id) AS total_clases

                    FROM docente d
                    INNER JOIN usuario u
                           ON d.id_usuario = u.id
                    INNER JOIN docente_asignatura_curso dac
                           ON dac.id_docente = d.id
                           AND dac.id_institucion = :id_institucion
                    INNER JOIN asignatura_curso ac
                           ON dac.id_asignatura_curso = ac.id
                    INNER JOIN asignatura asig
                           ON ac.id_asignatura = asig.id
                    INNER JOIN curso c
                           ON ac.id_curso = c.id
                    INNER JOIN matricula m
                           ON m.id_curso = c.id
                           AND m.id_estudiante = :id_estudiante
                           AND m.anio = :anio
                           AND m.estado != 'Retirada'
                    INNER JOIN estudiante e
                           ON m.id_estudiante = e.id
                           AND e.id_institucion = :id_institucion2

                    -- Actividades de esta materia impartidas por este docente
                    LEFT JOIN actividad act
                           ON act.id_asignatura_curso = ac.id
                           AND act.id_docente = d.id

                    -- Entregas del estudiante
                    LEFT JOIN entrega_actividad ea
                           ON ea.id_actividad = act.id
                           AND ea.id_estudiante = e.id

                    -- Calificaciones
                    LEFT JOIN calificacion cal
                           ON cal.id_entrega = ea.id

                    -- Asistencia por asignatura
                    LEFT JOIN asistencia asist
                           ON asist.id_estudiante = e.id
                           AND asist.id_asignatura = asig.id
                           AND asist.id_institucion = :id_institucion3

                    GROUP BY d.id, asig.id, ac.id
                    ORDER BY asig.nombre ASC";

            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(':id_estudiante',  $id_estudiante,  PDO::PARAM_INT);
            $stmt->bindValue(':id_institucion',  $id_institucion, PDO::PARAM_INT);
            $stmt->bindValue(':id_institucion2', $id_institucion, PDO::PARAM_INT);
            $stmt->bindValue(':id_institucion3', $id_institucion, PDO::PARAM_INT);
            $stmt->bindValue(':anio',            $anio,           PDO::PARAM_INT);
            $stmt->bindValue(':fecha_num',       $fechaHoy,       PDO::PARAM_STR);
            $stmt->bindValue(':fecha_den',       $fechaHoy,       PDO::PARAM_STR);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            error_log('Error en ProfesorEstudiante::obtenerProfesoresPorEstudiante -> ' . $e->getMessage());
            return [];
        }
    }
}
