<?php
/**
 * Controlador: Calificar Actividad Entregada
 * Permite al docente registrar o actualizar la nota de un estudiante
 */

// Iniciar sesión y validar
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once __DIR__ . '/../../../config/config.php';

// Limpiar cualquier output buffer previo
if (ob_get_level()) {
    ob_end_clean();
}

// Establecer header JSON
header('Content-Type: application/json');

// Validar que el usuario esté autenticado
if (!isset($_SESSION['user']) || $_SESSION['user']['rol'] !== 'Docente') {
    http_response_code(401);
    echo json_encode([
        'success' => false,
        'message' => 'Acceso no autorizado'
    ]);
    exit;
}

// Validar método POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => 'Método no permitido'
    ]);
    exit;
}

// Capturar datos del formulario
$id_entrega = isset($_POST['id_entrega']) ? intval($_POST['id_entrega']) : 0;
$nota = isset($_POST['nota']) ? floatval($_POST['nota']) : null;
$observacion = isset($_POST['observacion']) ? trim($_POST['observacion']) : '';

// Validaciones
if ($id_entrega <= 0) {
    echo json_encode([
        'success' => false,
        'message' => 'ID de entrega no válido'
    ]);
    exit;
}

if ($nota === null || $nota < 0 || $nota > 5) {
    echo json_encode([
        'success' => false,
        'message' => 'La nota debe estar entre 0 y 5'
    ]);
    exit;
}

try {
    // Cargar el modelo
    require_once BASE_PATH . '/app/models/docente/calificacion.php';
    $modeloCalificacion = new CalificacionDocente();
    
    // Datos del docente - Obtener id_docente desde la sesión o desde la BD
    $id_docente = $_SESSION['user']['id_docente'] ?? null;
    $id_institucion = $_SESSION['user']['id_institucion'];
    
    // Si no está en sesión, buscar en la BD
    if (!$id_docente) {
        require_once BASE_PATH . '/config/database.php';
        $db = new Conexion();
        $conn = $db->getConexion();
        $stmt = $conn->prepare("SELECT id FROM docente WHERE id_usuario = :id_usuario");
        $stmt->bindParam(':id_usuario', $_SESSION['user']['id'], PDO::PARAM_INT);
        $stmt->execute();
        $docente = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($docente) {
            $id_docente = $docente['id'];
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'No se encontró el registro del docente'
            ]);
            exit;
        }
    }
    
    // Verificar que la entrega existe y pertenece a un curso del docente
    $verificacion = $modeloCalificacion->verificarPermisoCalificar($id_entrega, $id_docente, $id_institucion);
    
    if (!$verificacion) {
        echo json_encode([
            'success' => false,
            'message' => 'No tienes permiso para calificar esta entrega'
        ]);
        exit;
    }
    
    // Guardar o actualizar calificación
    $resultado = $modeloCalificacion->guardarCalificacion(
        $id_entrega,
        $nota,
        $observacion,
        $id_docente
    );
    
    if ($resultado) {
        // Notificar al estudiante y su acudiente sobre la calificación
        try {
            require_once BASE_PATH . '/app/helpers/notificacion_helper.php';
            $notifModel = new Notificacion();
            $contexto   = $notifModel->obtenerContextoDeEntrega($id_entrega);
            if ($contexto) {
                $notaFormato  = number_format($nota, 1);
                $urlEstudiante = rtrim(BASE_URL, '/') . '/estudiante-panel-calificaciones';
                // Notificar al estudiante
                notificar(
                    'calificacion_publicada',
                    'Calificación publicada',
                    'Tu actividad "' . $contexto['titulo_actividad'] . '" fue calificada con ' . $notaFormato . ' / 5.0.',
                    (int)$contexto['id_usuario_estudiante'],
                    (int)$contexto['id_institucion'],
                    $urlEstudiante,
                    'calificacion',
                    (int)$contexto['id_actividad']
                );
                // Notificar al acudiente si existe
                if (!empty($contexto['id_usuario_acudiente'])) {
                    notificar(
                        'calificacion_publicada',
                        'Calificación publicada',
                        'Se registró una calificación de ' . $notaFormato . ' / 5.0 en "' . $contexto['titulo_actividad'] . '".',
                        (int)$contexto['id_usuario_acudiente'],
                        (int)$contexto['id_institucion'],
                        null,
                        'calificacion',
                        (int)$contexto['id_actividad']
                    );
                }
            }
        } catch (Throwable $_e) {
            error_log('[hook-calificacion_publicada] ' . $_e->getMessage());
        }
        echo json_encode([
            'success' => true,
            'message' => 'Calificación guardada exitosamente'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Error al guardar la calificación'
        ]);
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error del servidor: ' . $e->getMessage()
    ]);
}
