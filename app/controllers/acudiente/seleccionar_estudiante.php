<?php

/**
 * Controlador: seleccionar el estudiante "activo" del acudiente.
 * Valida que el estudiante pertenezca al acudiente (y a su institución)
 * antes de guardarlo en sesión.
 */

require_once BASE_PATH . '/app/helpers/session_acudiente.php';
require_once BASE_PATH . '/app/models/acudiente/estudiante.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . BASE_URL . '/acudiente/dashboard');
    exit();
}

$idEstudiante  = (int)($_POST['id_estudiante'] ?? 0);
$idAcudiente   = (int)($_SESSION['user']['id_acudiente'] ?? 0);
$idInstitucion = (int)($_SESSION['user']['id_institucion'] ?? 0);
$anio          = (int)date('Y');

$estudianteModel = new EstudianteAcudiente();
$estudiante = $estudianteModel->obtenerEstudiantePorId($idEstudiante, $idAcudiente, $idInstitucion, $anio);

if ($estudiante) {
    $_SESSION['acudiente']['id_estudiante_seleccionado'] = (int)$estudiante['id'];
}

header('Location: ' . BASE_URL . '/acudiente/dashboard');
exit();
