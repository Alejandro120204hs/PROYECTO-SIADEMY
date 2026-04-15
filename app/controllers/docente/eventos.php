<?php

require_once BASE_PATH . '/app/models/docente/curso.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user']) || $_SESSION['user']['rol'] !== 'Docente') {
    header('Location: ' . BASE_URL . '/login');
    exit;
}

$id_institucion = (int)($_SESSION['user']['id_institucion'] ?? 0);
$id_docente = (int)($_SESSION['user']['id'] ?? 0);

if ($id_institucion <= 0 || $id_docente <= 0) {
    header('Location: ' . BASE_URL . '/login');
    exit;
}

$objetoCurso = new Curso_docente();
$eventosRaw = $objetoCurso->obtenerEventosCalendario($id_institucion, $id_docente);

function normalizarTextoEvento($texto) {
    $texto = (string)$texto;
    $texto = mb_strtolower($texto, 'UTF-8');
    $texto = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $texto);
    return $texto !== false ? $texto : '';
}

function categoriaEventoDocente($tipoEvento, $fuente) {
    $base = normalizarTextoEvento($tipoEvento);

    if (strpos($base, 'reunion') !== false || strpos($base, 'junta') !== false || strpos($base, 'consejo') !== false) {
        return 'meetings';
    }

    if (strpos($base, 'examen') !== false || strpos($base, 'parcial') !== false || strpos($base, 'quiz') !== false || strpos($base, 'prueba') !== false || strpos($base, 'evaluacion') !== false) {
        return 'exams';
    }

    if ($fuente === 'actividad') {
        if (strpos($base, 'tarea') !== false || strpos($base, 'proyecto') !== false || strpos($base, 'taller') !== false || strpos($base, 'laboratorio') !== false) {
            return 'activities';
        }
    }

    return 'activities';
}

function nombreCategoriaEvento($category) {
    switch ($category) {
        case 'meetings':
            return 'Reunión';
        case 'exams':
            return 'Examen';
        default:
            return 'Actividad';
    }
}

function iconoCategoriaEvento($category) {
    switch ($category) {
        case 'meetings':
            return 'ri-user-voice-line';
        case 'exams':
            return 'ri-file-edit-line';
        default:
            return 'ri-calendar-event-line';
    }
}

$eventosDocente = [];
$hoy = date('Y-m-d');

foreach ($eventosRaw as $evento) {
    $fecha = (string)($evento['fecha_evento'] ?? '');
    if ($fecha === '') {
        continue;
    }

    $category = categoriaEventoDocente($evento['tipo_evento'] ?? '', $evento['fuente'] ?? 'evento');

    $eventosDocente[] = [
        'fecha_evento' => $fecha,
        'tipo_evento' => (string)($evento['tipo_evento'] ?? 'Evento'),
        'nombre_evento' => (string)($evento['nombre_evento'] ?? 'Evento académico'),
        'descripcion' => (string)($evento['descripcion'] ?? 'Sin descripción'),
        'hora_inicio' => (string)($evento['hora_inicio'] ?? ''),
        'fuente' => (string)($evento['fuente'] ?? 'evento'),
        'category' => $category,
        'category_name' => nombreCategoriaEvento($category),
        'icon' => iconoCategoriaEvento($category),
        'is_upcoming' => ($fecha >= $hoy),
    ];
}

usort($eventosDocente, function ($a, $b) {
    $left = $a['fecha_evento'] . ' ' . ($a['hora_inicio'] ?: '23:59:59');
    $right = $b['fecha_evento'] . ' ' . ($b['hora_inicio'] ?: '23:59:59');
    return strcmp($left, $right);
});

$statsEventos = [
    'all' => count($eventosDocente),
    'upcoming' => 0,
    'meetings' => 0,
    'exams' => 0,
    'activities' => 0,
];

foreach ($eventosDocente as $evento) {
    if (!empty($evento['is_upcoming'])) {
        $statsEventos['upcoming']++;
    }

    if (isset($statsEventos[$evento['category']])) {
        $statsEventos[$evento['category']]++;
    }
}

require BASE_PATH . '/app/views/dashboard/docente/eventos.php';
