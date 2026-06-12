<?php

/**
 * Controlador: seleccionar el estudiante "activo" del acudiente.
 * Valida que el estudiante pertenezca al acudiente (y a su institución)
 * antes de guardarlo en sesión.
 */

require_once BASE_PATH . '/app/helpers/session_acudiente.php';
require_once BASE_PATH . '/app/controllers/acudiente/view_data.php';
require_once BASE_PATH . '/app/models/acudiente/estudiante.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . BASE_URL . '/acudiente/dashboard');
    exit();
}

$idEstudiante  = (int)($_POST['id_estudiante'] ?? 0);
$idAcudiente   = acudienteObtenerIdDesdeSesion();
$idInstitucion = (int)($_SESSION['user']['id_institucion'] ?? 0);
$anio          = (int)date('Y');

$estudianteModel = new EstudianteAcudiente();
$estudiante = $estudianteModel->obtenerEstudiantePorId($idEstudiante, $idAcudiente, $idInstitucion, $anio);

if ($estudiante) {
    $_SESSION['acudiente']['id_estudiante_seleccionado'] = (int)$estudiante['id'];
}

$redirect = (string)($_POST['redirect'] ?? '');
if (!preg_match('#^/acudiente(/[a-zA-Z0-9\-]+)*$#', $redirect)) {
    $redirect = '/acudiente/dashboard';
}

header('Location: ' . BASE_URL . $redirect);
exit();
