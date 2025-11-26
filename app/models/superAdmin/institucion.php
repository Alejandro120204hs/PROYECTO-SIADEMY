<?php

    // IMPORTAMOS LAS DEPENDENCIAS NECESARIAS
    require_once __DIR__ . '/../../../config/database.php';

    class Institucion{
        // LLAMAMOS LA BASE DE DATOS
        private $conexion;

        public function __construct()
        {
            $db = new Conexion();
            $this -> conexion = $db -> getConexion();
        }

        // CREAMOS LAS FUNCIONES PUBLICAS
        public function registrar($data){
            try{
                // DEFINIMOS EN UNA VARIABLE LA CONSULTA DE SQL DE REGISTRAR INSTITUCION
                $insertar = "INSERT INTO institucion(nombre,direccion,telefono,correo,estado,tipo,jornada) VALUES(:nombre,:direccion,:telefono,:correo,'Activo',:tipo,:jornada)";

                // PREPARAMOS LA ACCION A EJECUTAR Y LA EJECUTAMOS
                $resultado = $this -> conexion -> prepare($insertar);
                $resultado -> bindParam(':nombre', $data['nombre']);
                $resultado -> bindParam(':direccion',$data['direccion']);
                $resultado -> bindParam(':telefono',$data['telefono']);
                $resultado -> bindParam(':correo',$data['correo']);
                $resultado -> bindParam(':tipo',$data['tipo']);
                $resultado -> bindParam(':jornada',$data['jornada']);
                
                return $resultado -> execute();

            }catch(PDOException $e){
                die("Error en Institucion::registrar->" . $e->getMessage());
                
            }
        }

        public function listar(){
            try{
                // DEFINIMOS EN UNA VARIABLE LA CONSULTA DE SQL PARA MOSTRAR LAS INSTITUCIONES
            $consultar = "SELECT * FROM institucion";

            // PREPARAMOS LA ACCION A EJECUTAR Y LA EJECUTAMOS
            $resultado = $this -> conexion -> prepare($consultar);
            $resultado -> execute();
            return $resultado -> fetchAll();
            }catch(PDOException $e){
                die("Error en Institucion::consultar->" . $e->getMessage());
                return [];
            }
        }

        public function eliminar($id){
            try{
                // DEFINIMOS EN UNA VARIABLE LA CONSULTA DE SQL PARA ACTUALIZAR EL ESTADO DE LA INSTITUCION
                $actualizar = "UPDATE institucion SET estado='Inactivo' WHERE id=:id";

                // PREPARAMOS LA ACCION A EJECUTAR Y LA EJECUTAMOS
                $resultado = $this -> conexion -> prepare($actualizar);
                $resultado -> bindParam(':id',$id);
                return $resultado -> execute();
            }catch(PDOException $e){
                die("Error en Institucion::consultar->" . $e->getMessage());

            }
        }

        public function listarInstitucionId($id){
            try{
                // DEFINIMOS EN UNA VARIABLE LA CONSULTA DE SQL PARA CONSULTAR LA INSTITUCION
                $consultar = "SELECT * FROM institucion WHERE id=:id";
                // PREPARAMOS LA ACCION A EJECUTAR Y LA EJECUTAMOS
                $resultado = $this -> conexion -> prepare($consultar);
                $resultado -> bindParam(':id',$id);
                $resultado -> execute();    
                return $resultado -> fetch();
            }catch(PDOException $e){
                die("Error en Institucion::consultar->" . $e->getMessage());
                return [];
            }
        }

        public function actualizar($data){
            try{
                // DEFINIMOS EN UNA VARIABLE LA CONSULTA DE SQL PARA ACTUALIAR LA INSTITUCION
                $actualizar = "UPDATE institucion SET nombre=:nombre, tipo=:tipo, jornada=:jornada, estado=:estado, direccion=:direccion, telefono=:telefono, correo=:correo WHERE id=:id";

                // PREPARAMOS LA ACCION A EJECUTAR Y LA EJECUTAMOS
                $resultado = $this -> conexion -> prepare($actualizar);
                $resultado -> bindParam(':id',$data['id']);
                $resultado -> bindParam(':nombre',$data['nombre']);
                $resultado -> bindParam(':tipo',$data['tipo']);
                $resultado -> bindParam(':jornada',$data['jornada']);
                $resultado -> bindParam(':estado',$data['estado']);
                $resultado -> bindParam(':direccion',$data['direccion']);
                $resultado -> bindParam('telefono',$data['telefono']);
                $resultado ->bindParam('correo',$data['correo']);
                return $resultado -> execute();
            }catch(PDOException $e){
                die("Error en Institucion::consultar->" . $e->getMessage());
                return false;
            }
        }
    }

?>