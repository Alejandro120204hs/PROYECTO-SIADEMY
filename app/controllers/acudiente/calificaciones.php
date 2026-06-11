<?php

/**
 * CONTROLADOR - CALIFICACIONES (ACUDIENTE)
 * Muestra el seguimiento académico (promedios por materia y periodo,
 * actividades evaluadas) del estudiante actualmente seleccionado por el acudiente.
 * Reutiliza el modelo MateriaEstudiante (mismas consultas y fórmulas que el rol Estudiante).
 */

require_once BASE_PATH . '/app/helpers/session_acudiente.php';
require_once BASE_PATH . '/app/controllers/acudiente/view_data.php';
require_once BASE_PATH . '/app/models/estudiante/materia.php';

$idInstitucion = (int)($_SESSION['user']['id_institucion'] ?? 0);
$idAcudiente   = acudienteObtenerIdDesdeSesion();
$anio          = (int)date('Y');

$estudianteModel = new EstudianteAcudiente();
$estudiantesAsociados = $estudianteModel->obtenerEstudiantesAsociados($idAcudiente, $idInstitucion, $anio);
$estudianteSeleccionado = acudienteObtenerEstudianteSeleccionado($estudiantesAsociados);

$resumenCalificaciones = [
    'total_materias' => 0,
    'promedio_general' => 0,
    'total_evaluaciones' => 0,
    'pendientes' => 0,
];
$periodoActual = 1;
$periodoSeleccionado = 1;
$calificacionesMaterias = [];
$mejorMateria = null;
$peorMateria = null;
$totalAprobadas = 0;
$totalEnObservacion = 0;

if ($estudianteSeleccionado) {
    $idEstudiante = (int)$estudianteSeleccionado['id'];
    $materiaModel = new MateriaEstudiante();

    $resumenCalificaciones = $materiaModel->obtenerResumenCalificaciones($idEstudiante, $idInstitucion, $anio);
    $periodoActual = $materiaModel->obtenerPeriodoActualNumero($idInstitucion, $anio);

    $periodoSeleccionado = (int)($_GET['periodo'] ?? $periodoActual);
    if ($periodoSeleccionado < 1 || $periodoSeleccionado > 4) {
        $periodoSeleccionado = $periodoActual;
    }

    $materiasBase = $materiaModel->obtenerMateriasConEstadisticas($idEstudiante, $idInstitucion, $anio);
    $evaluaciones = $materiaModel->obtenerEvaluacionesPorMateriaYPeriodo($idEstudiante, $idInstitucion, $anio);
    $calificacionesMaterias = $materiaModel->agruparEvaluacionesPorMateriaYPeriodo($materiasBase, $evaluaciones);

    foreach ($calificacionesMaterias as $materia) {
        $promedio = $materia['promedio_general'];
        if ($promedio === null) {
            continue;
        }

        if ($promedio >= 3.0) {
            $totalAprobadas++;
        } else {
            $totalEnObservacion++;
            if ($peorMateria === null || $promedio < $peorMateria['promedio']) {
                $peorMateria = ['nombre' => $materia['nombre'], 'promedio' => $promedio];
            }
        }

        if ($mejorMateria === null || $promedio > $mejorMateria['promedio']) {
            $mejorMateria = ['nombre' => $materia['nombre'], 'promedio' => $promedio];
        }
    }
}

$usuario = obtenerPerfilAcudienteDesdeSesion();

require BASE_PATH . '/app/views/dashboard/acudiente/calificaciones.php';
