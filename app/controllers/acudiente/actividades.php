<?php

require_once BASE_PATH . '/app/helpers/session_acudiente.php';
require_once BASE_PATH . '/app/controllers/acudiente/view_data.php';
require_once BASE_PATH . '/app/models/acudiente/actividad.php';

$idInstitucion = (int)($_SESSION['user']['id_institucion'] ?? 0);
$idAcudiente   = acudienteObtenerIdDesdeSesion();
$anio          = (int)date('Y');

$estudianteModel      = new EstudianteAcudiente();
$estudiantesAsociados = $estudianteModel->obtenerEstudiantesAsociados($idAcudiente, $idInstitucion, $anio);
$estudianteSeleccionado = acudienteObtenerEstudianteSeleccionado($estudiantesAsociados);

$actividades   = [];
$statActividades = ['total' => 0, 'pendientes' => 0, 'entregadas' => 0, 'calificadas' => 0, 'vencidas' => 0];

if ($estudianteSeleccionado) {
    $idEstudiante = (int)$estudianteSeleccionado['id'];
    $model        = new ActividadAcudiente();
    $actividades  = $model->obtenerTodasLasActividades($idEstudiante, $idInstitucion, $anio);

    foreach ($actividades as $act) {
        $statActividades['total']++;
        switch ($act['estado_entrega']) {
            case 'Pendiente':   $statActividades['pendientes']++;  break;
            case 'Entregada':   $statActividades['entregadas']++;  break;
            case 'Calificada':  $statActividades['calificadas']++; break;
            case 'Vencida':     $statActividades['vencidas']++;    break;
        }
    }
}

$usuario = obtenerPerfilAcudienteDesdeSesion();

require BASE_PATH . '/app/views/dashboard/acudiente/actividades.php';
