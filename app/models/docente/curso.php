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
                    asignatura.nombre AS nombre_asignatura,
                    (SELECT COUNT(*) 
                     FROM matricula 
                     WHERE matricula.id_curso = curso.id) as total_estudiantes
                FROM docente_asignatura_curso 
                INNER JOIN asignatura_curso ON docente_asignatura_curso.id_asignatura_curso = asignatura_curso.id 
                INNER JOIN curso ON asignatura_curso.id_curso = curso.id 
                INNER JOIN asignatura ON asignatura_curso.id_asignatura = asignatura.id
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

        public function obtenerEstadisticasDashboard($id_institucion, $id_docente){
            try {
                $consultar = "SELECT
                    COUNT(DISTINCT ac.id_curso) AS total_cursos,
                    COUNT(DISTINCT m.id_estudiante) AS total_estudiantes,
                    COUNT(DISTINCT e.id_acudiente) AS total_acudientes,
                    (
                        SELECT COUNT(*)
                        FROM eventos ev
                        WHERE ev.id_institucion = :id_institucion_eventos
                    ) AS total_eventos
                FROM docente_asignatura_curso dac
                INNER JOIN docente d ON dac.id_docente = d.id
                INNER JOIN asignatura_curso ac ON dac.id_asignatura_curso = ac.id
                LEFT JOIN matricula m ON m.id_curso = ac.id_curso
                LEFT JOIN estudiante e ON e.id = m.id_estudiante
                WHERE d.id_usuario = :id_docente
                  AND dac.id_institucion = :id_institucion
                  AND dac.estado = 'activo'";

                $resultado = $this->conexion->prepare($consultar);
                $resultado->bindParam(':id_docente', $id_docente, PDO::PARAM_INT);
                $resultado->bindParam(':id_institucion', $id_institucion, PDO::PARAM_INT);
                $resultado->bindParam(':id_institucion_eventos', $id_institucion, PDO::PARAM_INT);
                $resultado->execute();

                $fila = $resultado->fetch(PDO::FETCH_ASSOC) ?: [];

                return [
                    'total_estudiantes' => (int)($fila['total_estudiantes'] ?? 0),
                    'total_acudientes' => (int)($fila['total_acudientes'] ?? 0),
                    'total_cursos' => (int)($fila['total_cursos'] ?? 0),
                    'total_eventos' => (int)($fila['total_eventos'] ?? 0),
                ];
            } catch (PDOException $e) {
                error_log("Error en Curso_docente::obtenerEstadisticasDashboard -> " . $e->getMessage());
                return [
                    'total_estudiantes' => 0,
                    'total_acudientes' => 0,
                    'total_cursos' => 0,
                    'total_eventos' => 0,
                ];
            }
        }

        public function listarEstudiantesBajoRendimiento($id_institucion, $id_docente, $limite = 20){
            try {
                $consultar = "SELECT
                    e.nombres,
                    e.apellidos,
                    e.documento,
                    c.grado,
                    c.curso,
                    a.nombre AS asignatura,
                    ROUND(AVG(cal.nota), 2) AS promedio
                FROM docente_asignatura_curso dac
                INNER JOIN docente d ON dac.id_docente = d.id
                INNER JOIN asignatura_curso ac ON dac.id_asignatura_curso = ac.id
                INNER JOIN curso c ON ac.id_curso = c.id
                INNER JOIN asignatura a ON ac.id_asignatura = a.id
                INNER JOIN matricula m ON m.id_curso = c.id
                INNER JOIN estudiante e ON m.id_estudiante = e.id
                LEFT JOIN actividad act ON act.id_asignatura_curso = ac.id
                    AND act.id_docente = d.id
                    AND act.id_institucion = dac.id_institucion
                LEFT JOIN calificacion cal ON cal.id_actividad = act.id
                    AND cal.id_estudiante = e.id
                WHERE d.id_usuario = :id_docente
                  AND dac.id_institucion = :id_institucion
                  AND dac.estado = 'activo'
                GROUP BY e.id, a.id, c.id
                HAVING promedio IS NOT NULL AND promedio < 3.0
                ORDER BY promedio ASC, e.apellidos ASC, e.nombres ASC
                LIMIT :limite";

                $resultado = $this->conexion->prepare($consultar);
                $resultado->bindParam(':id_docente', $id_docente, PDO::PARAM_INT);
                $resultado->bindParam(':id_institucion', $id_institucion, PDO::PARAM_INT);
                $resultado->bindParam(':limite', $limite, PDO::PARAM_INT);
                $resultado->execute();

                return $resultado->fetchAll(PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                error_log("Error en Curso_docente::listarEstudiantesBajoRendimiento -> " . $e->getMessage());
                return [];
            }
        }

        public function obtenerEventosCalendario($id_institucion, $id_docente){
            try {
                $consultar = "
                    SELECT
                        DATE(a.fecha_entrega) AS fecha_evento,
                        COALESCE(NULLIF(a.tipo, ''), 'Tarea') AS tipo_evento,
                        a.titulo AS nombre_evento,
                        a.descripcion,
                        NULL AS hora_inicio,
                        'actividad' AS fuente
                    FROM actividad a
                    LEFT JOIN docente d
                        ON (a.id_docente = d.id OR a.id_docente = d.id_usuario)
                    WHERE a.id_institucion = :id_institucion_actividad
                      AND a.fecha_entrega IS NOT NULL
                      AND (
                          a.id_docente = :id_docente_actividad
                          OR d.id_usuario = :id_docente_actividad_usuario
                      )

                    UNION ALL

                    SELECT
                        DATE(ev.fecha_evento) AS fecha_evento,
                        COALESCE(NULLIF(ev.tipo_evento, ''), 'Evento') AS tipo_evento,
                        ev.nombre_evento,
                        ev.descripcion,
                        ev.hora_inicio,
                        'evento' AS fuente
                    FROM eventos ev
                    WHERE ev.id_institucion = :id_institucion_evento
                      AND ev.fecha_evento IS NOT NULL

                    ORDER BY fecha_evento ASC, hora_inicio ASC, nombre_evento ASC
                ";

                $resultado = $this->conexion->prepare($consultar);
                $resultado->bindParam(':id_institucion_actividad', $id_institucion, PDO::PARAM_INT);
                $resultado->bindParam(':id_docente_actividad', $id_docente, PDO::PARAM_INT);
                $resultado->bindParam(':id_docente_actividad_usuario', $id_docente, PDO::PARAM_INT);
                $resultado->bindParam(':id_institucion_evento', $id_institucion, PDO::PARAM_INT);
                $resultado->execute();

                return $resultado->fetchAll(PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                error_log('Error en Curso_docente::obtenerEventosCalendario -> ' . $e->getMessage());
                return [];
            }
        }
    }

?>