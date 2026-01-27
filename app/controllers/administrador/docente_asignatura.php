<?php

// CONTROLADOR PARA ASIGNACIÓN DE DOCENTES A ASIGNATURAS

require_once __DIR__ . '/../../models/administradores/docente_asignatura.php';

// La sesión se valida en la vista (session_administrador.php)
// Instanciar la clase
$docenteAsignatura = new DocenteAsignatura();

$method = $_SERVER['REQUEST_METHOD'];

switch($method){
    case 'POST':
        $accion = $_POST['accion'] ?? '';
        
        if($accion === 'asignar'){
            asignarDocenteAsignatura($docenteAsignatura);
        }
        break;

    case 'GET':
        $accion = $_GET['accion'] ?? '';
        
        // Eliminar asignación
        if($accion === 'eliminar'){
            eliminarAsignacionDocente($docenteAsignatura, $_GET['id']);
        }
        
        // Cambiar estado
        if($accion === 'cambiar_estado'){
            cambiarEstado($docenteAsignatura, $_GET['id'], $_GET['estado']);
        }
        
        // Mostrar vista principal
        mostrarVistaAsignaciones($docenteAsignatura);
        break;
      
    default:
        http_response_code(405);
        echo "Método no permitido";
        break;            
}

// ==================== FUNCIONES ====================

function asignarDocenteAsignatura($docenteAsignatura){
    // Capturar el ID de la institución del admin logueado
    session_start();
    if(!isset($_SESSION['user']['id_institucion'])){
        header('Location: ' . BASE_URL . '/login');
        exit();
    }
    $id_institucion = $_SESSION['user']['id_institucion'];
    
    // Capturar datos del formulario
    $id_docente = $_POST['docente'] ?? null;
    $id_curso = $_POST['curso'] ?? null;
    $id_asignatura = $_POST['asignatura'] ?? null;
    
    // Preparar parámetro de curso para mantener el filtro
    $curso_param = $id_curso ? '?curso=' . $id_curso : '';
    
    // Validar datos
    if(!$id_docente || !$id_curso || !$id_asignatura){
        header('Location: ' . BASE_URL . '/administrador/asignar-docentes' . $curso_param);
        $_SESSION['alerta'] = ['tipo' => 'warning', 'mensaje' => 'Por favor complete todos los campos'];
        exit();
    }
    
    // Paso 1: Asignar la asignatura al curso (si no existe)
    $id_asignatura_curso = $docenteAsignatura->asignarAsignaturaCurso($id_institucion, $id_curso, $id_asignatura);
    
    if(!$id_asignatura_curso){
        header('Location: ' . BASE_URL . '/administrador/asignar-docentes' . $curso_param);
        $_SESSION['alerta'] = ['tipo' => 'danger', 'mensaje' => 'Error al asignar la asignatura al curso'];
        exit();
    }
    
    // Paso 2: Asignar el docente a la asignatura_curso
    $resultado = $docenteAsignatura->asignarDocenteAsignaturaCurso($id_institucion, $id_docente, $id_asignatura_curso);
    
    if($resultado['success']){
        header('Location: ' . BASE_URL . '/administrador/asignar-docentes' . $curso_param);
        $_SESSION['alerta'] = ['tipo' => 'success', 'mensaje' => $resultado['message']];
        exit();
    } else {
        header('Location: ' . BASE_URL . '/administrador/asignar-docentes' . $curso_param);
        $_SESSION['alerta'] = ['tipo' => 'warning', 'mensaje' => $resultado['message']];
        exit();
    }
}

function eliminarAsignacionDocente($docenteAsignatura, $id){
    // Mantener el parámetro de curso si existe
    $curso_param = isset($_GET['curso']) ? '?curso=' . $_GET['curso'] : '';
    
    if($docenteAsignatura->eliminarAsignacion($id)){
        header('Location: ' . BASE_URL . '/administrador/asignar-docentes' . $curso_param);
        $_SESSION['alerta'] = ['tipo' => 'success', 'mensaje' => 'Asignación eliminada correctamente'];
        exit();
    } else {
        header('Location: ' . BASE_URL . '/administrador/asignar-docentes' . $curso_param);
        $_SESSION['alerta'] = ['tipo' => 'danger', 'mensaje' => 'Error al eliminar la asignación'];
        exit();
    }
}

function cambiarEstado($docenteAsignatura, $id, $estado){
    // Mantener el parámetro de curso si existe
    $curso_param = isset($_GET['curso']) ? '?curso=' . $_GET['curso'] : '';
    
    $nuevo_estado = ($estado === 'activo') ? 'inactivo' : 'activo';
    
    if($docenteAsignatura->cambiarEstadoAsignacion($id, $nuevo_estado)){
        header('Location: ' . BASE_URL . '/administrador/asignar-docentes' . $curso_param);
        $_SESSION['alerta'] = ['tipo' => 'success', 'mensaje' => 'Estado actualizado correctamente'];
        exit();
    } else {
        header('Location: ' . BASE_URL . '/administrador/asignar-docentes' . $curso_param);
        $_SESSION['alerta'] = ['tipo' => 'danger', 'mensaje' => 'Error al cambiar el estado'];
        exit();
    }
}

function mostrarVistaAsignaciones($docenteAsignatura){
    // Verificar si la sesión ya está iniciada
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    // Capturar el ID de la institución del admin logueado
    $id_institucion = $_SESSION['user']['id_institucion'];
    
    // Capturar el parámetro de curso para filtrado
    $id_curso_filtro = $_GET['curso'] ?? null;
    
    $docentes = $docenteAsignatura->obtenerDocentes($id_institucion);
    $asignaturas = $docenteAsignatura->obtenerAsignaturas($id_institucion);
    $cursos = $docenteAsignatura->obtenerCursos($id_institucion);
    
    // Si hay un curso específico, filtrar las asignaciones
    if($id_curso_filtro){
        $asignaciones = $docenteAsignatura->obtenerAsignacionesPorCurso($id_institucion, $id_curso_filtro);
    } else {
        $asignaciones = $docenteAsignatura->obtenerAsignaciones($id_institucion);
    }
    
    // Pasar datos a la vista
    include BASE_PATH . '/app/views/dashboard/administrador/asignar-docente-asignatura.php';
}

?>
