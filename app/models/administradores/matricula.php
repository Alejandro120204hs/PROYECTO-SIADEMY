<?php

    // IMPORTAMOS LAS DEPENDENCIAS NECESARIAS
    require_once __DIR__ . '/../../../config/database.php';

    class Matricula{
        // LLAMAMOS LA BASE DE DATOS
        private $conexion;

        public function __construct(){
            $db = new Conexion();
            $this -> conexion = $db -> getConexion();
        }

        public function registrar($data){
            try{
                // Validar que el estudiante no esté ya matriculado en el mismo curso en el mismo año
                $validar = "SELECT id FROM matricula WHERE id_estudiante = :id_estudiante AND id_curso = :id_curso AND anio = :anio";
                $stmt = $this->conexion->prepare($validar);
                $stmt->bindParam(':id_estudiante', $data['id_estudiante']);
                $stmt->bindParam(':id_curso', $data['id_curso']);
                $stmt->bindParam(':anio', $data['anio']);
                $stmt->execute();
                
                if($stmt->fetch()){
                    return ['success' => false, 'message' => 'El estudiante ya está matriculado en este curso para el año seleccionado.'];
                }

                // Validar cupo disponible
                $consultaCupo = "SELECT 
                    c.cupo_maximo,
                    COUNT(m.id) as matriculados
                FROM curso c
                LEFT JOIN matricula m ON c.id = m.id_curso AND m.anio = :anio
                WHERE c.id = :id_curso
                GROUP BY c.id, c.cupo_maximo";
                
                $stmtCupo = $this->conexion->prepare($consultaCupo);
                $stmtCupo->bindParam(':id_curso', $data['id_curso']);
                $stmtCupo->bindParam(':anio', $data['anio']);
                $stmtCupo->execute();
                $cupoInfo = $stmtCupo->fetch();

                if($cupoInfo && $cupoInfo['matriculados'] >= $cupoInfo['cupo_maximo']){
                    return ['success' => false, 'message' => 'El curso ha alcanzado su cupo máximo (' . $cupoInfo['cupo_maximo'] . ' estudiantes).'];
                }

                // DEFINIMOS EN UNA VARIABLE LA CONSULTA DE SQL PARA REGISTRAR UNA MATRÍCULA
                $insertar = "INSERT INTO matricula(id_institucion, anio, fecha, id_estudiante, id_curso) 
                            VALUES(:id_institucion, :anio, :fecha, :id_estudiante, :id_curso)";

                // PREPARAMOS LA ACCION A EJECUTAR Y LA EJECUTAMOS
                $resultado = $this->conexion->prepare($insertar);
                $resultado->bindParam(':id_institucion', $data['id_institucion']);
                $resultado->bindParam(':anio', $data['anio']);
                $resultado->bindParam(':fecha', $data['fecha']);
                $resultado->bindParam(':id_estudiante', $data['id_estudiante']);
                $resultado->bindParam(':id_curso', $data['id_curso']);

                if($resultado->execute()){
                    return ['success' => true];
                }
                return ['success' => false, 'message' => 'Error al registrar la matrícula.'];

            }catch(PDOException $e){
                return ['success' => false, 'message' => 'Error en la base de datos: ' . $e->getMessage()];
            }
        }

        public function listar($id_institucion){
            try{
                // DEFINIMOS EN UNA VARIABLE LA CONSULTA DE SQL PARA MOSTRAR LAS MATRÍCULAS
                $consultar = "SELECT 
                    m.*,
                    e.nombres AS estudiante_nombres,
                    e.apellidos AS estudiante_apellidos,
                    e.documento AS estudiante_documento,
                    c.grado,
                    c.curso AS nombre_curso,
                    c.estado AS estado_curso,
                    n.nombre AS nivel_academico
                FROM matricula m
                INNER JOIN estudiante e ON m.id_estudiante = e.id
                INNER JOIN curso c ON m.id_curso = c.id
                INNER JOIN nivel_academico n ON c.id_nivel_academico = n.id
                WHERE m.id_institucion = :id_institucion
                ORDER BY m.anio DESC, c.grado ASC, c.curso ASC";

                // PREPARAMOS LA ACCION A EJECUTAR Y LA EJECUTAMOS
                $resultado = $this->conexion->prepare($consultar);
                $resultado->bindParam(':id_institucion', $id_institucion);
                $resultado->execute();
                return $resultado->fetchAll();

            }catch(PDOException $e){
                die("Error en Matricula::listar -> " . $e->getMessage());
                return [];
            }
        }

        public function listarPorCurso($id_curso, $anio){
            try{
                $consultar = "SELECT 
                    m.*,
                    e.nombres AS estudiante_nombres,
                    e.apellidos AS estudiante_apellidos,
                    e.documento AS estudiante_documento,
                    e.foto
                FROM matricula m
                INNER JOIN estudiante e ON m.id_estudiante = e.id
                WHERE m.id_curso = :id_curso AND m.anio = :anio
                ORDER BY e.apellidos ASC, e.nombres ASC";

                $resultado = $this->conexion->prepare($consultar);
                $resultado->bindParam(':id_curso', $id_curso);
                $resultado->bindParam(':anio', $anio);
                $resultado->execute();
                return $resultado->fetchAll();

            }catch(PDOException $e){
                die("Error en Matricula::listarPorCurso -> " . $e->getMessage());
                return [];
            }
        }

        public function listarMatriculaId($id){
            try{
                // DEFINIMOS EN UNA VARIABLE LA CONSULTA DE SQL PARA MOSTRAR LA MATRÍCULA POR ID
                $consultar = "SELECT 
                    m.*,
                    e.nombres AS estudiante_nombres,
                    e.apellidos AS estudiante_apellidos,
                    e.documento AS estudiante_documento,
                    c.grado,
                    c.curso AS nombre_curso,
                    n.nombre AS nivel_academico
                FROM matricula m
                INNER JOIN estudiante e ON m.id_estudiante = e.id
                INNER JOIN curso c ON m.id_curso = c.id
                INNER JOIN nivel_academico n ON c.id_nivel_academico = n.id
                WHERE m.id = :id
                LIMIT 1";

                // PREPARAMOS LA ACCION A EJECUTAR Y LA EJECUTAMOS
                $resultado = $this->conexion->prepare($consultar);
                $resultado->bindParam(':id', $id);
                $resultado->execute();
                return $resultado->fetch();

            }catch(PDOException $e){
                die("Error en Matricula::listarMatriculaId -> " . $e->getMessage());
                return [];
            }
        }

        public function actualizar($data){
            try{
                // Validar que no exista otra matrícula del mismo estudiante en el mismo año (excepto la actual)
                $validar = "SELECT id FROM matricula 
                           WHERE id_estudiante = :id_estudiante 
                           AND id_curso = :id_curso 
                           AND anio = :anio 
                           AND id != :id";
                $stmt = $this->conexion->prepare($validar);
                $stmt->bindParam(':id_estudiante', $data['id_estudiante']);
                $stmt->bindParam(':id_curso', $data['id_curso']);
                $stmt->bindParam(':anio', $data['anio']);
                $stmt->bindParam(':id', $data['id']);
                $stmt->execute();
                
                if($stmt->fetch()){
                    return ['success' => false, 'message' => 'El estudiante ya está matriculado en este curso para el año seleccionado.'];
                }

                // DEFINIMOS EN UNA VARIABLE LA CONSULTA DE SQL PARA ACTUALIZAR LA MATRÍCULA
                $actualizar = "UPDATE matricula 
                              SET anio = :anio, 
                                  fecha = :fecha, 
                                  id_estudiante = :id_estudiante, 
                                  id_curso = :id_curso 
                              WHERE id = :id";

                // PREPARAMOS LA ACCION A EJECUTAR Y LA EJECUTAMOS
                $resultado = $this->conexion->prepare($actualizar);
                $resultado->bindParam(':anio', $data['anio']);
                $resultado->bindParam(':fecha', $data['fecha']);
                $resultado->bindParam(':id_estudiante', $data['id_estudiante']);
                $resultado->bindParam(':id_curso', $data['id_curso']);
                $resultado->bindParam(':id', $data['id']);

                if($resultado->execute()){
                    return ['success' => true];
                }
                return ['success' => false, 'message' => 'Error al actualizar la matrícula.'];

            }catch(PDOException $e){
                return ['success' => false, 'message' => 'Error en la base de datos: ' . $e->getMessage()];
            }
        }

        public function eliminar($id){
            try{
                // DEFINIMOS EN UNA VARIABLE LA CONSULTA DE SQL PARA ELIMINAR LA MATRÍCULA
                $eliminar = "DELETE FROM matricula WHERE id = :id";
                $resultado = $this->conexion->prepare($eliminar);
                $resultado->bindParam(':id', $id);

                return $resultado->execute();

            }catch(PDOException $e){
                die("Error en Matricula::eliminar -> " . $e->getMessage());
            }
        }

        // Método auxiliar para obtener estadísticas
        public function obtenerEstadisticas($id_institucion, $anio){
            try{
                $consultar = "SELECT 
                    COUNT(DISTINCT m.id) as total_matriculas,
                    COUNT(DISTINCT m.id_estudiante) as total_estudiantes,
                    COUNT(DISTINCT m.id_curso) as cursos_con_matriculas
                FROM matricula m
                WHERE m.id_institucion = :id_institucion AND m.anio = :anio";

                $resultado = $this->conexion->prepare($consultar);
                $resultado->bindParam(':id_institucion', $id_institucion);
                $resultado->bindParam(':anio', $anio);
                $resultado->execute();
                return $resultado->fetch();

            }catch(PDOException $e){
                return ['total_matriculas' => 0, 'total_estudiantes' => 0, 'cursos_con_matriculas' => 0];
            }
        }
    }

?>
