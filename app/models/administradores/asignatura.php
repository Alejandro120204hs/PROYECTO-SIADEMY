<?php

    // IMPORTAMOS LAS DEPENDENCIAS NECESARIAS
    require_once __DIR__ . '/../../../config/database.php';

    class Asignatura{
        // llamamos la base datos
        private $conexion;
        public function __construct(){
            $db = new Conexion;
            $this -> conexion = $db -> getConexion();
        }

        public function registrar($data){
            try{
                // INSERTAMOS DATOS EN LA TABLA USUARIO

                $insertar = "INSERT INTO asignatura(id_institucion, nombre, descripcion, estado) VALUES (:id_institucion, :nombre, :descripcion, 'Activo')";

                // PREPARAMOS LA ACCION A EJECUTAR Y LA EJECUTAMOS
                $resultado = $this->conexion->prepare($insertar);
                $resultado->bindParam(':id_institucion', $data['id_institucion']);
                $resultado->bindParam(':nombre', $data['nombre']);
                $resultado->bindParam(':descripcion', $data['descripcion']);


                return $resultado -> execute();

            }catch(PDOException $e){
                error_log("Error en Acudiente::registrar->" . $e->getMessage());
                return false;
            }
        }


        public function listar($id_institucion){
            try{

                // DEFINIMOS EN UNA VARIABLE LA CONSULTA DE SQL SEGUN SEA EL CASO
                $consultar = "SELECT * FROM asignatura WHERE id_institucion = :id_institucion  ORDER BY estado ASC";


                // PREPARAMOS LA ACCION A EJECUTAR Y LA EJECUTAMOS
                $resultado = $this->conexion->prepare($consultar);
                $resultado -> bindParam(':id_institucion', $id_institucion);
                $resultado -> execute();
                return $resultado -> fetchAll();


            }catch(PDOException $e){
                error_log("Error en Asignatura::listar->" . $e->getMessage());
                return[];
            }
        }

        public function listarAsignaturaId($id){

            try{
                // DEFINIMOS EN UNA VARIABLE LA CONSULTA DE SQL SEGUN SEA EL CASO
                $consultar = "SELECT * FROM asignatura WHERE id = :id LIMIT 1";

                // PREPARAR Y EJECUTAR
                $resultado = $this->conexion->prepare($consultar);
                $resultado->bindParam(':id', $id, PDO::PARAM_INT);
                $resultado->execute();

                return $resultado->fetch();

            }catch(PDOException $e){
                error_log("Error en Asignatura::listar->" . $e->getMessage());
                return[];
            }
        }


        public function actualizar($data){
            try{
                // DEFINIMOS EN UNA VARIABLE LA CONSULTA DE SQL SEGUN SEA EL CASO

                $actualizar = "UPDATE asignatura SET nombre=:nombre, descripcion=:descripcion, estado=:estado WHERE id = :id";

                // PREPARAMOS LA ACCION A EJECUTAR Y LA EJECUTAMOS
                $resultado = $this->conexion->prepare($actualizar);
                $resultado->bindParam(':id',$data['id']);
                $resultado->bindParam(':nombre',$data['nombre']);
                $resultado->bindParam(':descripcion',$data['descripcion']);
                $resultado->bindParam(':estado',$data['estado']);

                $resultado -> execute();

                 if($resultado){
                    return true;
                }else{
                    return false;
                }

            }catch(PDOException $e){
                error_log("Error en Asignatura::actualizar->" . $e->getMessage());
                return false;
            }
        }

        public function eliminar($id){
            try{

                $actualizar = "UPDATE asignatura SET estado = 'Inactivo' WHERE id = :id";
                 // PREPARAMOS LA ACCION A EJECUTAR Y LA EJECUTAMOS
                $resultado = $this -> conexion -> prepare($actualizar);
                $resultado -> bindParam(':id',$id);
                return $resultado -> execute();
            }catch(PDOException $e){
                error_log("Error en Asignatura::eliminar -> " . $e->getMessage());
                return false;
            }
        }

        public function contar($id_institucion){
            try{
                $consultar = "SELECT COUNT(*) as total FROM asignatura WHERE id_institucion = :id_institucion AND estado = 'Activo'";
                $resultado = $this->conexion->prepare($consultar);
                $resultado->bindParam(':id_institucion', $id_institucion);
                $resultado->execute();
                $fila = $resultado->fetch();
                return $fila['total'] ?? 0;
            }catch(PDOException $e){
                error_log("Error en Asignatura::contar->" . $e->getMessage());
                return 0;
            }
        }

        public function obtenerDocentesDeAsignatura(int $id_asignatura, int $id_institucion): array
        {
            try {
                $stmt = $this->conexion->prepare(
                    "SELECT DISTINCT d.id, d.nombres, d.apellidos, d.foto,
                            c.id AS id_curso, c.grado, c.curso, c.jornada
                     FROM docente_asignatura_curso dac
                     INNER JOIN docente d ON d.id = dac.id_docente
                     INNER JOIN asignatura_curso ac ON ac.id = dac.id_asignatura_curso
                     INNER JOIN curso c ON c.id = ac.id_curso
                     WHERE ac.id_asignatura = :id_asignatura
                       AND c.id_institucion = :id_institucion
                     ORDER BY c.grado ASC, d.apellidos ASC"
                );
                $stmt->bindValue(':id_asignatura',  $id_asignatura,  PDO::PARAM_INT);
                $stmt->bindValue(':id_institucion', $id_institucion, PDO::PARAM_INT);
                $stmt->execute();
                return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
            } catch (PDOException $e) {
                error_log("Asignatura::obtenerDocentesDeAsignatura->" . $e->getMessage());
                return [];
            }
        }

        public function obtenerCursosDeAsignatura(int $id_asignatura, int $id_institucion): array
        {
            try {
                $anio = (int) date('Y');
                $stmt = $this->conexion->prepare(
                    "SELECT c.id, c.grado, c.curso, c.jornada, c.estado,
                            COUNT(DISTINCT m.id_estudiante) AS total_estudiantes
                     FROM asignatura_curso ac
                     INNER JOIN curso c ON c.id = ac.id_curso
                     LEFT JOIN matricula m ON m.id_curso = c.id AND m.anio = :anio
                     WHERE ac.id_asignatura = :id_asignatura
                       AND c.id_institucion = :id_institucion
                     GROUP BY c.id, c.grado, c.curso, c.jornada, c.estado
                     ORDER BY c.grado ASC, c.curso ASC"
                );
                $stmt->bindValue(':id_asignatura',  $id_asignatura,  PDO::PARAM_INT);
                $stmt->bindValue(':id_institucion', $id_institucion, PDO::PARAM_INT);
                $stmt->bindValue(':anio',           $anio,           PDO::PARAM_INT);
                $stmt->execute();
                return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
            } catch (PDOException $e) {
                error_log("Asignatura::obtenerCursosDeAsignatura->" . $e->getMessage());
                return [];
            }
        }

        public function obtenerEstadisticasDetalle(int $id_asignatura): array
        {
            $default = [
                'total_calificaciones' => 0,
                'promedio'             => null,
                'nivel_superior'       => 0,
                'nivel_alto'           => 0,
                'nivel_basico'         => 0,
                'nivel_bajo'           => 0,
            ];
            try {
                $stmt = $this->conexion->prepare(
                    "SELECT
                         COUNT(cal.id)                                                    AS total_calificaciones,
                         COALESCE(ROUND(AVG(cal.nota), 1), NULL)                         AS promedio,
                         SUM(CASE WHEN cal.nota >= 4.5 THEN 1 ELSE 0 END)               AS nivel_superior,
                         SUM(CASE WHEN cal.nota >= 4.0 AND cal.nota < 4.5 THEN 1 ELSE 0 END) AS nivel_alto,
                         SUM(CASE WHEN cal.nota > 3.0  AND cal.nota < 4.0 THEN 1 ELSE 0 END) AS nivel_basico,
                         SUM(CASE WHEN cal.nota <= 3.0 THEN 1 ELSE 0 END)               AS nivel_bajo
                     FROM calificacion cal
                     INNER JOIN actividad act ON act.id = cal.id_actividad
                     INNER JOIN asignatura_curso ac ON ac.id = act.id_asignatura_curso
                     WHERE ac.id_asignatura = :id_asignatura"
                );
                $stmt->bindValue(':id_asignatura', $id_asignatura, PDO::PARAM_INT);
                $stmt->execute();
                return $stmt->fetch(PDO::FETCH_ASSOC) ?: $default;
            } catch (PDOException $e) {
                error_log("Asignatura::obtenerEstadisticasDetalle->" . $e->getMessage());
                return $default;
            }
        }

        public function obtenerPromedioMensual(int $id_institucion, int $anio): array
        {
            $serie = array_fill(0, 12, null);
            try {
                $stmt = $this->conexion->prepare(
                    "SELECT MONTH(act.fecha_entrega) AS mes,
                            ROUND(AVG(cal.nota), 2) AS promedio
                     FROM calificacion cal
                     INNER JOIN actividad act ON act.id = cal.id_actividad
                     INNER JOIN asignatura_curso ac ON ac.id = act.id_asignatura_curso
                     INNER JOIN asignatura asig ON asig.id = ac.id_asignatura
                     WHERE asig.id_institucion = :id_institucion
                       AND YEAR(act.fecha_entrega) = :anio
                     GROUP BY MONTH(act.fecha_entrega)"
                );
                $stmt->bindValue(':id_institucion', $id_institucion, PDO::PARAM_INT);
                $stmt->bindValue(':anio',           $anio,           PDO::PARAM_INT);
                $stmt->execute();
                foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
                    $idx = (int)$row['mes'] - 1;
                    if ($idx >= 0 && $idx < 12) {
                        $serie[$idx] = (float)$row['promedio'];
                    }
                }
            } catch (PDOException $e) {
                error_log("Asignatura::obtenerPromedioMensual->" . $e->getMessage());
            }
            return $serie;
        }

        public function obtenerPromedioAnual(int $id_institucion, int $anio): float
        {
            try {
                $stmt = $this->conexion->prepare(
                    "SELECT ROUND(AVG(cal.nota), 1) AS promedio
                     FROM calificacion cal
                     INNER JOIN actividad act ON act.id = cal.id_actividad
                     INNER JOIN asignatura_curso ac ON ac.id = act.id_asignatura_curso
                     INNER JOIN asignatura asig ON asig.id = ac.id_asignatura
                     WHERE asig.id_institucion = :id_institucion
                       AND YEAR(act.fecha_entrega) = :anio"
                );
                $stmt->bindValue(':id_institucion', $id_institucion, PDO::PARAM_INT);
                $stmt->bindValue(':anio',           $anio,           PDO::PARAM_INT);
                $stmt->execute();
                return (float)($stmt->fetchColumn() ?: 0);
            } catch (PDOException $e) {
                error_log("Asignatura::obtenerPromedioAnual->" . $e->getMessage());
                return 0.0;
            }
        }

        public function obtenerPromedioGeneral(int $id_institucion): string
        {
            try {
                $stmt = $this->conexion->prepare(
                    "SELECT COALESCE(ROUND(AVG(cal.nota), 1), 0) AS promedio
                     FROM calificacion cal
                     INNER JOIN actividad a      ON a.id  = cal.id_actividad
                     INNER JOIN asignatura_curso ac ON ac.id = a.id_asignatura_curso
                     INNER JOIN asignatura asig  ON asig.id = ac.id_asignatura
                     WHERE asig.id_institucion = :id_institucion"
                );
                $stmt->bindParam(':id_institucion', $id_institucion, PDO::PARAM_INT);
                $stmt->execute();
                return (string) ($stmt->fetchColumn() ?: '0.0');
            } catch (PDOException $e) {
                error_log("Error en Asignatura::obtenerPromedioGeneral->" . $e->getMessage());
                return '0.0';
            }
        }

        public function obtenerStatsTodasAsignaturas(int $id_institucion): array
        {
            $stats = [];

            try {
                // Docentes por asignatura
                $stmt = $this->conexion->prepare(
                    "SELECT ac.id_asignatura, COUNT(DISTINCT dac.id_docente) AS total_docentes
                     FROM asignatura_curso ac
                     INNER JOIN docente_asignatura_curso dac ON dac.id_asignatura_curso = ac.id
                     INNER JOIN asignatura asig ON asig.id = ac.id_asignatura
                     WHERE asig.id_institucion = :id_institucion
                     GROUP BY ac.id_asignatura"
                );
                $stmt->bindParam(':id_institucion', $id_institucion, PDO::PARAM_INT);
                $stmt->execute();
                foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
                    $stats[$row['id_asignatura']]['total_docentes'] = (int) $row['total_docentes'];
                }

                // Estudiantes por asignatura (en cursos activos)
                $stmt = $this->conexion->prepare(
                    "SELECT ac.id_asignatura, COUNT(DISTINCT m.id_estudiante) AS total_estudiantes
                     FROM asignatura_curso ac
                     INNER JOIN curso c     ON c.id  = ac.id_curso
                     INNER JOIN matricula m ON m.id_curso = c.id
                     INNER JOIN asignatura asig ON asig.id = ac.id_asignatura
                     WHERE asig.id_institucion = :id_institucion
                       AND c.estado = 'Activo'
                     GROUP BY ac.id_asignatura"
                );
                $stmt->bindParam(':id_institucion', $id_institucion, PDO::PARAM_INT);
                $stmt->execute();
                foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
                    $stats[$row['id_asignatura']]['total_estudiantes'] = (int) $row['total_estudiantes'];
                }

                // Promedio de calificaciones por asignatura
                $stmt = $this->conexion->prepare(
                    "SELECT ac.id_asignatura, COALESCE(ROUND(AVG(cal.nota), 1), 0) AS promedio
                     FROM calificacion cal
                     INNER JOIN actividad a      ON a.id  = cal.id_actividad
                     INNER JOIN asignatura_curso ac ON ac.id = a.id_asignatura_curso
                     INNER JOIN asignatura asig  ON asig.id = ac.id_asignatura
                     WHERE asig.id_institucion = :id_institucion
                     GROUP BY ac.id_asignatura"
                );
                $stmt->bindParam(':id_institucion', $id_institucion, PDO::PARAM_INT);
                $stmt->execute();
                foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
                    $stats[$row['id_asignatura']]['promedio'] = $row['promedio'];
                }

                // Cursos donde se dicta cada asignatura
                $stmt = $this->conexion->prepare(
                    "SELECT ac.id_asignatura, COUNT(DISTINCT ac.id_curso) AS total_cursos
                     FROM asignatura_curso ac
                     INNER JOIN asignatura asig ON asig.id = ac.id_asignatura
                     WHERE asig.id_institucion = :id_institucion
                     GROUP BY ac.id_asignatura"
                );
                $stmt->bindParam(':id_institucion', $id_institucion, PDO::PARAM_INT);
                $stmt->execute();
                foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
                    $stats[$row['id_asignatura']]['total_cursos'] = (int) $row['total_cursos'];
                }

            } catch (PDOException $e) {
                error_log("Error en Asignatura::obtenerStatsTodasAsignaturas->" . $e->getMessage());
            }

            return $stats;
        }
    }




?>