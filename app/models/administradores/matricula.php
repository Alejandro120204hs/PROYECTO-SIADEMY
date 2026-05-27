<?php

/**
 * Modelo: Matricula
 * Gestión de matrículas académicas con aislamiento multiinstitucional.
 *
 * CORRECCIONES APLICADAS:
 *  - registrar(): usa transacción + SELECT FOR UPDATE para prevenir race condition
 *    en validación de cupo; valida que estudiante y curso pertenezcan a la institución.
 *  - listar(): agrega columna `estado` al SELECT.
 *  - listarPorCurso(): añade parámetro $id_institucion obligatorio (aislamiento).
 *  - listarMatriculaId(): añade parámetro $id_institucion obligatorio (fix IDOR).
 *  - actualizar(): verifica pertenencia institucional en el WHERE.
 *  - eliminar(): cambiado a soft-delete (UPDATE estado='Retirada') con ownership check.
 *  - die() reemplazado por error_log() + return false en todos los métodos.
 */

require_once __DIR__ . '/../../../config/database.php';

class Matricula {

    private $conexion;

    public function __construct() {
        $db = new Conexion();
        $this->conexion = $db->getConexion();
    }

    // -----------------------------------------------------------------
    // REGISTRAR
    //  - Valida que el estudiante y el curso pertenezcan a la institución
    //  - Valida duplicado y cupo dentro de una transacción con bloqueo
    //    (SELECT FOR UPDATE) para evitar race conditions concurrentes
    // -----------------------------------------------------------------
    public function registrar($data) {
        try {
            $this->conexion->beginTransaction();

            // 1. Verificar que el estudiante pertenece a la institución
            $stmtEst = $this->conexion->prepare(
                "SELECT id FROM estudiante
                 WHERE id = :id_estudiante AND id_institucion = :id_institucion
                 LIMIT 1"
            );
            $stmtEst->execute([
                ':id_estudiante' => $data['id_estudiante'],
                ':id_institucion' => $data['id_institucion'],
            ]);
            if (!$stmtEst->fetch()) {
                $this->conexion->rollBack();
                return ['success' => false, 'message' => 'El estudiante no pertenece a su institución.'];
            }

            // 2. Verificar que el curso pertenece a la institución y bloquearlo
            //    (FOR UPDATE previene que otro proceso modifique el cupo al mismo tiempo)
            $stmtCurso = $this->conexion->prepare(
                "SELECT id, cupo_maximo FROM curso
                 WHERE id = :id_curso AND id_institucion = :id_institucion
                 FOR UPDATE"
            );
            $stmtCurso->execute([
                ':id_curso'       => $data['id_curso'],
                ':id_institucion' => $data['id_institucion'],
            ]);
            $curso = $stmtCurso->fetch(PDO::FETCH_ASSOC);
            if (!$curso) {
                $this->conexion->rollBack();
                return ['success' => false, 'message' => 'El curso no pertenece a su institución.'];
            }

            // 3. Verificar cupo disponible (dentro de la transacción bloqueada)
            $stmtCupo = $this->conexion->prepare(
                "SELECT COUNT(*) AS matriculados
                 FROM matricula
                 WHERE id_curso = :id_curso AND anio = :anio AND estado = 'Activa'"
            );
            $stmtCupo->execute([
                ':id_curso' => $data['id_curso'],
                ':anio'     => $data['anio'],
            ]);
            $filaCupo = $stmtCupo->fetch(PDO::FETCH_ASSOC);
            $matriculados = (int)($filaCupo['matriculados'] ?? 0);

            if ($matriculados >= (int)$curso['cupo_maximo']) {
                $this->conexion->rollBack();
                return [
                    'success' => false,
                    'message' => 'El curso ha alcanzado su cupo máximo (' . $curso['cupo_maximo'] . ' estudiantes).',
                ];
            }

            // 4. Verificar que el estudiante no esté ya matriculado en este curso y año
            $stmtDup = $this->conexion->prepare(
                "SELECT id FROM matricula
                 WHERE id_estudiante = :id_estudiante
                   AND id_curso      = :id_curso
                   AND anio          = :anio
                   AND estado        = 'Activa'
                 LIMIT 1"
            );
            $stmtDup->execute([
                ':id_estudiante' => $data['id_estudiante'],
                ':id_curso'      => $data['id_curso'],
                ':anio'          => $data['anio'],
            ]);
            if ($stmtDup->fetch()) {
                $this->conexion->rollBack();
                return ['success' => false, 'message' => 'El estudiante ya está matriculado en este curso para el año seleccionado.'];
            }

            // 5. Registrar la matrícula
            $stmtInsert = $this->conexion->prepare(
                "INSERT INTO matricula
                     (id_institucion, anio, fecha, estado, id_estudiante, id_curso)
                 VALUES
                     (:id_institucion, :anio, :fecha, 'Activa', :id_estudiante, :id_curso)"
            );
            $stmtInsert->execute([
                ':id_institucion' => $data['id_institucion'],
                ':anio'           => $data['anio'],
                ':fecha'          => $data['fecha'],
                ':id_estudiante'  => $data['id_estudiante'],
                ':id_curso'       => $data['id_curso'],
            ]);

            $this->conexion->commit();
            return ['success' => true];

        } catch (PDOException $e) {
            $this->conexion->rollBack();
            error_log("Error en Matricula::registrar -> " . $e->getMessage());
            // Duplicado a nivel BD (constraint uk_matricula_unica)
            if ($e->getCode() === '23000') {
                return ['success' => false, 'message' => 'El estudiante ya está matriculado en este curso para el año seleccionado.'];
            }
            return ['success' => false, 'message' => 'Error al registrar la matrícula.'];
        }
    }

