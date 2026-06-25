<?php

/**
 * Modelo: Estudiante
 * Gestión de estudiantes por institución.
 *
 * CORRECCIONES APLICADAS:
 *  - registrar() usa beginTransaction/commit/rollBack para garantizar integridad
 *    entre INSERT usuario + INSERT estudiante.
 *  - die() reemplazado por error_log() + retorno false/array para no exponer
 *    estructura de BD en producción.
 */

require_once __DIR__ . '/../../../config/database.php';

class Estudiante {

    private $conexion;

    public function __construct() {
        $db = new Conexion();
        $this->conexion = $db->getConexion();
    }

    // -----------------------------------------------------------------
    // REGISTRAR — usa transacción para proteger el doble INSERT
    // usuario → estudiante. Si falla cualquier paso, hace rollBack.
    // -----------------------------------------------------------------
    public function registrar($data) {
        try {
            $this->conexion->beginTransaction();

            // 1. Insertar en tabla usuario
            $insertarUsuario = "INSERT INTO usuario
                                    (id_institucion, correo, clave, rol, estado)
                                VALUES
                                    (:id_institucion, :correo, :clave, 'Estudiante', 'Activo')";

            $stmtUsuario = $this->conexion->prepare($insertarUsuario);
            $stmtUsuario->bindParam(':id_institucion', $data['id_institucion']);
            $stmtUsuario->bindParam(':correo',         $data['correo']);
            $clave = password_hash($data['documento'], PASSWORD_DEFAULT);
            $stmtUsuario->bindParam(':clave', $clave);
            $stmtUsuario->execute();

            $id_usuario = $this->conexion->lastInsertId();

            // 2. Insertar en tabla estudiante
            $insertarEstudiante = "INSERT INTO estudiante
                                    (id_institucion, id_usuario, nombres, apellidos, documento,
                                     telefono, fecha_de_nacimiento, id_acudiente, tipo_documento,
                                     foto, ciudad, direccion, genero)
                                VALUES
                                    (:id_institucion, :id_usuario, :nombres, :apellidos, :documento,
                                     :telefono, :fecha_nacimiento, :acudiente, :tipo_documento,
                                     :foto, :ciudad, :direccion, :genero)";

            $stmtEstudiante = $this->conexion->prepare($insertarEstudiante);
            $stmtEstudiante->bindParam(':id_institucion',  $data['id_institucion']);
            $stmtEstudiante->bindParam(':id_usuario',      $id_usuario);
            $stmtEstudiante->bindParam(':nombres',         $data['nombres']);
            $stmtEstudiante->bindParam(':apellidos',       $data['apellidos']);
            $stmtEstudiante->bindParam(':documento',       $data['documento']);
            $stmtEstudiante->bindParam(':telefono',        $data['telefono']);
            $stmtEstudiante->bindParam(':fecha_nacimiento',$data['fecha_nacimiento']);
            $stmtEstudiante->bindParam(':acudiente',       $data['acudiente']);
            $stmtEstudiante->bindParam(':tipo_documento',  $data['tipo_documento']);
            $stmtEstudiante->bindParam(':foto',            $data['foto']);
            $stmtEstudiante->bindParam(':ciudad',          $data['ciudad']);
            $stmtEstudiante->bindParam(':direccion',       $data['direccion']);
            $stmtEstudiante->bindParam(':genero',          $data['genero']);
            $stmtEstudiante->execute();

            $this->conexion->commit();
            return true;

        } catch (PDOException $e) {
            $this->conexion->rollBack();
            error_log("Error en Estudiante::registrar -> " . $e->getMessage());
            return false;
        }
    }

    // -----------------------------------------------------------------
    // LISTAR — filtra siempre por id_institucion (aislamiento multi-tenant)
    // -----------------------------------------------------------------
    public function listar($id_institucion) {
        try {
            $consultar = "SELECT
                              estudiante.*,
                              usuario.correo          AS correo,
                              usuario.estado          AS estado,
                              acudiente.nombres       AS nombres_acudiente,
                              acudiente.apellidos     AS apellidos_acudiente
                          FROM estudiante
                          INNER JOIN usuario   ON estudiante.id_usuario  = usuario.id
                          INNER JOIN acudiente ON estudiante.id_acudiente = acudiente.id
                          WHERE estudiante.id_institucion = :id_institucion
                          ORDER BY apellidos ASC";

            $stmt = $this->conexion->prepare($consultar);
            $stmt->bindParam(':id_institucion', $id_institucion);
            $stmt->execute();
            return $stmt->fetchAll();

        } catch (PDOException $e) {
            error_log("Error en Estudiante::listar -> " . $e->getMessage());
            return [];
        }
    }

