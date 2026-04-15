<?php
/**
 * Controlador: Descargar Entrega de Actividad
 * Permite al docente descargar el archivo PDF subido por el estudiante
 */

// Iniciar sesión y validar
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once __DIR__ . '/../../../config/config.php';

// Limpiar cualquier output buffer previo
if (ob_get_level()) {
    ob_end_clean();
}

// Validar que el usuario esté autenticado
if (!isset($_SESSION['user']) || $_SESSION['user']['rol'] !== 'Docente') {
    http_response_code(401);
    die('Acceso no autorizado');
}

// Validar parámetros
$id_entrega = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id_entrega <= 0) {
    http_response_code(400);
    die('ID de entrega no válido');
}

try {
    // Cargar el modelo
    require_once BASE_PATH . '/app/models/docente/entrega.php';
    $modeloEntrega = new EntregaDocente();
    
    // Resolver ID real del docente
    $id_docente = $_SESSION['user']['id_docente'] ?? null;
    if (!$id_docente) {
        require_once BASE_PATH . '/config/database.php';
        $db = new Conexion();
        $conn = $db->getConexion();
        $stmtDoc = $conn->prepare('SELECT id FROM docente WHERE id_usuario = :id_usuario LIMIT 1');
        $stmtDoc->bindParam(':id_usuario', $_SESSION['user']['id'], PDO::PARAM_INT);
        $stmtDoc->execute();
        $doc = $stmtDoc->fetch(PDO::FETCH_ASSOC);
        if (!$doc) {
            http_response_code(403);
            die('No se encontró el docente asociado al usuario actual');
        }
        $id_docente = (int)$doc['id'];
    }

    $id_institucion = $_SESSION['user']['id_institucion'];
    
    // Obtener información del archivo
    $archivo = $modeloEntrega->obtenerArchivoEntrega($id_entrega, $id_docente, $id_institucion);
    
    if (!$archivo) {
        http_response_code(404);
        die('Archivo no encontrado o no tienes permiso para descargarlo');
    }
    
    // Construir la ruta completa del archivo
    $rutaRelativa = trim((string)($archivo['archivo_ruta'] ?? ''));
    if ($rutaRelativa === '' && !empty($archivo['archivo'])) {
        $rutaRelativa = 'public/uploads/entregas/' . ltrim((string)$archivo['archivo'], '/');
    }

    if ($rutaRelativa === '') {
        http_response_code(404);
        die('La entrega no tiene archivo asociado');
    }

    $rutaNormalizada = str_replace('\\', '/', $rutaRelativa);
    if (strpos($rutaNormalizada, 'public/uploads/') === 0) {
        $rutaCompleta = BASE_PATH . '/' . $rutaNormalizada;
    } elseif (strpos($rutaNormalizada, 'uploads/') === 0) {
        $rutaCompleta = BASE_PATH . '/public/' . $rutaNormalizada;
    } else {
        $rutaCompleta = BASE_PATH . '/public/uploads/' . ltrim($rutaNormalizada, '/');
    }
    
    // Verificar que el archivo existe físicamente
    if (!file_exists($rutaCompleta)) {
        http_response_code(404);
        die('El archivo no existe en el servidor');
    }
    
    // Preparar nombre del archivo para descarga
    $nombreEstudiante = str_replace(' ', '_', $archivo['nombre_estudiante']);
    $tituloActividad = str_replace(' ', '_', $archivo['titulo_actividad']);
    $extension = strtolower(pathinfo($rutaCompleta, PATHINFO_EXTENSION));
    if ($extension === '') {
        $extension = 'pdf';
    }
    $nombreDescarga = "{$nombreEstudiante}-{$tituloActividad}.{$extension}";
    
    // Limpiar el buffer de salida
    if (ob_get_level()) {
        ob_end_clean();
    }
    
    // Establecer headers para descarga
    $mime = mime_content_type($rutaCompleta) ?: 'application/octet-stream';
    header('Content-Type: ' . $mime);
    header('Content-Disposition: attachment; filename="' . $nombreDescarga . '"');
    header('Content-Length: ' . filesize($rutaCompleta));
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    
    // Leer y enviar el archivo
    readfile($rutaCompleta);
    exit;
    
} catch (Exception $e) {
    error_log("Error descargando archivo: " . $e->getMessage());
    http_response_code(500);
    die('Error al descargar el archivo');
}
