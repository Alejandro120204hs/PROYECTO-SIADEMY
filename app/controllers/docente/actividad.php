<?php

// CONTROLADOR PARA GESTIONAR ACTIVIDADES DEL DOCENTE
require_once BASE_PATH . '/app/models/docente/actividad.php';
require_once BASE_PATH . '/app/helpers/alert_helper.php';

/**
 * Obtener la URL base del proyecto
 */
function obtenerBaseUrl() {
    return rtrim(BASE_URL, '/');
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
        'fecha_entrega' => $_POST['fecha_entrega'],
        'archivo' => null
    ];

    // Manejar archivo adjunto opcional
    if (isset($_FILES['archivo_actividad']) && $_FILES['archivo_actividad']['error'] !== UPLOAD_ERR_NO_FILE) {
        $file = $_FILES['archivo_actividad'];
        $extensionesPermitidas = ['pdf', 'jpg', 'jpeg', 'png', 'doc', 'docx'];
        $mimePermitidos = [
            'application/pdf',
            'image/jpeg',
            'image/png',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
        ];

        if ($file['error'] !== UPLOAD_ERR_OK) {
            mostrarSweetAlert('error', 'Error de archivo', 'Error al subir el archivo adjunto');
            exit;
        }
        if ($file['size'] > 10485760) { // 10 MB
            mostrarSweetAlert('error', 'Error de archivo', 'El archivo excede el tamaño máximo permitido (10 MB)');
            exit;
        }
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, $extensionesPermitidas)) {
            mostrarSweetAlert('error', 'Tipo no permitido', 'Solo se permiten archivos: PDF, JPG, PNG, DOC, DOCX');
            exit;
        }
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($file['tmp_name']);
        if (!in_array($mime, $mimePermitidos)) {
            mostrarSweetAlert('error', 'Tipo no permitido', 'El archivo no es un tipo válido');
            exit;
        }
        $nombreArchivo = 'actividad_' . ($datos['id_docente']) . '_' . time() . '.' . $ext;
        $destino = BASE_PATH . '/public/uploads/actividades/' . $nombreArchivo;
        if (!move_uploaded_file($file['tmp_name'], $destino)) {
            mostrarSweetAlert('error', 'Error', 'No se pudo guardar el archivo adjunto');
            exit;
        }
        $datos['archivo'] = $nombreArchivo;
    }

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

    // Manejar reemplazo opcional de archivo adjunto
    if (isset($_FILES['archivo_actividad']) && $_FILES['archivo_actividad']['error'] !== UPLOAD_ERR_NO_FILE) {
        $file = $_FILES['archivo_actividad'];
        $extensionesPermitidas = ['pdf', 'jpg', 'jpeg', 'png', 'doc', 'docx'];
        $mimePermitidos = [
            'application/pdf',
            'image/jpeg',
            'image/png',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
        ];

        if ($file['error'] !== UPLOAD_ERR_OK) {
            mostrarSweetAlert('error', 'Error de archivo', 'Error al subir el archivo adjunto');
            exit;
        }
        if ($file['size'] > 10485760) {
            mostrarSweetAlert('error', 'Error de archivo', 'El archivo excede el tamaño máximo permitido (10 MB)');
            exit;
        }

        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, $extensionesPermitidas)) {
            mostrarSweetAlert('error', 'Tipo no permitido', 'Solo se permiten archivos: PDF, JPG, PNG, DOC, DOCX');
            exit;
        }

        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($file['tmp_name']);
        if (!in_array($mime, $mimePermitidos)) {
            mostrarSweetAlert('error', 'Tipo no permitido', 'El archivo no es un tipo válido');
            exit;
        }

        $nombreArchivo = 'actividad_' . $id . '_' . time() . '.' . $ext;
        $directorioDestino = BASE_PATH . '/public/uploads/actividades';
        if (!is_dir($directorioDestino)) {
            mkdir($directorioDestino, 0755, true);
        }
        $destino = $directorioDestino . '/' . $nombreArchivo;

        if (!move_uploaded_file($file['tmp_name'], $destino)) {
            mostrarSweetAlert('error', 'Error', 'No se pudo guardar el archivo adjunto');
            exit;
        }

        // Intentar eliminar archivo anterior si existe
        $archivoAnterior = trim($_POST['archivo_actual'] ?? '');
        if ($archivoAnterior !== '') {
            $rutaAnterior = $directorioDestino . '/' . basename($archivoAnterior);
            if (is_file($rutaAnterior)) {
                @unlink($rutaAnterior);
            }
        }

        $datos['archivo'] = $nombreArchivo;
    }

    $actividadModel = new Actividad_docente();
    $resultado = $actividadModel->actualizar($id, $datos);

    $id_curso_red = isset($_POST['id_curso']) ? filter_var($_POST['id_curso'], FILTER_SANITIZE_NUMBER_INT) : '';
    $redirect_url = obtenerBaseUrl() . '/docente/actividades?id_curso=' . $id_curso_red;

    if ($resultado) {
        mostrarSweetAlert('success', '¡Éxito!', 'Actividad actualizada correctamente', $redirect_url);
    } else {
        mostrarSweetAlert('error', 'Error', 'No se pudo actualizar la actividad', $redirect_url);
    }
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
