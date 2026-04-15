<?php

require_once __DIR__ . '/../../../config/database.php';

class AsistenciaDocente {

    private $conexion;

    public function __construct() {
        $db = new Conexion();
        $this->conexion = $db->getConexion();
    }

    // -------------------------------------------------------
    // Devuelve los cursos del docente agrupados con sus
    // asignaturas: [ { id_curso, curso_nombre, grado, jornada,
    //                  asignaturas: [ {id, nombre}, ... ] } ]
    // -------------------------------------------------------
    public function obtenerCursosConAsignaturas(int $id_usuario, int $id_institucion): array {
        try {
            $sql = "SELECT
                        c.id       AS id_curso,
                        c.curso    AS nombre_grupo,
                        c.grado,
                        c.jornada,
                        a.id       AS id_asignatura,
                        a.nombre   AS nombre_asignatura
                    FROM docente_asignatura_curso dac
                    INNER JOIN docente          d  ON dac.id_docente         = d.id
                    INNER JOIN asignatura_curso ac ON dac.id_asignatura_curso = ac.id
                    INNER JOIN curso            c  ON ac.id_curso             = c.id
                    INNER JOIN asignatura       a  ON ac.id_asignatura        = a.id
                    WHERE d.id_usuario          = :id_usuario
                      AND dac.id_institucion    = :id_institucion
                      AND dac.estado            = 'activo'
                    ORDER BY c.grado ASC, c.curso ASC, a.nombre ASC";

            $stmt = $this->conexion->prepare($sql);
            $stmt->bindParam(':id_usuario',    $id_usuario,    PDO::PARAM_INT);
            $stmt->bindParam(':id_institucion', $id_institucion, PDO::PARAM_INT);
            $stmt->execute();
            $filas = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Agrupar por curso
            $cursos = [];
            foreach ($filas as $fila) {
                $idCurso = (int) $fila['id_curso'];
                if (!isset($cursos[$idCurso])) {
                    $cursos[$idCurso] = [
                        'id_curso'    => $idCurso,
                        'curso_nombre'=> $fila['grado'] . '° - ' . $fila['nombre_grupo'],
                        'grado'       => (int) $fila['grado'],
                        'jornada'     => (string) ($fila['jornada'] ?? ''),
                        'asignaturas' => [],
                    ];
                }
                $cursos[$idCurso]['asignaturas'][] = [
                    'id'     => (int) $fila['id_asignatura'],
                    'nombre' => (string) $fila['nombre_asignatura'],
                ];
            }

            return array_values($cursos);
        } catch (PDOException $e) {
            error_log('AsistenciaDocente::obtenerCursosConAsignaturas -> ' . $e->getMessage());
            return [];
        }
    }

