<?php

/**
 * Modelo: AdminBoletinModel
 * Provee listado de estudiantes y validación multi-tenant para el
 * panel de boletines del administrador.
 * El detalle de cada boletín usa BoletinEstudiante (modelo del estudiante).
 */

require_once __DIR__ . '/../../../config/database.php';

class AdminBoletinModel
{
    private $conexion;

    public function __construct()
    {
        $db = new Conexion();
        $this->conexion = $db->getConexion();
    }

    /**
     * Cursos activos de la institución (para el filtro).
     */
    public function obtenerCursos(int $id_institucion): array
    {
        try {
            $stmt = $this->conexion->prepare(
                "SELECT id, grado, curso, jornada
                 FROM curso
                 WHERE id_institucion = :id_institucion
                   AND estado = 'Activo'
                 ORDER BY grado ASC, curso ASC"
            );
            $stmt->bindParam(':id_institucion', $id_institucion, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (PDOException $e) {
            error_log('AdminBoletinModel::obtenerCursos -> ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Lista de estudiantes matriculados en la institución para el año dado.
     * Filtrables por curso y búsqueda de texto (nombre / apellido / documento).
     */
    public function obtenerEstudiantes(
        int    $id_institucion,
        int    $anio,
        ?int   $id_curso  = null,
        string $busqueda  = ''
    ): array {
        try {
            $sql = "SELECT e.id, e.nombres, e.apellidos, e.documento, e.foto,
                           c.id AS id_curso, c.grado, c.curso, c.jornada
                    FROM estudiante e
                    INNER JOIN matricula m ON m.id_estudiante = e.id
                    INNER JOIN curso c     ON m.id_curso      = c.id
                    WHERE e.id_institucion = :id_institucion
                      AND m.anio           = :anio
                      AND c.estado         = 'Activo'";

            if ($id_curso) {
                $sql .= " AND c.id = :id_curso";
            }

            if ($busqueda !== '') {
                $sql .= " AND (e.nombres LIKE :bus OR e.apellidos LIKE :bus
                              OR e.documento LIKE :bus)";
            }

            $sql .= " ORDER BY c.grado ASC, e.apellidos ASC, e.nombres ASC";

            $stmt = $this->conexion->prepare($sql);
            $stmt->bindParam(':id_institucion', $id_institucion, PDO::PARAM_INT);
            $stmt->bindParam(':anio',           $anio,           PDO::PARAM_INT);

            if ($id_curso) {
                $stmt->bindParam(':id_curso', $id_curso, PDO::PARAM_INT);
            }
            if ($busqueda !== '') {
                $like = '%' . $busqueda . '%';
                $stmt->bindParam(':bus', $like, PDO::PARAM_STR);
            }

            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (PDOException $e) {
            error_log('AdminBoletinModel::obtenerEstudiantes -> ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Verifica que el estudiante pertenece a la institución (seguridad multi-tenant).
     */
    public function validarEstudianteEnInstitucion(int $id_estudiante, int $id_institucion): bool
    {
        try {
            $stmt = $this->conexion->prepare(
                "SELECT 1 FROM estudiante
                 WHERE id = :id AND id_institucion = :id_institucion LIMIT 1"
            );
            $stmt->bindParam(':id',             $id_estudiante,  PDO::PARAM_INT);
            $stmt->bindParam(':id_institucion', $id_institucion, PDO::PARAM_INT);
            $stmt->execute();
            return (bool) $stmt->fetchColumn();
        } catch (PDOException $e) {
            error_log('AdminBoletinModel::validarEstudianteEnInstitucion -> ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Conteos rápidos para las stat-cards del panel.
     */
    public function obtenerStats(int $id_institucion, int $anio): array
    {
        try {
            $stmt = $this->conexion->prepare(
                "SELECT
                     COUNT(DISTINCT e.id)  AS total_estudiantes,
                     COUNT(DISTINCT c.id)  AS total_cursos
                 FROM estudiante e
                 INNER JOIN matricula m ON m.id_estudiante = e.id
                 INNER JOIN curso c     ON m.id_curso      = c.id
                 WHERE e.id_institucion = :id_institucion
                   AND m.anio           = :anio
                   AND c.estado         = 'Activo'"
            );
            $stmt->bindParam(':id_institucion', $id_institucion, PDO::PARAM_INT);
            $stmt->bindParam(':anio',           $anio,           PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC) ?: ['total_estudiantes' => 0, 'total_cursos' => 0];
        } catch (PDOException $e) {
            error_log('AdminBoletinModel::obtenerStats -> ' . $e->getMessage());
            return ['total_estudiantes' => 0, 'total_cursos' => 0];
        }
    }
}
