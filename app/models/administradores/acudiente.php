<?php 

    // IMPORTAMOS LAS DEPENDENCIAS NECESARIAS
    require_once __DIR__ . '/../../../config/database.php';

    class Acudiente{
        // LLAMAMOS LA BASE DE DATOS
        private $conexion;

        public function __construct(){
            $db = new Conexion();
            $this -> conexion = $db -> getConexion();
        }

        public function registrar($data){
            try{

                // INSERTAMOS DATOS EN LA TABLA USUARIO
                $insertarUsuario = "INSERT INTO usuario(id_institucion,correo,clave,rol,estado)VALUES(:id_institucion,:correo,:clave,'Acudiente','Activo')";
                
                // SE GENERA LA CONTRASEÑA
                $resultadoUusario = $this->conexion->prepare($insertarUsuario);
                $resultadoUusario->bindParam(':id_institucion', $data['id_institucion']);
                $resultadoUusario->bindParam(':correo', $data['correo']);

                 $clave = password_hash($data['documento'],PASSWORD_DEFAULT);
                 $resultadoUusario->bindParam(':clave',$clave);

                 $resultadoUusario->execute();
                 $id_usuario = $this->conexion->lastInsertId();


                // INSERTAR DATOS EN TABLA ACUDIENTE
                $insertar = "INSERT INTO acudiente(id_institucion,id_usuario,nombres,parentesco,telefono,documento,apellidos,edad,foto) VALUES(:id_institucion,:id_usuario,:nombres,:parentesco,:telefono,:documento,:apellidos,:edad,:foto)";

                

                // PREPARAMOS LA ACCION A EJECUTAR Y LA EJECUTAMOS
                $resultado = $this->conexion->prepare($insertar);
                $resultado->bindParam(':id_institucion', $data['id_institucion']);
                $resultado->bindParam(':id_usuario', $id_usuario);
                $resultado->bindParam(':nombres', $data['nombres']);
                $resultado->bindParam(':parentesco', $data['parentesco']);
                $resultado->bindParam(':telefono', $data['telefono']);
                $resultado->bindParam(':documento', $data['documento']);
                $resultado->bindParam(':apellidos', $data['apellidos']);
                $resultado->bindParam(':edad', $data['edad']);
                $resultado->bindParam(':foto', $data['foto']);


             

                return $resultado -> execute();


            }catch(PDOException $e){
                error_log("Error en Acudiente::registrar->" . $e->getMessage());
                return false;
            }
        }

        public function listar($id_institucion){
            try{

                // DEFINIMOS EN UNA VARIABLE LA CONSULTA DE SQL SEGUN SEA EL CASO
                $consultar = "SELECT acudiente.*, usuario.correo AS correo, usuario.estado AS estado FROM acudiente INNER JOIN usuario ON acudiente.id_usuario = usuario.id WHERE acudiente.id_institucion = :id_institucion  ORDER BY apellidos ASC";

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

        public function listarAcudienteId($id){
            try{
                // DEFINIMOS EN UNA VARIABLE LA CONSULTA DE SQL SEGUN SEA EL CASO
                $consultar = "SELECT acudiente.*, usuario.correo AS correo, usuario.estado AS estado FROM acudiente INNER JOIN usuario ON acudiente.id_usuario = usuario.id WHERE acudiente.id = :id LIMIT 1";

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

                // ACTUALIZAR USUARIO
                $actualizarUsuario = "UPDATE usuario SET correo=:correo, estado=:estado WHERE id=:id_usuario";

                // PREPARAMOS LA ACCION A EJECUTAR Y LA EJECUTAMOS
                $resultado = $this -> conexion -> prepare($actualizarUsuario);
                $resultado -> bindParam(':correo',$data['correo']);
                $resultado -> bindParam(':estado',$data['estado']); 
                $resultado -> bindParam(':id_usuario',$data['id_usuario']);

                $resultadoUsuario = $resultado -> execute();


                // DEFINIMOS EN UNA VARIABLE LA CONSULTA DE SQL SEGUN SEA EL CASO
                $actualizar = "UPDATE acudiente SET nombres=:nombres, parentesco=:parentesco, telefono=:telefono, apellidos=:apellidos, edad=:edad WHERE id_usuario = :id_usuario";

                // PREPARAMOS LA ACCION A EJECUTAR Y LA EJECUTAMOS
                $resultado2 = $this->conexion->prepare($actualizar);
                $resultado2->bindParam(':id_usuario',$data['id_usuario']);
                $resultado2->bindParam(':nombres',$data['nombres']);
                $resultado2->bindParam(':parentesco',$data['parentesco']);
                $resultado2->bindParam(':telefono',$data['telefono']);
                $resultado2->bindParam(':apellidos',$data['apellidos']);
                $resultado2->bindParam(':edad',$data['edad']);
              

                $resultadoAdministrador = $resultado2 -> execute();

                 if($resultadoUsuario && $resultadoAdministrador){
                    return true;
                }else{
                    return false;
                }

            }catch(PDOException $e){
                error_log("Error en Acudiente::actualizar->" . $e->getMessage());
                return false;
            }
        }

        public function eliminar($id){
            try{

                $actualizar = "UPDATE usuario SET estado = 'Inactivo' WHERE id=:id";
                 // PREPARAMOS LA ACCION A EJECUTAR Y LA EJECUTAMOS
                $resultado = $this -> conexion -> prepare($actualizar);
                $resultado -> bindParam(':id',$id);
                return $resultado -> execute();
            }catch(PDOException $e){
                die("Error en Acudiente::actualizar->" . $e->getMessage());

            }
        }
    }

?>