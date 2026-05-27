<?php

/**
 * Controlador: Horario del Estudiante (rol Estudiante)
 * Muestra el horario semanal del curso al que pertenece el estudiante.
 */

require_once BASE_PATH . '/app/helpers/session_estudiante.php';
require_once BASE_PATH . '/app/models/administradores/horario.php';
require_once BASE_PATH . '/config/database.php';

$idUsuario     = (int) ($_SESSION['user']['id']            ?? 0);
$idInstitucion = (int) ($_SESSION['user']['id_institucion'] ?? 0);

// Resolver id_estudiante
try {
    $db  = new Conexion();
    $pdo = $db->getConexion();
    $stmt = $pdo->prepare("SELECT id FROM estudiante WHERE id_usuario = :u AND id_institucion = :i LIMIT 1");
    $stmt->bindValue(':u', $idUsuario,     PDO::PARAM_INT);
    $stmt->bindValue(':i', $idInstitucion, PDO::PARAM_INT);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $idEstudiante = (int) ($row['id'] ?? 0);
} catch (PDOException $e) {
    error_log('estudiante/horario.php: error resolviendo id_estudiante → ' . $e->getMessage());
    $idEstudiante = 0;
}

$model    = new HorarioModel();
$horarios = ($idEstudiante > 0)
    ? $model->obtenerHorariosPorEstudiante($idEstudiante, $idInstitucion)
    : [];

// Organizar por día
$horariosPorDia = array_fill(1, 6, []);
foreach ($horarios as $h) {
    $horariosPorDia[(int) $h['dia_semana']][] = $h;
}

// Colores por asignatura
$coloresPorAsignatura = [];
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

require BASE_PATH . '/app/views/dashboard/estudiante/horario.php';
