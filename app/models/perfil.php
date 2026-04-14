<?php

// IMPORTAMOS LA CONEXION A LA BASE DE DATOS
require_once __DIR__ . '/../../config/database.php';

class Perfil
{
    // LLAMAMOS LA BASE DE DATOS
    private $conexion;

    public function __construct()
    {
        $db = new Conexion();
        $this->conexion = $db->getConexion();
    }

    private function ejecutarConsultaPerfil($consulta, $id)
    {
        try {
            $resultado = $this->conexion->prepare($consulta);
            $resultado->bindParam(':id', $id);
            $resultado->execute();
            $usuario = $resultado->fetch(PDO::FETCH_ASSOC);
            return $usuario ?: [];
        } catch (PDOException $e) {
            error_log("Error en Perfil::ejecutarConsultaPerfil -> " . $e->getMessage());
            return [];
        }
    }

    public function mostrarPerfilAdministrador($id)
    {
        $consulta = "SELECT 
                administrador.*, 
                usuario.correo AS correo, 
                usuario.rol AS rol,
                institucion.nombre AS nombre_institucion, 
                institucion.direccion AS direccion_institucion
            FROM administrador
            INNER JOIN usuario ON administrador.id_usuario = usuario.id
            INNER JOIN institucion ON administrador.id_institucion = institucion.id
            WHERE usuario.id = :id
            LIMIT 1";

        return $this->ejecutarConsultaPerfil($consulta, $id);
    }

    public function mostrarPerfilSuperAdmin($id)
    {
        $consulta = "SELECT 
                COALESCE(administrador.nombres, 'Super Administrador') AS nombres,
                COALESCE(administrador.apellidos, '') AS apellidos,
                administrador.documento,
                administrador.telefono,
                administrador.edad,
                COALESCE(administrador.foto, 'default.png') AS foto,
                usuario.correo AS correo,
                usuario.rol AS rol,
                institucion.nombre AS nombre_institucion,
                institucion.direccion AS direccion_institucion
            FROM usuario
            LEFT JOIN administrador ON administrador.id_usuario = usuario.id
            LEFT JOIN institucion ON institucion.id = COALESCE(administrador.id_institucion, usuario.id_institucion)
            WHERE usuario.id = :id
            LIMIT 1";

        return $this->ejecutarConsultaPerfil($consulta, $id);
    }

    public function mostrarPerfilDocente($id)
    {
        $consulta = "SELECT
                docente.nombres AS nombres,
                docente.apellidos AS apellidos,
                docente.documento AS documento,
                docente.telefono AS telefono,
                NULL AS edad,
                COALESCE(docente.foto, 'default.png') AS foto,
                usuario.correo AS correo,
                usuario.rol AS rol,
                institucion.nombre AS nombre_institucion,
                institucion.direccion AS direccion_institucion
            FROM docente
            INNER JOIN usuario ON docente.id_usuario = usuario.id
            LEFT JOIN institucion ON institucion.id = docente.id_institucion
            WHERE usuario.id = :id
            LIMIT 1";

        return $this->ejecutarConsultaPerfil($consulta, $id);
    }

    public function mostrarPerfilGenerico($id)
    {
        $consulta = "SELECT 
                'Usuario' AS nombres,
                '' AS apellidos,
                NULL AS documento,
                NULL AS telefono,
                NULL AS edad,
                'default.png' AS foto,
                usuario.correo AS correo,
                usuario.rol AS rol,
                NULL AS nombre_institucion,
                NULL AS direccion_institucion
            FROM usuario
            WHERE usuario.id = :id
            LIMIT 1";

        return $this->ejecutarConsultaPerfil($consulta, $id);
    }

    public function actualizarCorreoUsuario($idUsuario, $correo)
    {
        try {
            $consulta = "UPDATE usuario SET correo = :correo WHERE id = :id";
            $resultado = $this->conexion->prepare($consulta);
            $resultado->bindParam(':correo', $correo);
            $resultado->bindParam(':id', $idUsuario, PDO::PARAM_INT);
            return $resultado->execute();
        } catch (PDOException $e) {
            error_log("Error en Perfil::actualizarCorreoUsuario -> " . $e->getMessage());
            return false;
        }
    }

    public function actualizarDatosAdministradorPorUsuario($idUsuario, $data)
    {
        try {
            $consulta = "UPDATE administrador
                         SET nombres = :nombres,
                             apellidos = :apellidos,
                             telefono = :telefono,
                             edad = :edad
                         WHERE id_usuario = :id_usuario";

            $resultado = $this->conexion->prepare($consulta);
            $resultado->bindParam(':nombres', $data['nombres']);
            $resultado->bindParam(':apellidos', $data['apellidos']);
            $resultado->bindParam(':telefono', $data['telefono']);
            $resultado->bindParam(':edad', $data['edad']);
            $resultado->bindParam(':id_usuario', $idUsuario, PDO::PARAM_INT);

            return $resultado->execute();
        } catch (PDOException $e) {
            error_log("Error en Perfil::actualizarDatosAdministradorPorUsuario -> " . $e->getMessage());
            return false;
        }
    }

    public function obtenerClaveUsuario($idUsuario)
    {
        try {
            $consulta = "SELECT clave FROM usuario WHERE id = :id LIMIT 1";
            $resultado = $this->conexion->prepare($consulta);
            $resultado->bindParam(':id', $idUsuario, PDO::PARAM_INT);
            $resultado->execute();
            $fila = $resultado->fetch(PDO::FETCH_ASSOC);
            return $fila['clave'] ?? null;
        } catch (PDOException $e) {
            error_log("Error en Perfil::obtenerClaveUsuario -> " . $e->getMessage());
            return null;
        }
    }

    public function actualizarClaveUsuario($idUsuario, $claveHash)
    {
        try {
            $consulta = "UPDATE usuario SET clave = :clave WHERE id = :id";
            $resultado = $this->conexion->prepare($consulta);
            $resultado->bindParam(':clave', $claveHash);
            $resultado->bindParam(':id', $idUsuario, PDO::PARAM_INT);
            return $resultado->execute();
        } catch (PDOException $e) {
            error_log("Error en Perfil::actualizarClaveUsuario -> " . $e->getMessage());
            return false;
        }
    }
}
