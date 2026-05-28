<?php

/**
 * Modelo: HorarioModel
 * Gestiona los horarios académicos semanales.
 * Multi-tenant: todos los métodos filtran por id_institucion.
 */

require_once __DIR__ . '/../../../config/database.php';

class HorarioModel
{
    private $pdo;

    // Mapeo de número a nombre de día
    public static array $dias = [
        1 => 'Lunes',
        2 => 'Martes',
        3 => 'Miércoles',
        4 => 'Jueves',
        5 => 'Viernes',
        6 => 'Sábado',
    ];

    // Paleta de colores para asignaturas (cycling)
    public static array $colores = [
        '#4f46e5', '#10b981', '#f59e0b', '#ef4444',
        '#8b5cf6', '#06b6d4', '#ec4899', '#14b8a6',
        '#3b82f6', '#a16207',
    ];

    public function __construct()
    {
        $db = new Conexion();
        $this->pdo = $db->getConexion();
    }

    // ─────────────────────────────────────────────────────────────────────────
    //  OBTENER CURSOS ACTIVOS (para el selector del panel admin)
    // ─────────────────────────────────────────────────────────────────────────
    public function obtenerCursos(int $id_institucion): array
    {
        try {
            $sql = "SELECT id, grado, curso, anio, jornada
                    FROM curso
                    WHERE id_institucion = :id_institucion
                      AND estado = 'Activo'
                    ORDER BY grado ASC, curso ASC";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':id_institucion', $id_institucion, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('HorarioModel::obtenerCursos → ' . $e->getMessage());
            return [];
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    //  OBTENER ASIGNACIONES (DAC) DE UN CURSO — para el select del modal
    // ─────────────────────────────────────────────────────────────────────────
    public function obtenerDacPorCurso(int $id_institucion, int $id_curso): array
    {
        try {
            $sql = "SELECT dac.id                                    AS id_dac,
                           asig.id                                   AS id_asignatura,
                           asig.nombre                               AS asignatura_nombre,
                           d.id                                      AS id_docente,
                           CONCAT(d.nombres, ' ', d.apellidos)       AS docente_nombre
                    FROM docente_asignatura_curso dac
                    INNER JOIN asignatura_curso ac   ON dac.id_asignatura_curso = ac.id
                    INNER JOIN asignatura asig        ON ac.id_asignatura = asig.id
                    INNER JOIN docente d              ON dac.id_docente = d.id
                    INNER JOIN curso c                ON ac.id_curso = c.id
                    WHERE dac.id_institucion = :id_institucion
                      AND dac.estado = 'activo'
                      AND c.id = :id_curso
                    ORDER BY asig.nombre ASC";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':id_institucion', $id_institucion, PDO::PARAM_INT);
            $stmt->bindValue(':id_curso',       $id_curso,       PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('HorarioModel::obtenerDacPorCurso → ' . $e->getMessage());
            return [];
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    //  HORARIOS DE UN CURSO (para el panel admin y vista estudiante)
    // ─────────────────────────────────────────────────────────────────────────
    public function obtenerHorariosPorCurso(int $id_institucion, int $id_curso): array
    {
        try {
            $sql = "SELECT h.id,
                           h.dia_semana,
                           h.hora_inicio,
                           h.hora_fin,
                           h.aula,
                           h.estado,
                           h.id_docente_asignatura_curso AS id_dac,
                           asig.id   AS id_asignatura,
                           asig.nombre AS asignatura_nombre,
                           d.id      AS id_docente,
                           CONCAT(d.nombres, ' ', d.apellidos) AS docente_nombre,
                           c.id      AS id_curso,
                           CONCAT(c.grado, '-', c.curso)       AS curso_nombre,
                           c.anio
                    FROM horario h
                    INNER JOIN docente_asignatura_curso dac ON h.id_docente_asignatura_curso = dac.id
                    INNER JOIN asignatura_curso ac           ON dac.id_asignatura_curso = ac.id
                    INNER JOIN asignatura asig               ON ac.id_asignatura = asig.id
                    INNER JOIN docente d                     ON dac.id_docente = d.id
                    INNER JOIN curso c                       ON ac.id_curso = c.id
                    WHERE h.id_institucion = :id_institucion
                      AND c.id             = :id_curso
                      AND h.estado         = 'activo'
                    ORDER BY h.dia_semana ASC, h.hora_inicio ASC";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':id_institucion', $id_institucion, PDO::PARAM_INT);
            $stmt->bindValue(':id_curso',       $id_curso,       PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('HorarioModel::obtenerHorariosPorCurso → ' . $e->getMessage());
            return [];
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    //  HORARIOS DE UN DOCENTE (para la vista personal del docente)
    // ─────────────────────────────────────────────────────────────────────────
    public function obtenerHorariosPorDocente(int $id_institucion, int $id_docente): array
    {
        try {
            $sql = "SELECT h.id,
                           h.dia_semana,
                           h.hora_inicio,
                           h.hora_fin,
                           h.aula,
                           h.estado,
                           asig.nombre AS asignatura_nombre,
                           CONCAT(c.grado, '-', c.curso) AS curso_nombre,
                           c.anio
                    FROM horario h
                    INNER JOIN docente_asignatura_curso dac ON h.id_docente_asignatura_curso = dac.id
                    INNER JOIN asignatura_curso ac           ON dac.id_asignatura_curso = ac.id
                    INNER JOIN asignatura asig               ON ac.id_asignatura = asig.id
                    INNER JOIN curso c                       ON ac.id_curso = c.id
                    WHERE h.id_institucion = :id_institucion
                      AND dac.id_docente   = :id_docente
                      AND h.estado         = 'activo'
                    ORDER BY h.dia_semana ASC, h.hora_inicio ASC";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':id_institucion', $id_institucion, PDO::PARAM_INT);
            $stmt->bindValue(':id_docente',     $id_docente,     PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('HorarioModel::obtenerHorariosPorDocente → ' . $e->getMessage());
            return [];
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    //  HORARIOS DEL CURSO DE UN ESTUDIANTE (vista estudiante)
    // ─────────────────────────────────────────────────────────────────────────
    public function obtenerHorariosPorEstudiante(int $id_estudiante, int $id_institucion): array
    {
        try {
            $anio = (int) date('Y');
            $sql = "SELECT h.id,
                           h.dia_semana,
                           h.hora_inicio,
                           h.hora_fin,
                           h.aula,
                           asig.nombre AS asignatura_nombre,
                           CONCAT(d.nombres, ' ', d.apellidos) AS docente_nombre,
                           CONCAT(c.grado, '-', c.curso)       AS curso_nombre
                    FROM horario h
                    INNER JOIN docente_asignatura_curso dac ON h.id_docente_asignatura_curso = dac.id
                    INNER JOIN asignatura_curso ac           ON dac.id_asignatura_curso = ac.id
                    INNER JOIN asignatura asig               ON ac.id_asignatura = asig.id
                    INNER JOIN docente d                     ON dac.id_docente = d.id
                    INNER JOIN curso c                       ON ac.id_curso = c.id
                    INNER JOIN matricula m                   ON m.id_curso = c.id
                                                             AND m.id_estudiante = :id_estudiante
                                                             AND m.anio = :anio
                                                             AND m.estado != 'Retirada'
                    WHERE h.id_institucion = :id_institucion
                      AND h.estado         = 'activo'
                    ORDER BY h.dia_semana ASC, h.hora_inicio ASC";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':id_estudiante',  $id_estudiante,  PDO::PARAM_INT);
            $stmt->bindValue(':id_institucion', $id_institucion, PDO::PARAM_INT);
            $stmt->bindValue(':anio',           $anio,           PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('HorarioModel::obtenerHorariosPorEstudiante → ' . $e->getMessage());
            return [];
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    //  OBTENER UN HORARIO POR ID (para edición)
    // ─────────────────────────────────────────────────────────────────────────
    public function obtenerPorId(int $id, int $id_institucion): ?array
    {
        try {
            $stmt = $this->pdo->prepare(
                "SELECT h.*, dac.id_docente, ac.id_curso
                 FROM horario h
                 INNER JOIN docente_asignatura_curso dac ON h.id_docente_asignatura_curso = dac.id
                 INNER JOIN asignatura_curso ac           ON dac.id_asignatura_curso = ac.id
                 WHERE h.id = :id AND h.id_institucion = :id_institucion
                 LIMIT 1"
            );
            $stmt->bindValue(':id',             $id,             PDO::PARAM_INT);
            $stmt->bindValue(':id_institucion', $id_institucion, PDO::PARAM_INT);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row ?: null;
        } catch (PDOException $e) {
            error_log('HorarioModel::obtenerPorId → ' . $e->getMessage());
            return null;
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    //  VERIFICAR CONFLICTOS
    //  Retorna array con las colisiones encontradas (vacío = sin conflicto).
    // ─────────────────────────────────────────────────────────────────────────
    public function verificarConflictos(
        int     $id_institucion,
        int     $id_dac,
        int     $dia_semana,
        string  $hora_inicio,
        string  $hora_fin,
        ?int    $excluir_id = null,
        ?string $aula       = null
    ): array {
        $conflictos = [];

        // ── Validación básica de horas ────────────────────────────────────────
        if ($hora_inicio >= $hora_fin) {
            return [['tipo' => 'hora', 'mensaje' => 'La hora de inicio debe ser menor que la hora de fin.']];
        }

        // ── Obtener id_docente e id_curso del DAC ────────────────────────────
        try {
            $stmtDac = $this->pdo->prepare(
                "SELECT dac.id_docente, ac.id_curso
                 FROM docente_asignatura_curso dac
                 INNER JOIN asignatura_curso ac ON dac.id_asignatura_curso = ac.id
                 WHERE dac.id = :id_dac AND dac.id_institucion = :id_institucion
                 LIMIT 1"
            );
            $stmtDac->bindValue(':id_dac',         $id_dac,         PDO::PARAM_INT);
            $stmtDac->bindValue(':id_institucion',  $id_institucion, PDO::PARAM_INT);
            $stmtDac->execute();
            $dac = $stmtDac->fetch(PDO::FETCH_ASSOC);
            if (!$dac) return [['tipo' => 'error', 'mensaje' => 'Asignación docente-asignatura no encontrada.']];
        } catch (PDOException $e) {
            return [['tipo' => 'error', 'mensaje' => 'Error al verificar conflictos.']];
        }

        $id_docente = (int) $dac['id_docente'];
        $id_curso   = (int) $dac['id_curso'];

        // ── Cláusula de solapamiento de intervalos (estándar académico) ───────
        // Dos bloques se solapan si: inicio_nuevo < fin_existente AND fin_nuevo > inicio_existente
        $baseWhere = "h.dia_semana  = :dia
                      AND h.estado  = 'activo'
                      AND h.hora_inicio < :hora_fin
                      AND h.hora_fin    > :hora_inicio";
        if ($excluir_id !== null) {
            $baseWhere .= " AND h.id != :excluir_id";
        }

        $bindBase = function ($s) use ($dia_semana, $hora_inicio, $hora_fin, $excluir_id) {
            $s->bindValue(':dia',         $dia_semana,  PDO::PARAM_INT);
            $s->bindValue(':hora_inicio', $hora_inicio, PDO::PARAM_STR);
            $s->bindValue(':hora_fin',    $hora_fin,    PDO::PARAM_STR);
            if ($excluir_id !== null) {
                $s->bindValue(':excluir_id', $excluir_id, PDO::PARAM_INT);
            }
        };

        try {
            // ── 1. Conflicto de DOCENTE ──────────────────────────────────────
            $sqlDoc = "SELECT h.hora_inicio, h.hora_fin,
                              asig.nombre AS asignatura,
                              CONCAT(c.grado, '°', c.curso) AS curso
                       FROM horario h
                       INNER JOIN docente_asignatura_curso dac2 ON h.id_docente_asignatura_curso = dac2.id
                       INNER JOIN asignatura_curso ac2           ON dac2.id_asignatura_curso = ac2.id
                       INNER JOIN asignatura asig                ON ac2.id_asignatura = asig.id
                       INNER JOIN curso c                        ON ac2.id_curso = c.id
                       WHERE dac2.id_docente   = :id_docente
                         AND h.id_institucion  = :id_institucion
                         AND $baseWhere
                       LIMIT 1";
            $s1 = $this->pdo->prepare($sqlDoc);
            $s1->bindValue(':id_docente',     $id_docente,     PDO::PARAM_INT);
            $s1->bindValue(':id_institucion', $id_institucion, PDO::PARAM_INT);
            $bindBase($s1);
            $s1->execute();
            if ($row = $s1->fetch(PDO::FETCH_ASSOC)) {
                $conflictos[] = [
                    'tipo'    => 'docente',
                    'mensaje' => "El docente ya tiene clase de <strong>{$row['asignatura']}</strong> en {$row['curso']} de {$row['hora_inicio']} a {$row['hora_fin']}.",
                ];
            }

            // ── 2. Conflicto de CURSO ────────────────────────────────────────
            $sqlCurso = "SELECT h.hora_inicio, h.hora_fin, asig.nombre AS asignatura
                         FROM horario h
                         INNER JOIN docente_asignatura_curso dac3 ON h.id_docente_asignatura_curso = dac3.id
                         INNER JOIN asignatura_curso ac3           ON dac3.id_asignatura_curso = ac3.id
                         INNER JOIN asignatura asig                ON ac3.id_asignatura = asig.id
                         WHERE ac3.id_curso       = :id_curso
                           AND h.id_institucion   = :id_institucion2
                           AND $baseWhere
                         LIMIT 1";
            $s2 = $this->pdo->prepare($sqlCurso);
            $s2->bindValue(':id_curso',        $id_curso,       PDO::PARAM_INT);
            $s2->bindValue(':id_institucion2', $id_institucion, PDO::PARAM_INT);
            $bindBase($s2);
            $s2->execute();
            if ($row = $s2->fetch(PDO::FETCH_ASSOC)) {
                $conflictos[] = [
                    'tipo'    => 'curso',
                    'mensaje' => "El curso ya tiene <strong>{$row['asignatura']}</strong> en ese horario ({$row['hora_inicio']} – {$row['hora_fin']}).",
                ];
            }

            // ── 3. Conflicto de AULA ─────────────────────────────────────────
            $aulaLimpia = trim($aula ?? '');
            if ($aulaLimpia !== '') {
                $sqlAula = "SELECT h.hora_inicio, h.hora_fin,
                                   asig.nombre AS asignatura,
                                   CONCAT(c.grado, '°', c.curso) AS curso
                            FROM horario h
                            INNER JOIN docente_asignatura_curso dac4 ON h.id_docente_asignatura_curso = dac4.id
                            INNER JOIN asignatura_curso ac4           ON dac4.id_asignatura_curso = ac4.id
                            INNER JOIN asignatura asig                ON ac4.id_asignatura = asig.id
                            INNER JOIN curso c                        ON ac4.id_curso = c.id
                            WHERE h.aula            = :aula
                              AND h.id_institucion  = :id_institucion3
                              AND $baseWhere
                            LIMIT 1";
                $s3 = $this->pdo->prepare($sqlAula);
                $s3->bindValue(':aula',            $aulaLimpia,     PDO::PARAM_STR);
                $s3->bindValue(':id_institucion3', $id_institucion, PDO::PARAM_INT);
                $bindBase($s3);
                $s3->execute();
                if ($row = $s3->fetch(PDO::FETCH_ASSOC)) {
                    $conflictos[] = [
                        'tipo'    => 'aula',
                        'mensaje' => "El aula <strong>{$aulaLimpia}</strong> ya está ocupada por <strong>{$row['asignatura']}</strong> ({$row['curso']}) de {$row['hora_inicio']} a {$row['hora_fin']}.",
                    ];
                }
            }

        } catch (PDOException $e) {
            error_log('HorarioModel::verificarConflictos → ' . $e->getMessage());
            $conflictos[] = ['tipo' => 'error', 'mensaje' => 'Error interno al verificar conflictos.'];
        }

        return $conflictos;
    }

    // ─────────────────────────────────────────────────────────────────────────
    //  GUARDAR (INSERT)
    // ─────────────────────────────────────────────────────────────────────────
    public function guardar(
        int    $id_institucion,
        int    $id_dac,
        int    $dia_semana,
        string $hora_inicio,
        string $hora_fin,
        ?string $aula
    ): array {
        $conflictos = $this->verificarConflictos(
            $id_institucion, $id_dac, $dia_semana, $hora_inicio, $hora_fin, null, $aula
        );
        if (!empty($conflictos)) {
            return ['success' => false, 'conflictos' => $conflictos];
        }

        try {
            $stmt = $this->pdo->prepare(
                "INSERT INTO horario
                    (id_institucion, id_docente_asignatura_curso, dia_semana, hora_inicio, hora_fin, aula, estado)
                 VALUES
                    (:id_institucion, :id_dac, :dia_semana, :hora_inicio, :hora_fin, :aula, 'activo')"
            );
            $stmt->bindValue(':id_institucion', $id_institucion, PDO::PARAM_INT);
            $stmt->bindValue(':id_dac',         $id_dac,         PDO::PARAM_INT);
            $stmt->bindValue(':dia_semana',     $dia_semana,     PDO::PARAM_INT);
            $stmt->bindValue(':hora_inicio',    $hora_inicio,    PDO::PARAM_STR);
            $stmt->bindValue(':hora_fin',       $hora_fin,       PDO::PARAM_STR);
            $stmt->bindValue(':aula',           $aula ?: null,   $aula ? PDO::PARAM_STR : PDO::PARAM_NULL);
            $stmt->execute();
            return ['success' => true, 'id' => (int) $this->pdo->lastInsertId()];
        } catch (PDOException $e) {
            error_log('HorarioModel::guardar → ' . $e->getMessage());
            return ['success' => false, 'conflictos' => [['tipo' => 'error', 'mensaje' => 'Error al guardar el horario.']]];
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    //  ACTUALIZAR (UPDATE)
    // ─────────────────────────────────────────────────────────────────────────
    public function actualizar(
        int    $id,
        int    $id_institucion,
        int    $id_dac,
        int    $dia_semana,
        string $hora_inicio,
        string $hora_fin,
        ?string $aula
    ): array {
        $conflictos = $this->verificarConflictos(
            $id_institucion, $id_dac, $dia_semana, $hora_inicio, $hora_fin, $id, $aula
        );
        if (!empty($conflictos)) {
            return ['success' => false, 'conflictos' => $conflictos];
        }

        try {
            $stmt = $this->pdo->prepare(
                "UPDATE horario
                 SET id_docente_asignatura_curso = :id_dac,
                     dia_semana   = :dia_semana,
                     hora_inicio  = :hora_inicio,
                     hora_fin     = :hora_fin,
                     aula         = :aula
                 WHERE id = :id AND id_institucion = :id_institucion"
            );
            $stmt->bindValue(':id_dac',          $id_dac,         PDO::PARAM_INT);
            $stmt->bindValue(':dia_semana',       $dia_semana,     PDO::PARAM_INT);
            $stmt->bindValue(':hora_inicio',      $hora_inicio,    PDO::PARAM_STR);
            $stmt->bindValue(':hora_fin',         $hora_fin,       PDO::PARAM_STR);
            $stmt->bindValue(':aula',             $aula ?: null,   $aula ? PDO::PARAM_STR : PDO::PARAM_NULL);
            $stmt->bindValue(':id',               $id,             PDO::PARAM_INT);
            $stmt->bindValue(':id_institucion',   $id_institucion, PDO::PARAM_INT);
            $stmt->execute();
            return ['success' => true];
        } catch (PDOException $e) {
            error_log('HorarioModel::actualizar → ' . $e->getMessage());
            return ['success' => false, 'conflictos' => [['tipo' => 'error', 'mensaje' => 'Error al actualizar el horario.']]];
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    //  ELIMINAR
    // ─────────────────────────────────────────────────────────────────────────
    public function eliminar(int $id, int $id_institucion): bool
    {
        try {
            $stmt = $this->pdo->prepare(
                "DELETE FROM horario WHERE id = :id AND id_institucion = :id_institucion"
            );
            $stmt->bindValue(':id',             $id,             PDO::PARAM_INT);
            $stmt->bindValue(':id_institucion', $id_institucion, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            error_log('HorarioModel::eliminar → ' . $e->getMessage());
            return false;
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    //  ESTADÍSTICAS PARA LAS STAT-CARDS DEL PANEL
    // ─────────────────────────────────────────────────────────────────────────
    public function obtenerStats(int $id_institucion, int $id_curso): array
    {
        try {
            $stmt = $this->pdo->prepare(
                "SELECT
                     COUNT(DISTINCT h.id)          AS total_bloques,
                     COUNT(DISTINCT dac.id_docente) AS total_docentes,
                     COUNT(DISTINCT ac.id_asignatura) AS total_asignaturas,
                     SUM(TIMESTAMPDIFF(MINUTE, h.hora_inicio, h.hora_fin)) AS minutos_semana
                 FROM horario h
                 INNER JOIN docente_asignatura_curso dac ON h.id_docente_asignatura_curso = dac.id
                 INNER JOIN asignatura_curso ac           ON dac.id_asignatura_curso = ac.id
                 WHERE h.id_institucion = :id_institucion
                   AND ac.id_curso      = :id_curso
                   AND h.estado         = 'activo'"
            );
            $stmt->bindValue(':id_institucion', $id_institucion, PDO::PARAM_INT);
            $stmt->bindValue(':id_curso',       $id_curso,       PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
        } catch (PDOException $e) {
            error_log('HorarioModel::obtenerStats → ' . $e->getMessage());
            return [];
        }
    }
}
