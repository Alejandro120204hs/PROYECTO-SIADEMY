<?php
require_once __DIR__ . '/../../../config/database.php';

class EntregaEstudiante {
    private $conn;

    public function __construct() {
        $database = new Conexion();
        $this->conn = $database->getConexion();
    }

    /**
     * Verificar si ya existe una entrega para una actividad
     */
    public function verificarEntregaExistente($id_actividad, $id_estudiante) {
        try {
            $sql = "SELECT id, archivo_ruta, fecha_entrega, estado 
                    FROM entrega_actividad 
                    WHERE id_actividad = :id_actividad 
                      AND id_estudiante = :id_estudiante";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':id_actividad', $id_actividad, PDO::PARAM_INT);
            $stmt->bindParam(':id_estudiante', $id_estudiante, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al verificar entrega: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Crear una nueva entrega
     */
    public function crear($datos) {
        try {
            $sql = "INSERT INTO entrega_actividad 
                    (id_actividad, id_estudiante, archivo_ruta, observaciones_estudiante, estado) 
                    VALUES 
                    (:id_actividad, :id_estudiante, :archivo_ruta, :observaciones_estudiante, 'Entregado')";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':id_actividad', $datos['id_actividad'], PDO::PARAM_INT);
            $stmt->bindParam(':id_estudiante', $datos['id_estudiante'], PDO::PARAM_INT);
            $stmt->bindParam(':archivo_ruta', $datos['archivo_ruta'], PDO::PARAM_STR);
            $stmt->bindParam(':observaciones_estudiante', $datos['observaciones_estudiante'], PDO::PARAM_STR);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error al crear entrega: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Actualizar una entrega existente (re-entrega)
     */
    public function actualizar($id_actividad, $id_estudiante, $datos) {
        try {
            $sql = "UPDATE entrega_actividad 
                    SET archivo_ruta = :archivo_ruta,
                        observaciones_estudiante = :observaciones_estudiante,
                        fecha_entrega = CURRENT_TIMESTAMP,
                        estado = 'Entregado'
                    WHERE id_actividad = :id_actividad 
                      AND id_estudiante = :id_estudiante";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':archivo_ruta', $datos['archivo_ruta'], PDO::PARAM_STR);
            $stmt->bindParam(':observaciones_estudiante', $datos['observaciones_estudiante'], PDO::PARAM_STR);
            $stmt->bindParam(':id_actividad', $id_actividad, PDO::PARAM_INT);
            $stmt->bindParam(':id_estudiante', $id_estudiante, PDO::PARAM_INT);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error al actualizar entrega: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Obtener detalles de la entrega de un estudiante para una actividad
     */
    public function obtenerDetalleEntrega($id_actividad, $id_estudiante) {
        try {
            $sql = "SELECT 
                        ea.id,
                        ea.archivo_ruta,
                        ea.fecha_entrega,
                        ea.observaciones_estudiante,
                        ea.estado,
                        a.titulo AS actividad_titulo,
                        a.tipo AS actividad_tipo,
                        a.fecha_entrega AS fecha_limite,
                        CASE 
                            WHEN ea.fecha_entrega <= a.fecha_entrega THEN 'A tiempo'
                            ELSE 'Tarde'
                        END AS puntualidad
                    FROM entrega_actividad ea
                    INNER JOIN actividad a ON ea.id_actividad = a.id
                    WHERE ea.id_estudiante = :id_estudiante
                      AND ea.id_actividad = :id_actividad
                    ORDER BY ea.fecha_entrega DESC
                    LIMIT 1";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':id_estudiante', $id_estudiante, PDO::PARAM_INT);
            $stmt->bindParam(':id_actividad', $id_actividad, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al obtener detalle de entrega: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Obtener información de actividad con datos del curso y asignatura para crear carpetas
     */
    public function obtenerInfoActividad($id_actividad) {
        try {
            $sql = "SELECT 
                        a.id,
                        a.titulo,
                        a.id_asignatura,
                        a.id_asignatura_curso,
                        a.id_institucion,
                        ac.id_curso,
                        c.id AS curso_id,
                        c.grado,
                        c.curso AS nombre_curso,
                        asig.id AS asignatura_id,
                        asig.nombre AS nombre_asignatura,
                        inst.nombre AS nombre_institucion
                    FROM actividad a
                    INNER JOIN asignatura_curso ac ON a.id_asignatura_curso = ac.id
                    INNER JOIN curso c ON ac.id_curso = c.id
                    INNER JOIN asignatura asig ON a.id_asignatura = asig.id
                    INNER JOIN institucion inst ON a.id_institucion = inst.id
                    WHERE a.id = :id_actividad";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':id_actividad', $id_actividad, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al obtener info de actividad: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Obtener información del estudiante para carpetas
     */
    public function obtenerInfoEstudiante($id_estudiante) {
        try {
            $sql = "SELECT 
                        id,
                        nombres,
                        apellidos,
                        documento
                    FROM estudiante
                    WHERE id = :id_estudiante";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':id_estudiante', $id_estudiante, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al obtener info de estudiante: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Eliminar una entrega
     */
    public function eliminar($id, $id_estudiante) {
        try {
            // Primero obtenemos la ruta del archivo para eliminarlo físicamente
            $sql_select = "SELECT archivo_ruta 
                          FROM entrega_actividad 
                          WHERE id = :id 
                            AND id_estudiante = :id_estudiante";
            
            $stmt = $this->conn->prepare($sql_select);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':id_estudiante', $id_estudiante, PDO::PARAM_INT);
            $stmt->execute();
            
            $entrega = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($entrega) {
                // Eliminar registro de la base de datos
                $sql_delete = "DELETE FROM entrega_actividad 
                              WHERE id = :id 
                                AND id_estudiante = :id_estudiante";
                
                $stmt = $this->conn->prepare($sql_delete);
                $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                $stmt->bindParam(':id_estudiante', $id_estudiante, PDO::PARAM_INT);
                
                if ($stmt->execute()) {
                    return $entrega['archivo_ruta']; // Retornamos la ruta para eliminar el archivo
                }
            }
            
            return false;
        } catch (PDOException $e) {
            error_log("Error al eliminar entrega: " . $e->getMessage());
            return false;
        }
    }
}
