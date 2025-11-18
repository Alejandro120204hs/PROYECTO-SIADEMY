<?php 

    // IMPORTAMOS LAS DEPENDENCIAS NECESARIAS
    require_once __DIR__ . '/../../config/database.php';

    class Acudiente{
        // LLAMAMOS LA BASE DE DATOS
        private $conexion;

        public function __construct(){
            $db = new Conexion();
            $this -> conexion = $db -> getConexion();
        }

        public function registrar($data){
            try{
                
                // DEFINIMOS EN UNA VARIABLE LA CONSULTA DE SQL SEGUN SEA EL CASO
                $insertar = "INSERT INTO acudiente(nombres,parentesco,correo,telefono,documento,apellidos,edad,clave) VALUES(:nombres,:parentesco,:correo,:telefono,:documento,:apellidos,:edad,:clave)";

                // PREPARAMOS LA ACCION A EJECUTAR Y LA EJECUTAMOS
                $resultado = $this->conexion->prepare($insertar);
                $resultado->bindParam(':nombres', $data['nombres']);
                $resultado->bindParam(':parentesco', $data['parentesco']);
                $resultado->bindParam(':correo', $data['correo']);
                $resultado->bindParam(':telefono', $data['telefono']);
                $resultado->bindParam(':documento', $data['documento']);
                $resultado->bindParam(':apellidos', $data['apellidos']);
                $resultado->bindParam(':edad', $data['edad']);


                // SE GENERA LA CONTRASEÑA
                 $clave = password_hash($data['documento'],PASSWORD_DEFAULT);
                 $resultado->bindParam(':clave',$clave);

                return $resultado -> execute();


            }catch(PDOException $e){
                error_log("Error en Acudiente::registrar->" . $e->getMessage());
                return false;
            }
        }

        public function listar(){
            try{

                // DEFINIMOS EN UNA VARIABLE LA CONSULTA DE SQL SEGUN SEA EL CASO
                $consultar = "SELECT * FROM acudiente ORDER BY apellidos ASC";

                // PREPARAMOS LA ACCION A EJECUTAR Y LA EJECUTAMOS
                $resultado = $this->conexion->prepare($consultar);
                $resultado -> execute();
                return $resultado -> fetchAll();

            }catch(PDOException $e){
                error_log("Error en Acudiente::listar->" . $e->getMessage());
                return[];
            }
        }

        public function listarAcudienteId($id){
            try{
                // DEFINIMOS EN UNA VARIABLE LA CONSULTA DE SQL SEGUN SEA EL CASO
                $consultar = "SELECT * FROM acudiente WHERE id = :id LIMIT 1";

                // PREPARAMOS LA ACCION A EJECUTAR Y LA EJECUTAMOS
                $resultado = $this->conexion->prepare($consultar);
                $resultado->bindParam(':id',$id);
                $resultado -> execute();
                return $resultado -> fetch();

            }catch(PDOException $e){
                error_log("Error en Acudiente::listar->" . $e->getMessage());
                return[];
            }
        }

        public function acutalizar($data){
            try{
                // DEFINIMOS EN UNA VARIABLE LA CONSULTA DE SQL SEGUN SEA EL CASO
                $actualizar = "UPDATE acudiente SET nombres=:nombres, parentesco=:parentesco, correo=:correo, telefono=:telefono, apellidos=:apellidos, edad=:edad, estado=:estado WHERE id = :id";

                // PREPARAMOS LA ACCION A EJECUTAR Y LA EJECUTAMOS
                $resultado = $this->conexion->prepare($actualizar);
                $resultado->bindParam(':id',$data['id']);
                $resultado->bindParam(':nombres',$data['nombres']);
                $resultado->bindParam(':parentesco',$data['parentesco']);
                $resultado->bindParam(':correo',$data['correo']);
                $resultado->bindParam(':telefono',$data['telefono']);
                $resultado->bindParam(':apellidos',$data['apellidos']);
                $resultado->bindParam(':edad',$data['edad']);
                $resultado->bindParam(':estado',$data['estado']);

                return $resultado-> execute();
            }catch(PDOException $e){
                error_log("Error en Acudiente::actualizar->" . $e->getMessage());
                return false;
            }
        }

        public function eliminar($id){
            try{
                // DEFINIMOS EN UNA VARIABLE LA CONSULTA DE SQL SEGUN SEA EL CASO
                $eliminar = "DELETE FROM acudiente WHERE id=:id LIMIT 1";

                // PREPARAMOS LA ACCION A EJECUTAR Y LA EJECUTAMOS
                $resultado = $this->conexion->prepare($eliminar);
                $resultado->bindParam(':id',$id);
                return $resultado->execute();
            }catch(PDOException $e){
                error_log("Error en Acudiente::listar->" . $e->getMessage());
                return[];
            }
        }
    }

?>