    // -----------------------------------------------------------------
    // LISTAR — filtra por institución, retorna todas las matrículas
    //          incluyendo su estado académico
    // -----------------------------------------------------------------
    public function listar($id_institucion) {
        try {
            $sql = "SELECT
                        m.*,
                        m.estado              AS estado_matricula,
                        e.nombres             AS estudiante_nombres,
                        e.apellidos           AS estudiante_apellidos,
                        e.documento           AS estudiante_documento,
                        c.grado,
                        c.curso               AS nombre_curso,
                        c.estado              AS estado_curso,
                        n.nombre              AS nivel_academico
                    FROM matricula m
                    INNER JOIN estudiante     e ON m.id_estudiante      = e.id
                    INNER JOIN curso          c ON m.id_curso           = c.id
                    INNER JOIN nivel_academico n ON c.id_nivel_academico = n.id
                    WHERE m.id_institucion = :id_institucion
                    ORDER BY m.anio DESC, c.grado ASC, c.curso ASC";

            $stmt = $this->conexion->prepare($sql);
            $stmt->bindParam(':id_institucion', $id_institucion);
            $stmt->execute();
            return $stmt->fetchAll();

        } catch (PDOException $e) {
            error_log("Error en Matricula::listar -> " . $e->getMessage());
            return [];
        }
    }

