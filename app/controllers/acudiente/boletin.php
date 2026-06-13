<?php

/**
 * Controlador: Boletín académico (vista del acudiente).
 * Reutiliza BoletinEstudiante::obtenerResumenAnual(), el mismo modelo
 * que usa el rol Estudiante, garantizando que el acudiente vea exactamente
 * la misma información académica oficial (calificaciones + asistencia por período).
 *
 * Seguridad: el estudiante se resuelve SIEMPRE desde
 * acudienteObtenerEstudianteSeleccionado(), que valida que pertenezca
 * al acudiente autenticado (id_acudiente + id_institucion). No se acepta
 * ningún id_estudiante por parámetro de la petición.
 */

require_once BASE_PATH . '/app/helpers/session_acudiente.php';
require_once BASE_PATH . '/app/controllers/acudiente/view_data.php';
require_once BASE_PATH . '/app/models/estudiante/boletin.php';

$idInstitucion = (int)($_SESSION['user']['id_institucion'] ?? 0);
$idAcudiente   = acudienteObtenerIdDesdeSesion();
$anio          = (int)date('Y');

$estudianteModel = new EstudianteAcudiente();
$estudiantesAsociados = $estudianteModel->obtenerEstudiantesAsociados($idAcudiente, $idInstitucion, $anio);
$estudianteSeleccionado = acudienteObtenerEstudianteSeleccionado($estudiantesAsociados);

$boletin_estudiante  = null;
$boletin_periodos    = [];
$boletin_por_periodo = [];
$boletin_sin_datos   = true;
$periodoActivoDefault = 1;

if ($estudianteSeleccionado) {
    $idEstudiante = (int)$estudianteSeleccionado['id'];

    $boletinModel = new BoletinEstudiante();
    $dataBoletin  = $boletinModel->obtenerResumenAnual($idEstudiante, $idInstitucion, $anio);

    $boletin_estudiante  = $dataBoletin['estudiante'];
    $boletin_periodos    = $dataBoletin['periodos'];
    $boletin_por_periodo = $dataBoletin['por_periodo'];
    $boletin_sin_datos   = empty($boletin_periodos) || $boletin_estudiante === null;

    foreach ($boletin_periodos as $p) {
        if ((int)$p['activo'] === 1) {
            $periodoActivoDefault = (int)$p['numero_periodo'];
            break;
        }
    }
}

$usuario = obtenerPerfilAcudienteDesdeSesion();

require BASE_PATH . '/app/views/dashboard/acudiente/boletin.php';
