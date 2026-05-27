<?php

/**
 * Controller: Boletín del Estudiante
 * Carga los datos del boletín anual y despacha la vista.
 */

require_once BASE_PATH . '/app/helpers/session_estudiante.php';
require_once BASE_PATH . '/app/models/estudiante/boletin.php';
require_once BASE_PATH . '/config/database.php';

$idInstitucion = (int)($_SESSION['user']['id_institucion'] ?? 0);
$idUsuario     = (int)($_SESSION['user']['id']             ?? 0);
$anio          = (int)date('Y');

// ── Resolver id_estudiante desde sesión ──────────────────────────────────────
$db   = new Conexion();
$pdo  = $db->getConexion();
$stmt = $pdo->prepare('SELECT id FROM estudiante WHERE id_usuario = :id_usuario LIMIT 1');
$stmt->bindParam(':id_usuario', $idUsuario, PDO::PARAM_INT);
$stmt->execute();
$row  = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$row) {
    header('Location: ' . BASE_URL . '/login');
    exit;
}

$idEstudiante = (int)$row['id'];

// ── Obtener datos del boletín ─────────────────────────────────────────────────
$boletinModel = new BoletinEstudiante();
$dataBoletin  = $boletinModel->obtenerResumenAnual($idEstudiante, $idInstitucion, $anio);

// ── Variables para la vista ───────────────────────────────────────────────────
$boletin_estudiante  = $dataBoletin['estudiante'];
$boletin_periodos    = $dataBoletin['periodos'];
$boletin_por_periodo = $dataBoletin['por_periodo'];
$boletin_sin_datos   = empty($boletin_periodos) || $boletin_estudiante === null;

// Período activo por defecto: el marcado como activo en BD, o el primero disponible
$periodoActivoDefault = 1;
foreach ($boletin_periodos as $p) {
    if ((int)$p['activo'] === 1) {
        $periodoActivoDefault = (int)$p['numero_periodo'];
        break;
    }
}

require BASE_PATH . '/app/views/dashboard/estudiante/boletin.php';