    // -----------------------------------------------------------------
    // LISTAR POR CURSO — requiere id_institucion para aislamiento
    // -----------------------------------------------------------------
    public function listarPorCurso($id_curso, $anio, $id_institucion) {
        try {
            $sql = "SELECT
                        m.*,
                        e.nombres             AS estudiante_nombres,
                        e.apellidos           AS estudiante_apellidos,
                        e.documento           AS estudiante_documento,
                        e.foto
                    FROM matricula m
                    INNER JOIN estudiante e ON m.id_estudiante = e.id
                    WHERE m.id_curso        = :id_curso
                      AND m.anio            = :anio
                      AND m.id_institucion  = :id_institucion
                      AND m.estado          = 'Activa'
                    ORDER BY e.apellidos ASC, e.nombres ASC";

            $stmt = $this->conexion->prepare($sql);
            $stmt->bindParam(':id_curso',       $id_curso,       PDO::PARAM_INT);
            $stmt->bindParam(':anio',           $anio);
            $stmt->bindParam(':id_institucion', $id_institucion, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll();

        } catch (PDOException $e) {
            error_log("Error en Matricula::listarPorCurso -> " . $e->getMessage());
            return [];
        }
    }

    // -----------------------------------------------------------------
    // LISTAR POR ID — requiere id_institucion para prevenir IDOR
    // -----------------------------------------------------------------
    public function listarMatriculaId($id, $id_institucion) {
        try {
            $sql = "SELECT
                        m.*,
                        m.estado              AS estado_matricula,
                        e.nombres             AS estudiante_nombres,
                        e.apellidos           AS estudiante_apellidos,
                        e.documento           AS estudiante_documento,
                        c.grado,
                        c.curso               AS nombre_curso,
                        n.nombre              AS nivel_academico
                    FROM matricula m
                    INNER JOIN estudiante     e ON m.id_estudiante      = e.id
                    INNER JOIN curso          c ON m.id_curso           = c.id
                    INNER JOIN nivel_academico n ON c.id_nivel_academico = n.id
                    WHERE m.id             = :id
                      AND m.id_institucion = :id_institucion
                    LIMIT 1";

            $stmt = $this->conexion->prepare($sql);
            $stmt->bindParam(':id',            $id,            PDO::PARAM_INT);
            $stmt->bindParam(':id_institucion', $id_institucion, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch() ?: null;

        } catch (PDOException $e) {
            error_log("Error en Matricula::listarMatriculaId -> " . $e->getMessage());
            return null;
        }
    }

    // -----------------------------------------------------------------
    // ACTUALIZAR — verifica pertenencia institucional antes de modificar
    // -----------------------------------------------------------------
    public function actualizar($data) {
        try {
            // 1. Verificar que la matrícula pertenece a la institución (ownership check)
            $stmtOwn = $this->conexion->prepare(
                "SELECT id FROM matricula
                 WHERE id = :id AND id_institucion = :id_institucion
                 LIMIT 1"
            );
            $stmtOwn->execute([
                ':id'             => $data['id'],
                ':id_institucion' => $data['id_institucion'],
            ]);
            if (!$stmtOwn->fetch()) {
                return ['success' => false, 'message' => 'La matrícula no pertenece a su institución.'];
            }

            // 2. Verificar que el nuevo estudiante pertenece a la institución
            $stmtEst = $this->conexion->prepare(
                "SELECT id FROM estudiante
                 WHERE id = :id_estudiante AND id_institucion = :id_institucion
                 LIMIT 1"
            );
            $stmtEst->execute([
                ':id_estudiante'  => $data['id_estudiante'],
                ':id_institucion' => $data['id_institucion'],
            ]);
            if (!$stmtEst->fetch()) {
                return ['success' => false, 'message' => 'El estudiante no pertenece a su institución.'];
            }

            // 3. Verificar que el nuevo curso pertenece a la institución
            $stmtCurso = $this->conexion->prepare(
                "SELECT id FROM curso
                 WHERE id = :id_curso AND id_institucion = :id_institucion
                 LIMIT 1"
            );
            $stmtCurso->execute([
                ':id_curso'       => $data['id_curso'],
                ':id_institucion' => $data['id_institucion'],
            ]);
            if (!$stmtCurso->fetch()) {
                return ['success' => false, 'message' => 'El curso no pertenece a su institución.'];
            }

            // 4. Verificar duplicado (excluyendo la matrícula actual)
            $stmtDup = $this->conexion->prepare(
                "SELECT id FROM matricula
                 WHERE id_estudiante = :id_estudiante
                   AND id_curso      = :id_curso
                   AND anio          = :anio
                   AND id           != :id
                 LIMIT 1"
            );
            $stmtDup->execute([
                ':id_estudiante' => $data['id_estudiante'],
                ':id_curso'      => $data['id_curso'],
                ':anio'          => $data['anio'],
                ':id'            => $data['id'],
            ]);
            if ($stmtDup->fetch()) {
                return ['success' => false, 'message' => 'El estudiante ya está matriculado en este curso para el año seleccionado.'];
            }

            // 5. Ejecutar el UPDATE con ownership en el WHERE
            $sql = "UPDATE matricula
                    SET anio          = :anio,
                        fecha         = :fecha,
                        estado        = :estado,
                        id_estudiante = :id_estudiante,
                        id_curso      = :id_curso
                    WHERE id             = :id
                      AND id_institucion = :id_institucion";

            $stmt = $this->conexion->prepare($sql);
            $ok = $stmt->execute([
                ':anio'           => $data['anio'],
                ':fecha'          => $data['fecha'],
                ':estado'         => $data['estado'] ?? 'Activa',
                ':id_estudiante'  => $data['id_estudiante'],
                ':id_curso'       => $data['id_curso'],
                ':id'             => $data['id'],
                ':id_institucion' => $data['id_institucion'],
            ]);

            if ($ok) {
                return ['success' => true];
            }
            return ['success' => false, 'message' => 'Error al actualizar la matrícula.'];

        } catch (PDOException $e) {
            error_log("Error en Matricula::actualizar -> " . $e->getMessage());
            if ($e->getCode() === '23000') {
                return ['success' => false, 'message' => 'El estudiante ya está matriculado en este curso para el año seleccionado.'];
            }
            return ['success' => false, 'message' => 'Error en la base de datos.'];
        }
    }

    // -----------------------------------------------------------------
    // ELIMINAR — soft delete: cambia estado a 'Retirada'
    //            preserva el historial académico del estudiante
    //            Verifica pertenencia institucional antes de operar
    // -----------------------------------------------------------------
    public function eliminar($id, $id_institucion) {
        try {
            $sql  = "UPDATE matricula
                     SET estado = 'Retirada'
                     WHERE id             = :id
                       AND id_institucion = :id_institucion";

            $stmt = $this->conexion->prepare($sql);
            $ok   = $stmt->execute([
                ':id'             => $id,
                ':id_institucion' => $id_institucion,
            ]);

            // rowCount() = 0 puede significar que el registro no existe o no pertenece a esta institución
            if (!$ok || $stmt->rowCount() === 0) {
                return false;
            }
            return true;

        } catch (PDOException $e) {
            error_log("Error en Matricula::eliminar -> " . $e->getMessage());
            return false;
        }
    }

    // -----------------------------------------------------------------
    // ESTADÍSTICAS — filtra por institución y año
    // -----------------------------------------------------------------
    public function obtenerEstadisticas($id_institucion, $anio) {
        try {
            $sql = "SELECT
                        COUNT(DISTINCT m.id)           AS total_matriculas,
                        COUNT(DISTINCT m.id_estudiante) AS total_estudiantes,
                        COUNT(DISTINCT m.id_curso)      AS cursos_con_matriculas
                    FROM matricula m
                    WHERE m.id_institucion = :id_institucion
                      AND m.anio           = :anio
                      AND m.estado         = 'Activa'";

            $stmt = $this->conexion->prepare($sql);
            $stmt->bindParam(':id_institucion', $id_institucion);
            $stmt->bindParam(':anio',           $anio);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC) ?: ['total_matriculas' => 0, 'total_estudiantes' => 0, 'cursos_con_matriculas' => 0];

        } catch (PDOException $e) {
            error_log("Error en Matricula::obtenerEstadisticas -> " . $e->getMessage());
            return ['total_matriculas' => 0, 'total_estudiantes' => 0, 'cursos_con_matriculas' => 0];
        }
    }
}
