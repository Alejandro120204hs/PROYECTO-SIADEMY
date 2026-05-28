<?php

/**
 * Modelo: DocenteBoletinModel
 * Devuelve los estudiantes a los que el docente les dicta clase.
 *
 * Aislamiento multi-tenant: todos los queries filtran por
 * id_institucion (del docente) y id_docente.
 *
 * El detalle de cada boletín individual usa BoletinEstudiante.
 */

require_once __DIR__ . '/../../../config/database.php';

class DocenteBoletinModel
{
    private $conexion;

    public function __construct()
    {
        $db = new Conexion();
        $this->conexion = $db->getConexion();
    }

    /**
     * Cursos donde el docente dicta clase en el año dado (para el filtro).
     */
    public function obtenerCursosDocente(int $id_docente, int $id_institucion, int $anio): array
    {
        try {
            $stmt = $this->conexion->prepare(
                "SELECT DISTINCT c.id, c.grado, c.curso, c.jornada
                 FROM docente_asignatura_curso dac
                 INNER JOIN asignatura_curso ac ON ac.id    = dac.id_asignatura_curso
                 INNER JOIN curso c             ON c.id     = ac.id_curso
                 INNER JOIN matricula m         ON m.id_curso = c.id AND m.anio = :anio
                 WHERE dac.id_docente    = :id_docente
                   AND c.id_institucion  = :id_institucion
                   AND c.estado         = 'Activo'
                 ORDER BY c.grado ASC, c.curso ASC"
            );
            $stmt->bindParam(':id_docente',    $id_docente,    PDO::PARAM_INT);
            $stmt->bindParam(':id_institucion',$id_institucion,PDO::PARAM_INT);
            $stmt->bindParam(':anio',          $anio,          PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (PDOException $e) {
            error_log('DocenteBoletinModel::obtenerCursosDocente -> ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Estudiantes matriculados en los cursos donde el docente dicta clase.
     * Filtrables por id_curso y búsqueda de texto.
     *
     * UNIQUE PDO params: :id_inst_e para la segunda referencia a id_institucion.
     */
    public function obtenerEstudiantes(
        int    $id_docente,
        int    $id_institucion,
        int    $anio,
        ?int   $id_curso = null,
        string $busqueda = ''
    ): array {
        try {
            $sql = "SELECT DISTINCT
                        e.id, e.nombres, e.apellidos, e.documento, e.foto,
                        c.id AS id_curso, c.grado, c.curso, c.jornada
                    FROM docente_asignatura_curso dac
                    INNER JOIN asignatura_curso ac ON ac.id      = dac.id_asignatura_curso
                    INNER JOIN curso c             ON c.id       = ac.id_curso
                    INNER JOIN matricula m         ON m.id_curso = c.id AND m.anio = :anio
                    INNER JOIN estudiante e        ON e.id       = m.id_estudiante
                    WHERE dac.id_docente    = :id_docente
                      AND c.id_institucion  = :id_institucion
                      AND e.id_institucion  = :id_inst_e
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
            $stmt->bindParam(':id_docente',    $id_docente,    PDO::PARAM_INT);
            $stmt->bindParam(':id_institucion',$id_institucion,PDO::PARAM_INT);
            $stmt->bindParam(':id_inst_e',     $id_institucion,PDO::PARAM_INT);
            $stmt->bindParam(':anio',          $anio,          PDO::PARAM_INT);

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
            error_log('DocenteBoletinModel::obtenerEstudiantes -> ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Verifica que el estudiante esté en un curso donde este docente dicta clase
     * y que pertenezca a la misma institución (seguridad multi-tenant).
     */
    public function validarEstudianteParaDocente(
        int $id_estudiante,
        int $id_docente,
        int $id_institucion
    ): bool {
        try {
            $stmt = $this->conexion->prepare(
                "SELECT 1
                 FROM estudiante e
                 INNER JOIN matricula m         ON m.id_estudiante       = e.id
                 INNER JOIN asignatura_curso ac ON ac.id_curso           = m.id_curso
                 INNER JOIN docente_asignatura_curso dac
                                                ON dac.id_asignatura_curso = ac.id
                 WHERE e.id              = :id_estudiante
                   AND dac.id_docente    = :id_docente
                   AND e.id_institucion  = :id_institucion
                 LIMIT 1"
            );
            $stmt->bindParam(':id_estudiante', $id_estudiante, PDO::PARAM_INT);
            $stmt->bindParam(':id_docente',    $id_docente,    PDO::PARAM_INT);
            $stmt->bindParam(':id_institucion',$id_institucion,PDO::PARAM_INT);
            $stmt->execute();
            return (bool) $stmt->fetchColumn();
        } catch (PDOException $e) {
            error_log('DocenteBoletinModel::validarEstudianteParaDocente -> ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Conteos para las stat-cards del panel.
     */
    public function obtenerStats(int $id_docente, int $id_institucion, int $anio): array
    {
        try {
            $stmt = $this->conexion->prepare(
                "SELECT
                     COUNT(DISTINCT e.id) AS total_estudiantes,
                     COUNT(DISTINCT c.id) AS total_cursos
                 FROM docente_asignatura_curso dac
                 INNER JOIN asignatura_curso ac ON ac.id      = dac.id_asignatura_curso
                 INNER JOIN curso c             ON c.id       = ac.id_curso
                 INNER JOIN matricula m         ON m.id_curso = c.id AND m.anio = :anio
                 INNER JOIN estudiante e        ON e.id       = m.id_estudiante
                 WHERE dac.id_docente    = :id_docente
                   AND c.id_institucion  = :id_institucion
                   AND c.estado         = 'Activo'"
            );
            $stmt->bindParam(':id_docente',    $id_docente,    PDO::PARAM_INT);
            $stmt->bindParam(':id_institucion',$id_institucion,PDO::PARAM_INT);
            $stmt->bindParam(':anio',          $anio,          PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC)
                ?: ['total_estudiantes' => 0, 'total_cursos' => 0];
        } catch (PDOException $e) {
            error_log('DocenteBoletinModel::obtenerStats -> ' . $e->getMessage());
            return ['total_estudiantes' => 0, 'total_cursos' => 0];
        }
    }
}
