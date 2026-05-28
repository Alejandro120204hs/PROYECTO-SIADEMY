<?php

/**
 * Controller: Boletines — Rol Administrador
 *
 * Modos:
 *   - lista  : GET /administrador/boletines         → tabla de estudiantes + filtros
 *   - boletin: GET /administrador/boletines?id=123  → boletín del estudiante seleccionado
 *
 * Seguridad multi-tenant: todo filtrado por id_institucion de sesión.
 */

require_once BASE_PATH . '/app/helpers/session_administrador.php';
require_once BASE_PATH . '/app/models/administradores/boletin.php';
require_once BASE_PATH . '/app/models/estudiante/boletin.php';
require_once BASE_PATH . '/config/database.php';

$idInstitucion = (int)($_SESSION['user']['id_institucion'] ?? 0);
$anio          = (int)date('Y');

$adminModel = new AdminBoletinModel();

// ── ¿Se solicitó el boletín de un estudiante? ────────────────────────────────
$idEstudianteParam = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($idEstudianteParam > 0) {

    // Verificar que el estudiante pertenece a esta institución
    if (!$adminModel->validarEstudianteEnInstitucion($idEstudianteParam, $idInstitucion)) {
        header('Location: ' . BASE_URL . '/administrador/boletines');
        exit;
    }

    $boletinModel        = new BoletinEstudiante();
    $dataBoletin         = $boletinModel->obtenerResumenAnual($idEstudianteParam, $idInstitucion, $anio);

    $boletin_estudiante  = $dataBoletin['estudiante'];
    $boletin_periodos    = $dataBoletin['periodos'];
    $boletin_por_periodo = $dataBoletin['por_periodo'];
    $boletin_sin_datos   = empty($boletin_periodos) || $boletin_estudiante === null;

    $periodoActivoDefault = 1;
    foreach ($boletin_periodos as $p) {
        if ((int)$p['activo'] === 1) {
            $periodoActivoDefault = (int)$p['numero_periodo'];
            break;
        }
    }

    $modo         = 'boletin';
    $idEstudiante = $idEstudianteParam;

} else {

    // ── Modo lista ────────────────────────────────────────────────────────────
    $idCursoFiltro = isset($_GET['curso']) ? (int)$_GET['curso'] : 0;
    $busqueda      = trim($_GET['q'] ?? '');

    $cursos      = $adminModel->obtenerCursos($idInstitucion);
    $estudiantes = $adminModel->obtenerEstudiantes(
        $idInstitucion,
        $anio,
        $idCursoFiltro ?: null,
        $busqueda
    );
    $stats       = $adminModel->obtenerStats($idInstitucion, $anio);

    $modo = 'lista';
}

require BASE_PATH . '/app/views/dashboard/administrador/panel-boletines.php';
