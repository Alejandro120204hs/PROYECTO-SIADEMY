<?php
require_once __DIR__ . '/../../../config/database.php';

class EntregaDocente {
    private $conn;

    public function __construct() {
        $database = new Conexion();
        $this->conn = $database->getConexion();
    }

    /**
     * Obtener información completa de una actividad
     */
    public function obtenerInfoActividad($id_actividad, $id_institucion) {
        try {
            $sql = "SELECT 
                        a.id,
                        a.titulo,
                        a.descripcion,
                        a.tipo,
                        a.ponderacion,
                        a.fecha_entrega,
                        a.estado,
                        asig.nombre AS nombre_asignatura,
                        c.grado,
                        c.curso AS nombre_curso,
                        CONCAT(d.nombres, ' ', d.apellidos) AS nombre_docente
                    FROM actividad a
                    INNER JOIN asignatura_curso ac ON a.id_asignatura_curso = ac.id
                    INNER JOIN asignatura asig ON a.id_asignatura = asig.id
                    INNER JOIN curso c ON ac.id_curso = c.id
                    LEFT JOIN docente d ON a.id_docente = d.id
                    WHERE a.id = :id_actividad 
                      AND a.id_institucion = :id_institucion";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':id_actividad', $id_actividad, PDO::PARAM_INT);
            $stmt->bindParam(':id_institucion', $id_institucion, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al obtener info de actividad: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Obtener lista de estudiantes matriculados en el curso con estado de entrega
     */
    public function obtenerEstudiantesConEntregas($id_actividad, $id_institucion) {
        try {
            $sql = "SELECT 
                        e.id AS id_estudiante,
                        e.nombres,
                        e.apellidos,
                        e.documento,
                        e.foto,
                        ea.id AS id_entrega,
                        ea.archivo_ruta,
                        ea.fecha_entrega AS fecha_entrega_archivo,
                        ea.observaciones_estudiante,
                        ea.estado AS estado_entrega,
                        cal.nota,
                        cal.observacion AS observacion_docente,
                        a.fecha_entrega AS fecha_limite,
                        CASE 
                            WHEN ea.id IS NULL THEN 'Pendiente'
                            WHEN ea.fecha_entrega <= a.fecha_entrega THEN 'A tiempo'
                            ELSE 'Tarde'
                        END AS puntualidad,
                        CASE
                            WHEN ea.id IS NULL AND CURDATE() > a.fecha_entrega THEN 'Atrasado'
                            WHEN ea.id IS NULL THEN 'Pendiente'
                            WHEN cal.nota IS NOT NULL THEN 'Calificado'
                            ELSE 'Entregado'
                        END AS estado_general
                    FROM actividad a
                    INNER JOIN asignatura_curso ac ON a.id_asignatura_curso = ac.id
                    INNER JOIN matricula m ON ac.id_curso = m.id_curso
                    INNER JOIN estudiante e ON m.id_estudiante = e.id
                    LEFT JOIN entrega_actividad ea ON a.id = ea.id_actividad AND e.id = ea.id_estudiante
                    LEFT JOIN calificacion cal ON a.id = cal.id_actividad AND e.id = cal.id_estudiante
                    WHERE a.id = :id_actividad
                      AND a.id_institucion = :id_institucion
                    ORDER BY e.apellidos ASC, e.nombres ASC";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':id_actividad', $id_actividad, PDO::PARAM_INT);
            $stmt->bindParam(':id_institucion', $id_institucion, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al obtener estudiantes con entregas: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Obtener estadísticas de entregas para una actividad
     */
    public function obtenerEstadisticasEntregas($id_actividad, $id_institucion) {
        try {
            $sql = "SELECT 
                        COUNT(DISTINCT e.id) AS total_estudiantes,
                        COUNT(DISTINCT ea.id) AS total_entregas,
                        COUNT(DISTINCT CASE WHEN cal.nota IS NOT NULL THEN e.id END) AS total_calificadas,
                        COUNT(DISTINCT CASE WHEN ea.id IS NULL THEN e.id END) AS total_pendientes,
                        COUNT(DISTINCT CASE WHEN ea.id IS NULL AND CURDATE() > a.fecha_entrega THEN e.id END) AS total_atrasadas,
                        ROUND(AVG(cal.nota), 2) AS promedio_notas
                    FROM actividad a
                    INNER JOIN asignatura_curso ac ON a.id_asignatura_curso = ac.id
                    INNER JOIN matricula m ON ac.id_curso = m.id_curso
                    INNER JOIN estudiante e ON m.id_estudiante = e.id
                    LEFT JOIN entrega_actividad ea ON a.id = ea.id_actividad AND e.id = ea.id_estudiante
                    LEFT JOIN calificacion cal ON a.id = cal.id_actividad AND e.id = cal.id_estudiante
                    WHERE a.id = :id_actividad
                      AND a.id_institucion = :id_institucion";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':id_actividad', $id_actividad, PDO::PARAM_INT);
            $stmt->bindParam(':id_institucion', $id_institucion, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al obtener estadísticas: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Descargar información de archivo de entrega
     */
    public function obtenerArchivoEntrega($id_entrega, $id_estudiante, $id_institucion) {
        try {
            $sql = "SELECT 
                        ea.archivo_ruta,
                        a.titulo AS titulo_actividad,
                        CONCAT(e.nombres, ' ', e.apellidos) AS nombre_estudiante
                    FROM entrega_actividad ea
                    INNER JOIN actividad a ON ea.id_actividad = a.id
                    INNER JOIN estudiante e ON ea.id_estudiante = e.id
                    WHERE ea.id = :id_entrega
                      AND ea.id_estudiante = :id_estudiante
                      AND a.id_institucion = :id_institucion";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':id_entrega', $id_entrega, PDO::PARAM_INT);
            $stmt->bindParam(':id_estudiante', $id_estudiante, PDO::PARAM_INT);
            $stmt->bindParam(':id_institucion', $id_institucion, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al obtener archivo: " . $e->getMessage());
            return false;
        }
    }
}
