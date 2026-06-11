<?php

require_once __DIR__ . '/../../config/database.php';

class Notificacion {

    private $conexion;

    public function __construct() {
        $db = new Conexion();
        $this->conexion = $db->getConexion();
    }

    // ── ESCRITURA ─────────────────────────────────────────────────────────────

    /**
     * Crear una notificación individual.
     * Retorna el ID insertado, o false si falla.
     */
    public function crear(array $datos) {
        try {
            $sql = "INSERT INTO notificacion
                        (id_institucion, id_destinatario, tipo, titulo, mensaje,
                         url_accion, entidad_tipo, entidad_id)
                    VALUES
                        (:id_institucion, :id_destinatario, :tipo, :titulo, :mensaje,
                         :url_accion, :entidad_tipo, :entidad_id)";

            $stmt = $this->conexion->prepare($sql);
            $stmt->bindParam(':id_institucion',  $datos['id_institucion'],  PDO::PARAM_INT);
            $stmt->bindParam(':id_destinatario', $datos['id_destinatario'], PDO::PARAM_INT);
            $stmt->bindParam(':tipo',            $datos['tipo'],            PDO::PARAM_STR);
            $stmt->bindParam(':titulo',          $datos['titulo'],          PDO::PARAM_STR);
            $stmt->bindParam(':mensaje',         $datos['mensaje'],         PDO::PARAM_STR);

            $urlAccion   = $datos['url_accion']   ?? null;
            $entidadTipo = $datos['entidad_tipo'] ?? null;
            $entidadId   = isset($datos['entidad_id']) ? (int)$datos['entidad_id'] : null;

            $stmt->bindValue(':url_accion',   $urlAccion,   $urlAccion   !== null ? PDO::PARAM_STR : PDO::PARAM_NULL);
            $stmt->bindValue(':entidad_tipo', $entidadTipo, $entidadTipo !== null ? PDO::PARAM_STR : PDO::PARAM_NULL);
            $stmt->bindValue(':entidad_id',   $entidadId,   $entidadId   !== null ? PDO::PARAM_INT : PDO::PARAM_NULL);

            $stmt->execute();
            return (int)$this->conexion->lastInsertId();

        } catch (PDOException $e) {
            error_log('[Notificacion::crear] ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Insertar múltiples notificaciones en una sola transacción (fanout on write).
     * Todos los destinatarios reciben el mismo contenido con filas independientes.
     *
     * @param array[] $notificaciones  Array de arrays con las mismas claves que crear().
     * @return int Número de filas insertadas exitosamente.
     */
    public function crearBatch(array $notificaciones) {
        if (empty($notificaciones)) {
            return 0;
        }

        $inserted = 0;
        try {
            $this->conexion->beginTransaction();

            $sql = "INSERT INTO notificacion
                        (id_institucion, id_destinatario, tipo, titulo, mensaje,
                         url_accion, entidad_tipo, entidad_id)
                    VALUES
                        (:id_institucion, :id_destinatario, :tipo, :titulo, :mensaje,
                         :url_accion, :entidad_tipo, :entidad_id)";

            $stmt = $this->conexion->prepare($sql);

            foreach ($notificaciones as $datos) {
                $urlAccion   = $datos['url_accion']   ?? null;
                $entidadTipo = $datos['entidad_tipo'] ?? null;
                $entidadId   = isset($datos['entidad_id']) ? (int)$datos['entidad_id'] : null;

                $stmt->bindParam(':id_institucion',  $datos['id_institucion'],  PDO::PARAM_INT);
                $stmt->bindParam(':id_destinatario', $datos['id_destinatario'], PDO::PARAM_INT);
                $stmt->bindParam(':tipo',            $datos['tipo'],            PDO::PARAM_STR);
                $stmt->bindParam(':titulo',          $datos['titulo'],          PDO::PARAM_STR);
                $stmt->bindParam(':mensaje',         $datos['mensaje'],         PDO::PARAM_STR);
                $stmt->bindValue(':url_accion',   $urlAccion,   $urlAccion   !== null ? PDO::PARAM_STR : PDO::PARAM_NULL);
                $stmt->bindValue(':entidad_tipo', $entidadTipo, $entidadTipo !== null ? PDO::PARAM_STR : PDO::PARAM_NULL);
                $stmt->bindValue(':entidad_id',   $entidadId,   $entidadId   !== null ? PDO::PARAM_INT : PDO::PARAM_NULL);

                if ($stmt->execute()) {
                    $inserted++;
                }
            }

            $this->conexion->commit();
            return $inserted;

        } catch (PDOException $e) {
            if ($this->conexion->inTransaction()) {
                $this->conexion->rollBack();
            }
            error_log('[Notificacion::crearBatch] ' . $e->getMessage());
            return 0;
        }
    }

    // ── LECTURA ───────────────────────────────────────────────────────────────

    /**
     * Listar notificaciones de un usuario ordenadas por fecha desc.
     * Excluye las descartadas.
     */
    public function listarParaUsuario($id_usuario, $id_institucion, $limit = 50) {
        try {
            $sql = "SELECT *
                    FROM notificacion
                    WHERE id_destinatario = :id_usuario
                      AND id_institucion  = :id_institucion
                      AND descartada      = 0
                    ORDER BY created_at DESC
                    LIMIT :limit";

            $stmt = $this->conexion->prepare($sql);
            $stmt->bindParam(':id_usuario',     $id_usuario,     PDO::PARAM_INT);
            $stmt->bindParam(':id_institucion', $id_institucion, PDO::PARAM_INT);
            $stmt->bindParam(':limit',          $limit,          PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            error_log('[Notificacion::listarParaUsuario] ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Contar notificaciones no leídas (valor del badge).
     */
    public function contarNoLeidas($id_usuario, $id_institucion) {
        try {
            $sql = "SELECT COUNT(*) AS total
                    FROM notificacion
                    WHERE id_destinatario = :id_usuario
                      AND id_institucion  = :id_institucion
                      AND leida           = 0
                      AND descartada      = 0";

            $stmt = $this->conexion->prepare($sql);
            $stmt->bindParam(':id_usuario',     $id_usuario,     PDO::PARAM_INT);
            $stmt->bindParam(':id_institucion', $id_institucion, PDO::PARAM_INT);
            $stmt->execute();

            $fila = $stmt->fetch(PDO::FETCH_ASSOC);
            return (int)($fila['total'] ?? 0);

        } catch (PDOException $e) {
            error_log('[Notificacion::contarNoLeidas] ' . $e->getMessage());
            return 0;
        }
    }

    // ── ACTUALIZACIÓN DE ESTADO ───────────────────────────────────────────────

    /**
     * Marcar una notificación como leída.
     * El WHERE incluye id_destinatario para evitar que otro usuario marque ajenas.
     */
    public function marcarLeida($id, $id_usuario) {
        try {
            $sql = "UPDATE notificacion
                    SET leida = 1
                    WHERE id              = :id
                      AND id_destinatario = :id_usuario";

            $stmt = $this->conexion->prepare($sql);
            $stmt->bindParam(':id',         $id,         PDO::PARAM_INT);
            $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);

            return $stmt->execute();

        } catch (PDOException $e) {
            error_log('[Notificacion::marcarLeida] ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Marcar todas las notificaciones no leídas de un usuario como leídas.
     */
    public function marcarTodasLeidas($id_usuario, $id_institucion) {
        try {
            $sql = "UPDATE notificacion
                    SET leida = 1
                    WHERE id_destinatario = :id_usuario
                      AND id_institucion  = :id_institucion
                      AND leida           = 0
                      AND descartada      = 0";

            $stmt = $this->conexion->prepare($sql);
            $stmt->bindParam(':id_usuario',     $id_usuario,     PDO::PARAM_INT);
            $stmt->bindParam(':id_institucion', $id_institucion, PDO::PARAM_INT);

            return $stmt->execute();

        } catch (PDOException $e) {
            error_log('[Notificacion::marcarTodasLeidas] ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Descartar (soft-hide) una notificación.
     * También la marca como leída para que no sume al badge.
     */
    public function descartar($id, $id_usuario) {
        try {
            $sql = "UPDATE notificacion
                    SET descartada = 1,
                        leida      = 1
                    WHERE id              = :id
                      AND id_destinatario = :id_usuario";

            $stmt = $this->conexion->prepare($sql);
            $stmt->bindParam(':id',         $id,         PDO::PARAM_INT);
            $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);

            return $stmt->execute();

        } catch (PDOException $e) {
            error_log('[Notificacion::descartar] ' . $e->getMessage());
            return false;
        }
    }

    // ── RESOLUCIÓN DE DESTINATARIOS ───────────────────────────────────────────

    /**
     * Obtener id_usuario de un estudiante (tabla estudiante → usuario).
     */
    public function obtenerIdUsuarioPorEstudiante($id_estudiante) {
        try {
            $sql = "SELECT id_usuario FROM estudiante WHERE id = :id";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindParam(':id', $id_estudiante, PDO::PARAM_INT);
            $stmt->execute();
            $fila = $stmt->fetch(PDO::FETCH_ASSOC);
            return $fila ? (int)$fila['id_usuario'] : null;

        } catch (PDOException $e) {
            error_log('[Notificacion::obtenerIdUsuarioPorEstudiante] ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Obtener id_usuario del acudiente de un estudiante.
     * Cadena: estudiante.id_acudiente → acudiente.id_usuario
     */
    public function obtenerIdUsuarioAcudienteDeEstudiante($id_estudiante) {
        try {
            $sql = "SELECT a.id_usuario
                    FROM estudiante e
                    INNER JOIN acudiente a ON e.id_acudiente = a.id
                    WHERE e.id = :id_estudiante";

            $stmt = $this->conexion->prepare($sql);
            $stmt->bindParam(':id_estudiante', $id_estudiante, PDO::PARAM_INT);
            $stmt->execute();
            $fila = $stmt->fetch(PDO::FETCH_ASSOC);
            return $fila ? (int)$fila['id_usuario'] : null;

        } catch (PDOException $e) {
            error_log('[Notificacion::obtenerIdUsuarioAcudienteDeEstudiante] ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Obtener id_usuario del docente de una actividad.
     * Cadena: actividad.id_docente → docente.id_usuario
     */
    public function obtenerIdUsuarioDocenteDeActividad($id_actividad) {
        try {
            $sql = "SELECT d.id_usuario
                    FROM actividad a
                    INNER JOIN docente d ON a.id_docente = d.id
                    WHERE a.id = :id_actividad";

            $stmt = $this->conexion->prepare($sql);
            $stmt->bindParam(':id_actividad', $id_actividad, PDO::PARAM_INT);
            $stmt->execute();
            $fila = $stmt->fetch(PDO::FETCH_ASSOC);
            return $fila ? (int)$fila['id_usuario'] : null;

        } catch (PDOException $e) {
            error_log('[Notificacion::obtenerIdUsuarioDocenteDeActividad] ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Obtener los id_usuario de todos los estudiantes matriculados activamente
     * en un id_asignatura_curso dado. Usado para fanout de actividad_nueva.
     */
    public function obtenerEstudiantesPorAsignaturaCurso($id_asignatura_curso, $id_institucion, $anio) {
        try {
            $sql = "SELECT DISTINCT u.id AS id_usuario
                    FROM asignatura_curso ac
                    INNER JOIN matricula m     ON m.id_curso       = ac.id_curso
                    INNER JOIN estudiante e    ON m.id_estudiante  = e.id
                    INNER JOIN usuario u       ON e.id_usuario     = u.id
                    WHERE ac.id              = :id_asignatura_curso
                      AND m.anio            = :anio
                      AND m.estado         != 'Retirada'
                      AND u.id_institucion  = :id_institucion";

            $stmt = $this->conexion->prepare($sql);
            $stmt->bindParam(':id_asignatura_curso', $id_asignatura_curso, PDO::PARAM_INT);
            $stmt->bindParam(':anio',                $anio,                PDO::PARAM_INT);
            $stmt->bindParam(':id_institucion',      $id_institucion,      PDO::PARAM_INT);
            $stmt->execute();

            return array_column($stmt->fetchAll(PDO::FETCH_ASSOC), 'id_usuario');

        } catch (PDOException $e) {
            error_log('[Notificacion::obtenerEstudiantesPorAsignaturaCurso] ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Obtener el contexto completo de una entrega para generar notificaciones.
     * Retorna: id_usuario_estudiante, id_acudiente_usuario, id_actividad, titulo_actividad, id_asignatura_curso
     */
    public function obtenerContextoDeEntrega($id_entrega) {
        try {
            $sql = "SELECT
                        u.id              AS id_usuario_estudiante,
                        a2.id_usuario     AS id_usuario_acudiente,
                        act.id            AS id_actividad,
                        act.titulo        AS titulo_actividad,
                        act.id_asignatura_curso,
                        act.id_institucion
                    FROM entrega_actividad ea
                    INNER JOIN estudiante e    ON ea.id_estudiante = e.id
                    INNER JOIN usuario u       ON e.id_usuario     = u.id
                    INNER JOIN actividad act   ON ea.id_actividad  = act.id
                    LEFT  JOIN acudiente a2    ON e.id_acudiente   = a2.id
                    WHERE ea.id = :id_entrega";

            $stmt = $this->conexion->prepare($sql);
            $stmt->bindParam(':id_entrega', $id_entrega, PDO::PARAM_INT);
            $stmt->execute();

            $fila = $stmt->fetch(PDO::FETCH_ASSOC);
            return $fila ?: null;

        } catch (PDOException $e) {
            error_log('[Notificacion::obtenerContextoDeEntrega] ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Obtener IDs de usuarios (estudiantes) activos de una institución.
     * Si $grado no es null/vacío, filtra por ese grado en la tabla curso.
     * Retorna array de id_usuario.
     */
    public function obtenerEstudiantesInstitucion($id_institucion, $grado = null) {
        try {
            $sql = "SELECT DISTINCT u.id AS id_usuario
                    FROM matricula m
                    INNER JOIN estudiante e  ON m.id_estudiante = e.id
                    INNER JOIN usuario u     ON e.id_usuario    = u.id
                    INNER JOIN curso c       ON m.id_curso      = c.id
                    WHERE m.anio            = :anio
                      AND m.estado         != 'Retirada'
                      AND u.id_institucion  = :id_institucion";

            if (!empty($grado)) {
                $sql .= " AND c.grado = :grado";
            }

            $stmt = $this->conexion->prepare($sql);
            $anio = (int)date('Y');
            $stmt->bindParam(':anio',           $anio,           PDO::PARAM_INT);
            $stmt->bindParam(':id_institucion', $id_institucion, PDO::PARAM_INT);
            if (!empty($grado)) {
                $stmt->bindParam(':grado', $grado, PDO::PARAM_STR);
            }
            $stmt->execute();

            return array_column($stmt->fetchAll(PDO::FETCH_ASSOC), 'id_usuario');

        } catch (PDOException $e) {
            error_log('[Notificacion::obtenerEstudiantesInstitucion] ' . $e->getMessage());
            return [];
        }
    }

    public function obtenerDocentesInstitucion(int $id_institucion): array
    {
        try {
            $sql  = "SELECT DISTINCT d.id_usuario
                     FROM docente d
                     WHERE d.id_institucion = :id_institucion";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindParam(':id_institucion', $id_institucion, PDO::PARAM_INT);
            $stmt->execute();
            return array_column($stmt->fetchAll(PDO::FETCH_ASSOC), 'id_usuario');
        } catch (PDOException $e) {
            error_log('[Notificacion::obtenerDocentesInstitucion] ' . $e->getMessage());
            return [];
        }
    }

    public function obtenerAcudientesInstitucion(int $id_institucion): array
    {
        try {
            $sql  = "SELECT DISTINCT a.id_usuario
                     FROM acudiente a
                     WHERE a.id_institucion = :id_institucion";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindParam(':id_institucion', $id_institucion, PDO::PARAM_INT);
            $stmt->execute();
            return array_column($stmt->fetchAll(PDO::FETCH_ASSOC), 'id_usuario');
        } catch (PDOException $e) {
            error_log('[Notificacion::obtenerAcudientesInstitucion] ' . $e->getMessage());
            return [];
        }
    }
}
