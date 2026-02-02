<?php
/**
 * Modelo: Calificación de Entregas (Docente)
 * Gestión de notas y observaciones del docente sobre entregas
 */

require_once BASE_PATH . '/config/database.php';

class CalificacionDocente extends Conexion {
    
    /**
     * Verificar que el docente tiene permiso para calificar esta entrega
     * Valida que la entrega pertenezca a un curso donde el docente imparte la asignatura
     */
    public function verificarPermisoCalificar($id_entrega, $id_docente, $id_institucion) {
        try {
            $sql = "SELECT COUNT(*) as tiene_permiso
                    FROM entrega_actividad ea
                    INNER JOIN actividad a ON ea.id_actividad = a.id
                    INNER JOIN asignatura_curso ac ON a.id_asignatura_curso = ac.id
                    INNER JOIN docente_asignatura da ON da.id_asignatura_curso = ac.id
                    WHERE ea.id = :id_entrega
                      AND da.id_docente = :id_docente
                      AND ea.id_institucion = :id_institucion
                      AND a.id_institucion = :id_institucion2";
            
            $stmt = $this->getConexion()->prepare($sql);
            $stmt->bindParam(':id_entrega', $id_entrega, PDO::PARAM_INT);
            $stmt->bindParam(':id_docente', $id_docente, PDO::PARAM_INT);
            $stmt->bindParam(':id_institucion', $id_institucion, PDO::PARAM_INT);
            $stmt->bindParam(':id_institucion2', $id_institucion, PDO::PARAM_INT);
            $stmt->execute();
            
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            return $resultado['tiene_permiso'] > 0;
            
        } catch (Exception $e) {
            error_log("Error verificando permiso: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Guardar o actualizar calificación
     * Si ya existe una calificación, la actualiza; si no, la crea
     */
    public function guardarCalificacion($id_entrega, $nota, $observacion, $id_docente) {
        try {
            // Verificar si ya existe una calificación
            $sqlCheck = "SELECT id FROM calificacion 
                         WHERE id_entrega = :id_entrega";
            
            $stmtCheck = $this->getConexion()->prepare($sqlCheck);
            $stmtCheck->bindParam(':id_entrega', $id_entrega, PDO::PARAM_INT);
            $stmtCheck->execute();
            
            $existe = $stmtCheck->fetch(PDO::FETCH_ASSOC);
            
            if ($existe) {
                // Actualizar calificación existente
                $sqlUpdate = "UPDATE calificacion 
                             SET nota = :nota,
                                 observacion = :observacion,
                                 id_docente_calificador = :id_docente,
                                 fecha_calificacion = NOW()
                             WHERE id_entrega = :id_entrega";
                
                $stmtUpdate = $this->getConexion()->prepare($sqlUpdate);
                $stmtUpdate->bindParam(':nota', $nota);
                $stmtUpdate->bindParam(':observacion', $observacion, PDO::PARAM_STR);
                $stmtUpdate->bindParam(':id_docente', $id_docente, PDO::PARAM_INT);
                $stmtUpdate->bindParam(':id_entrega', $id_entrega, PDO::PARAM_INT);
                
                return $stmtUpdate->execute();
                
            } else {
                // Crear nueva calificación
                $sqlInsert = "INSERT INTO calificacion 
                             (id_entrega, nota, observacion, id_docente_calificador, fecha_calificacion)
                             VALUES (:id_entrega, :nota, :observacion, :id_docente, NOW())";
                
                $stmtInsert = $this->getConexion()->prepare($sqlInsert);
                $stmtInsert->bindParam(':id_entrega', $id_entrega, PDO::PARAM_INT);
                $stmtInsert->bindParam(':nota', $nota);
                $stmtInsert->bindParam(':observacion', $observacion, PDO::PARAM_STR);
                $stmtInsert->bindParam(':id_docente', $id_docente, PDO::PARAM_INT);
                
                return $stmtInsert->execute();
            }
            
        } catch (Exception $e) {
            error_log("Error guardando calificación: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Obtener calificación de una entrega
     */
    public function obtenerCalificacion($id_entrega) {
        try {
            $sql = "SELECT c.*, 
                           CONCAT(d.nombres, ' ', d.apellidos) as nombre_docente
                    FROM calificacion c
                    LEFT JOIN docente d ON c.id_docente_calificador = d.id
                    WHERE c.id_entrega = :id_entrega";
            
            $stmt = $this->getConexion()->prepare($sql);
            $stmt->bindParam(':id_entrega', $id_entrega, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
            
        } catch (Exception $e) {
            error_log("Error obteniendo calificación: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Obtener estadísticas de calificaciones de una actividad
     */
    public function obtenerEstadisticasActividad($id_actividad, $id_institucion) {
        try {
            $sql = "SELECT 
                        COUNT(*) as total_calificaciones,
                        AVG(c.nota) as promedio_notas,
                        MAX(c.nota) as nota_maxima,
                        MIN(c.nota) as nota_minima
                    FROM calificacion c
                    INNER JOIN entrega_actividad ea ON c.id_entrega = ea.id
                    WHERE ea.id_actividad = :id_actividad
                      AND ea.id_institucion = :id_institucion";
            
            $stmt = $this->getConexion()->prepare($sql);
            $stmt->bindParam(':id_actividad', $id_actividad, PDO::PARAM_INT);
            $stmt->bindParam(':id_institucion', $id_institucion, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
            
        } catch (Exception $e) {
            error_log("Error obteniendo estadísticas: " . $e->getMessage());
            return null;
        }
    }
}
