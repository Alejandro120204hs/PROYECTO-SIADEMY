<?php

// MODELO PARA GESTIONAR ACTIVIDADES DEL DOCENTE
require_once __DIR__ . '/../../../config/database.php';

class Actividad_docente {
    private $conexion;

    public function __construct() {
        $db = new Conexion();
        $this->conexion = $db->getConexion();
    }

    /**
     * Crear una nueva actividad
     */
    public function crear($datos) {
        try {
            $sql = "INSERT INTO actividad (
                        id_institucion,
                        id_docente,
                        id_asignatura,
                        id_asignatura_curso,
                        titulo,
                        descripcion,
                        tipo,
                        ponderacion,
                        fecha_entrega,
                        estado
                    ) VALUES (
                        :id_institucion,
                        :id_docente,
                        :id_asignatura,
                        :id_asignatura_curso,
                        :titulo,
                        :descripcion,
                        :tipo,
                        :ponderacion,
                        :fecha_entrega,
                        'activa'
                    )";
            
            $stmt = $this->conexion->prepare($sql);
            
            $stmt->bindParam(':id_institucion', $datos['id_institucion'], PDO::PARAM_INT);
            $stmt->bindParam(':id_docente', $datos['id_docente'], PDO::PARAM_INT);
            $stmt->bindParam(':id_asignatura', $datos['id_asignatura'], PDO::PARAM_INT);
            $stmt->bindParam(':id_asignatura_curso', $datos['id_asignatura_curso'], PDO::PARAM_INT);
            $stmt->bindParam(':titulo', $datos['titulo'], PDO::PARAM_STR);
            $stmt->bindParam(':descripcion', $datos['descripcion'], PDO::PARAM_STR);
            $stmt->bindParam(':tipo', $datos['tipo'], PDO::PARAM_STR);
            $stmt->bindParam(':ponderacion', $datos['ponderacion']);
            $stmt->bindParam(':fecha_entrega', $datos['fecha_entrega'], PDO::PARAM_STR);
            
            $resultado = $stmt->execute();
            
            if ($resultado) {
                return [
                    'success' => true,
                    'message' => 'Actividad creada exitosamente',
                    'id' => $this->conexion->lastInsertId()
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Error al crear la actividad'
                ];
            }
            
        } catch(PDOException $e) {
            return [
                'success' => false,
                'message' => 'Error en la base de datos: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Listar actividades por curso y docente
     */
    public function listarPorCurso($id_curso, $id_docente, $id_institucion) {
        try {
            $sql = "SELECT a.*, 
                           c.grado,
                           c.curso,
                           asig.nombre as nombre_asignatura
                    FROM actividad a
                    INNER JOIN asignatura_curso ac ON a.id_asignatura_curso = ac.id
                    INNER JOIN curso c ON ac.id_curso = c.id
                    INNER JOIN asignatura asig ON a.id_asignatura = asig.id
                    WHERE ac.id_curso = :id_curso 
                    AND a.id_docente = :id_docente
                    AND a.id_institucion = :id_institucion
                    ORDER BY a.fecha_entrega DESC";
            
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindParam(':id_curso', $id_curso, PDO::PARAM_INT);
            $stmt->bindParam(':id_docente', $id_docente, PDO::PARAM_INT);
            $stmt->bindParam(':id_institucion', $id_institucion, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch(PDOException $e) {
            error_log("Error en listarPorCurso: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Obtener una actividad por ID
     */
    public function obtenerPorId($id) {
        try {
            $sql = "SELECT a.*, 
                           c.grado,
                           c.curso,
                           asig.nombre as nombre_asignatura
                    FROM actividad a
                    INNER JOIN asignatura_curso ac ON a.id_asignatura_curso = ac.id
                    INNER JOIN curso c ON ac.id_curso = c.id
                    INNER JOIN asignatura asig ON a.id_asignatura = asig.id
                    WHERE a.id = :id";
            
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
            
        } catch(PDOException $e) {
            error_log("Error en obtenerPorId: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Actualizar una actividad
     */
    public function actualizar($id, $datos) {
        try {
            $sql = "UPDATE actividad SET
                        titulo = :titulo,
                        descripcion = :descripcion,
                        tipo = :tipo,
                        ponderacion = :ponderacion,
                        fecha_entrega = :fecha_entrega,
                        estado = :estado
                    WHERE id = :id";
            
            $stmt = $this->conexion->prepare($sql);
            
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':titulo', $datos['titulo'], PDO::PARAM_STR);
            $stmt->bindParam(':descripcion', $datos['descripcion'], PDO::PARAM_STR);
            $stmt->bindParam(':tipo', $datos['tipo'], PDO::PARAM_STR);
            $stmt->bindParam(':ponderacion', $datos['ponderacion']);
            $stmt->bindParam(':fecha_entrega', $datos['fecha_entrega'], PDO::PARAM_STR);
            $stmt->bindParam(':estado', $datos['estado'], PDO::PARAM_STR);
            
            return $stmt->execute();
            
        } catch(PDOException $e) {
            error_log("Error en actualizar: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Eliminar una actividad
     */
    public function eliminar($id) {
        try {
            $sql = "DELETE FROM actividad WHERE id = :id";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            
            return $stmt->execute();
            
        } catch(PDOException $e) {
            error_log("Error en eliminar: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Cambiar estado de la actividad
     */
    public function cambiarEstado($id, $estado) {
        try {
            $sql = "UPDATE actividad SET estado = :estado WHERE id = :id";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':estado', $estado, PDO::PARAM_STR);
            
            return $stmt->execute();
            
        } catch(PDOException $e) {
            error_log("Error en cambiarEstado: " . $e->getMessage());
            return false;
        }
    }
}

?>
