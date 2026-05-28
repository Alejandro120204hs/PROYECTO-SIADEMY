<?php

/**
 * Controller: Boletines — Rol Docente
 *
 * El docente solo puede ver los boletines de los estudiantes
 * a quienes les dicta clase en su institución.
 *
 * Modos:
 *   - lista  : GET /docente/boletines        → tabla de sus estudiantes
 *   - boletin: GET /docente/boletines?id=123 → boletín del estudiante
 */

require_once BASE_PATH . '/app/helpers/session_docente.php';
require_once BASE_PATH . '/app/models/docente/boletin.php';
require_once BASE_PATH . '/app/models/estudiante/boletin.php';
require_once BASE_PATH . '/config/database.php';

$idUsuario     = (int)($_SESSION['user']['id']            ?? 0);
$idInstitucion = (int)($_SESSION['user']['id_institucion'] ?? 0);
$anio          = (int)date('Y');

// ── Resolver id_docente ───────────────────────────────────────────────────────
try {
    $db   = new Conexion();
    $pdo  = $db->getConexion();
    $stmt = $pdo->prepare(
        "SELECT id FROM docente WHERE id_usuario = :u AND id_institucion = :i LIMIT 1"
    );
    $stmt->bindValue(':u', $idUsuario,     PDO::PARAM_INT);
    $stmt->bindValue(':i', $idInstitucion, PDO::PARAM_INT);
    $stmt->execute();
    $row       = $stmt->fetch(PDO::FETCH_ASSOC);
    $idDocente = (int)($row['id'] ?? 0);
} catch (PDOException $e) {
    error_log('docente/boletin.php: error resolviendo id_docente → ' . $e->getMessage());
    $idDocente = 0;
}

$docenteModel = new DocenteBoletinModel();

// ── ¿Se solicitó el boletín de un estudiante específico? ─────────────────────
$idEstudianteParam = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($idDocente > 0 && $idEstudianteParam > 0) {

    // Verificar que el estudiante es de un curso del docente (multi-tenant)
    if (!$docenteModel->validarEstudianteParaDocente($idEstudianteParam, $idDocente, $idInstitucion)) {
        header('Location: ' . BASE_URL . '/docente/boletines');
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

    $cursos      = $idDocente > 0
        ? $docenteModel->obtenerCursosDocente($idDocente, $idInstitucion, $anio)
        : [];

    $estudiantes = ($idDocente > 0)
        ? $docenteModel->obtenerEstudiantes(
            $idDocente, $idInstitucion, $anio,
            $idCursoFiltro ?: null, $busqueda
          )
        : [];

    $stats = $idDocente > 0
        ? $docenteModel->obtenerStats($idDocente, $idInstitucion, $anio)
        : ['total_estudiantes' => 0, 'total_cursos' => 0];

    $modo = 'lista';
}

require BASE_PATH . '/app/views/dashboard/docente/boletin.php';
