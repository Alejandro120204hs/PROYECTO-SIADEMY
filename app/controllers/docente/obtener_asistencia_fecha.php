<?php
/**
 * Controlador: Obtener asistencia por fecha
 * Devuelve todos los estudiantes con su asistencia para una fecha específica
 * Responde con JSON
 */

declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');

require_once BASE_PATH . '/app/models/docente/asistencia.php';
require_once BASE_PATH . '/app/helpers/session_helper.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Validar autenticación
if (!isset($_SESSION['user']) || $_SESSION['user']['rol'] !== 'Docente') {
    http_response_code(403);
    echo json_encode([
        'success' => false,
        'message' => 'No autorizado'
    ]);
    exit;
}

// Obtener parámetros
$id_curso = isset($_GET['curso']) ? (int)$_GET['curso'] : 0;
$id_asignatura = isset($_GET['asignatura']) ? (int)$_GET['asignatura'] : 0;
$fecha = isset($_GET['fecha']) ? (string)$_GET['fecha'] : '';

// Validar parámetros
if ($id_curso <= 0 || $id_asignatura <= 0 || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha)) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Parámetros inválidos'
    ]);
    exit;
}

$id_institucion = (int)$_SESSION['user']['id_institucion'];
$id_usuario = (int)$_SESSION['user']['id'];

try {
    // Cargar modelo
    $asistenciaModel = new AsistenciaDocente();
    
    // Verificar que el docente tiene acceso a este curso/asignatura
    $cursos = $asistenciaModel->obtenerCursosConAsignaturas($id_usuario, $id_institucion);
    
    $tieneAcceso = false;
    foreach ($cursos as $curso) {
        if ($curso['id_curso'] === $id_curso) {
            foreach ($curso['asignaturas'] as $asig) {
                if ($asig['id'] === $id_asignatura) {
                    $tieneAcceso = true;
                    break;
                }
            }
            if ($tieneAcceso) break;
        }
    }
    
    if (!$tieneAcceso) {
        http_response_code(403);
        echo json_encode([
            'success' => false,
            'message' => 'No tienes acceso a este curso/asignatura'
        ]);
        exit;
    }
    
    // Obtener estudiantes con asistencia
    $estudiantes = $asistenciaModel->obtenerEstudiantesConAsistencia(
        $id_curso,
        $id_asignatura,
        $fecha,
        $id_institucion
    );
    
    // Procesar y normalizar datos
    $estudiantesFormato = [];
    foreach ($estudiantes as $est) {
        $estudiantesFormato[] = [
            'id' => (int)$est['id'],
            'nombres' => htmlspecialchars((string)($est['nombres'] ?? ''), ENT_QUOTES, 'UTF-8'),
            'apellidos' => htmlspecialchars((string)($est['apellidos'] ?? ''), ENT_QUOTES, 'UTF-8'),
            'documento' => htmlspecialchars((string)($est['documento'] ?? ''), ENT_QUOTES, 'UTF-8'),
            'foto' => !empty($est['foto']) ? $est['foto'] : 'default.png',
            'estado' => $est['asistencia_estado'] ?? null,
            'estado_badge' => $est['asistencia_estado'] ? ucfirst(strtolower((string)$est['asistencia_estado'])) : 'Sin marcar'
        ];
    }
    
    // Contar estados
    $conteos = [
        'total' => count($estudiantesFormato),
        'presentes' => 0,
        'ausentes' => 0,
        'justificados' => 0,
        'sin_marcar' => 0
    ];
    
    foreach ($estudiantesFormato as $est) {
        switch ($est['estado']) {
            case 'Presente':
                $conteos['presentes']++;
                break;
            case 'Ausente':
                $conteos['ausentes']++;
                break;
            case 'Justificado':
                $conteos['justificados']++;
                break;
            default:
                $conteos['sin_marcar']++;
        }
    }
    
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'fecha' => $fecha,
        'fecha_formateada' => date('d/m/Y', strtotime($fecha)),
        'estudiantes' => $estudiantesFormato,
        'conteos' => $conteos
    ]);
    
} catch (Exception $e) {
    error_log('obtener_asistencia_fecha.php -> ' . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error al obtener asistencia'
    ]);
}
