<?php

/**
 * Modelo: Administrador (superAdmin)
 * Gestión de administradores por institución.
 *
 * CORRECCIONES APLICADAS:
 *  - registrar() usa beginTransaction/commit/rollBack para garantizar integridad
 *    entre INSERT usuario + INSERT administrador.
 *  - actualizar() usa beginTransaction/commit/rollBack para proteger el doble UPDATE.
 *  - die() reemplazado por error_log() + return false en todos los métodos.
 */

require_once __DIR__ . '/../../../config/database.php';

class Administrador {

    private $conexion;

    public function __construct() {
        $db = new Conexion();
        $this->conexion = $db->getConexion();
    }

    // -----------------------------------------------------------------
    // REGISTRAR — transacción protege el doble INSERT usuario + administrador
    // -----------------------------------------------------------------
    public function registrar($data) {
        try {
            $this->conexion->beginTransaction();

            // 1. Insertar en tabla usuario
            $insertarUsuario = "INSERT INTO usuario
                                    (id_institucion, correo, clave, rol, estado)
                                VALUES
                                    (:institucion, :correo, :clave, 'Administrador', 'Activo')";

            $stmtUsuario = $this->conexion->prepare($insertarUsuario);
            $stmtUsuario->bindParam(':institucion', $data['institucion']);
            $stmtUsuario->bindParam(':correo',      $data['correo']);
            $clave = password_hash($data['documento'], PASSWORD_DEFAULT);
            $stmtUsuario->bindParam(':clave', $clave);
            $stmtUsuario->execute();

            $id_usuario = $this->conexion->lastInsertId();

            // 2. Insertar en tabla administrador
            $insertarAdministrador = "INSERT INTO administrador
                                        (id_usuario, nombres, apellidos, id_institucion,
                                         documento, telefono, edad, foto)
                                      VALUES
                                        (:id_usuario, :nombres, :apellidos, :institucion,
                                         :documento, :telefono, :edad, :foto)";

            $stmtAdmin = $this->conexion->prepare($insertarAdministrador);
            $stmtAdmin->bindParam(':id_usuario',  $id_usuario);
            $stmtAdmin->bindParam(':nombres',     $data['nombres']);
            $stmtAdmin->bindParam(':apellidos',   $data['apellidos']);
            $stmtAdmin->bindParam(':institucion', $data['institucion']);
            $stmtAdmin->bindParam(':documento',   $data['documento']);
            $stmtAdmin->bindParam(':telefono',    $data['telefono']);
            $stmtAdmin->bindParam(':edad',        $data['edad']);
            $stmtAdmin->bindParam(':foto',        $data['foto']);
            $stmtAdmin->execute();

            $this->conexion->commit();
            return true;

        } catch (PDOException $e) {
            $this->conexion->rollBack();
            error_log("Error en Administrador::registrar -> " . $e->getMessage());
            return false;
        }
    }

    // -----------------------------------------------------------------
    // LISTAR — todos los administradores con institución
    // -----------------------------------------------------------------
    public function listar() {
        try {
            $consultar = "SELECT
                              administrador.*,
                              usuario.correo           AS correo,
                              usuario.estado           AS estado,
                              institucion.nombre       AS nombre_institucion,
                              institucion.logo         AS logo
                          FROM administrador
                          INNER JOIN usuario     ON administrador.id_usuario     = usuario.id
                          INNER JOIN institucion ON administrador.id_institucion = institucion.id";

            $stmt = $this->conexion->prepare($consultar);
            $stmt->execute();
            return $stmt->fetchAll();

        } catch (PDOException $e) {
            error_log("Error en Administrador::listar -> " . $e->getMessage());
            return [];
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
            error_log("Error en Administrador::eliminar -> " . $e->getMessage());
            return false;
        }
    }

    // -----------------------------------------------------------------
    // LISTAR POR ID
    // -----------------------------------------------------------------
    public function listarAdministradorID($id) {
        try {
            $consultar = "SELECT
                              administrador.*,
                              usuario.correo  AS correo,
                              usuario.estado  AS estado
                          FROM administrador
                          INNER JOIN usuario ON administrador.id_usuario = usuario.id
                          WHERE administrador.id = :id
                          LIMIT 1";

            $stmt = $this->conexion->prepare($consultar);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            return $stmt->fetch();

        } catch (PDOException $e) {
            error_log("Error en Administrador::listarAdministradorID -> " . $e->getMessage());
            return null;
        }
    }

    // -----------------------------------------------------------------
    // ACTUALIZAR — transacción protege el doble UPDATE usuario + administrador
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

            // Actualizar administrador
            $actualizarAdministrador = "UPDATE administrador
                                        SET nombres   = :nombres,
                                            apellidos = :apellidos,
                                            edad      = :edad,
                                            telefono  = :telefono
                                        WHERE id_usuario = :id_usuario";
            $stmtA = $this->conexion->prepare($actualizarAdministrador);
            $stmtA->bindParam(':nombres',   $data['nombres']);
            $stmtA->bindParam(':apellidos', $data['apellidos']);
            $stmtA->bindParam(':edad',      $data['edad']);
            $stmtA->bindParam(':telefono',  $data['telefono']);
            $stmtA->bindParam(':id_usuario',$data['id_usuario']);
            $stmtA->execute();

            $this->conexion->commit();
            return true;

        } catch (PDOException $e) {
            $this->conexion->rollBack();
            error_log("Error en Administrador::actualizar -> " . $e->getMessage());
            return false;
        }
    }
}
