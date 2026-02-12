<?php 

    // IMPORTAMOS LAS DEPENDENCIAS NECESARIAS
    require_once __DIR__ . '/../../../config/database.php';

    class Docente{
        // LLAMAMOS LA BASE DE DATOS
        private $conexion;

        public function __construct(){
            $db = new Conexion();
            $this -> conexion = $db -> getConexion();
        }

        public function registrar($data){
            try{

                // INSERTAMOS DATOS EN LA TABLA USUARIO
                $insertarUsuario = "INSERT INTO usuario(id_institucion,correo,clave,rol,estado)VALUES(:id_institucion,:correo,:clave,'Docente','Activo')";
                
                // SE GENERA LA CONTRASEÑA
                $resultadoUusario = $this->conexion->prepare($insertarUsuario);
                $resultadoUusario->bindParam(':id_institucion', $data['id_institucion']);
                $resultadoUusario->bindParam(':correo', $data['correo']);

                 $clave = password_hash($data['documento'],PASSWORD_DEFAULT);
                 $resultadoUusario->bindParam(':clave',$clave);

                 $resultadoUusario->execute();
                 $id_usuario = $this->conexion->lastInsertId();


                // INSERTAR DATOS EN TABLA ACUDIENTE
                 $insertar ="INSERT INTO docente(id_institucion,id_usuario,nombres,apellidos,tipo_documento,documento,fecha_nacimiento,genero,telefono,direccion,ciudad,profesion,tipo_contrato,fecha_ingreso,fecha_fin_contrato,foto) VALUES(:id_institucion,:id_usuario,:nombres,:apellidos,:tipo_documento,:documento,:fecha_nacimiento,:genero,:telefono,:direccion,:ciudad,:profesion,:tipo_contrato,:fecha_ingreso,:fecha_fin_contrato,:foto)";

                

                    $resultado = $this->conexion->prepare($insertar);

                    $resultado->bindParam(':id_institucion', $data['id_institucion']);
                    $resultado->bindParam(':id_usuario', $id_usuario);
                    $resultado->bindParam(':nombres', $data['nombres']);
                    $resultado->bindParam(':apellidos', $data['apellidos']);
                    $resultado->bindParam(':tipo_documento', $data['tipo_documento']);
                    $resultado->bindParam(':documento', $data['documento']);
                    $resultado->bindParam(':fecha_nacimiento', $data['fecha_nacimiento']);
                    $resultado->bindParam(':genero', $data['genero']);
                    $resultado->bindParam(':telefono', $data['telefono']);
                    $resultado->bindParam(':direccion', $data['direccion']);
                    $resultado->bindParam(':ciudad', $data['ciudad']);
                    $resultado->bindParam(':profesion', $data['profesion']);
                    $resultado->bindParam(':tipo_contrato', $data['tipo_contrato']);
                    $resultado->bindParam(':fecha_ingreso', $data['fecha_ingreso']);
                    $resultado->bindParam(':fecha_fin_contrato', $data['fecha_fin_contrato']);
                    $resultado->bindParam(':foto', $data['foto']);



             

                return $resultado -> execute();


            }catch(PDOException $e){
                die("Error en Docente::registrar->" . $e->getMessage());
            }
        }


        public function listar($id_institucion){

            try{

                // DEFINIMOS EN UNA VARIABLE LA CONSULTA DE SQL SEGUN SEA EL CASO
                $consultar = "SELECT docente.*, usuario.correo AS correo, usuario.estado AS estado FROM docente INNER JOIN usuario ON docente.id_usuario = usuario.id WHERE docente.id_institucion = :id_institucion  ORDER BY apellidos ASC";

                // PREPARAMOS LA ACCION A EJECUTAR Y LA EJECUTAMOS
                $resultado = $this->conexion->prepare($consultar);
                $resultado -> bindParam(':id_institucion', $id_institucion);
                $resultado -> execute();
                return $resultado -> fetchAll();

            }catch(PDOException $e){
                error_log("Error en Docente::listar->" . $e->getMessage());
                return[];
            }
        }

            public function listarId($id){

                try{
                   
                $consultar = "SELECT docente.*, usuario.correo AS correo, usuario.estado AS estado FROM docente INNER JOIN usuario ON docente.id_usuario = usuario.id WHERE docente.id = :id LIMIT 1";

                    // PREPARAMOS LA ACCION A EJECUTAR Y LA EJECUTAMOS
                    $resultado = $this -> conexion -> prepare($consultar);
                    $resultado -> bindParam(':id',$id);
                    $resultado -> execute();
                    return $resultado -> fetch();

                }catch(PDOException $e){
                    die("Error en Docente::listar->" . $e->getMessage());
                    return [];
                }
            }


            public function actualizar($data){
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
                $actualizar = "UPDATE docente SET nombres=:nombres, apellidos=:apellidos, tipo_documento=:tipo_documento, fecha_nacimiento=:fecha_nacimiento, genero=:genero, telefono=:telefono, direccion=:direccion, ciudad=:ciudad, profesion=:profesion, tipo_contrato=:tipo_contrato, fecha_ingreso=:fecha_ingreso, fecha_fin_contrato=:fecha_fin_contrato WHERE id_usuario = :id_usuario";

                // PREPARAMOS LA ACCION A EJECUTAR Y LA EJECUTAMOS
                    $resultado2 = $this->conexion->prepare($actualizar);
                    $resultado2->bindParam(':id_usuario',$data['id_usuario']);
                    $resultado2->bindParam(':nombres', $data['nombres']);
                    $resultado2->bindParam(':apellidos', $data['apellidos']);
                    $resultado2->bindParam(':tipo_documento', $data['tipo_documento']);
                    $resultado2->bindParam(':fecha_nacimiento', $data['fecha_nacimiento']);
                    $resultado2->bindParam(':genero', $data['genero']);
                    $resultado2->bindParam(':telefono', $data['telefono']);
                    $resultado2->bindParam(':direccion', $data['direccion']);
                    $resultado2->bindParam(':ciudad', $data['ciudad']);
                    $resultado2->bindParam(':profesion', $data['profesion']);
                    $resultado2->bindParam(':tipo_contrato', $data['tipo_contrato']);
                    $resultado2->bindParam(':fecha_ingreso', $data['fecha_ingreso']);
                    $resultado2->bindParam(':fecha_fin_contrato', $data['fecha_fin_contrato']);

                    $resultadoAdministrador = $resultado2 -> execute();

                 // EJECUTAMOS EL ACTUALIZAR

                 if($resultadoUsuario && $resultadoAdministrador){
                    return true;
                }else{
                    return false;
                }

            }catch(PDOException $e){
                error_log("Error en Docente::actualizar->" . $e->getMessage());
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
                die("Error en Docente::actualizar->" . $e->getMessage());

            }
        }

        public function contar($id_institucion){
            try{
                $consultar = "SELECT COUNT(*) as total FROM docente INNER JOIN usuario ON docente.id_usuario = usuario.id WHERE docente.id_institucion = :id_institucion AND usuario.estado = 'Activo'";
                $resultado = $this->conexion->prepare($consultar);
                $resultado->bindParam(':id_institucion', $id_institucion);
                $resultado->execute();
                $fila = $resultado->fetch();
                return $fila['total'] ?? 0;
            }catch(PDOException $e){
                error_log("Error en Docente::contar->" . $e->getMessage());
                return 0;
            }
        }
    }
        ?>