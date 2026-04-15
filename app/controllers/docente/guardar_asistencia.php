<?php

// Solo acepta POST de AJAX
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    exit;
}

// Verificar sesión activa como Docente
if (!isset($_SESSION['user']) || $_SESSION['user']['rol'] !== 'Docente') {
    http_response_code(403);
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit;
}

require_once BASE_PATH . '/app/models/docente/asistencia.php';

header('Content-Type: application/json');

// Leer JSON del cuerpo
$input = json_decode(file_get_contents('php://input'), true);
if (!is_array($input)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Datos inválidos']);
    exit;
}

$id_curso       = isset($input['curso_id'])      ? (int) $input['curso_id']      : 0;
$id_asignatura  = isset($input['asignatura_id']) ? (int) $input['asignatura_id'] : 0;
$fecha          = isset($input['fecha'])         ? trim((string) $input['fecha']) : '';
$asistencias    = isset($input['asistencias'])   ? $input['asistencias']          : [];

// Validaciones básicas
if ($id_asignatura <= 0 || $fecha === '' || !is_array($asistencias) || count($asistencias) === 0) {
    http_response_code(422);
    echo json_encode(['success' => false, 'message' => 'Faltan campos obligatorios']);
    exit;
}

if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha)) {
    http_response_code(422);
    echo json_encode(['success' => false, 'message' => 'Formato de fecha inválido']);
    exit;
}

$id_usuario     = (int) $_SESSION['user']['id'];
$id_institucion = (int) ($_SESSION['user']['id_institucion'] ?? 0);

if ($id_institucion === 0) {
    http_response_code(422);
    echo json_encode(['success' => false, 'message' => 'Institución no encontrada en sesión']);
    exit;
}

$objAsistencia = new AsistenciaDocente();
$id_docente    = $objAsistencia->obtenerIdDocente($id_usuario, $id_institucion);

if ($id_docente === 0) {
    http_response_code(422);
    echo json_encode(['success' => false, 'message' => 'Docente no encontrado']);
    exit;
}

// Mapeo de códigos de la vista → enum de la BD
$mapaEstados = [
    'P' => 'Presente',
    'A' => 'Ausente',
    'T' => 'Ausente',      // Tardanza se almacena como Ausente
    'E' => 'Justificado',
];

// Construir registros para el modelo
$registros = [];
foreach ($asistencias as $id_estudiante_raw => $codigo) {
    $id_est = (int) $id_estudiante_raw;
    $codigoLimpio = strtoupper(trim((string) $codigo));
    if ($id_est <= 0 || !array_key_exists($codigoLimpio, $mapaEstados)) {
        continue;
    }
    $registros[] = [
        'id_estudiante' => $id_est,
        'estado'        => $mapaEstados[$codigoLimpio],
    ];
}

if (empty($registros)) {
    echo json_encode(['success' => false, 'message' => 'No hay registros válidos para guardar']);
    exit;
}

$ok = $objAsistencia->guardarAsistencia($registros, $id_asignatura, $id_institucion, $id_docente, $fecha);

if ($ok) {
    echo json_encode([
        'success'  => true,
        'message'  => 'Asistencia guardada correctamente',
        'guardados' => count($registros),
    ]);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error al guardar en la base de datos']);
}
