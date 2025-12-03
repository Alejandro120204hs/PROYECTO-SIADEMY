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
                //insertamos datos en la tabla usuario
                $insertarUsuario = "INSERT into usuario(id_institucion,correo,clave,rol,estado)VALUES(:id_institucion,:correo,:clave,'Docente','Activo')";

                //se genera la contraseña
                $resultadoUsuario = $this->conexion->prepare($insertarUsuario);
                $resultadoUsuario->bindParam(':id_institucion', $data['id_institucion']);     
                $resultadoUsuario->bindParam(':correo', $data['correo']);

                 $clave = password_hash($data['documento'],PASSWORD_DEFAULT);
                 $resultadoUsuario->bindParam(':clave',$clave);

                 $resultadoUsuario->execute();
                 $id_usuario = $this->conexion->lastInsertId();

                 //insertamos los datos del registro de docente en la tabla de docente

                 $insertar ="INSERT INTO docente (id_institucion,id_usuario,nombres,apellidos,tipo_documento,documento,fecha_nacimiento,genero,telefono,direccion,ciudad,profesion,tipo_contrato,fecha_ingreso,fecha_fin_contrato,estado,foto,fecha_registro,actualizado_en) VALUES(:id_institucion,:id_usuario,:nombres,:apellidos,:tipo_documento,:documento,:fecha_nacimiento,:genero,:telefono,:direccion,:ciudad,:profesion,:tipo_contrato,:fecha_ingreso,:fecha_fin_contrato,:estado,:foto)";

                //preparamos la accion a ejecutar y la ejecutamos 
                // PREPARAMOS LA ACCION A EJECUTAR Y LA EJECUTAMOS
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
                $resultado->bindParam(':estado', $data['estado']);
                $resultado->bindParam(':foto', $data['foto']);

                return $resultado -> execute();


            }catch(PDOException $e){
                error_log("Error en Docente::registrar->" . $e->getMessage());
                return false;
                }


         }
        }
?>