    // -------------------------------------------------------
    // Devuelve cada estudiante matriculado en el curso con
    // su estado de asistencia para la fecha+asignatura dada.
    // Estado devuelto: 'Presente' | 'Ausente' | 'Justificado' | null
    // -------------------------------------------------------
    public function obtenerEstudiantesConAsistencia(
        int    $id_curso,
        int    $id_asignatura,
        string $fecha,
        int    $id_institucion
    ): array {
        try {
            $sql = "SELECT
                        e.id,
                        e.nombres,
                        e.apellidos,
                        e.documento,
                        e.foto,
                        ast.estado AS asistencia_estado
                    FROM matricula m
                    INNER JOIN estudiante e ON m.id_estudiante = e.id
                    LEFT JOIN asistencia ast
                        ON  ast.id_estudiante  = e.id
                        AND ast.id_asignatura  = :id_asignatura
                        AND ast.id_institucion = :id_institucion2
                        AND ast.id_calendario IN (
                            SELECT id FROM calendario
                            WHERE fecha = :fecha
                              AND id_institucion = :id_institucion3
                        )
                    WHERE m.id_curso      = :id_curso
                      AND m.id_institucion = :id_institucion
                    ORDER BY e.apellidos ASC, e.nombres ASC";

            $stmt = $this->conexion->prepare($sql);
            $stmt->bindParam(':id_curso',       $id_curso,       PDO::PARAM_INT);
            $stmt->bindParam(':id_asignatura',  $id_asignatura,  PDO::PARAM_INT);
            $stmt->bindParam(':fecha',          $fecha,          PDO::PARAM_STR);
            $stmt->bindParam(':id_institucion',  $id_institucion, PDO::PARAM_INT);
            $stmt->bindParam(':id_institucion2', $id_institucion, PDO::PARAM_INT);
            $stmt->bindParam(':id_institucion3', $id_institucion, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('AsistenciaDocente::obtenerEstudiantesConAsistencia -> ' . $e->getMessage());
            return [];
        }
    }

    // -------------------------------------------------------
    // Obtiene (o crea) el registro de calendario para una
    // fecha+institución, devolviendo su id.
    // -------------------------------------------------------
    public function obtenerOCrearCalendario(string $fecha, int $id_institucion): int {
        try {
            $stmt = $this->conexion->prepare(
                "SELECT id FROM calendario
                 WHERE fecha = :fecha AND id_institucion = :id_institucion
                 LIMIT 1"
            );
            $stmt->bindParam(':fecha',          $fecha,          PDO::PARAM_STR);
            $stmt->bindParam(':id_institucion', $id_institucion, PDO::PARAM_INT);
            $stmt->execute();
            $fila = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($fila) {
                return (int) $fila['id'];
            }

            // Crear entrada automática
            $ins = $this->conexion->prepare(
                "INSERT INTO calendario (evento, fecha, descripcion, id_institucion)
                 VALUES ('Sesión de clase', :fecha, 'Registro automático de asistencia', :id_institucion)"
            );
            $ins->bindParam(':fecha',          $fecha,          PDO::PARAM_STR);
            $ins->bindParam(':id_institucion', $id_institucion, PDO::PARAM_INT);
            $ins->execute();

            return (int) $this->conexion->lastInsertId();
        } catch (PDOException $e) {
            error_log('AsistenciaDocente::obtenerOCrearCalendario -> ' . $e->getMessage());
            return 0;
        }
    }

    // -------------------------------------------------------
    // Guarda (INSERT o UPDATE) un lote de registros de
    // asistencia para una sesión dada.
    //
    // $registros = [ ['id_estudiante' => N, 'estado' => 'Presente'|'Ausente'|'Justificado'], ... ]
    // -------------------------------------------------------
    public function guardarAsistencia(
        array  $registros,
        int    $id_asignatura,
        int    $id_institucion,
        int    $id_docente,
        string $fecha
    ): bool {
        if (empty($registros)) {
            return true;
        }

        $id_calendario = $this->obtenerOCrearCalendario($fecha, $id_institucion);
        if ($id_calendario === 0) {
            return false;
        }

        try {
            $this->conexion->beginTransaction();

            $sqlCheck = "SELECT id FROM asistencia
                         WHERE id_estudiante  = :id_estudiante
                           AND id_asignatura  = :id_asignatura
                           AND id_institucion = :id_institucion
                           AND id_calendario  = :id_calendario
                         LIMIT 1";

            $sqlInsert = "INSERT INTO asistencia
                            (id_institucion, id_estudiante, id_asignatura, id_calendario, id_docente, estado)
                          VALUES
                            (:id_institucion, :id_estudiante, :id_asignatura, :id_calendario, :id_docente, :estado)";

            $sqlUpdate = "UPDATE asistencia SET estado = :estado, id_docente = :id_docente
                          WHERE id = :id";

            $stmtCheck  = $this->conexion->prepare($sqlCheck);
            $stmtInsert = $this->conexion->prepare($sqlInsert);
            $stmtUpdate = $this->conexion->prepare($sqlUpdate);

            foreach ($registros as $reg) {
                $idEst  = (int) $reg['id_estudiante'];
                $estado = (string) $reg['estado'];

                $stmtCheck->execute([
                    ':id_estudiante'  => $idEst,
                    ':id_asignatura'  => $id_asignatura,
                    ':id_institucion' => $id_institucion,
                    ':id_calendario'  => $id_calendario,
                ]);
                $existente = $stmtCheck->fetch(PDO::FETCH_ASSOC);

                if ($existente) {
                    $stmtUpdate->execute([
                        ':estado'    => $estado,
                        ':id_docente'=> $id_docente,
                        ':id'        => (int) $existente['id'],
                    ]);
                } else {
                    $stmtInsert->execute([
                        ':id_institucion' => $id_institucion,
                        ':id_estudiante'  => $idEst,
                        ':id_asignatura'  => $id_asignatura,
                        ':id_calendario'  => $id_calendario,
                        ':id_docente'     => $id_docente,
                        ':estado'         => $estado,
                    ]);
                }
            }

            $this->conexion->commit();
            return true;
        } catch (PDOException $e) {
            $this->conexion->rollBack();
            error_log('AsistenciaDocente::guardarAsistencia -> ' . $e->getMessage());
            return false;
        }
    }

    // -------------------------------------------------------
    // Obtiene el id real de docente a partir del id_usuario
    // -------------------------------------------------------
    public function obtenerIdDocente(int $id_usuario, int $id_institucion): int {
        try {
            $stmt = $this->conexion->prepare(
                "SELECT id FROM docente
                 WHERE id_usuario = :id_usuario AND id_institucion = :id_institucion
                 LIMIT 1"
            );
            $stmt->bindParam(':id_usuario',    $id_usuario,    PDO::PARAM_INT);
            $stmt->bindParam(':id_institucion', $id_institucion, PDO::PARAM_INT);
            $stmt->execute();
            $fila = $stmt->fetch(PDO::FETCH_ASSOC);
            return $fila ? (int) $fila['id'] : 0;
        } catch (PDOException $e) {
            error_log('AsistenciaDocente::obtenerIdDocente -> ' . $e->getMessage());
            return 0;
        }
    }

    // -------------------------------------------------------
    // Historial de asistencia por fecha para curso+asignatura
    // -------------------------------------------------------
    public function obtenerHistorialAsistencia(
        int $id_curso,
        int $id_asignatura,
        int $id_docente,
        int $id_institucion,
        int $limite = 15
    ): array {
        try {
            $sql = "SELECT
                        cal.fecha,
                        SUM(CASE WHEN a.estado = 'Presente' THEN 1 ELSE 0 END)    AS presentes,
                        SUM(CASE WHEN a.estado = 'Ausente' THEN 1 ELSE 0 END)     AS ausentes,
                        SUM(CASE WHEN a.estado = 'Justificado' THEN 1 ELSE 0 END) AS justificados,
                        COUNT(a.id_estudiante) AS total_registrados
                    FROM asistencia a
                    INNER JOIN calendario cal
                        ON cal.id = a.id_calendario
                       AND cal.id_institucion = a.id_institucion
                    INNER JOIN estudiante e
                        ON e.id = a.id_estudiante
                       AND e.id_institucion = a.id_institucion
                    INNER JOIN matricula m
                        ON m.id_estudiante = e.id
                       AND m.id_institucion = e.id_institucion
                    WHERE a.id_institucion = :id_institucion
                      AND a.id_docente     = :id_docente
                      AND a.id_asignatura  = :id_asignatura
                      AND m.id_curso       = :id_curso
                    GROUP BY cal.fecha
                    ORDER BY cal.fecha DESC
                    LIMIT :limite";

            $stmt = $this->conexion->prepare($sql);
            $stmt->bindParam(':id_institucion', $id_institucion, PDO::PARAM_INT);
            $stmt->bindParam(':id_docente',     $id_docente,     PDO::PARAM_INT);
            $stmt->bindParam(':id_asignatura',  $id_asignatura,  PDO::PARAM_INT);
            $stmt->bindParam(':id_curso',       $id_curso,       PDO::PARAM_INT);
            $stmt->bindParam(':limite',         $limite,         PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (PDOException $e) {
            error_log('AsistenciaDocente::obtenerHistorialAsistencia -> ' . $e->getMessage());
            return [];
        }
    }
}
