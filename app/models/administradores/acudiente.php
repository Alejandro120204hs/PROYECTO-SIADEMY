<?php

/**
 * Modelo: Acudiente
 * Gestión de acudientes por institución.
 *
 * CORRECCIONES APLICADAS:
 *  - registrar() usa beginTransaction/commit/rollBack para garantizar integridad
 *    entre INSERT usuario + INSERT acudiente.
 *  - die() reemplazado por error_log() + return false.
 *  - acutalizar() (typo original conservado como alias) usa transacción.
 */

require_once __DIR__ . '/../../../config/database.php';

class Acudiente {

    private $conexion;

    public function __construct() {
        $db = new Conexion();
        $this->conexion = $db->getConexion();
    }

    // -----------------------------------------------------------------
    // REGISTRAR — transacción protege el doble INSERT usuario + acudiente
    // -----------------------------------------------------------------
    public function registrar($data) {
        try {
            $this->conexion->beginTransaction();

            // 1. Insertar en tabla usuario
            $insertarUsuario = "INSERT INTO usuario
                                    (id_institucion, correo, clave, rol, estado)
                                VALUES
                                    (:id_institucion, :correo, :clave, 'Acudiente', 'Activo')";

            $stmtUsuario = $this->conexion->prepare($insertarUsuario);
            $stmtUsuario->bindParam(':id_institucion', $data['id_institucion']);
            $stmtUsuario->bindParam(':correo',         $data['correo']);
            $clave = password_hash($data['documento'], PASSWORD_DEFAULT);
            $stmtUsuario->bindParam(':clave', $clave);
            $stmtUsuario->execute();

            $id_usuario = $this->conexion->lastInsertId();

            // 2. Insertar en tabla acudiente
            $insertarAcudiente = "INSERT INTO acudiente
                                    (id_institucion, id_usuario, nombres, parentesco, genero,
                                     telefono, tipo_documento, documento, apellidos,
                                     fecha_de_nacimiento, foto, ciudad, direccion)
                                VALUES
                                    (:id_institucion, :id_usuario, :nombres, :parentesco, :genero,
                                     :telefono, :tipo_documento, :documento, :apellidos,
                                     :fecha_nacimiento, :foto, :ciudad, :direccion)";

            $stmtAcudiente = $this->conexion->prepare($insertarAcudiente);
            $stmtAcudiente->bindParam(':id_institucion', $data['id_institucion']);
            $stmtAcudiente->bindParam(':id_usuario',     $id_usuario);
            $stmtAcudiente->bindParam(':nombres',        $data['nombres']);
            $stmtAcudiente->bindParam(':parentesco',     $data['parentesco']);
            $stmtAcudiente->bindParam(':genero',         $data['genero']);
            $stmtAcudiente->bindParam(':telefono',       $data['telefono']);
            $stmtAcudiente->bindParam(':tipo_documento', $data['tipo_documento']);
            $stmtAcudiente->bindParam(':documento',      $data['documento']);
            $stmtAcudiente->bindParam(':apellidos',      $data['apellidos']);
            $stmtAcudiente->bindParam(':fecha_nacimiento',$data['fecha_nacimiento']);
            $stmtAcudiente->bindParam(':foto',           $data['foto']);
            $stmtAcudiente->bindParam(':ciudad',         $data['ciudad']);
            $stmtAcudiente->bindParam(':direccion',      $data['direccion']);
            $stmtAcudiente->execute();

            $this->conexion->commit();
            return true;

        } catch (PDOException $e) {
            $this->conexion->rollBack();
            error_log("Error en Acudiente::registrar -> " . $e->getMessage());
            return false;
        }
    }

    // -----------------------------------------------------------------
    // LISTAR — filtra siempre por id_institucion (aislamiento multi-tenant)
    // -----------------------------------------------------------------
    public function listar($id_institucion) {
        try {
            $consultar = "SELECT
                              acudiente.*,
                              usuario.correo AS correo,
                              usuario.estado AS estado
                          FROM acudiente
                          INNER JOIN usuario ON acudiente.id_usuario = usuario.id
                          WHERE acudiente.id_institucion = :id_institucion
                          ORDER BY apellidos ASC";

            $stmt = $this->conexion->prepare($consultar);
            $stmt->bindParam(':id_institucion', $id_institucion);
            $stmt->execute();
            return $stmt->fetchAll();

        } catch (PDOException $e) {
            error_log("Error en Acudiente::listar -> " . $e->getMessage());
            return [];
        }
    }

