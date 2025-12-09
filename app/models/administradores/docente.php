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
    }
        ?>