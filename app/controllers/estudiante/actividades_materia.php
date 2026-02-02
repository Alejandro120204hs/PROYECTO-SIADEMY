<?php

// CONTROLADOR PARA VER ACTIVIDADES DE UNA MATERIA ESPECÍFICA
require_once BASE_PATH . '/app/models/estudiante/actividad.php';
require_once BASE_PATH . '/config/database.php';

// VERIFICAR SESIÓN
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// VERIFICAR QUE ESTÉ LOGUEADO COMO ESTUDIANTE
if (!isset($_SESSION['user']) || $_SESSION['user']['rol'] !== 'Estudiante') {
    header('Location: ' . BASE_URL . '/login');
    exit;
}

// VALIDAR PARÁMETRO ID
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: ' . BASE_URL . '/estudiante-panel-materias');
    exit;
}

$id_asignatura_curso = filter_var($_GET['id'], FILTER_VALIDATE_INT);

if (!$id_asignatura_curso) {
    header('Location: ' . BASE_URL . '/estudiante-panel-materias');
    exit;
}

// OBTENER ID DEL ESTUDIANTE
$id_usuario_sesion = $_SESSION['user']['id'];
$id_institucion = $_SESSION['user']['id_institucion'];

$db = new Conexion();
$pdo = $db->getConexion();

$stmt = $pdo->prepare("SELECT id FROM estudiante WHERE id_usuario = ?");
$stmt->execute([$id_usuario_sesion]);
$estudiante_info = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$estudiante_info) {
    header('Location: ' . BASE_URL . '/login');
    exit;
}

$id_estudiante = $estudiante_info['id'];

// INSTANCIAR MODELO Y OBTENER DATOS
$actividadModel = new ActividadEstudiante();

// Obtener información de la materia con estadísticas
$materia_info = $actividadModel->obtenerInfoMateriaConEstadisticas($id_estudiante, $id_asignatura_curso, $id_institucion);

if (!$materia_info) {
    header('Location: ' . BASE_URL . '/estudiante-panel-materias');
    exit;
}

// Obtener todas las actividades de la materia
$actividades = $actividadModel->obtenerActividadesPorMateria($id_estudiante, $id_asignatura_curso, $id_institucion);

// Calcular estadísticas de actividades
$total_actividades = count($actividades);
$pendientes = 0;
$completadas = 0;
$atrasadas = 0;

foreach ($actividades as $actividad) {
    switch ($actividad['estado_entrega']) {
        case 'Pendiente':
            $pendientes++;
            break;
        case 'Calificada':
            $completadas++;
            break;
        case 'Vencida':
            $atrasadas++;
            break;
    }
}

// INCLUIR LA VISTA
require BASE_PATH . '/app/views/dashboard/estudiante/actividades_materia.php';

?>
