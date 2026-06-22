<?php

require_once __DIR__ . '/../../../config/database.php';

class ActividadAcudiente
{
    private $pdo;

    public function __construct()
    {
        $db = new Conexion();
        $this->pdo = $db->getConexion();
    }

    /**
     * Todas las actividades del estudiante (todas las materias) con estado de entrega.
     */
    public function obtenerTodasLasActividades(int $id_estudiante, int $id_institucion, int $anio): array
    {
        $fechaHoy = date('Y-m-d');

        try {
            $sql = "SELECT
                        act.id,
                        act.titulo,
                        act.descripcion,
                        act.fecha_entrega,
                        act.tipo,
                        act.ponderacion,
                        act.estado                          AS estado_actividad,
                        asig.nombre                         AS materia,
                        CONCAT(d.nombres,' ',d.apellidos)   AS docente,
                        ea.id                               AS id_entrega,
                        ea.fecha_entrega                    AS fecha_entregada,
                        cal.nota,
                        CASE
                            WHEN cal.nota IS NOT NULL             THEN 'Calificada'
                            WHEN ea.id IS NOT NULL                THEN 'Entregada'
                            WHEN DATE(act.fecha_entrega) < :hoy   THEN 'Vencida'
                            ELSE 'Pendiente'
                        END AS estado_entrega
                    FROM estudiante e
                    INNER JOIN matricula m           ON m.id_estudiante = e.id
                                                    AND m.anio = :anio
                                                    AND m.estado != 'Retirada'
                    INNER JOIN curso c               ON m.id_curso = c.id
                                                    AND c.estado = 'Activo'
                    INNER JOIN asignatura_curso ac   ON ac.id_curso = c.id
                    INNER JOIN actividad act         ON act.id_asignatura_curso = ac.id
                                                    AND act.estado = 'activa'
                    INNER JOIN asignatura asig       ON ac.id_asignatura = asig.id
                    INNER JOIN docente d             ON act.id_docente = d.id
                    LEFT JOIN entrega_actividad ea   ON ea.id_actividad = act.id
                                                    AND ea.id_estudiante = e.id
                    LEFT JOIN calificacion cal       ON cal.id_entrega = ea.id
                    WHERE e.id             = :id_estudiante
                      AND e.id_institucion = :id_institucion
                    ORDER BY act.fecha_entrega DESC";

            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':id_estudiante',  $id_estudiante,  PDO::PARAM_INT);
            $stmt->bindValue(':id_institucion', $id_institucion, PDO::PARAM_INT);
            $stmt->bindValue(':anio',           $anio,           PDO::PARAM_INT);
            $stmt->bindValue(':hoy',            $fechaHoy,       PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('ActividadAcudiente::obtenerTodasLasActividades → ' . $e->getMessage());
            return [];
        }
    }
}
