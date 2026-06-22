<?php

require_once BASE_PATH . '/app/helpers/session_acudiente.php';
require_once BASE_PATH . '/app/controllers/acudiente/view_data.php';
require_once BASE_PATH . '/app/models/estudiante/profesor.php';

$idInstitucion = (int)($_SESSION['user']['id_institucion'] ?? 0);
$idAcudiente   = acudienteObtenerIdDesdeSesion();
$anio          = (int)date('Y');

$estudianteModel      = new EstudianteAcudiente();
$estudiantesAsociados = $estudianteModel->obtenerEstudiantesAsociados($idAcudiente, $idInstitucion, $anio);
$estudianteSeleccionado = acudienteObtenerEstudianteSeleccionado($estudiantesAsociados);

$profesores      = [];
$totalProfesores = 0;
$totalMaterias   = 0;

if ($estudianteSeleccionado) {
    $idEstudiante = (int)$estudianteSeleccionado['id'];
    $model        = new ProfesorEstudiante();
    $profesores   = $model->obtenerProfesoresPorEstudiante($idEstudiante, $idInstitucion, $anio);

    $totalProfesores = count($profesores);
    $totalMaterias   = count(array_unique(array_column($profesores, 'id_asignatura')));
}

$usuario = obtenerPerfilAcudienteDesdeSesion();

require BASE_PATH . '/app/views/dashboard/acudiente/profesores.php';
