<?php

// CONTROLADOR PARA GESTIONAR ACTIVIDADES DEL DOCENTE
require_once BASE_PATH . '/app/models/docente/actividad.php';
require_once BASE_PATH . '/app/helpers/alert_helper.php';

/**
 * Obtener la URL base del proyecto
 */
function obtenerBaseUrl() {
    return (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'] . "/siademy";
}

/**
 * Guardar una nueva actividad
 */
function guardarActividad() {
    // Iniciar sesión si no está iniciada
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // Verificar que el usuario esté autenticado
    if (!isset($_SESSION['user']) || $_SESSION['user']['rol'] !== 'Docente') {
        header('Location: ' . obtenerBaseUrl() . '/login');
        exit;
    }

    // Validar que sea una petición POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header('Location: ' . obtenerBaseUrl() . '/docente/cursos');
        exit;
    }

    // Validar campos requeridos
    $camposRequeridos = ['id_asignatura_curso', 'id_asignatura', 'titulo_actividad', 'tipo_actividad', 'ponderacion', 'fecha_entrega'];
    $errores = [];

    foreach ($camposRequeridos as $campo) {
        if (!isset($_POST[$campo]) || empty(trim($_POST[$campo]))) {
            $errores[] = "El campo $campo es obligatorio";
        }
    }

    if (!empty($errores)) {
        mostrarSweetAlert('error', 'Error de validación', implode('<br>', $errores));
        exit;
    }

    // Preparar datos para insertar
    $datos = [
        'id_institucion' => $_SESSION['user']['id_institucion'],
        'id_docente' => $_SESSION['user']['id_docente'] ?? $_SESSION['user']['id'], // Usar id_docente si existe
        'id_asignatura_curso' => filter_var($_POST['id_asignatura_curso'], FILTER_SANITIZE_NUMBER_INT),
        'id_asignatura' => filter_var($_POST['id_asignatura'], FILTER_SANITIZE_NUMBER_INT),
        'titulo' => htmlspecialchars(trim($_POST['titulo_actividad']), ENT_QUOTES, 'UTF-8'),
        'descripcion' => htmlspecialchars(trim($_POST['descripcion']), ENT_QUOTES, 'UTF-8'),
        'tipo' => htmlspecialchars(trim($_POST['tipo_actividad']), ENT_QUOTES, 'UTF-8'),
        'ponderacion' => filter_var($_POST['ponderacion'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION),
        'fecha_entrega' => $_POST['fecha_entrega']
    ];

    // Validar que la ponderación esté entre 0 y 100
    if ($datos['ponderacion'] < 0 || $datos['ponderacion'] > 100) {
        mostrarSweetAlert('error', 'Error de validación', 'La ponderación debe estar entre 0 y 100%');
        exit;
    }

    // Validar tipos permitidos
    $tiposPermitidos = ['Taller', 'Quiz', 'Examen', 'Proyecto', 'Exposición', 'Laboratorio', 'Tarea'];
    if (!in_array($datos['tipo'], $tiposPermitidos)) {
        mostrarSweetAlert('error', 'Error de validación', 'El tipo de actividad no es válido');
        exit;
    }

    // Crear instancia del modelo
    $actividadModel = new Actividad_docente();
    
    // Intentar guardar la actividad
    $resultado = $actividadModel->crear($datos);

    // Obtener id_curso para redirección
    $id_curso = isset($_POST['id_curso']) ? filter_var($_POST['id_curso'], FILTER_SANITIZE_NUMBER_INT) : '';
    
    // Construir URL de redirección
    $base_url = obtenerBaseUrl();
    $redirect_url = $base_url . '/docente/actividades?id_curso=' . $id_curso;

    if ($resultado['success']) {
        mostrarSweetAlert('success', '¡Éxito!', $resultado['message'], $redirect_url);
    } else {
        mostrarSweetAlert('error', 'Error', $resultado['message']);
    }
    exit;
}

/**
 * Listar actividades por curso
 */
function listarActividades() {
    // Iniciar sesión si no está iniciada
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // Verificar que se recibió el id_curso
    if (!isset($_GET['id_curso']) || empty($_GET['id_curso'])) {
        return [];
    }

    $id_curso = filter_var($_GET['id_curso'], FILTER_SANITIZE_NUMBER_INT);
    $id_docente = $_SESSION['user']['id_docente'] ?? $_SESSION['user']['id']; // Usar id_docente si existe
    $id_institucion = $_SESSION['user']['id_institucion'];

    $actividadModel = new Actividad_docente();
    return $actividadModel->listarPorCurso($id_curso, $id_docente, $id_institucion);
}

/**
 * Obtener una actividad por ID
 */
function obtenerActividad($id) {
    $actividadModel = new Actividad_docente();
    return $actividadModel->obtenerPorId($id);
}

/**
 * Actualizar una actividad
 */
function actualizarActividad() {
    // Validar que sea una petición POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header('Location: ' . obtenerBaseUrl() . '/docente/cursos');
        exit;
    }

    $id = filter_var($_POST['id'], FILTER_SANITIZE_NUMBER_INT);
    
    $datos = [
        'titulo' => htmlspecialchars(trim($_POST['titulo_actividad']), ENT_QUOTES, 'UTF-8'),
        'descripcion' => htmlspecialchars(trim($_POST['descripcion']), ENT_QUOTES, 'UTF-8'),
        'tipo' => htmlspecialchars(trim($_POST['tipo_actividad']), ENT_QUOTES, 'UTF-8'),
        'ponderacion' => filter_var($_POST['ponderacion'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION),
        'fecha_entrega' => $_POST['fecha_entrega'],
        'estado' => $_POST['estado']
    ];

    $actividadModel = new Actividad_docente();
    $resultado = $actividadModel->actualizar($id, $datos);

    if ($resultado) {
        mostrarSweetAlert('success', '¡Éxito!', 'Actividad actualizada correctamente');
    } else {
        mostrarSweetAlert('error', 'Error', 'No se pudo actualizar la actividad');
    }

    header('Location: ' . obtenerBaseUrl() . '/docente/actividades?id_curso=' . $_POST['id_curso']);
    exit;
}

/**
 * Eliminar una actividad
 */
function eliminarActividad() {
    if (!isset($_GET['id']) || empty($_GET['id'])) {
        header('Location: ' . obtenerBaseUrl() . '/docente/cursos');
        exit;
    }

    $id = filter_var($_GET['id'], FILTER_SANITIZE_NUMBER_INT);
    $id_curso = filter_var($_GET['id_curso'], FILTER_SANITIZE_NUMBER_INT);

    $actividadModel = new Actividad_docente();
    $resultado = $actividadModel->eliminar($id);

    if ($resultado) {
        mostrarSweetAlert('success', '¡Éxito!', 'Actividad eliminada correctamente');
    } else {
        mostrarSweetAlert('error', 'Error', 'No se pudo eliminar la actividad');
    }

    header('Location: ' . obtenerBaseUrl() . '/docente/actividades?id_curso=' . $id_curso);
    exit;
}

/**
 * Cambiar estado de una actividad
 */
function cambiarEstadoActividad() {
    if (!isset($_POST['id']) || !isset($_POST['estado'])) {
        header('Location: ' . obtenerBaseUrl() . '/docente/cursos');
        exit;
    }

    $id = filter_var($_POST['id'], FILTER_SANITIZE_NUMBER_INT);
    $estado = htmlspecialchars(trim($_POST['estado']), ENT_QUOTES, 'UTF-8');
    $id_curso = filter_var($_POST['id_curso'], FILTER_SANITIZE_NUMBER_INT);

    $actividadModel = new Actividad_docente();
    $resultado = $actividadModel->cambiarEstado($id, $estado);

    if ($resultado) {
        mostrarSweetAlert('success', '¡Éxito!', 'Estado actualizado correctamente');
    } else {
        mostrarSweetAlert('error', 'Error', 'No se pudo actualizar el estado');
    }

    header('Location: ' . obtenerBaseUrl() . '/docente/actividades?id_curso=' . $id_curso);
    exit;
}

?>
