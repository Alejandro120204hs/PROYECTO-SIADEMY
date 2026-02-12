<?php

    // IMPORTAMOS LAS DEPENDENCIAS NECESARIAS
    require_once __DIR__ . '/../../../config/database.php';

    class Evento{
        // LLAMAMOS LA BASE DE DATOS
        private $conexion;

        public function __construct(){
            $db = new Conexion();
            $this -> conexion = $db -> getConexion();
        }

        public function contar($id_institucion){
            try{
                $consultar = "SELECT COUNT(*) as total FROM eventos WHERE id_institucion = :id_institucion AND estado = 'Activo'";
                $resultado = $this->conexion->prepare($consultar);
                $resultado->bindParam(':id_institucion', $id_institucion);
                $resultado->execute();
                $fila = $resultado->fetch();
                return $fila['total'] ?? 0;
            }catch(PDOException $e){
                error_log("Error en Evento::contar->" . $e->getMessage());
                return 0;
            }
        }
    }

?>