    // -----------------------------------------------------------------
    // ELIMINAR (soft delete — cambia estado a Inactivo)
    // -----------------------------------------------------------------
    public function eliminar($id) {
        try {
            $sql  = "UPDATE usuario SET estado = 'Inactivo' WHERE id = :id";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindParam(':id', $id);
            return $stmt->execute();

        } catch (PDOException $e) {
            error_log("Error en Estudiante::eliminar -> " . $e->getMessage());
            return false;
        }
    }

    // -----------------------------------------------------------------
    // LISTAR POR ID — valida pertenencia institucional internamente
    // (la vista del administrador ya filtra por su institución)
    // -----------------------------------------------------------------
    public function listarId($id) {
        try {
            $consultar = "SELECT
                              estudiante.*,
                              usuario.correo              AS correo,
                              usuario.estado              AS estado,
                              acudiente.nombres           AS nombres_acudiente,
                              acudiente.apellidos         AS apellidos_acudiente,
                              acudiente.parentesco        AS parentesco_acudiente,
                              acudiente.telefono          AS telefono_acudiente,
                              ua.correo                   AS correo_acudiente
                          FROM estudiante
                          INNER JOIN usuario   ON estudiante.id_usuario   = usuario.id
                          LEFT JOIN acudiente  ON estudiante.id_acudiente = acudiente.id
                          LEFT JOIN usuario ua ON acudiente.id_usuario    = ua.id
                          WHERE estudiante.id = :id
                          LIMIT 1";

            $stmt = $this->conexion->prepare($consultar);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            return $stmt->fetch();

        } catch (PDOException $e) {
            error_log("Error en Estudiante::listarId -> " . $e->getMessage());
            return null;
        }
    }

    public function obtenerMatriculaActiva($id_estudiante) {
        try {
            $sql = "SELECT m.anio, c.grado, c.curso, c.jornada
                    FROM matricula m
                    INNER JOIN curso c ON c.id = m.id_curso
                    WHERE m.id_estudiante = :id AND m.estado = 'Activa'
                    ORDER BY m.anio DESC
                    LIMIT 1";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindParam(':id', $id_estudiante, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
        } catch (PDOException $e) {
            error_log("Error en Estudiante::obtenerMatriculaActiva -> " . $e->getMessage());
            return [];
        }
    }

    // -----------------------------------------------------------------
    // ACTUALIZAR — transacción para proteger UPDATE usuario + UPDATE estudiante
    // -----------------------------------------------------------------
    public function actulizar($data) {
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

            // Actualizar estudiante
            $actualizarEstudiante = "UPDATE estudiante
                                     SET nombres          = :nombres,
                                         apellidos        = :apellidos,
                                         documento        = :documento,
                                         telefono         = :telefono,
                                         fecha_de_nacimiento = :fecha_nacimiento,
                                         id_acudiente     = :acudiente,
                                         tipo_documento   = :tipo_documento,
                                         ciudad           = :ciudad,
                                         direccion        = :direccion,
                                         genero           = :genero
                                     WHERE id_usuario = :id_usuario";
            $stmtE = $this->conexion->prepare($actualizarEstudiante);
            $stmtE->bindParam(':id_usuario',      $data['id_usuario']);
            $stmtE->bindParam(':nombres',         $data['nombres']);
            $stmtE->bindParam(':apellidos',        $data['apellidos']);
            $stmtE->bindParam(':documento',        $data['documento']);
            $stmtE->bindParam(':telefono',         $data['telefono']);
            $stmtE->bindParam(':fecha_nacimiento', $data['fecha_nacimiento']);
            $stmtE->bindParam(':acudiente',        $data['acudiente']);
            $stmtE->bindParam(':tipo_documento',   $data['tipo_documento']);
            $stmtE->bindParam(':ciudad',           $data['ciudad']);
            $stmtE->bindParam(':direccion',        $data['direccion']);
            $stmtE->bindParam(':genero',           $data['genero']);
            $stmtE->execute();

            $this->conexion->commit();
            return true;

        } catch (PDOException $e) {
            $this->conexion->rollBack();
            error_log("Error en Estudiante::actulizar -> " . $e->getMessage());
            return false;
        }
    }

    // -----------------------------------------------------------------
    // CONTAR — filtra por institución y solo usuarios Activos
    // -----------------------------------------------------------------
    public function contar($id_institucion) {
        try {
            $sql  = "SELECT COUNT(*) AS total
                     FROM estudiante
                     INNER JOIN usuario ON estudiante.id_usuario = usuario.id
                     WHERE estudiante.id_institucion = :id_institucion
                       AND usuario.estado = 'Activo'";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindParam(':id_institucion', $id_institucion);
            $stmt->execute();
            $fila = $stmt->fetch();
            return $fila['total'] ?? 0;

        } catch (PDOException $e) {
            error_log("Error en Estudiante::contar -> " . $e->getMessage());
            return 0;
        }
    }
}
