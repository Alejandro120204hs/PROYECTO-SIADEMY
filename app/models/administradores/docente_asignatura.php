<?php

// MODELO PARA GESTIONAR ASIGNACIÓN DE DOCENTES A ASIGNATURAS

require_once __DIR__ . '/../../../config/database.php';

class DocenteAsignatura {
    
    private $conexion;
    
    public function __construct(){
        $db = new Conexion();
        $this->conexion = $db->getConexion();
    }

    // Obtener todos los docentes activos de la institución
    public function obtenerDocentes($id_institucion){
        try {
            $sql = "SELECT d.*, CONCAT(d.nombres, ' ', d.apellidos) as nombre_completo 
                    FROM docente d 
                    INNER JOIN usuario u ON d.id_usuario = u.id
                    WHERE d.id_institucion = :id_institucion AND u.estado = 'Activo'
                    ORDER BY d.nombres ASC";
            
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindParam(':id_institucion', $id_institucion);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch(PDOException $e){
            error_log("Error en obtenerDocentes: " . $e->getMessage());
            return [];
        }
    }

    // Obtener todas las asignaturas activas de la institución
    public function obtenerAsignaturas($id_institucion){
        try {
            $sql = "SELECT * FROM asignatura 
                    WHERE id_institucion = :id_institucion AND estado = 'Activo'
                    ORDER BY nombre ASC";
            
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindParam(':id_institucion', $id_institucion);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch(PDOException $e){
            error_log("Error en obtenerAsignaturas: " . $e->getMessage());
            return [];
        }
    }

    // Obtener todos los cursos activos de la institución
    public function obtenerCursos($id_institucion){
        try {
            $sql = "SELECT c.*, CONCAT(c.grado, '-', c.curso) as nombre_curso,
                           CONCAT(d.nombres, ' ', d.apellidos) as director
                    FROM curso c
                    LEFT JOIN docente d ON c.id_docente = d.id
                    WHERE c.id_institucion = :id_institucion AND c.estado = 'Activo'
                    ORDER BY c.grado ASC, c.curso ASC";
            
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindParam(':id_institucion', $id_institucion);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch(PDOException $e){
            error_log("Error en obtenerCursos: " . $e->getMessage());
            return [];
        }
    }

    // Asignar asignatura a un curso (tabla asignatura_curso)
    public function asignarAsignaturaCurso($id_institucion, $id_curso, $id_asignatura){
        try {
            // Verificar si ya existe la asignación
            $sql_check = "SELECT id FROM asignatura_curso 
                          WHERE id_curso = :id_curso AND id_asignatura = :id_asignatura";
            $stmt_check = $this->conexion->prepare($sql_check);
            $stmt_check->bindParam(':id_curso', $id_curso);
            $stmt_check->bindParam(':id_asignatura', $id_asignatura);
            $stmt_check->execute();
            
            $resultado = $stmt_check->fetch(PDO::FETCH_ASSOC);
            
            if($resultado){
                // Ya existe, retornar el ID existente
                return $resultado['id'];
            }
            
            // No existe, crear nueva asignación
            $sql = "INSERT INTO asignatura_curso (id_institucion, id_curso, id_asignatura, estado) 
                    VALUES (:id_institucion, :id_curso, :id_asignatura, 'activo')";
            
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindParam(':id_institucion', $id_institucion);
            $stmt->bindParam(':id_curso', $id_curso);
            $stmt->bindParam(':id_asignatura', $id_asignatura);
            
            if($stmt->execute()){
                return $this->conexion->lastInsertId();
            }
            
            return false;
            
        } catch(PDOException $e){
            error_log("Error en asignarAsignaturaCurso: " . $e->getMessage());
            return false;
        }
    }

    // Asignar docente a una asignatura_curso (tabla docente_asignatura_curso)
    public function asignarDocenteAsignaturaCurso($id_institucion, $id_docente, $id_asignatura_curso){
        try {
            // Verificar si ya existe la asignación
            $sql_check = "SELECT id FROM docente_asignatura_curso 
                          WHERE id_docente = :id_docente AND id_asignatura_curso = :id_asignatura_curso";
            $stmt_check = $this->conexion->prepare($sql_check);
            $stmt_check->bindParam(':id_docente', $id_docente);
            $stmt_check->bindParam(':id_asignatura_curso', $id_asignatura_curso);
            $stmt_check->execute();
            
            if($stmt_check->fetch()){
                return ['success' => false, 'message' => 'Este docente ya está asignado a esta asignatura en este curso'];
            }
            
            // Crear nueva asignación
            $sql = "INSERT INTO docente_asignatura_curso (id_institucion, id_docente, id_asignatura_curso, estado) 
                    VALUES (:id_institucion, :id_docente, :id_asignatura_curso, 'activo')";
            
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindParam(':id_institucion', $id_institucion);
            $stmt->bindParam(':id_docente', $id_docente);
            $stmt->bindParam(':id_asignatura_curso', $id_asignatura_curso);
            
            if($stmt->execute()){
                return ['success' => true, 'message' => 'Docente asignado correctamente'];
            }
            
            return ['success' => false, 'message' => 'Error al asignar docente'];
            
        } catch(PDOException $e){
            error_log("Error en asignarDocenteAsignaturaCurso: " . $e->getMessage());
            return ['success' => false, 'message' => 'Error al asignar docente: ' . $e->getMessage()];
        }
    }

    // Obtener todas las asignaciones actuales
    public function obtenerAsignaciones($id_institucion){
        try {
            $sql = "SELECT 
                        dac.id,
                        dac.id_docente,
                        dac.id_asignatura_curso,
                        CONCAT(d.nombres, ' ', d.apellidos) as docente,
                        a.nombre as asignatura,
                        CONCAT(c.grado, '-', c.curso) as curso,
                        c.jornada,
                        dac.estado,
                        dac.creado_en
                    FROM docente_asignatura_curso dac
                    INNER JOIN docente d ON dac.id_docente = d.id
                    INNER JOIN asignatura_curso ac ON dac.id_asignatura_curso = ac.id
                    INNER JOIN asignatura a ON ac.id_asignatura = a.id
                    INNER JOIN curso c ON ac.id_curso = c.id
                    WHERE dac.id_institucion = :id_institucion
                    ORDER BY d.apellidos ASC, a.nombre ASC";
            
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindParam(':id_institucion', $id_institucion);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch(PDOException $e){
            error_log("Error en obtenerAsignaciones: " . $e->getMessage());
            return [];
        }
    }

    // Obtener asignaciones filtradas por un curso específico
    public function obtenerAsignacionesPorCurso($id_institucion, $id_curso){
        try {
            $sql = "SELECT 
                        dac.id,
                        dac.id_docente,
                        dac.id_asignatura_curso,
                        CONCAT(d.nombres, ' ', d.apellidos) as docente,
                        a.nombre as asignatura,
                        CONCAT(c.grado, '-', c.curso) as curso,
                        c.jornada,
                        dac.estado,
                        dac.creado_en
                    FROM docente_asignatura_curso dac
                    INNER JOIN docente d ON dac.id_docente = d.id
                    INNER JOIN asignatura_curso ac ON dac.id_asignatura_curso = ac.id
                    INNER JOIN asignatura a ON ac.id_asignatura = a.id
                    INNER JOIN curso c ON ac.id_curso = c.id
                    WHERE dac.id_institucion = :id_institucion AND ac.id_curso = :id_curso
                    ORDER BY d.apellidos ASC, a.nombre ASC";
            
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindParam(':id_institucion', $id_institucion);
            $stmt->bindParam(':id_curso', $id_curso);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch(PDOException $e){
            error_log("Error en obtenerAsignacionesPorCurso: " . $e->getMessage());
            return [];
        }
    }

    // Eliminar asignación
    public function eliminarAsignacion($id){
        try {
            $sql = "DELETE FROM docente_asignatura_curso WHERE id = :id";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindParam(':id', $id);
            
            return $stmt->execute();
            
        } catch(PDOException $e){
            error_log("Error en eliminarAsignacion: " . $e->getMessage());
            return false;
        }
    }

    // Cambiar estado de asignación (activar/desactivar)
    public function cambiarEstadoAsignacion($id, $nuevo_estado){
        try {
            $sql = "UPDATE docente_asignatura_curso SET estado = :estado WHERE id = :id";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindParam(':estado', $nuevo_estado);
            $stmt->bindParam(':id', $id);
            
            return $stmt->execute();
            
        } catch(PDOException $e){
            error_log("Error en cambiarEstadoAsignacion: " . $e->getMessage());
            return false;
        }
    }

    // Obtener asignaturas de un curso específico con sus docentes y estados individuales
    public function obtenerAsignaturasPorCurso($id_curso){
        try {
            $sql = "SELECT 
                        a.id as id_asignatura,
                        a.nombre as asignatura,
                        a.descripcion,
                        ac.estado as estado_asignatura_curso,
                        dac.id as id_asignacion,
                        CONCAT(d.nombres, ' ', d.apellidos) as docente,
                        dac.estado as estado_docente
                    FROM asignatura_curso ac
                    INNER JOIN asignatura a ON ac.id_asignatura = a.id
                    LEFT JOIN docente_asignatura_curso dac ON ac.id = dac.id_asignatura_curso
                    LEFT JOIN docente d ON dac.id_docente = d.id
                    WHERE ac.id_curso = :id_curso
                    ORDER BY a.nombre ASC, d.apellidos ASC";
            
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindParam(':id_curso', $id_curso);
            $stmt->execute();
            
            $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Agrupar asignaturas con sus docentes
            $asignaturas = [];
            foreach($resultados as $row){
                $id_asig = $row['id_asignatura'];
                
                if(!isset($asignaturas[$id_asig])){
                    $asignaturas[$id_asig] = [
                        'id_asignatura' => $row['id_asignatura'],
                        'asignatura' => $row['asignatura'],
                        'descripcion' => $row['descripcion'],
                        'estado' => $row['estado_asignatura_curso'],
                        'docentes' => []
                    ];
                }
                
                if($row['docente']){
                    $asignaturas[$id_asig]['docentes'][] = [
                        'id_asignacion' => $row['id_asignacion'],
                        'nombre' => $row['docente'],
                        'estado' => $row['estado_docente']
                    ];
                }
            }
            
            return array_values($asignaturas);
            
        } catch(PDOException $e){
            error_log("Error en obtenerAsignaturasPorCurso: " . $e->getMessage());
            return [];
        }
    }
}

?>
