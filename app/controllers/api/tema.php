<?php

header('Content-Type: application/json');

if (!isset($_SESSION['user']['id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'No autenticado']);
    exit;
}

$id = (int) $_SESSION['user']['id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $body = json_decode(file_get_contents('php://input'), true) ?? [];
    $tema = ($body['tema'] ?? '') === 'light' ? 'light' : 'dark';

    try {
        require_once BASE_PATH . '/config/database.php';
        $db  = new Conexion();
        $conn = $db->getConexion();

        $stmt = $conn->prepare("UPDATE usuario SET tema_preferido = :tema WHERE id = :id");
        $stmt->execute([':tema' => $tema, ':id' => $id]);

        $_SESSION['user']['tema'] = $tema;
        echo json_encode(['success' => true, 'tema' => $tema]);
    } catch (PDOException $e) {
        error_log('[tema API] ' . $e->getMessage());
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Error interno']);
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    echo json_encode(['success' => true, 'tema' => $_SESSION['user']['tema'] ?? 'dark']);
} else {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Método no permitido']);
}
exit;
