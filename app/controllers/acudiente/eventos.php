<?php

require_once BASE_PATH . '/app/helpers/session_acudiente.php';
require_once BASE_PATH . '/app/controllers/acudiente/view_data.php';
require_once BASE_PATH . '/app/models/administradores/eventos.php';

$idInstitucion = (int)($_SESSION['user']['id_institucion'] ?? 0);
$idAcudiente   = acudienteObtenerIdDesdeSesion();
$anio          = (int)date('Y');

$estudianteModel        = new EstudianteAcudiente();
$estudiantesAsociados   = $estudianteModel->obtenerEstudiantesAsociados($idAcudiente, $idInstitucion, $anio);
$estudianteSeleccionado = acudienteObtenerEstudianteSeleccionado($estudiantesAsociados);

$model      = new Evento();
$rawEventos = $model->listar($idInstitucion);
$hoy        = date('Y-m-d');

// Mapeo de tipo a category/icon (mismo que docente)
$categoryMap = [
    'reuniones'   => 'meetings',
    'examen'      => 'exams',
    'actividad'   => 'activities',
    'taller'      => 'activities',
    'conferencia' => 'meetings',
];
$iconMap = [
    'meetings'   => 'ri-user-voice-line',
    'exams'      => 'ri-file-edit-line',
    'activities' => 'ri-calendar-event-line',
];

// Transformar al formato que entiende el JS del calendario
$eventos = array_map(function ($ev) use ($hoy, $categoryMap, $iconMap) {
    $tipo     = strtolower($ev['tipo_evento'] ?? '');
    $category = $categoryMap[$tipo] ?? 'activities';
    return [
        'fecha_evento'    => $ev['fecha_evento'],
        'nombre_evento'   => $ev['nombre_evento'] ?? $ev['nombre'] ?? 'Evento',
        'descripcion'     => $ev['descripcion'] ?? '',
        'hora_inicio'     => $ev['hora_inicio'] ?? '',
        'hora_fin'        => $ev['hora_fin']    ?? '',
        'ubicacion'       => $ev['ubicacion']   ?? '',
        'responsable'     => $ev['responsable'] ?? '',
        'correo_contacto' => $ev['correo_contacto'] ?? '',
        'category'        => $category,
        'category_name'   => ucfirst($ev['tipo_evento'] ?? 'Evento'),
        'icon'            => $iconMap[$category] ?? 'ri-calendar-event-line',
        'fuente'          => 'evento',
        'is_upcoming'     => ($ev['fecha_evento'] >= $hoy),
    ];
}, $rawEventos);

// Stats para los badges de los tabs
$statsEventos = [
    'all'        => count($eventos),
    'upcoming'   => count(array_filter($eventos, fn($e) => $e['is_upcoming'])),
    'meetings'   => count(array_filter($eventos, fn($e) => $e['category'] === 'meetings')),
    'exams'      => count(array_filter($eventos, fn($e) => $e['category'] === 'exams')),
    'activities' => count(array_filter($eventos, fn($e) => $e['category'] === 'activities')),
];

$eventosJson = htmlspecialchars(json_encode($eventos, JSON_UNESCAPED_UNICODE), ENT_QUOTES, 'UTF-8');
$usuario     = obtenerPerfilAcudienteDesdeSesion();

require BASE_PATH . '/app/views/dashboard/acudiente/eventos.php';
