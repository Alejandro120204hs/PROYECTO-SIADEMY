<?php

require_once BASE_PATH . '/app/helpers/session_acudiente.php';
require_once BASE_PATH . '/app/controllers/acudiente/view_data.php';
require_once BASE_PATH . '/app/models/acudiente/asistencia.php';

$idInstitucion = (int)($_SESSION['user']['id_institucion'] ?? 0);
$idAcudiente   = acudienteObtenerIdDesdeSesion();
$anio          = (int)date('Y');

$estudianteModel      = new EstudianteAcudiente();
$estudiantesAsociados = $estudianteModel->obtenerEstudiantesAsociados($idAcudiente, $idInstitucion, $anio);
$estudianteSeleccionado = acudienteObtenerEstudianteSeleccionado($estudiantesAsociados);

$totalesGlobales      = [];
$resumenPorAsignatura = [];
$historial            = [];

if ($estudianteSeleccionado) {
    $idEstudiante = (int)$estudianteSeleccionado['id'];
    $model = new AsistenciaAcudiente();

    $totalesGlobales      = $model->obtenerTotalesGlobales($idEstudiante, $idInstitucion);
    $resumenPorAsignatura = $model->obtenerResumenPorAsignatura($idEstudiante, $idInstitucion);
    $historial            = $model->obtenerHistorial($idEstudiante, $idInstitucion, 50);
}

$usuario = obtenerPerfilAcudienteDesdeSesion();

require BASE_PATH . '/app/views/dashboard/acudiente/asistencia.php';
