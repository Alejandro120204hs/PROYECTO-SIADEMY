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
                $consultar = "SELECT * FROM asignatura WHERE id_institucion = :id_institucion ORDER BY nombre ASC";


                // PREPARAMOS LA ACCION A EJECUTAR Y LA EJECUTAMOS
                $resultado = $this->conexion->prepare($consultar);
                $resultado -> bindParam(':id_institucion', $id_institucion);
                $resultado -> execute();
                return $resultado -> fetchAll();


            }catch(PDOException $e){
                error_log("Error en Asignatura::listar->" . $e->getMessage());
                return[];
            }
        }


        public function listarAsignaturaId($id){

            try{
                // DEFINIMOS EN UNA VARIABLE LA CONSULTA DE SQL SEGUN SEA EL CASO
                $consultar = "SELECT * FROM asignatura WHERE id = :id LIMIT 1";

                // PREPARAR Y EJECUTAR
                $resultado = $this->conexion->prepare($consultar);
                $resultado->bindParam(':id', $id, PDO::PARAM_INT);
                $resultado->execute();

                return $resultado->fetch();

            }catch(PDOException $e){
                error_log("Error en Asignatura::listar->" . $e->getMessage());
                return[];
            }
        }


        public function actualizar($data){
            try{
                // DEFINIMOS EN UNA VARIABLE LA CONSULTA DE SQL SEGUN SEA EL CASO

                $actualizar = "UPDATE asignatura SET nombre=:nombre, descripcion=:descripcion, estado=:estado WHERE id = :id";

                // PREPARAMOS LA ACCION A EJECUTAR Y LA EJECUTAMOS
                $resultado = $this->conexion->prepare($actualizar);
                $resultado->bindParam(':id',$data['id']);
                $resultado->bindParam(':nombre',$data['nombre']);
                $resultado->bindParam(':descripcion',$data['descripcion']);
                $resultado->bindParam(':estado',$data['estado']);

                $resultado -> execute();

                 if($resultado){
                    return true;
                }else{
                    return false;
                }

            }catch(PDOException $e){
                error_log("Error en Asignatura::actualizar->" . $e->getMessage());
                return false;
            }
        }



    }




?>