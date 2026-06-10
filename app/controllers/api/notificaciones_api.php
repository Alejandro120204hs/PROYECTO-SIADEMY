<?php
/**
 * API de Notificaciones — endpoints JSON para el frontend.
 * Ruta: GET|POST /api/notificaciones?action=<accion>
 *
 * Acciones disponibles:
 *   badge       GET  — devuelve {count} de no leídas
 *   preview     GET  — devuelve las últimas 5 no leídas
 *   leer        POST — marca una notificación como leída  (param: id)
 *   leer-todas  POST — marca todas como leídas
 *   descartar   POST — descarta una notificación           (param: id)
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../../config/config.php';
require_once BASE_PATH . '/app/helpers/session_helper.php';

// Evitar output buffer previo que rompa JSON
if (ob_get_level()) {
    ob_end_clean();
}

header('Content-Type: application/json; charset=utf-8');
header('X-Content-Type-Options: nosniff');

// Todas las rutas requieren sesión activa
if (!isSessionActive()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'No autenticado']);
    exit;
}

$idUsuario     = (int)$_SESSION['user']['id'];
$idInstitucion = (int)($_SESSION['user']['id_institucion'] ?? 0);

if ($idInstitucion <= 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Institución no definida']);
    exit;
}

require_once BASE_PATH . '/app/models/notificaciones.php';
$model  = new Notificacion();
$action = $_GET['action'] ?? ($_POST['action'] ?? '');

switch ($action) {

    // ── Conteo para badge ──────────────────────────────────────────────────
    case 'badge':
        $count = $model->contarNoLeidas($idUsuario, $idInstitucion);
        echo json_encode(['success' => true, 'count' => $count]);
        break;

    // ── Vista previa (últimas 5 no leídas) ────────────────────────────────
    case 'preview':
        $notifs = $model->listarParaUsuario($idUsuario, $idInstitucion, 5);
        echo json_encode(['success' => true, 'data' => $notifs]);
        break;

    // ── Marcar una como leída ─────────────────────────────────────────────
    case 'leer':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Método no permitido']);
            break;
        }
        $id = (int)($_POST['id'] ?? 0);
        if ($id <= 0) {
            echo json_encode(['success' => false, 'message' => 'ID inválido']);
            break;
        }
        $ok = $model->marcarLeida($id, $idUsuario);
        echo json_encode(['success' => (bool)$ok]);
        break;

    // ── Marcar todas como leídas ──────────────────────────────────────────
    case 'leer-todas':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Método no permitido']);
            break;
        }
        $ok = $model->marcarTodasLeidas($idUsuario, $idInstitucion);
        echo json_encode(['success' => (bool)$ok]);
        break;

    // ── Descartar una notificación ────────────────────────────────────────
    case 'descartar':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Método no permitido']);
            break;
        }
        $id = (int)($_POST['id'] ?? 0);
        if ($id <= 0) {
            echo json_encode(['success' => false, 'message' => 'ID inválido']);
            break;
        }
        $ok = $model->descartar($id, $idUsuario);
        echo json_encode(['success' => (bool)$ok]);
        break;

    default:
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Acción no reconocida']);
        break;
}
