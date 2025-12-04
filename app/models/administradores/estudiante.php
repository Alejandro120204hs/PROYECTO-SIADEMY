<?php 

    // IMPORTAMOS LAS DEPENDENCIAS NECESARIAS
    require_once __DIR__ . '/../../../config/database.php';

    class Estudiante{
        // LLAMAMOS LA BASE DE DATOS
        private $conexion;

        public function __construct(){
            $db = new Conexion();
            $this -> conexion = $db -> getConexion();
        }

        public function registrar($data){
            try{
                
                // INSERTAMOS LOS DATOS EN LA TABLA USUARIO
                $insertarUsuario = "INSERT INTO usuario(id_institucion,correo,clave,rol,estado) VALUES(:id_institucion,:correo,:clave,'Estudiante','Activo')";

                // PREPARAMOS LA ACCION A EJECUTAR Y LA EJECUTAMOS
                $resultadoUsuario = $this -> conexion -> prepare($insertarUsuario);
                $resultadoUsuario -> bindParam(':id_institucion',$data['id_institucion']);
                $resultadoUsuario -> bindParam(':correo',$data['correo']);
                // SE GENERA LA CLAVE
                $clave = password_hash($data['documento'],PASSWORD_DEFAULT);
                $resultadoUsuario -> bindParam(':clave',$clave);

                $resultadoUsuario->execute();
                $id_usuario = $this->conexion->lastInsertId();

                // INSERTAMOS LOS DATOS EN LA TABLA ESTUDIANTE
                $insertarEstudiante = "INSERT INTO estudiante(id_institucion,id_usuario,nombres,apellidos,documento,telefono,fecha_de_nacimiento,id_acudiente,tipo_documento,foto,ciudad,direccion,genero) VALUES(:id_institucion,:id_usuario,:nombres,:apellidos,:documento,:telefono,:fecha_nacimiento,:acudiente,:tipo_documento,:foto,:ciudad,:direccion,:genero)";

                // PREPARAMOS LA ACCION A EJECUTAR Y LA EJECUATMOS
                $resultadoEstudiante = $this -> conexion -> prepare($insertarEstudiante);
                $resultadoEstudiante -> bindParam(':id_institucion',$data['id_institucion']);
                $resultadoEstudiante -> bindParam(':id_usuario',$id_usuario);
                $resultadoEstudiante -> bindParam(':nombres',$data['nombres']);
                $resultadoEstudiante -> bindParam(':apellidos',$data['apellidos']);
                $resultadoEstudiante -> bindParam(':documento',$data['documento']);
                $resultadoEstudiante -> bindParam(':telefono',$data['telefono']);
                $resultadoEstudiante -> bindParam(':fecha_nacimiento',$data['fecha_nacimiento']);
                $resultadoEstudiante -> bindParam(':acudiente',$data['acudiente']);
                $resultadoEstudiante -> bindParam(':tipo_documento',$data['tipo_documento']);
                $resultadoEstudiante -> bindParam(':foto',$data['foto']);
                $resultadoEstudiante -> bindParam(':ciudad',$data['ciudad']);
                $resultadoEstudiante -> bindParam(':direccion',$data['direccion']);
                $resultadoEstudiante -> bindParam(':genero',$data['genero']);



                return $resultadoEstudiante -> execute();


            }catch(PDOException $e){
                die("Error en Estudiante::registrar->" . $e->getMessage());
            }
        }

        public function listar($id_institucion){
            try{
                // DEFINIMOS EN UNA VARIABLE LA CONSULTA DE SQL PARA LISTAR LOS ESTUDIANTES
                $consultar = "SELECT estudiante.*, usuario.correo AS correo, usuario.estado AS estado, acudiente.nombres AS nombres_acudiente, acudiente.apellidos AS apellidos_acudiente FROM estudiante INNER JOIN usuario ON estudiante.id_usuario = usuario.id INNER JOIN acudiente ON estudiante.id_acudiente = acudiente.id WHERE estudiante.id_institucion = :id_institucion ORDER BY apellidos ASC";

                // PREPARAMOS LA ACCION A EJECUTAR Y LA EJECUTAMOS
                $resultado = $this -> conexion -> prepare($consultar);
                $resultado -> bindParam(':id_institucion',$id_institucion);
                $resultado -> execute();
                return $resultado -> fetchAll();

            }catch(PDOException $e){
                die("Error en Estudiante::listar->" . $e->getMessage());
                return [];
            }
        }
        
        public function  eliminar($id){
            try{
                // DEFINIMOS EN UNA VARIABLE LA CONSULTA DE SQL PARA ELIMINAR
                $eliminar = "UPDATE usuario SET estado = 'Inactivo' WHERE id=:id";

                // PREPARAMOS LA ACCION A EJECUTAR Y LA EJECUTAMOS
                $resultado = $this -> conexion -> prepare($eliminar);
                $resultado -> bindParam(':id',$id);
                return $resultado -> execute();

            }catch(PDOException $e){
                die("Error en Estudiante::eliminar->" . $e->getMessage());
                
            }
        }

        public function listarId($id){
            try{
                // DEFINIMOS EN UNA VARIABLE LA CONSULTA DE SQL PARA LISTAR
                $consultar = "SELECT estudiante.*, usuario.correo AS correo, usuario.estado AS estado, acudiente.nombres AS nombres_acudiente, acudiente.apellidos AS apellidos_acudiente FROM estudiante INNER JOIN usuario ON estudiante.id_usuario = usuario.id INNER JOIN acudiente ON estudiante.id_acudiente = acudiente.id WHERE estudiante.id = :id LIMIT 1";

                // PREPARAMOS LA ACCION A EJECUTAR Y LA EJECUTAMOS
                $resultado = $this -> conexion -> prepare($consultar);
                $resultado -> bindParam(':id',$id);
                $resultado -> execute();
                return $resultado -> fetch();

            }catch(PDOException $e){
                die("Error en Estudiante::listar->" . $e->getMessage());
                return [];
            }
        }

        public function actulizar($data){
            try{
                // ACTUALIZAR USUARIO
                $actualizarUsuario = "UPDATE usuario SET correo=:correo, estado=:estado WHERE id=:id_usuario";
                // PREPARAMOS LA ACCION A EJECUTAR Y LA EJECUTAMOS
                $resultado = $this -> conexion -> prepare($actualizarUsuario);
                $resultado -> bindParam(':correo',$data['correo']);
                $resultado -> bindParam(':estado',$data['estado']);
                $resultado -> bindParam(':id_usuario',$data['id_usuario']);

                $resultadoUsuario = $resultado -> execute();

                // ACTUALIZAR ESTUDIANTE
                $actualizarEstudiante = "UPDATE estudiante SET nombres=:nombres, apellidos=:apellidos, documento=:documento, telefono=:telefono, fecha_de_nacimiento=:fecha_nacimiento, id_acudiente=:acudiente, tipo_documento=:tipo_documento, ciudad=:ciudad, direccion=:direccion, genero=:genero WHERE id_usuario=:id_usuario";

                // PREPARAMOS LA ACCION A EJECUTAR Y LA EJECUTAMOS
                $resultado2 = $this -> conexion -> prepare($actualizarEstudiante);
                $resultado2 -> bindParam(':id_usuario',$data['id_usuario']);
                $resultado2 -> bindParam(':nombres',$data['nombres']);
                $resultado2 -> bindParam(':apellidos',$data['apellidos']);
                $resultado2 -> bindParam(':documento',$data['documento']);
                $resultado2 -> bindParam(':telefono',$data['telefono']);
                $resultado2 -> bindParam(':fecha_nacimiento',$data['fecha_nacimiento']);
                $resultado2 -> bindParam(':acudiente',$data['acudiente']);
                $resultado2 -> bindParam(':tipo_documento',$data['tipo_documento']);
                $resultado2 -> bindParam(':ciudad',$data['ciudad']);
                $resultado2 -> bindParam(':direccion',$data['direccion']);
                $resultado2 -> bindParam(':genero',$data['genero']);

                $resultadoEstudiante = $resultado2 -> execute();

                // EJECUTAMOS EL ACTUALIZAR

                if($resultadoUsuario && $resultadoEstudiante){
                    return true;
                }else{
                    return false;
                }


            }catch(PDOException $e){
                die("Error en Estudiante::listar->" . $e->getMessage());
                return false;
            }
        }
    }

?>