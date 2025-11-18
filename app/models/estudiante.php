<?php 

    // IMPORTAMOS LAS DEPENDENCIAS NECESARIAS
    require_once __DIR__ . '/../../config/database.php';

    class Estudiante{
        // LLAMAMOS LA BASE DE DATOS
        private $conexion;

        public function __construct(){
            $db = new Conexion();
            $this -> conexion = $db -> getConexion();
        }

        public function registrar($data){
            try{
                
                // INSERTAMOS EL ESTUDIANTE
                $insertar = "INSERT INTO estudiante(nombres,apellidos,documento,correo,telefono,fecha_de_nacimiento,id_acudiente,tipo_documento,clave) VALUES(:nombres,:apellidos,:documento,:correo,:telefono,:fecha_nacimiento,:acudiente,:tipo_documento,:clave)";

                $resultado = $this->conexion->prepare($insertar);
                $resultado->bindParam(':nombres', $data['nombres']);
                $resultado->bindParam(':apellidos', $data['apellidos']);
                $resultado->bindParam(':documento', $data['documento']);
                $resultado->bindParam(':correo', $data['correo']);
                $resultado->bindParam(':telefono', $data['telefono']);
                $resultado->bindParam(':fecha_nacimiento', $data['fecha_nacimiento']);
                $resultado->bindParam(':tipo_documento', $data['tipo_documento']);
                $resultado->bindParam(':acudiente', $data['acudiente']);

                // SE GENERA LA CONTRASEÑA
                 $clave = password_hash($data['documento'],PASSWORD_DEFAULT);
                 $resultado->bindParam(':clave',$clave);

                return $resultado -> execute();


            }catch(PDOException $e){
                error_log("Error en Estudiante::registrar->" . $e->getMessage());
                return false;
            }
        }
    }

?>