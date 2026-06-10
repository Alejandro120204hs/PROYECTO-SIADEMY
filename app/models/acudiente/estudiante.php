<?php

/**
 * Modelo: EstudianteAcudiente
 * Resuelve la relación acudiente -> estudiante(s) asociados, respetando
 * el aislamiento multi-institución (id_institucion) y la pertenencia
 * (estudiante.id_acudiente debe corresponder al acudiente en sesión).
 */

require_once __DIR__ . '/../../../config/database.php';

class EstudianteAcudiente
{
    private $conexion;

    public function __construct()
    {
        $db = new Conexion();
        $this->conexion = $db->getConexion();
    }

    /**
     * Lista los estudiantes asociados a un acudiente, con el curso/grado
     * de su matrícula activa del año indicado (si existe).
     */
    public function obtenerEstudiantesAsociados($id_acudiente, $id_institucion, $anio)
    {
        try {
            $sql = "SELECT
                        e.id,
                        e.nombres,
                        e.apellidos,
                        e.documento,
                        e.tipo_documento,
                        e.foto,
                        c.id AS id_curso,
                        c.grado,
                        c.curso AS nombre_curso,
                        c.jornada
                    FROM estudiante e
                    LEFT JOIN matricula m ON m.id_estudiante = e.id
                                          AND m.anio = :anio
                                          AND m.estado = 'Activa'
                    LEFT JOIN curso c ON c.id = m.id_curso
                    WHERE e.id_acudiente = :id_acudiente
                      AND e.id_institucion = :id_institucion
                    ORDER BY e.nombres ASC, e.apellidos ASC";

            $stmt = $this->conexion->prepare($sql);
            $stmt->bindParam(':id_acudiente', $id_acudiente, PDO::PARAM_INT);
            $stmt->bindParam(':id_institucion', $id_institucion, PDO::PARAM_INT);
            $stmt->bindParam(':anio', $anio, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            error_log("Error en EstudianteAcudiente::obtenerEstudiantesAsociados -> " . $e->getMessage());
            return [];
        }
    }

    /**
     * Obtiene un estudiante por id, validando que pertenezca al acudiente
     * y a la institución indicados (previene IDOR entre acudientes/instituciones).
     */
    public function obtenerEstudiantePorId($id_estudiante, $id_acudiente, $id_institucion, $anio)
    {
        try {
            $sql = "SELECT
                        e.id,
                        e.nombres,
                        e.apellidos,
                        e.documento,
                        e.tipo_documento,
                        e.foto,
                        c.id AS id_curso,
                        c.grado,
                        c.curso AS nombre_curso,
                        c.jornada
                    FROM estudiante e
                    LEFT JOIN matricula m ON m.id_estudiante = e.id
                                          AND m.anio = :anio
                                          AND m.estado = 'Activa'
                    LEFT JOIN curso c ON c.id = m.id_curso
                    WHERE e.id = :id_estudiante
                      AND e.id_acudiente = :id_acudiente
                      AND e.id_institucion = :id_institucion
                    LIMIT 1";

            $stmt = $this->conexion->prepare($sql);
            $stmt->bindParam(':id_estudiante', $id_estudiante, PDO::PARAM_INT);
            $stmt->bindParam(':id_acudiente', $id_acudiente, PDO::PARAM_INT);
            $stmt->bindParam(':id_institucion', $id_institucion, PDO::PARAM_INT);
            $stmt->bindParam(':anio', $anio, PDO::PARAM_INT);
            $stmt->execute();

            $fila = $stmt->fetch(PDO::FETCH_ASSOC);
            return $fila ?: null;

        } catch (PDOException $e) {
            error_log("Error en EstudianteAcudiente::obtenerEstudiantePorId -> " . $e->getMessage());
            return null;
        }
    }
}
