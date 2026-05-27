<?php

/**
 * Modelo: EntregaEstudiante
 * Gestiona entregas de actividades por parte del estudiante.
 *
 * CORRECCIONES APLICADAS:
 *  - Eliminada detección dinámica via INFORMATION_SCHEMA (anti-patrón, costosa).
 *  - Columna `archivo` hardcodeada como existente (confirmado en esquema de BD).
 *  - Propiedad $tieneColumnaArchivo y método detectarColumnaArchivo() eliminados.
 *  - Todas las ramas condicionales sobre `archivo` colapsadas a la versión que SÍ la incluye.
 */

require_once __DIR__ . '/../../../config/database.php';

class EntregaEstudiante {

    private $conn;

    public function __construct() {
        $database = new Conexion();
        $this->conn = $database->getConexion();
    }

    // -----------------------------------------------------------------
    // VERIFICAR si ya existe una entrega para una actividad
    // -----------------------------------------------------------------
    public function verificarEntregaExistente($id_actividad, $id_estudiante) {
        try {
            $sql = "SELECT id, archivo_ruta, archivo, fecha_entrega, estado
                    FROM entrega_actividad
                    WHERE id_actividad  = :id_actividad
                      AND id_estudiante = :id_estudiante";

            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':id_actividad',  $id_actividad,  PDO::PARAM_INT);
            $stmt->bindParam(':id_estudiante', $id_estudiante, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetch(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            error_log("Error en EntregaEstudiante::verificarEntregaExistente -> " . $e->getMessage());
            return false;
        }
    }

    // -----------------------------------------------------------------
    // CREAR una nueva entrega
    // -----------------------------------------------------------------
    public function crear($datos) {
        try {
            $sql = "INSERT INTO entrega_actividad
                        (id_actividad, id_estudiante, archivo_ruta, archivo,
                         observaciones_estudiante, estado)
                    VALUES
                        (:id_actividad, :id_estudiante, :archivo_ruta, :archivo,
                         :observaciones_estudiante, 'Entregado')";

            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':id_actividad',             $datos['id_actividad'],             PDO::PARAM_INT);
            $stmt->bindParam(':id_estudiante',            $datos['id_estudiante'],            PDO::PARAM_INT);
            $stmt->bindParam(':archivo_ruta',             $datos['archivo_ruta'],             PDO::PARAM_STR);
            $archivoNombre = $datos['archivo'] ?? basename($datos['archivo_ruta']);
            $stmt->bindParam(':archivo',                  $archivoNombre,                    PDO::PARAM_STR);
            $stmt->bindParam(':observaciones_estudiante', $datos['observaciones_estudiante'], PDO::PARAM_STR);

            return $stmt->execute();

        } catch (PDOException $e) {
            error_log("Error en EntregaEstudiante::crear -> " . $e->getMessage());
            return false;
        }
    }

