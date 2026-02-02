<?php
require_once BASE_PATH . '/app/models/docente/entrega.php';
require_once BASE_PATH . '/config/database.php';

// VERIFICAR SESIÓN
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// VERIFICAR QUE ESTÉ LOGUEADO COMO DOCENTE
if (!isset($_SESSION['user']) || $_SESSION['user']['rol'] !== 'Docente') {
    header('Location: ' . BASE_URL . '/login');
    exit;
}

// VALIDAR PARÁMETRO ID DE ACTIVIDAD
if (!isset($_GET['id_actividad']) || empty($_GET['id_actividad'])) {
    header('Location: ' . BASE_URL . '/docente-panel-actividades');
    exit;
}

$id_actividad = filter_var($_GET['id_actividad'], FILTER_VALIDATE_INT);
$id_institucion = $_SESSION['user']['id_institucion'];

if (!$id_actividad) {
    header('Location: ' . BASE_URL . '/docente-panel-actividades');
    exit;
}

// Instanciar modelo
$entregaModel = new EntregaDocente();

// Obtener información de la actividad
$info_actividad = $entregaModel->obtenerInfoActividad($id_actividad, $id_institucion);

if (!$info_actividad) {
    header('Location: ' . BASE_URL . '/docente-panel-actividades');
    exit;
}

// Obtener estudiantes con sus entregas
$estudiantes = $entregaModel->obtenerEstudiantesConEntregas($id_actividad, $id_institucion);

// Obtener estadísticas
$estadisticas = $entregaModel->obtenerEstadisticasEntregas($id_actividad, $id_institucion);

// Calcular porcentajes
$porcentaje_entregas = $estadisticas['total_estudiantes'] > 0 
    ? round(($estadisticas['total_entregas'] / $estadisticas['total_estudiantes']) * 100) 
    : 0;

$porcentaje_calificadas = $estadisticas['total_estudiantes'] > 0 
    ? round(($estadisticas['total_calificadas'] / $estadisticas['total_estudiantes']) * 100) 
    : 0;

// Incluir vista
require_once BASE_PATH . '/app/views/dashboard/docente/entregas_actividad.php';
