<?php

/**
 * Controlador: Mis Profesores (rol Estudiante)
 * Resuelve el id del estudiante desde sesión, consulta el modelo y
 * pasa los datos a la vista misProfesores.php.
 */

require_once BASE_PATH . '/app/helpers/session_estudiante.php'; // valida sesión y rol al incluirse
require_once BASE_PATH . '/app/models/estudiante/profesor.php';
require_once BASE_PATH . '/config/database.php';

$idUsuario     = (int)($_SESSION['user']['id']            ?? 0);
$idInstitucion = (int)($_SESSION['user']['id_institucion'] ?? 0);
$anio          = (int)date('Y');

// ── Resolver id real de la tabla `estudiante` ───────────────────────────────
try {
    $db  = new Conexion();
    $pdo = $db->getConexion();
    $stmtEst = $pdo->prepare("SELECT id FROM estudiante WHERE id_usuario = :u AND id_institucion = :i LIMIT 1");
    $stmtEst->bindValue(':u', $idUsuario,     PDO::PARAM_INT);
    $stmtEst->bindValue(':i', $idInstitucion, PDO::PARAM_INT);
    $stmtEst->execute();
    $rowEst      = $stmtEst->fetch(PDO::FETCH_ASSOC);
    $idEstudiante = (int)($rowEst['id'] ?? 0);
} catch (PDOException $e) {
    error_log('profesores.php: error resolviendo id_estudiante -> ' . $e->getMessage());
    $idEstudiante = 0;
}

// ── Consultar profesores ─────────────────────────────────────────────────────
$model      = new ProfesorEstudiante();
$profesores = ($idEstudiante > 0)
    ? $model->obtenerProfesoresPorEstudiante($idEstudiante, $idInstitucion, $anio)
    : [];

// ── Resumen para las stat-cards ──────────────────────────────────────────────
$totalProfesores = count($profesores);
$totalMaterias   = count(array_unique(array_column($profesores, 'id_asignatura')));
$promedioGeneral = $totalProfesores > 0
    ? round(
        array_sum(array_filter(array_column($profesores, 'promedio_estudiante'), 'is_numeric'))
        / max(1, count(array_filter(array_column($profesores, 'promedio_estudiante'), 'is_numeric'))),
        1
      )
    : null;

// ── Pasar a la vista ─────────────────────────────────────────────────────────
require BASE_PATH . '/app/views/dashboard/estudiante/misProfesores.php';
