<?php

declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');

require_once BASE_PATH . '/app/helpers/session_helper.php';
require_once BASE_PATH . '/app/models/docente/asistencia.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['usuario']) || (($_SESSION['rol'] ?? '') !== 'Docente')) {
    http_response_code(403);
    echo json_encode([
        'success' => false,
        'message' => 'No autorizado',
    ]);
    exit;
}

$id_usuario = (int) ($_SESSION['usuario']['id'] ?? 0);
$id_institucion = (int) ($_SESSION['usuario']['id_institucion'] ?? 0);
$id_curso = isset($_GET['curso']) ? (int) $_GET['curso'] : 0;
$id_asignatura = isset($_GET['asignatura']) ? (int) $_GET['asignatura'] : 0;
$limite = isset($_GET['limite']) ? (int) $_GET['limite'] : 15;
$limite = max(5, min(60, $limite));

if ($id_usuario <= 0 || $id_institucion <= 0 || $id_curso <= 0 || $id_asignatura <= 0) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Parámetros incompletos para consultar historial',
    ]);
    exit;
}

$modelo = new AsistenciaDocente();
$id_docente = $modelo->obtenerIdDocente($id_usuario, $id_institucion);

if ($id_docente <= 0) {
    http_response_code(404);
    echo json_encode([
        'success' => false,
        'message' => 'Docente no encontrado',
    ]);
    exit;
}

$historial = $modelo->obtenerHistorialAsistencia($id_curso, $id_asignatura, $id_docente, $id_institucion, $limite);

echo json_encode([
    'success' => true,
    'historial' => $historial,
]);
