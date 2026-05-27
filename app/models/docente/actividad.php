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
                        estado,
                        archivo
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
                        'activa',
                        :archivo
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
            $archivoVal = $datos['archivo'] ?? null;
            $stmt->bindParam(':archivo', $archivoVal, PDO::PARAM_STR);
            
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
                           c.id  AS id_curso,
                           c.grado,
                           c.curso,
                           asig.nombre AS nombre_asignatura,
                           -- Total estudiantes matriculados en el curso (año actual)
                           (SELECT COUNT(DISTINCT m2.id_estudiante)
                            FROM matricula m2
                            WHERE m2.id_curso = c.id
                              AND m2.anio = YEAR(CURDATE())) AS total_matriculados,
                           -- Total entregas recibidas para esta actividad
                           (SELECT COUNT(DISTINCT ea2.id_estudiante)
                            FROM entrega_actividad ea2
                            WHERE ea2.id_actividad = a.id) AS total_entregas
                    FROM actividad a
                    INNER JOIN asignatura_curso ac ON a.id_asignatura_curso = ac.id
                    INNER JOIN curso c             ON ac.id_curso = c.id
                    INNER JOIN asignatura asig     ON a.id_asignatura = asig.id
                    LEFT JOIN  docente d            ON d.id = a.id_docente
                    WHERE ac.id_curso = :id_curso
                      AND (a.id_docente = :id_docente OR d.id_usuario = :id_usuario_docente)
                      AND a.id_institucion = :id_institucion
                    ORDER BY a.fecha_entrega DESC";
            
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindParam(':id_curso', $id_curso, PDO::PARAM_INT);
            $stmt->bindParam(':id_docente', $id_docente, PDO::PARAM_INT);
            $stmt->bindParam(':id_usuario_docente', $id_docente, PDO::PARAM_INT);
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
            $campos = [
                'titulo = :titulo',
                'descripcion = :descripcion',
                'tipo = :tipo',
                'ponderacion = :ponderacion',
                'fecha_entrega = :fecha_entrega',
                'estado = :estado'
            ];

            if (array_key_exists('archivo', $datos)) {
                $campos[] = 'archivo = :archivo';
            }

            $sql = "UPDATE actividad SET " . implode(', ', $campos) . " WHERE id = :id";
            
            $stmt = $this->conexion->prepare($sql);
            
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':titulo', $datos['titulo'], PDO::PARAM_STR);
            $stmt->bindParam(':descripcion', $datos['descripcion'], PDO::PARAM_STR);
            $stmt->bindParam(':tipo', $datos['tipo'], PDO::PARAM_STR);
            $stmt->bindParam(':ponderacion', $datos['ponderacion']);
            $stmt->bindParam(':fecha_entrega', $datos['fecha_entrega'], PDO::PARAM_STR);
            $stmt->bindParam(':estado', $datos['estado'], PDO::PARAM_STR);
            if (array_key_exists('archivo', $datos)) {
                $stmt->bindParam(':archivo', $datos['archivo'], PDO::PARAM_STR);
            }
            
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
     * Obtener la suma total de ponderaciones ya asignadas a un id_asignatura_curso.
     * Útil para validar que el nuevo valor no supere el 100% restante.
     *
     * @param int      $id_asignatura_curso  ID del asignatura-curso
     * @param int|null $excluirId            ID de actividad a excluir (para edición)
     * @return float Suma de ponderaciones (0-100)
     */
    public function obtenerTotalPonderacion($id_asignatura_curso, $excluirId = null) {
        try {
            $sql = "SELECT COALESCE(SUM(ponderacion), 0) AS total
                    FROM actividad
                    WHERE id_asignatura_curso = :id_asignatura_curso";

            if ($excluirId) {
                $sql .= " AND id != :excluir_id";
            }

            $stmt = $this->conexion->prepare($sql);
            $stmt->bindParam(':id_asignatura_curso', $id_asignatura_curso, PDO::PARAM_INT);
            if ($excluirId) {
                $stmt->bindParam(':excluir_id', $excluirId, PDO::PARAM_INT);
            }
            $stmt->execute();

            $fila = $stmt->fetch(PDO::FETCH_ASSOC);
            return (float)($fila['total'] ?? 0);

        } catch (PDOException $e) {
            error_log("Error en Actividad_docente::obtenerTotalPonderacion -> " . $e->getMessage());
            return 0;
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
