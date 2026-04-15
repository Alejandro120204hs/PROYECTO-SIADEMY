<?php

require_once BASE_PATH . '/app/models/estudiante/materia.php';
require_once BASE_PATH . '/config/database.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user']) || $_SESSION['user']['rol'] !== 'Estudiante') {
    header('Location: ' . BASE_URL . '/login');
    exit;
}

$id_usuario_sesion = $_SESSION['user']['id'];
$id_institucion = $_SESSION['user']['id_institucion'];
$anio_actual = (int)date('Y');

$db = new Conexion();
$pdo = $db->getConexion();

$stmt = $pdo->prepare('SELECT id FROM estudiante WHERE id_usuario = ?');
$stmt->execute([$id_usuario_sesion]);
$estudiante_info = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$estudiante_info) {
    header('Location: ' . BASE_URL . '/login');
    exit;
}

$id_estudiante = (int)$estudiante_info['id'];
$materiaModel = new MateriaEstudiante();

$resumen_calificaciones = $materiaModel->obtenerResumenCalificaciones($id_estudiante, $id_institucion, $anio_actual);
$periodo_actual = $materiaModel->obtenerPeriodoActualNumero($id_institucion, $anio_actual);
$materias_base = $materiaModel->obtenerMateriasConEstadisticas($id_estudiante, $id_institucion, $anio_actual);
$evaluaciones = $materiaModel->obtenerEvaluacionesPorMateriaYPeriodo($id_estudiante, $id_institucion, $anio_actual);

$calificaciones_materias = [];

foreach ($materias_base as $materia) {
    $idMateriaCurso = (int)$materia['id_asignatura_curso'];
    $calificaciones_materias[$idMateriaCurso] = [
        'id' => $idMateriaCurso,
        'nombre' => $materia['materia'],
        'profesor' => 'Prof. ' . trim(($materia['docente_nombres'] ?? '') . ' ' . ($materia['docente_apellidos'] ?? '')),
        'icono' => $materia['icono'] ?? 'ri-book-line',
        'color_icono' => $materia['color_icono'] ?? 'linear-gradient(135deg, #3b82f6 0%, #2563eb 100%)',
        'periodos' => [
            1 => ['notaFinal' => null, 'estado' => null, 'evaluaciones' => []],
            2 => ['notaFinal' => null, 'estado' => null, 'evaluaciones' => []],
            3 => ['notaFinal' => null, 'estado' => null, 'evaluaciones' => []],
            4 => ['notaFinal' => null, 'estado' => null, 'evaluaciones' => []],
        ],
    ];
}

foreach ($evaluaciones as $fila) {
    $idMateriaCurso = (int)($fila['id_asignatura_curso'] ?? 0);
    if ($idMateriaCurso <= 0 || !isset($calificaciones_materias[$idMateriaCurso])) {
        continue;
    }

    if (empty($fila['evaluacion'])) {
        continue;
    }

    $periodo = (int)($fila['numero_periodo'] ?? 1);
    if ($periodo < 1 || $periodo > 4) {
        $periodo = 1;
    }

    $nota = isset($fila['nota']) ? (float)$fila['nota'] : null;
    $ponderacion = isset($fila['ponderacion']) ? (float)$fila['ponderacion'] : 0;

    $calificaciones_materias[$idMateriaCurso]['periodos'][$periodo]['evaluaciones'][] = [
        'nombre' => $fila['evaluacion'],
        'fecha' => !empty($fila['fecha_entrega']) ? date('d M Y', strtotime($fila['fecha_entrega'])) : '-',
        'nota' => $nota,
        'peso' => rtrim(rtrim(number_format($ponderacion, 1), '0'), '.') . '%',
    ];
}

foreach ($calificaciones_materias as &$materia) {
    for ($p = 1; $p <= 4; $p++) {
        $notas = [];
        foreach ($materia['periodos'][$p]['evaluaciones'] as $evaluacion) {
            if ($evaluacion['nota'] !== null) {
                $notas[] = (float)$evaluacion['nota'];
            }
        }

        if (!empty($notas)) {
            $promedio = round(array_sum($notas) / count($notas), 1);
            $materia['periodos'][$p]['notaFinal'] = $promedio;

            if ($promedio >= 4.5) {
                $materia['periodos'][$p]['estado'] = 'excelente';
            } elseif ($promedio >= 4.0) {
                $materia['periodos'][$p]['estado'] = 'bueno';
            } elseif ($promedio >= 3.0) {
                $materia['periodos'][$p]['estado'] = 'medio';
            } elseif ($promedio >= 2.5) {
                $materia['periodos'][$p]['estado'] = 'riesgo';
            } else {
                $materia['periodos'][$p]['estado'] = 'critico';
            }
        }
    }
}
unset($materia);

require BASE_PATH . '/app/views/dashboard/estudiante/calificaciones.php';