    // -----------------------------------------------------------------
    // ACTUALIZAR una entrega existente (re-entrega)
    // -----------------------------------------------------------------
    public function actualizar($id_actividad, $id_estudiante, $datos) {
        try {
            $sql = "UPDATE entrega_actividad
                    SET archivo_ruta             = :archivo_ruta,
                        archivo                  = :archivo,
                        observaciones_estudiante = :observaciones_estudiante,
                        fecha_entrega            = CURRENT_TIMESTAMP,
                        estado                   = 'Entregado'
                    WHERE id_actividad  = :id_actividad
                      AND id_estudiante = :id_estudiante";

            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':archivo_ruta',             $datos['archivo_ruta'],             PDO::PARAM_STR);
            $archivoNombre = $datos['archivo'] ?? basename($datos['archivo_ruta']);
            $stmt->bindParam(':archivo',                  $archivoNombre,                    PDO::PARAM_STR);
            $stmt->bindParam(':observaciones_estudiante', $datos['observaciones_estudiante'], PDO::PARAM_STR);
            $stmt->bindParam(':id_actividad',             $id_actividad,                     PDO::PARAM_INT);
            $stmt->bindParam(':id_estudiante',            $id_estudiante,                    PDO::PARAM_INT);

            return $stmt->execute();

        } catch (PDOException $e) {
            error_log("Error en EntregaEstudiante::actualizar -> " . $e->getMessage());
            return false;
        }
    }

    // -----------------------------------------------------------------
    // OBTENER detalle de la entrega de un estudiante para una actividad
    // -----------------------------------------------------------------
    public function obtenerDetalleEntrega($id_actividad, $id_estudiante) {
        try {
            $sql = "SELECT
                        ea.id,
                        ea.archivo_ruta,
                        ea.archivo,
                        ea.fecha_entrega,
                        ea.observaciones_estudiante,
                        ea.estado,
                        a.titulo        AS actividad_titulo,
                        a.tipo          AS actividad_tipo,
                        a.fecha_entrega AS fecha_limite,
                        CASE
                            WHEN ea.fecha_entrega <= a.fecha_entrega THEN 'A tiempo'
                            ELSE 'Tarde'
                        END AS puntualidad
                    FROM entrega_actividad ea
                    INNER JOIN actividad a ON ea.id_actividad = a.id
                    WHERE ea.id_estudiante = :id_estudiante
                      AND ea.id_actividad  = :id_actividad
                    ORDER BY ea.fecha_entrega DESC
                    LIMIT 1";

            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':id_estudiante', $id_estudiante, PDO::PARAM_INT);
            $stmt->bindParam(':id_actividad',  $id_actividad,  PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetch(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            error_log("Error en EntregaEstudiante::obtenerDetalleEntrega -> " . $e->getMessage());
            return false;
        }
    }

    // -----------------------------------------------------------------
    // OBTENER información de actividad (para construir rutas de carpetas)
    // -----------------------------------------------------------------
    public function obtenerInfoActividad($id_actividad) {
        try {
            $sql = "SELECT
                        a.id,
                        a.titulo,
                        a.id_asignatura,
                        a.id_asignatura_curso,
                        a.id_institucion,
                        ac.id_curso,
                        c.id            AS curso_id,
                        c.grado,
                        c.curso         AS nombre_curso,
                        asig.id         AS asignatura_id,
                        asig.nombre     AS nombre_asignatura,
                        inst.nombre     AS nombre_institucion
                    FROM actividad a
                    INNER JOIN asignatura_curso ac ON a.id_asignatura_curso = ac.id
                    INNER JOIN curso c             ON ac.id_curso = c.id
                    INNER JOIN asignatura asig     ON a.id_asignatura = asig.id
                    INNER JOIN institucion inst    ON a.id_institucion = inst.id
                    WHERE a.id = :id_actividad";

            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':id_actividad', $id_actividad, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetch(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            error_log("Error en EntregaEstudiante::obtenerInfoActividad -> " . $e->getMessage());
            return false;
        }
    }

    // -----------------------------------------------------------------
    // OBTENER información del estudiante (para construir rutas de carpetas)
    // -----------------------------------------------------------------
    public function obtenerInfoEstudiante($id_estudiante) {
        try {
            $sql = "SELECT id, nombres, apellidos, documento
                    FROM estudiante
                    WHERE id = :id_estudiante";

            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':id_estudiante', $id_estudiante, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetch(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            error_log("Error en EntregaEstudiante::obtenerInfoEstudiante -> " . $e->getMessage());
            return false;
        }
    }

    // -----------------------------------------------------------------
    // ELIMINAR entrega — retorna la ruta del archivo para borrarlo del disco
    // -----------------------------------------------------------------
    public function eliminar($id, $id_estudiante) {
        try {
            // 1. Obtener ruta del archivo antes de eliminar
            $sqlSelect = "SELECT archivo_ruta
                          FROM entrega_actividad
                          WHERE id = :id AND id_estudiante = :id_estudiante";

            $stmt = $this->conn->prepare($sqlSelect);
            $stmt->bindParam(':id',           $id,           PDO::PARAM_INT);
            $stmt->bindParam(':id_estudiante', $id_estudiante, PDO::PARAM_INT);
            $stmt->execute();
            $entrega = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$entrega) {
                return false;
            }

            // 2. Eliminar registro de BD
            $sqlDelete = "DELETE FROM entrega_actividad
                          WHERE id = :id AND id_estudiante = :id_estudiante";

            $stmt = $this->conn->prepare($sqlDelete);
            $stmt->bindParam(':id',           $id,           PDO::PARAM_INT);
            $stmt->bindParam(':id_estudiante', $id_estudiante, PDO::PARAM_INT);

            if ($stmt->execute()) {
                return $entrega['archivo_ruta']; // Ruta para eliminar el archivo físico
            }

            return false;

        } catch (PDOException $e) {
            error_log("Error en EntregaEstudiante::eliminar -> " . $e->getMessage());
            return false;
        }
    }
}
