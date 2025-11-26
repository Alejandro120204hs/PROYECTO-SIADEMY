<?php

    // IMPORTAMOS LAS DEPENDENCIAS NECESARIAS
    require_once __DIR__ . '/../../../config/database.php';

    class Administrador{
         // LLAMAMOS LA BASE DE DATOS
        private $conexion;

        public function __construct(){
            $db = new Conexion();
            $this -> conexion = $db -> getConexion();
        }

        public function registrar($data){
            try{
                // DEFINIMOS EN UNA VARIABLE LA CONSULTA DE SQL PARA REGISTRAR EL ADMINISTRADOR
                $insertarUsuario = "INSERT INTO usuario(correo,clave,rol,estado) VALUES(:correo,:clave,'Administrador','Activo')";

                // PREPARAMOS LA ACCION A EJECUTAR Y LA EJECUTAMOS
                $resultado = $this -> conexion -> prepare($insertarUsuario);
                $resultado -> bindParam(':correo',$data['correo']);

                $clave = password_hash($data['documento'],PASSWORD_DEFAULT);
                $resultado->bindParam(':clave',$clave);

                $resultado->execute();
                $id_usuario = $this->conexion->lastInsertId();


                // INSERTAMOS DATOS EN LA TABLA ADMINISTRADOR
                $insertarAdministrador = "INSERT INTO administrador(id_usuario,nombres,apellidos,id_institucion,documento,telefono,edad,foto) VALUES(:id_usuario,:nombres,:apellidos,:institucion,:documento,:telefono,:edad,:foto)";

                // PREPARAMOS LA ACCION A EJECUTAR Y LA EJECUTAMOS
                $resultado2 = $this -> conexion -> prepare($insertarAdministrador);
                $resultado2 -> bindParam(':id_usuario',$id_usuario);
                $resultado2 -> bindParam(':nombres',$data['nombres']);
                $resultado2 -> bindParam(':apellidos',$data['apellidos']);
                $resultado2 -> bindParam(':institucion',$data['institucion']);
                $resultado2 -> bindParam(':documento',$data['documento']);
                $resultado2 -> bindParam(':telefono',$data['telefono']);
                $resultado2 -> bindParam(':edad',$data['edad']);
                $resultado2 -> bindParam(':foto',$data['foto']);

                return $resultado2 -> execute();
            }catch(PDOException $e){
                die("Error en Administrador::registrar->" . $e->getMessage());
                
            }
        }

        public function listar(){
            try{
                // DEFINIMOS EN UNA VARIABLE LA CONSULTA DE SQL PARA LISTAR LOS ADMINISTRADORES
                $consultar = "SELECT administrador.*, usuario.correo AS correo, usuario.estado AS estado, institucion.nombre AS nombre_institucion FROM administrador INNER JOIN usuario ON administrador.id_usuario = usuario.id INNER JOIN institucion ON administrador.id_institucion = institucion.id";

                // PREPARAMOS LA ACCION A EJECUTAR Y LA EJECUTAMOS
                $resultado = $this -> conexion -> prepare($consultar);
                $resultado -> execute();
                return $resultado -> fetchAll();
                
            }catch(PDOException $e){
                die("Error en Administrador::listar->" . $e->getMessage());
                return [];   
            }
        }

        public function eliminar($id){
            try{
                // DECLARMOS EN UNA VARIABLE LA CONSULTA DE SQL PARA ELIMINAR
                $eliminar = "UPDATE usuario SET estado='Inactivo' WHERE id=:id";

                // PREPARAMOS LA ACCION A EJECUTAR Y LA EJECUTAMOS
                $resultado = $this -> conexion -> prepare($eliminar);
                $resultado -> bindParam(':id',$id);
                return $resultado -> execute();

            }catch(PDOException $e){
                die("Error en Administrador::eliminar->" . $e->getMessage());

            }
        }

        public function listarAdministradorID($id){
            try{
                // DEFINIMOS EN UNA VARIABLE LA CONSULTA DE SQL PARA MOSTRAR EL ADMINISTRADOR
                $consultar = "SELECT administrador.*, usuario.correo AS correo, usuario.estado AS estado FROM administrador INNER JOIN usuario ON administrador.id_usuario = usuario.id WHERE administrador.id = :id";

                // PREPARAMOS LA ACCION A EJECUTAR Y LA EJECUTAMOS
                $resultado = $this -> conexion -> prepare($consultar);
                $resultado -> bindParam(':id',$id);
                $resultado -> execute();
                return $resultado -> fetch();

            }catch(PDOException $e){
                die("Error en Administrador::listar->" . $e->getMessage());
                return [];   
            }
        }

        public function actualizar($data){
            try{
                // DEFINIMOS EN UNA VARIABLE LA CONSULTA DE SQL PARA ACTUALIZAR
                $actualizarUsuario = "UPDATE usuario SET correo=:correo, estado=:estado WHERE id=:id_usuario";
                // PREPARAMOS LA ACCIONA EJECUTAR Y LA EJECUTAMOS
                $resultado = $this -> conexion ->prepare($actualizarUsuario);
                $resultado -> bindParam(':correo',$data['correo']);
                $resultado -> bindParam(':estado',$data['estado']);
                $resultado -> bindParam(':id_usuario',$data['id_usuario']);
                $resultadoUsuario = $resultado -> execute();

                // ACTUALIZAR ADMINISTRADOR
                $actualizarAdministrador = "UPDATE administrador SET nombres=:nombres, apellidos=:apellidos, edad=:edad, telefono=:telefono WHERE id_usuario = :id_usuario";

                // PREPARAMOS LA ACCION A EJECUTAR Y LA EJECUTAMOS
                $resultado2 = $this -> conexion -> prepare($actualizarAdministrador);
                $resultado2 -> bindParam(':nombres',$data['nombres']);
                $resultado2 -> bindParam(':apellidos',$data['apellidos']);
                $resultado2 -> bindParam(':edad',$data['edad']);
                $resultado2 -> bindParam(':telefono',$data['telefono']);
                $resultado2 -> bindParam(':id_usuario',$data['id_usuario']);

                $resultadoAdministrador = $resultado2 -> execute();

                if($resultadoUsuario && $resultadoAdministrador){
                    return true;
                }else{
                    return false;
                }
                
            }catch(PDOException $e){
                die("Error en Administardor::actualizar->" . $e->getMessage());
                return false;
            }
        
        }

    }
?>