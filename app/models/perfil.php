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

    //  ESTA FUNCION SE DUPLICA POR CADA ROL
    public function mostrarPerfilAdmin($id)
    {
        try {
            // DEFINIMOS EN UNA VARIABLE LA CONSULTA DE SQL SEGUN SEA EL CASO
            // $consultar = "SELECT administrador.*, usuario.correo AS correo, usuario.rol AS rol, institucion.nombre AS nombre_institucion, institucion.direccion AS direccion_institucion FROM administrador INNER JOIN usuario ON administrador.id_usuario = usuario.id INNER JOIN institucion ON administrador.id_institucion = institucion.id WHERE administrador.id = :id LIMIT 1";
            $consultar = "SELECT administrador.*, usuario.correo AS correo, usuario.rol AS rol, 
              institucion.nombre AS nombre_institucion, institucion.direccion AS direccion_institucion 
              FROM administrador 
              INNER JOIN usuario ON administrador.id_usuario = usuario.id 
              INNER JOIN institucion ON administrador.id_institucion = institucion.id 
              WHERE usuario.id = :id LIMIT 1";

            // PREPARAMOS LA ACCION A EJECUTAR Y LA EJECUTAMOS
            $resultado = $this->conexion->prepare($consultar);
            $resultado->bindParam(':id', $id);
            $resultado->execute();
            return $resultado->fetch();
        } catch (PDOException $e) {
            error_log("Error en Acudiente::listar->" . $e->getMessage());
            return [];
        }
    }
}
