<?php

     // IMPORTAMOS LAS DEPENDENCIAS NECESARIAS
    require_once __DIR__ . '/../../../config/database.php';

    class Curso_docente{
        // LLAMAMOS LA BASE DE DATOS
        private $conexion;

        public function __construct(){
            $db = new Conexion();
            $this -> conexion = $db -> getConexion();
        }

        public function listar($id_institucion, $id_docente){
            try{
                // DEFINIMOS EN UNA VARIABLE LA CONSULTA DE SQL SEGUN SEA EL CASO
                $consultar = "SELECT DISTINCT
                    curso.id, 
                    curso.curso, 
                    curso.grado,
                    curso.jornada,
                    (SELECT COUNT(*) 
                     FROM matricula 
                     WHERE matricula.id_curso = curso.id) as total_estudiantes
                FROM docente_asignatura_curso 
                INNER JOIN asignatura_curso ON docente_asignatura_curso.id_asignatura_curso = asignatura_curso.id 
                INNER JOIN curso ON asignatura_curso.id_curso = curso.id 
                INNER JOIN docente ON docente_asignatura_curso.id_docente = docente.id
                WHERE docente.id_usuario = :id_docente
                AND docente_asignatura_curso.id_institucion = :id_institucion
                AND docente_asignatura_curso.estado = 'activo'
                ORDER BY curso.grado, curso.curso";

                // PREPARAMOS LA ACCION A EJECUTAR Y LA EJECUTAMOS
                $resultado = $this -> conexion -> prepare($consultar);
                $resultado -> bindParam(':id_institucion', $id_institucion);
                $resultado -> bindParam(':id_docente', $id_docente);
                $resultado -> execute();
                
                return $resultado -> fetchAll(PDO::FETCH_ASSOC);

            }catch(PDOException $e){
                die("Error en Curso::listar->" . $e->getMessage());
                return [];
            }
        }
    }

?>