<?php

/**
 * Modelo: Docente
 * Gestión de docentes por institución.
 *
 * CORRECCIONES APLICADAS:
 *  - registrar() usa beginTransaction/commit/rollBack para garantizar integridad
 *    entre INSERT usuario + INSERT docente.
 *  - die() reemplazado por error_log() + return false para no exponer estructura de BD.
 */

require_once __DIR__ . '/../../../config/database.php';

class Docente {

    private $conexion;

    public function __construct() {
        $db = new Conexion();
        $this->conexion = $db->getConexion();
    }

    // -----------------------------------------------------------------
    // REGISTRAR — transacción protege el doble INSERT usuario + docente
    // -----------------------------------------------------------------
    public function registrar($data) {
        try {
            $this->conexion->beginTransaction();

            // 1. Insertar en tabla usuario
            $insertarUsuario = "INSERT INTO usuario
                                    (id_institucion, correo, clave, rol, estado)
                                VALUES
                                    (:id_institucion, :correo, :clave, 'Docente', 'Activo')";

            $stmtUsuario = $this->conexion->prepare($insertarUsuario);
            $stmtUsuario->bindParam(':id_institucion', $data['id_institucion']);
            $stmtUsuario->bindParam(':correo',         $data['correo']);
            $clave = password_hash($data['documento'], PASSWORD_DEFAULT);
            $stmtUsuario->bindParam(':clave', $clave);
            $stmtUsuario->execute();

            $id_usuario = $this->conexion->lastInsertId();

            // 2. Insertar en tabla docente
            $insertarDocente = "INSERT INTO docente
                                    (id_institucion, id_usuario, nombres, apellidos,
                                     tipo_documento, documento, fecha_nacimiento, genero,
                                     telefono, direccion, ciudad, profesion,
                                     tipo_contrato, fecha_ingreso, fecha_fin_contrato, foto)
                                VALUES
                                    (:id_institucion, :id_usuario, :nombres, :apellidos,
                                     :tipo_documento, :documento, :fecha_nacimiento, :genero,
                                     :telefono, :direccion, :ciudad, :profesion,
                                     :tipo_contrato, :fecha_ingreso, :fecha_fin_contrato, :foto)";

            $stmtDocente = $this->conexion->prepare($insertarDocente);
            $stmtDocente->bindParam(':id_institucion',    $data['id_institucion']);
            $stmtDocente->bindParam(':id_usuario',        $id_usuario);
            $stmtDocente->bindParam(':nombres',           $data['nombres']);
            $stmtDocente->bindParam(':apellidos',         $data['apellidos']);
            $stmtDocente->bindParam(':tipo_documento',    $data['tipo_documento']);
            $stmtDocente->bindParam(':documento',         $data['documento']);
            $stmtDocente->bindParam(':fecha_nacimiento',  $data['fecha_nacimiento']);
            $stmtDocente->bindParam(':genero',            $data['genero']);
            $stmtDocente->bindParam(':telefono',          $data['telefono']);
            $stmtDocente->bindParam(':direccion',         $data['direccion']);
            $stmtDocente->bindParam(':ciudad',            $data['ciudad']);
            $stmtDocente->bindParam(':profesion',         $data['profesion']);
            $stmtDocente->bindParam(':tipo_contrato',     $data['tipo_contrato']);
            $stmtDocente->bindParam(':fecha_ingreso',     $data['fecha_ingreso']);
            $stmtDocente->bindParam(':fecha_fin_contrato',$data['fecha_fin_contrato']);
            $stmtDocente->bindParam(':foto',              $data['foto']);
            $stmtDocente->execute();

            $this->conexion->commit();
            return true;

        } catch (PDOException $e) {
            $this->conexion->rollBack();
            error_log("Error en Docente::registrar -> " . $e->getMessage());
            return false;
        }
    }

    // -----------------------------------------------------------------
    // LISTAR — filtra siempre por id_institucion (aislamiento multi-tenant)
    // -----------------------------------------------------------------
    public function listar($id_institucion) {
        try {
            $consultar = "SELECT
                              docente.*,
                              usuario.correo AS correo,
                              usuario.estado AS estado
                          FROM docente
                          INNER JOIN usuario ON docente.id_usuario = usuario.id
                          WHERE docente.id_institucion = :id_institucion
                          ORDER BY apellidos ASC";

            $stmt = $this->conexion->prepare($consultar);
            $stmt->bindParam(':id_institucion', $id_institucion);
            $stmt->execute();
            return $stmt->fetchAll();

        } catch (PDOException $e) {
            error_log("Error en Docente::listar -> " . $e->getMessage());
            return [];
        }
    }

    // -----------------------------------------------------------------
    // LISTAR POR ID
    // -----------------------------------------------------------------
    public function listarId($id) {
        try {
            $consultar = "SELECT
                              docente.*,
                              usuario.correo AS correo,
                              usuario.estado AS estado
                          FROM docente
                          INNER JOIN usuario ON docente.id_usuario = usuario.id
                          WHERE docente.id = :id
                          LIMIT 1";

            $stmt = $this->conexion->prepare($consultar);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            return $stmt->fetch();

        } catch (PDOException $e) {
            error_log("Error en Docente::listarId -> " . $e->getMessage());
            return null;
        }
    }

