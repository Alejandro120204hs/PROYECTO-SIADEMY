<?php

    // IMPORTAMOS LAS DEPENDENCIAS NECESARIAS
    require_once __DIR__ . '/../../../config/database.php';

    class Asignatura{
        // llamamos la base datos
        private $conexion;
        public function __construct(){
            $db = new Conexion;
            $this -> conexion = $db -> getConexion();
        }

        public function registrar($data){
            try{
                // INSERTAMOS DATOS EN LA TABLA USUARIO

                $insertar = "INSERT INTO asignatura(id_institucion, nombre, descripcion, estado) VALUES (:id_institucion, :nombre, :descripcion, 'Activo')";

                // PREPARAMOS LA ACCION A EJECUTAR Y LA EJECUTAMOS
                $resultado = $this->conexion->prepare($insertar);
                $resultado->bindParam(':id_institucion', $data['id_institucion']);
                $resultado->bindParam(':nombre', $data['nombre']);
                $resultado->bindParam(':descripcion', $data['descripcion']);


                return $resultado -> execute();

            }catch(PDOException $e){
                error_log("Error en Acudiente::registrar->" . $e->getMessage());
                return false;
            }
        }


        public function listar($id_institucion){
            try{

                // DEFINIMOS EN UNA VARIABLE LA CONSULTA DE SQL SEGUN SEA EL CASO
                $consultar = "SELECT nombre, descripcion, estado FROM asignatura WHERE id_institucion = :id_institucion ORDER BY nombre ASC";


                // PREPARAMOS LA ACCION A EJECUTAR Y LA EJECUTAMOS
                $resultado = $this->conexion->prepare($consultar);
                $resultado -> bindParam(':id_institucion', $id_institucion);
                $resultado -> execute();
                return $resultado -> fetchAll();


            }catch(PDOException $e){
                error_log("Error en Acudiente::listar->" . $e->getMessage());
                return[];
            }
        }



    }




?>