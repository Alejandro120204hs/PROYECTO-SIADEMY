<?php

require_once __DIR__ . '/../../../config/database.php';

class AsistenciaAcudiente
{
    private $pdo;

    public function __construct()
    {
        $db = new Conexion();
        $this->pdo = $db->getConexion();
    }

    public function obtenerTotalesGlobales(int $id_estudiante, int $id_institucion): array
    {
        try {
            $sql = "SELECT
                        COUNT(ast.id) AS total_clases,
                        SUM(CASE WHEN ast.estado = 'Presente'    THEN 1 ELSE 0 END) AS presentes,
                        SUM(CASE WHEN ast.estado = 'Ausente'     THEN 1 ELSE 0 END) AS ausentes,
                        SUM(CASE WHEN ast.estado = 'Tarde'       THEN 1 ELSE 0 END) AS tardes,
                        SUM(CASE WHEN ast.estado = 'Justificado' THEN 1 ELSE 0 END) AS justificados,
                        ROUND(
                            SUM(CASE WHEN ast.estado IN ('Presente','Tarde') THEN 1 ELSE 0 END)
                            * 100.0 / NULLIF(COUNT(ast.id), 0), 1
                        ) AS porcentaje_asistencia
                    FROM asistencia ast
                    WHERE ast.id_estudiante  = :id_estudiante
                      AND ast.id_institucion = :id_institucion";

            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':id_estudiante',  $id_estudiante,  PDO::PARAM_INT);
            $stmt->bindValue(':id_institucion', $id_institucion, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
        } catch (PDOException $e) {
            error_log('AsistenciaAcudiente::obtenerTotalesGlobales → ' . $e->getMessage());
            return [];
        }
    }

    public function obtenerResumenPorAsignatura(int $id_estudiante, int $id_institucion): array
    {
        try {
            $sql = "SELECT
                        asig.id              AS id_asignatura,
                        asig.nombre          AS nombre_asignatura,
                        COUNT(ast.id)        AS total_clases,
                        SUM(CASE WHEN ast.estado = 'Presente'    THEN 1 ELSE 0 END) AS presentes,
                        SUM(CASE WHEN ast.estado = 'Ausente'     THEN 1 ELSE 0 END) AS ausentes,
                        SUM(CASE WHEN ast.estado = 'Tarde'       THEN 1 ELSE 0 END) AS tardes,
                        SUM(CASE WHEN ast.estado = 'Justificado' THEN 1 ELSE 0 END) AS justificados,
                        ROUND(
                            SUM(CASE WHEN ast.estado IN ('Presente','Tarde') THEN 1 ELSE 0 END)
                            * 100.0 / NULLIF(COUNT(ast.id), 0), 1
                        ) AS porcentaje_asistencia
                    FROM asistencia ast
                    INNER JOIN asignatura asig ON ast.id_asignatura = asig.id
                    WHERE ast.id_estudiante  = :id_estudiante
                      AND ast.id_institucion = :id_institucion
                    GROUP BY asig.id, asig.nombre
                    ORDER BY asig.nombre ASC";

            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':id_estudiante',  $id_estudiante,  PDO::PARAM_INT);
            $stmt->bindValue(':id_institucion', $id_institucion, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('AsistenciaAcudiente::obtenerResumenPorAsignatura → ' . $e->getMessage());
            return [];
        }
    }

    public function obtenerHistorial(int $id_estudiante, int $id_institucion, int $limit = 50): array
    {
        try {
            $sql = "SELECT
                        cal.fecha,
                        asig.nombre AS nombre_asignatura,
                        ast.estado
                    FROM asistencia ast
                    INNER JOIN calendario cal  ON ast.id_calendario = cal.id
                    INNER JOIN asignatura asig ON ast.id_asignatura = asig.id
                    WHERE ast.id_estudiante  = :id_estudiante
                      AND ast.id_institucion = :id_institucion
                    ORDER BY cal.fecha DESC
                    LIMIT :lim";

            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':id_estudiante',  $id_estudiante,  PDO::PARAM_INT);
            $stmt->bindValue(':id_institucion', $id_institucion, PDO::PARAM_INT);
            $stmt->bindValue(':lim',            $limit,          PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('AsistenciaAcudiente::obtenerHistorial → ' . $e->getMessage());
            return [];
        }
    }
}
