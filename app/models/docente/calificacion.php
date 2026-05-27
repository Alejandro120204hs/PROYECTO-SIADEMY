<?php

/**
 * Modelo: CalificacionDocente
 * Gestión de notas y observaciones del docente sobre entregas.
 *
 * CORRECCIONES APLICADAS:
 *  - Cambiado de herencia (extends Conexion) a composición (private $conexion).
 *    La herencia era incorrecta: Conexion no está diseñada para ser extendida;
 *    los modelos deben usar composición para obtener la conexión PDO.
 *  - $this->getConexion() reemplazado por $this->conexion en todos los métodos.
 */

require_once BASE_PATH . '/config/database.php';

class CalificacionDocente {

    private $conexion;

    public function __construct() {
        $db = new Conexion();
        $this->conexion = $db->getConexion();
    }

    // -----------------------------------------------------------------
    // VERIFICAR que el docente tiene permiso para calificar esta entrega.
    // Valida que la entrega pertenezca a un curso donde el docente es autor.
    // -----------------------------------------------------------------
    public function verificarPermisoCalificar($id_entrega, $id_docente, $id_institucion) {
        try {
            $sql = "SELECT COUNT(*) AS tiene_permiso
                    FROM entrega_actividad ea
                    INNER JOIN actividad a ON ea.id_actividad = a.id
                    WHERE ea.id              = :id_entrega
                      AND a.id_docente      = :id_docente
                      AND a.id_institucion  = :id_institucion";

            $stmt = $this->conexion->prepare($sql);
            $stmt->bindParam(':id_entrega',    $id_entrega,    PDO::PARAM_INT);
            $stmt->bindParam(':id_docente',    $id_docente,    PDO::PARAM_INT);
            $stmt->bindParam(':id_institucion',$id_institucion,PDO::PARAM_INT);
            $stmt->execute();

            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            return (int)($resultado['tiene_permiso'] ?? 0) > 0;

        } catch (Exception $e) {
            error_log("Error en CalificacionDocente::verificarPermisoCalificar -> " . $e->getMessage());
            return false;
        }
    }

    // -----------------------------------------------------------------
    // GUARDAR O ACTUALIZAR calificación (upsert).
    // Si ya existe para la entrega, actualiza; si no, crea.
    // -----------------------------------------------------------------
    public function guardarCalificacion($id_entrega, $nota, $observacion, $id_docente) {
        try {
            // 1. Obtener datos de la entrega para el INSERT
            $sqlEntrega = "SELECT ea.id_actividad, ea.id_estudiante, a.id_institucion
                           FROM entrega_actividad ea
                           INNER JOIN actividad a ON ea.id_actividad = a.id
                           WHERE ea.id = :id_entrega";

            $stmtEntrega = $this->conexion->prepare($sqlEntrega);
            $stmtEntrega->bindParam(':id_entrega', $id_entrega, PDO::PARAM_INT);
            $stmtEntrega->execute();
            $datosEntrega = $stmtEntrega->fetch(PDO::FETCH_ASSOC);

            if (!$datosEntrega) {
                error_log("CalificacionDocente::guardarCalificacion — entrega no encontrada ID: " . $id_entrega);
                return false;
            }

            // 2. Verificar si ya existe calificación
            $sqlCheck = "SELECT id FROM calificacion WHERE id_entrega = :id_entrega";
            $stmtCheck = $this->conexion->prepare($sqlCheck);
            $stmtCheck->bindParam(':id_entrega', $id_entrega, PDO::PARAM_INT);
            $stmtCheck->execute();
            $existe = $stmtCheck->fetch(PDO::FETCH_ASSOC);

            if ($existe) {
                // Actualizar calificación existente
                $sqlUpdate = "UPDATE calificacion
                              SET nota                   = :nota,
                                  observacion            = :observacion,
                                  id_docente_calificador = :id_docente,
                                  fecha_calificacion     = NOW()
                              WHERE id_entrega = :id_entrega";

                $stmt = $this->conexion->prepare($sqlUpdate);
                $stmt->bindParam(':nota',        $nota);
                $stmt->bindParam(':observacion', $observacion,  PDO::PARAM_STR);
                $stmt->bindParam(':id_docente',  $id_docente,   PDO::PARAM_INT);
                $stmt->bindParam(':id_entrega',  $id_entrega,   PDO::PARAM_INT);

                return $stmt->execute();

            } else {
                // Crear nueva calificación
                $sqlInsert = "INSERT INTO calificacion
                                  (id_entrega, id_institucion, nota, id_actividad,
                                   id_estudiante, observacion, id_docente_calificador, fecha_calificacion)
                              VALUES
                                  (:id_entrega, :id_institucion, :nota, :id_actividad,
                                   :id_estudiante, :observacion, :id_docente, NOW())";

                $stmt = $this->conexion->prepare($sqlInsert);
                $stmt->bindParam(':id_entrega',    $id_entrega,                     PDO::PARAM_INT);
                $stmt->bindParam(':id_institucion',$datosEntrega['id_institucion'],  PDO::PARAM_INT);
                $stmt->bindParam(':nota',          $nota);
                $stmt->bindParam(':id_actividad',  $datosEntrega['id_actividad'],   PDO::PARAM_INT);
                $stmt->bindParam(':id_estudiante', $datosEntrega['id_estudiante'],  PDO::PARAM_INT);
                $stmt->bindParam(':observacion',   $observacion,                    PDO::PARAM_STR);
                $stmt->bindParam(':id_docente',    $id_docente,                     PDO::PARAM_INT);

                return $stmt->execute();
            }

        } catch (Exception $e) {
            error_log("Error en CalificacionDocente::guardarCalificacion -> " . $e->getMessage());
            return false;
        }
    }

    // -----------------------------------------------------------------
    // OBTENER calificación de una entrega
    // -----------------------------------------------------------------
    public function obtenerCalificacion($id_entrega) {
        try {
            $sql = "SELECT
                        c.*,
                        CONCAT(d.nombres, ' ', d.apellidos) AS nombre_docente
                    FROM calificacion c
                    LEFT JOIN docente d ON c.id_docente_calificador = d.id
                    WHERE c.id_entrega = :id_entrega";

            $stmt = $this->conexion->prepare($sql);
            $stmt->bindParam(':id_entrega', $id_entrega, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetch(PDO::FETCH_ASSOC);

        } catch (Exception $e) {
            error_log("Error en CalificacionDocente::obtenerCalificacion -> " . $e->getMessage());
            return null;
        }
    }

    // -----------------------------------------------------------------
    // OBTENER estadísticas de calificaciones de una actividad
    // -----------------------------------------------------------------
    public function obtenerEstadisticasActividad($id_actividad, $id_institucion) {
        try {
            $sql = "SELECT
                        COUNT(*)      AS total_calificaciones,
                        AVG(c.nota)   AS promedio_notas,
                        MAX(c.nota)   AS nota_maxima,
                        MIN(c.nota)   AS nota_minima
                    FROM calificacion c
                    INNER JOIN entrega_actividad ea ON c.id_entrega = ea.id
                    WHERE ea.id_actividad   = :id_actividad
                      AND c.id_institucion  = :id_institucion";

            $stmt = $this->conexion->prepare($sql);
            $stmt->bindParam(':id_actividad',  $id_actividad,  PDO::PARAM_INT);
            $stmt->bindParam(':id_institucion',$id_institucion,PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetch(PDO::FETCH_ASSOC);

        } catch (Exception $e) {
            error_log("Error en CalificacionDocente::obtenerEstadisticasActividad -> " . $e->getMessage());
            return null;
        }
    }
}