    // -----------------------------------------------------------------
    // LISTAR POR ID
    // -----------------------------------------------------------------
    public function listarAcudienteId($id) {
        try {
            $consultar = "SELECT
                              acudiente.*,
                              usuario.correo AS correo,
                              usuario.estado AS estado
                          FROM acudiente
                          INNER JOIN usuario ON acudiente.id_usuario = usuario.id
                          WHERE acudiente.id = :id
                          LIMIT 1";

            $stmt = $this->conexion->prepare($consultar);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            return $stmt->fetch();

        } catch (PDOException $e) {
            error_log("Error en Acudiente::listarAcudienteId -> " . $e->getMessage());
            return null;
        }
    }

    // -----------------------------------------------------------------
    // ACTUALIZAR — transacción protege el doble UPDATE usuario + acudiente
    // Alias acutalizar() conservado para compatibilidad con controladores existentes
    // -----------------------------------------------------------------
    public function acutalizar($data) {
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

            // Actualizar acudiente
            $actualizarAcudiente = "UPDATE acudiente
                                    SET nombres           = :nombres,
                                        parentesco        = :parentesco,
                                        genero            = :genero,
                                        telefono          = :telefono,
                                        tipo_documento    = :tipo_documento,
                                        apellidos         = :apellidos,
                                        fecha_de_nacimiento = :fecha_nacimiento,
                                        ciudad            = :ciudad,
                                        direccion         = :direccion
                                    WHERE id_usuario = :id_usuario";
            $stmtA = $this->conexion->prepare($actualizarAcudiente);
            $stmtA->bindParam(':id_usuario',      $data['id_usuario']);
            $stmtA->bindParam(':nombres',         $data['nombres']);
            $stmtA->bindParam(':parentesco',      $data['parentesco']);
            $stmtA->bindParam(':genero',          $data['genero']);
            $stmtA->bindParam(':telefono',        $data['telefono']);
            $stmtA->bindParam(':tipo_documento',  $data['tipo_documento']);
            $stmtA->bindParam(':apellidos',       $data['apellidos']);
            $stmtA->bindParam(':fecha_nacimiento',$data['fecha_nacimiento']);
            $stmtA->bindParam(':ciudad',          $data['ciudad']);
            $stmtA->bindParam(':direccion',       $data['direccion']);
            $stmtA->execute();

            $this->conexion->commit();
            return true;

        } catch (PDOException $e) {
            $this->conexion->rollBack();
            error_log("Error en Acudiente::acutalizar -> " . $e->getMessage());
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
            error_log("Error en Acudiente::eliminar -> " . $e->getMessage());
            return false;
        }
    }

    // -----------------------------------------------------------------
    // ESTUDIANTES VINCULADOS AL ACUDIENTE
    // -----------------------------------------------------------------
    public function listarEstudiantesVinculados($id_acudiente, $id_institucion) {
        try {
            $sql = "SELECT
                        e.id,
                        e.nombres,
                        e.apellidos,
                        e.documento,
                        e.foto,
                        c.grado,
                        c.curso AS nombre_curso,
                        c.jornada,
                        u.estado
                    FROM estudiante e
                    LEFT JOIN matricula m ON m.id_estudiante = e.id AND m.estado = 'Activa'
                    LEFT JOIN curso c ON c.id = m.id_curso
                    LEFT JOIN usuario u ON u.id = e.id_usuario
                    WHERE e.id_acudiente = :id_acudiente
                      AND e.id_institucion = :id_institucion
                    ORDER BY e.apellidos ASC, e.nombres ASC";

            $stmt = $this->conexion->prepare($sql);
            $stmt->bindParam(':id_acudiente', $id_acudiente, PDO::PARAM_INT);
            $stmt->bindParam(':id_institucion', $id_institucion, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            error_log("Error en Acudiente::listarEstudiantesVinculados -> " . $e->getMessage());
            return [];
        }
    }

    // -----------------------------------------------------------------
    // CONTAR — filtra por institución y usuarios Activos
    // -----------------------------------------------------------------
    public function contar($id_institucion) {
        try {
            $sql  = "SELECT COUNT(*) AS total
                     FROM acudiente
                     INNER JOIN usuario ON acudiente.id_usuario = usuario.id
                     WHERE acudiente.id_institucion = :id_institucion
                       AND usuario.estado = 'Activo'";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindParam(':id_institucion', $id_institucion);
            $stmt->execute();
            $fila = $stmt->fetch();
            return $fila['total'] ?? 0;

        } catch (PDOException $e) {
            error_log("Error en Acudiente::contar -> " . $e->getMessage());
            return 0;
        }
    }
}
