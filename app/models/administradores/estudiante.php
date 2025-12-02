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
                $insertarEstudiante = "INSERT INTO estudiante(id_institucion,id_usuario,nombres,apellidos,documento,telefono,fecha_de_nacimiento,id_acudiente,tipo_documento,foto) VALUES(:id_institucion,:id_usuario,:nombres,:apellidos,:documento,:telefono,:fecha_nacimiento,:acudiente,:tipo_documento,:foto)";

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

                return $resultadoEstudiante -> execute();


            }catch(PDOException $e){
                die("Error en Estudiante::registrar->" . $e->getMessage());
            }
        }
    }

?>