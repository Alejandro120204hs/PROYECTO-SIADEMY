<?php

    // IMPORTAMOS LAS DEPENDENCIAS NECESARIAS
    require_once __DIR__ . '/../../../config/database.php';

    class Curso{
        // LLAMAMOS LA BASE DE DATOS
        private $conexion;

        public function __construct(){
            $db = new Conexion();
            $this -> conexion = $db -> getConexion();
        }

        public function registrar($data){
            try{

                // Si NO enviaron el año → asignar año actual automáticamente
                if (empty($data['anio'])) {
                    $data['anio'] = date('Y');
                }

                // DEFINIMOS EN UNA VARIABLE LA CONSULTA DE SQL PARA REGISTRAR UN CURSO
                $insertar = "INSERT INTO curso(id_institucion,id_docente,grado,curso,anio,estado,id_nivel_academico,jornada,cupo_maximo)VALUES(:id_institucion,:docente,:grado,:curso,:anio,'Activo',:nivel,:jornada,:cupo)";

                // PREPARAMOS LA ACCION A EJECUTAR Y LA EJECUTAMOS
                $resultado = $this -> conexion -> prepare($insertar);
                $resultado -> bindParam(':id_institucion',$data['id_institucion']);
                $resultado -> bindParam(':docente',$data['docente']);
                $resultado -> bindParam(':grado',$data['grado']);
                $resultado -> bindParam(':curso',$data['curso']);
                $resultado -> bindParam(':anio',$data['anio']);
                $resultado -> bindParam(':nivel',$data['nivel']);
                $resultado -> bindParam(':jornada',$data['jornada']);
                $resultado -> bindParam(':cupo',$data['cupo']);

                return $resultado -> execute();

            }catch(PDOException $e){
                die("Error en Estudiante::registrar->" . $e->getMessage());
            }
        }

        public function listar($id_institucion){
            try{
                // DEFINIMOS EN UNA VARIABLE LA CONSULTA DE SQL PARA MOSTRAR LOS CURSOS
                $consultar = "SELECT curso.*, nivel_academico.nombre  AS nivel_academico, docente.nombres AS nombres_docente, docente.apellidos AS apellidos_docente FROM curso INNER JOIN nivel_academico ON curso.id_nivel_academico = nivel_academico.id INNER JOIN docente ON curso.id_docente = docente.id WHERE curso.id_institucion = :id_institucion ORDER BY curso.estado ASC";

                // PREPARAMOS LA ACCION A EJECUTAR Y LA EJECUTAMOS
                $resultado = $this -> conexion -> prepare($consultar);
                $resultado -> bindParam(':id_institucion',$id_institucion);
                $resultado -> execute();
                return $resultado -> fetchAll();

            }catch(PDOException $e){
                die("Error en Acudiente::listar->" . $e->getMessage());
                return[];
            }
        }

        public function eliminar($id){
            try{
                // DEFINIMOS EN UNA VARIABLE LA CONSULTA DE SQL PARA ELIMINAR EL CURSO
                $eliminar = "UPDATE curso SET estado = 'Inactivo' WHERE id=:id";
                $resultado = $this -> conexion -> prepare($eliminar);
                $resultado -> bindParam(':id',$id);

                return $resultado -> execute();

            }catch(PDOException $e){
                die("Error en Estudiante::registrar->" . $e->getMessage());
            }
        }

        public function listarCursoId($id){
            try{
                // DEFINIMOS EN UNA VARIABLE LA CONSULTA DE SQL PARA MOSTRAR EL CURSO POR ID
                $consultar = "SELECT curso.*, nivel_academico.nombre  AS nivel_academico, docente.nombres AS nombres_docente, docente.apellidos AS apellidos_docente FROM curso INNER JOIN nivel_academico ON curso.id_nivel_academico = nivel_academico.id INNER JOIN docente ON curso.id_docente = docente.id WHERE curso.id = :id LIMIT 1";

                // PREPARAMOS LA ACCION A EJECUTAR Y LA EJECUTAMOS
                $resultado = $this -> conexion -> prepare($consultar);
                $resultado -> bindParam(':id',$id);
                $resultado -> execute();
                return $resultado -> fetch();
            }catch(PDOException $e){
                die("Error en Curso::listarCursoId->" . $e->getMessage());
                return[];
            }
        }

        public function actualizar($data){
            try{
                // DEFINIMOS EN UNA VARIABLE LA CONSULTA DE SQL PARA ACTUALIZAR EL CURSO
                $actualizar = "UPDATE curso SET grado=:grado, id_docente=:docente, cupo_maximo=:cupo, estado=:estado, curso=:curso, id_nivel_academico=:nivel, jornada=:jornada WHERE id=:id";

                // PREPARAMOS LA ACCION A EJECUTAR Y LA EJECUTAMOS
                $resultado = $this -> conexion -> prepare($actualizar);
                $resultado -> bindParam(':grado',$data['grado']);
                $resultado -> bindParam(':docente',$data['docente']);
                $resultado -> bindParam(':cupo',$data['cupo']);
                $resultado -> bindParam(':estado',$data['estado']);
                $resultado -> bindParam(':curso',$data['curso']);
                $resultado -> bindParam(':nivel',$data['nivel']);
                $resultado -> bindParam(':jornada',$data['jornada']);
                $resultado -> bindParam(':id',$data['id']);

                return $resultado -> execute();

            }catch(PDOException $e){
                die("Error en Curso::actualizar->" . $e->getMessage());
            }
        }

        public function contar($id_institucion){
            try{
                $consultar = "SELECT COUNT(*) as total FROM curso WHERE id_institucion = :id_institucion AND estado = 'Activo'";
                $resultado = $this->conexion->prepare($consultar);
                $resultado->bindParam(':id_institucion', $id_institucion);
                $resultado->execute();
                $fila = $resultado->fetch();
                return $fila['total'] ?? 0;
            }catch(PDOException $e){
                error_log("Error en Curso::contar->" . $e->getMessage());
                return 0;
            }
        }
    }

?>