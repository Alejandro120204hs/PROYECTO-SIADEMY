<?php

/**
 * MODELO PARA GESTIONAR ACTIVIDADES DEL ESTUDIANTE
 * Maneja consultas relacionadas con las actividades y calificaciones del estudiante
 */

require_once __DIR__ . '/../../../config/database.php';

class ActividadEstudiante
{
    private $conexion;

    public function __construct()
    {
        $db = new Conexion();
        $this->conexion = $db->getConexion();
    }

    /**
     * Obtener todas las actividades de una materia específica del estudiante
     * 
     * @param int $id_estudiante ID del estudiante
     * @param int $id_asignatura_curso ID de la asignatura_curso
     * @param int $id_institucion ID de la institución
     * @return array Lista de actividades con sus calificaciones
     */
    public function obtenerActividadesPorMateria($id_estudiante, $id_asignatura_curso, $id_institucion)
    {
        try {
            $sql = "SELECT 
                        a.id AS id_actividad,
                        a.titulo,
                        a.descripcion,
                        a.tipo,
                        a.ponderacion,
                        a.fecha_entrega,
                        a.estado AS estado_actividad,
                        asig.nombre AS nombre_asignatura,
                        asig.descripcion AS descripcion_asignatura,
                        c.grado,
                        c.curso AS nombre_curso,
                        CONCAT(d.nombres, ' ', d.apellidos) AS nombre_docente,
                        d.nombres AS docente_nombres,
                        d.apellidos AS docente_apellidos,
                        u.correo AS docente_correo,
                        cal.id AS id_calificacion,
                        cal.nota,
                        cal.observacion,
                        cal.fecha_registro AS fecha_calificacion,
                        CASE 
                            WHEN cal.id IS NOT NULL THEN 'Calificada'
                            WHEN a.fecha_entrega < CURDATE() AND a.estado = 'activa' THEN 'Vencida'
                            WHEN a.estado = 'activa' THEN 'Pendiente'
                            ELSE 'Cerrada'
                        END AS estado_entrega,
                        DATEDIFF(a.fecha_entrega, CURDATE()) AS dias_restantes
                    FROM actividad a
                    INNER JOIN asignatura_curso ac ON a.id_asignatura_curso = ac.id
                    INNER JOIN asignatura asig ON ac.id_asignatura = asig.id
                    INNER JOIN curso c ON ac.id_curso = c.id
                    INNER JOIN docente d ON a.id_docente = d.id
                    INNER JOIN usuario u ON d.id_usuario = u.id
                    INNER JOIN matricula m ON m.id_curso = c.id
                    INNER JOIN estudiante e ON m.id_estudiante = e.id
                    LEFT JOIN calificacion cal ON cal.id_actividad = a.id AND cal.id_estudiante = e.id
                    WHERE 
                        e.id = :id_estudiante
                        AND ac.id = :id_asignatura_curso
                        AND m.anio = YEAR(CURDATE())
                        AND a.id_institucion = :id_institucion
                    ORDER BY 
                        CASE 
                            WHEN cal.id IS NULL AND a.estado = 'activa' AND a.fecha_entrega >= CURDATE() THEN 1
                            WHEN cal.id IS NULL AND a.estado = 'activa' AND a.fecha_entrega < CURDATE() THEN 2
                            WHEN cal.id IS NOT NULL THEN 3
                            ELSE 4
                        END,
                        a.fecha_entrega DESC";
            
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindParam(':id_estudiante', $id_estudiante, PDO::PARAM_INT);
            $stmt->bindParam(':id_asignatura_curso', $id_asignatura_curso, PDO::PARAM_INT);
            $stmt->bindParam(':id_institucion', $id_institucion, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en obtenerActividadesPorMateria: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Obtener información de una materia específica con estadísticas de actividades
     * 
     * @param int $id_estudiante ID del estudiante
     * @param int $id_asignatura_curso ID de la asignatura_curso
     * @param int $id_institucion ID de la institución
     * @return array|null Información de la materia
     */
    public function obtenerInfoMateriaConEstadisticas($id_estudiante, $id_asignatura_curso, $id_institucion)
    {
        try {
            $sql = "SELECT 
                        a.id AS id_asignatura,
                        a.nombre AS materia,
                        a.descripcion,
                        ac.id AS id_asignatura_curso,
                        c.id AS id_curso,
                        c.grado,
                        c.curso,
                        d.nombres AS docente_nombres,
                        d.apellidos AS docente_apellidos,
                        u.correo AS docente_correo,
                        d.foto AS docente_foto,
                        COUNT(DISTINCT act.id) AS total_actividades,
                        SUM(CASE WHEN act.estado = 'activa' AND act.fecha_entrega >= CURDATE() AND cal.id IS NULL THEN 1 ELSE 0 END) AS actividades_pendientes,
                        SUM(CASE WHEN act.estado = 'activa' AND act.fecha_entrega < CURDATE() AND cal.id IS NULL THEN 1 ELSE 0 END) AS actividades_vencidas,
                        SUM(CASE WHEN cal.id IS NOT NULL THEN 1 ELSE 0 END) AS actividades_completadas,
                        ROUND(AVG(cal.nota), 1) AS promedio
                    FROM estudiante e
                    INNER JOIN matricula m ON m.id_estudiante = e.id
                    INNER JOIN curso c ON m.id_curso = c.id
                    INNER JOIN asignatura_curso ac ON ac.id_curso = c.id
                    INNER JOIN asignatura a ON ac.id_asignatura = a.id
                    LEFT JOIN docente_asignatura_curso dac ON dac.id_asignatura_curso = ac.id
                    LEFT JOIN docente d ON dac.id_docente = d.id
                    LEFT JOIN usuario u ON d.id_usuario = u.id
                    LEFT JOIN actividad act ON act.id_asignatura_curso = ac.id
                    LEFT JOIN calificacion cal ON cal.id_actividad = act.id AND cal.id_estudiante = e.id
                    WHERE 
                        e.id = :id_estudiante
                        AND ac.id = :id_asignatura_curso
                        AND m.anio = YEAR(CURDATE())
                        AND e.id_institucion = :id_institucion
                    GROUP BY a.id, ac.id, c.id, d.id
                    LIMIT 1";
            
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindParam(':id_estudiante', $id_estudiante, PDO::PARAM_INT);
            $stmt->bindParam(':id_asignatura_curso', $id_asignatura_curso, PDO::PARAM_INT);
            $stmt->bindParam(':id_institucion', $id_institucion, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en obtenerInfoMateriaConEstadisticas: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Obtener detalle de una actividad específica
     * 
     * @param int $id_actividad ID de la actividad
     * @param int $id_estudiante ID del estudiante
     * @return array|null Información detallada de la actividad
     */
    public function obtenerDetalleActividad($id_actividad, $id_estudiante)
    {
        try {
            $sql = "SELECT 
                        a.*,
                        asig.nombre AS nombre_asignatura,
                        CONCAT(d.nombres, ' ', d.apellidos) AS nombre_docente,
                        u.correo AS docente_correo,
                        cal.id AS id_calificacion,
                        cal.nota,
                        cal.observacion,
                        cal.fecha_registro AS fecha_calificacion
                    FROM actividad a
                    INNER JOIN asignatura_curso ac ON a.id_asignatura_curso = ac.id
                    INNER JOIN asignatura asig ON ac.id_asignatura = asig.id
                    INNER JOIN docente d ON a.id_docente = d.id
                    INNER JOIN usuario u ON d.id_usuario = u.id
                    LEFT JOIN calificacion cal ON cal.id_actividad = a.id AND cal.id_estudiante = :id_estudiante
                    WHERE a.id = :id_actividad
                    LIMIT 1";
            
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindParam(':id_actividad', $id_actividad, PDO::PARAM_INT);
            $stmt->bindParam(':id_estudiante', $id_estudiante, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en obtenerDetalleActividad: " . $e->getMessage());
            return null;
        }
    }
}

?>
