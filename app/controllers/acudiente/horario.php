<?php

require_once BASE_PATH . '/app/helpers/session_acudiente.php';
require_once BASE_PATH . '/app/controllers/acudiente/view_data.php';
require_once BASE_PATH . '/app/models/administradores/horario.php';

$idInstitucion = (int)($_SESSION['user']['id_institucion'] ?? 0);
$idAcudiente   = acudienteObtenerIdDesdeSesion();
$anio          = (int)date('Y');

$estudianteModel      = new EstudianteAcudiente();
$estudiantesAsociados = $estudianteModel->obtenerEstudiantesAsociados($idAcudiente, $idInstitucion, $anio);
$estudianteSeleccionado = acudienteObtenerEstudianteSeleccionado($estudiantesAsociados);

$horarios       = [];
$horariosPorDia = array_fill(1, 6, []);
$coloresPorAsignatura = [];
$totalBloques   = 0;
$cursoNombre    = '';

if ($estudianteSeleccionado) {
    $idEstudiante = (int)$estudianteSeleccionado['id'];
    $model    = new HorarioModel();
    $horarios = $model->obtenerHorariosPorEstudiante($idEstudiante, $idInstitucion);

    foreach ($horarios as $h) {
        $horariosPorDia[(int)$h['dia_semana']][] = $h;
    }

    $colorIdx = 0;
    $colores  = HorarioModel::$colores;
    foreach ($horarios as $h) {
        if (!isset($coloresPorAsignatura[$h['asignatura_nombre']])) {
            $coloresPorAsignatura[$h['asignatura_nombre']] = $colores[$colorIdx % count($colores)];
            $colorIdx++;
        }
    }

    $totalBloques = count($horarios);
    $cursoNombre  = $horarios[0]['curso_nombre'] ?? '';
}

$usuario = obtenerPerfilAcudienteDesdeSesion();

require BASE_PATH . '/app/views/dashboard/acudiente/horario.php';