    // -----------------------------------------------------------------
    // ACTUALIZAR — transacción protege el doble UPDATE usuario + docente
    // -----------------------------------------------------------------
    public function actualizar($data) {
        try {
            $this->conexion->beginTransaction();

            // Actualizar usuario
            $actualizarUsuario = "UPDATE usuario
                                  SET correo = :correo, estado = :estado
                                  WHERE id = :id_usuario";
            $stmtU = $this->conexion->prepare($actualizarUsuario);
            $stmtU->bindParam(':correo',    $data['correo']);
            $stmtU->bindParam(':estado',    $data['estado']);
            $stmtU->bindParam(':id_usuario',$data['id_usuario']);
            $stmtU->execute();

            // Actualizar docente
            $actualizarDocente = "UPDATE docente
                                  SET nombres           = :nombres,
                                      apellidos         = :apellidos,
                                      tipo_documento    = :tipo_documento,
                                      fecha_nacimiento  = :fecha_nacimiento,
                                      genero            = :genero,
                                      telefono          = :telefono,
                                      direccion         = :direccion,
                                      ciudad            = :ciudad,
                                      profesion         = :profesion,
                                      tipo_contrato     = :tipo_contrato,
                                      fecha_ingreso     = :fecha_ingreso,
                                      fecha_fin_contrato= :fecha_fin_contrato
                                  WHERE id_usuario = :id_usuario";
            $stmtD = $this->conexion->prepare($actualizarDocente);
            $stmtD->bindParam(':id_usuario',        $data['id_usuario']);
            $stmtD->bindParam(':nombres',           $data['nombres']);
            $stmtD->bindParam(':apellidos',         $data['apellidos']);
            $stmtD->bindParam(':tipo_documento',    $data['tipo_documento']);
            $stmtD->bindParam(':fecha_nacimiento',  $data['fecha_nacimiento']);
            $stmtD->bindParam(':genero',            $data['genero']);
            $stmtD->bindParam(':telefono',          $data['telefono']);
            $stmtD->bindParam(':direccion',         $data['direccion']);
            $stmtD->bindParam(':ciudad',            $data['ciudad']);
            $stmtD->bindParam(':profesion',         $data['profesion']);
            $stmtD->bindParam(':tipo_contrato',     $data['tipo_contrato']);
            $stmtD->bindParam(':fecha_ingreso',     $data['fecha_ingreso']);
            $stmtD->bindParam(':fecha_fin_contrato',$data['fecha_fin_contrato']);
            $stmtD->execute();

            $this->conexion->commit();
            return true;

        } catch (PDOException $e) {
            $this->conexion->rollBack();
            error_log("Error en Docente::actualizar -> " . $e->getMessage());
            return false;
        }
    }

    // -----------------------------------------------------------------
    // ELIMINAR (soft delete)
    // -----------------------------------------------------------------
    public function eliminar($id) {
        try {
            $sql  = "UPDATE usuario SET estado = 'Inactivo' WHERE id = :id";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindParam(':id', $id);
            return $stmt->execute();

        } catch (PDOException $e) {
            error_log("Error en Docente::eliminar -> " . $e->getMessage());
            return false;
        }
    }

    // -----------------------------------------------------------------
    // ASIGNATURAS Y CURSOS ASIGNADOS AL DOCENTE
    // -----------------------------------------------------------------
    public function obtenerAsignaciones($id_docente) {
        try {
            $sql = "SELECT
                        a.nombre  AS nombre_asignatura,
                        c.curso,
                        c.jornada,
                        dac.estado,
                        (SELECT COUNT(*) FROM matricula m WHERE m.id_curso = c.id AND m.estado = 'Activa') AS total_estudiantes
                    FROM docente_asignatura_curso dac
                    INNER JOIN asignatura_curso ac ON dac.id_asignatura_curso = ac.id
                    INNER JOIN asignatura a         ON ac.id_asignatura = a.id
                    INNER JOIN curso c              ON ac.id_curso = c.id
                    WHERE dac.id_docente = :id_docente AND dac.estado = 'activo'
                    ORDER BY c.curso ASC, a.nombre ASC";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindParam(':id_docente', $id_docente, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en Docente::obtenerAsignaciones -> " . $e->getMessage());
            return [];
        }
    }

    // -----------------------------------------------------------------
    // CONTAR — filtra por institución y usuarios Activos
    // -----------------------------------------------------------------
    public function contar($id_institucion) {
        try {
            $sql  = "SELECT COUNT(*) AS total
                     FROM docente
                     INNER JOIN usuario ON docente.id_usuario = usuario.id
                     WHERE docente.id_institucion = :id_institucion
                       AND usuario.estado = 'Activo'";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindParam(':id_institucion', $id_institucion);
            $stmt->execute();
            $fila = $stmt->fetch();
            return $fila['total'] ?? 0;

        } catch (PDOException $e) {
            error_log("Error en Docente::contar -> " . $e->getMessage());
            return 0;
        }
    }
}
