<?php

/**
 * Controlador: Horarios Académicos (rol Administrador)
 * Maneja el CRUD de bloques de horario y los endpoints AJAX.
 */

require_once BASE_PATH . '/app/helpers/session_administrador.php';
require_once BASE_PATH . '/app/models/administradores/horario.php';

$idInstitucion = (int) ($_SESSION['user']['id_institucion'] ?? 0);

if ($idInstitucion === 0) {
    header('Location: ' . BASE_URL . '/login');
    exit();
}

$model  = new HorarioModel();
$method = $_SERVER['REQUEST_METHOD'];
$accion = $method === 'POST' ? ($_POST['accion'] ?? '') : ($_GET['accion'] ?? 'panel');

// ── AJAX: obtener DAC por curso ──────────────────────────────────────────────
if ($accion === 'obtener_dac_por_curso') {
    header('Content-Type: application/json');
    $idCurso = (int) ($_GET['id_curso'] ?? 0);
    if ($idCurso === 0) {
        echo json_encode(['success' => false, 'data' => []]);
        exit();
    }
    $dac = $model->obtenerDacPorCurso($idInstitucion, $idCurso);
    echo json_encode(['success' => true, 'data' => $dac]);
    exit();
}

// ── AJAX: verificar conflictos (live) ───────────────────────────────────────
if ($accion === 'verificar_conflicto') {
    header('Content-Type: application/json');
    $idDac      = (int)  ($_GET['id_dac']      ?? 0);
    $dia        = (int)  ($_GET['dia']          ?? 0);
    $horaInicio = trim(  ($_GET['hora_inicio']  ?? ''));
    $horaFin    = trim(  ($_GET['hora_fin']     ?? ''));
    $excluirId  = (int)  ($_GET['excluir_id']   ?? 0) ?: null;
    $aula       = trim(  ($_GET['aula']         ?? '')) ?: null;

    if ($idDac === 0 || $dia === 0 || !$horaInicio || !$horaFin) {
        echo json_encode(['success' => true, 'conflictos' => []]);
        exit();
    }
    $conflictos = $model->verificarConflictos(
        $idInstitucion, $idDac, $dia, $horaInicio, $horaFin, $excluirId, $aula
    );
    echo json_encode(['success' => true, 'conflictos' => $conflictos]);
    exit();
}

// ── POST: guardar nuevo horario ──────────────────────────────────────────────
if ($method === 'POST' && $accion === 'guardar') {
    $idDac      = (int)   ($_POST['id_dac']      ?? 0);
    $dia        = (int)   ($_POST['dia_semana']  ?? 0);
    $horaInicio = trim(   ($_POST['hora_inicio'] ?? ''));
    $horaFin    = trim(   ($_POST['hora_fin']    ?? ''));
    $aula       = trim(   ($_POST['aula']        ?? '')) ?: null;
    $idCurso    = (int)   ($_POST['id_curso']    ?? 0);

    if ($idDac === 0 || $dia === 0 || !$horaInicio || !$horaFin) {
        $_SESSION['horario_alerta'] = ['tipo' => 'warning', 'mensaje' => 'Completa todos los campos requeridos.'];
        header('Location: ' . BASE_URL . '/administrador/horarios?id_curso=' . $idCurso);
        exit();
    }

    $res = $model->guardar($idInstitucion, $idDac, $dia, $horaInicio, $horaFin, $aula);

    if ($res['success']) {
        $_SESSION['horario_alerta'] = ['tipo' => 'success', 'mensaje' => 'Bloque de horario guardado correctamente.'];
    } else {
        $msgs = array_column($res['conflictos'], 'mensaje');
        $_SESSION['horario_alerta'] = ['tipo' => 'warning', 'mensaje' => implode(' | ', $msgs)];
    }

    header('Location: ' . BASE_URL . '/administrador/horarios?id_curso=' . $idCurso);
    exit();
}

// ── POST: actualizar horario ─────────────────────────────────────────────────
if ($method === 'POST' && $accion === 'actualizar') {
    $idHorario  = (int)   ($_POST['id']          ?? 0);
    $idDac      = (int)   ($_POST['id_dac']      ?? 0);
    $dia        = (int)   ($_POST['dia_semana']  ?? 0);
    $horaInicio = trim(   ($_POST['hora_inicio'] ?? ''));
    $horaFin    = trim(   ($_POST['hora_fin']    ?? ''));
    $aula       = trim(   ($_POST['aula']        ?? '')) ?: null;
    $idCurso    = (int)   ($_POST['id_curso']    ?? 0);

    if ($idHorario === 0 || $idDac === 0 || $dia === 0 || !$horaInicio || !$horaFin) {
        $_SESSION['horario_alerta'] = ['tipo' => 'warning', 'mensaje' => 'Datos incompletos para actualizar.'];
        header('Location: ' . BASE_URL . '/administrador/horarios?id_curso=' . $idCurso);
        exit();
    }

    $res = $model->actualizar($idHorario, $idInstitucion, $idDac, $dia, $horaInicio, $horaFin, $aula);

    if ($res['success']) {
        $_SESSION['horario_alerta'] = ['tipo' => 'success', 'mensaje' => 'Horario actualizado correctamente.'];
    } else {
        $msgs = array_column($res['conflictos'], 'mensaje');
        $_SESSION['horario_alerta'] = ['tipo' => 'warning', 'mensaje' => implode(' | ', $msgs)];
    }

    header('Location: ' . BASE_URL . '/administrador/horarios?id_curso=' . $idCurso);
    exit();
}

// ── GET: eliminar horario ────────────────────────────────────────────────────
if ($method === 'GET' && $accion === 'eliminar') {
    $idHorario = (int) ($_GET['id']       ?? 0);
    $idCurso   = (int) ($_GET['id_curso'] ?? 0);

    if ($model->eliminar($idHorario, $idInstitucion)) {
        $_SESSION['horario_alerta'] = ['tipo' => 'success', 'mensaje' => 'Bloque eliminado correctamente.'];
    } else {
        $_SESSION['horario_alerta'] = ['tipo' => 'danger', 'mensaje' => 'No se pudo eliminar el bloque.'];
    }

    header('Location: ' . BASE_URL . '/administrador/horarios?id_curso=' . $idCurso);
    exit();
}

// ── GET: panel principal ─────────────────────────────────────────────────────
$cursos  = $model->obtenerCursos($idInstitucion);
$idCurso = (int) ($_GET['id_curso'] ?? ($cursos[0]['id'] ?? 0));

$horarios    = $idCurso > 0 ? $model->obtenerHorariosPorCurso($idInstitucion, $idCurso) : [];
$stats       = $idCurso > 0 ? $model->obtenerStats($idInstitucion, $idCurso) : [];
$dacOptions  = $idCurso > 0 ? $model->obtenerDacPorCurso($idInstitucion, $idCurso) : [];

// Organizar horarios por día para la grilla semanal
$horariosPorDia = [];
for ($d = 1; $d <= 6; $d++) {
    $horariosPorDia[$d] = [];
}
foreach ($horarios as $h) {
    $horariosPorDia[(int) $h['dia_semana']][] = $h;
}

// Asignar colores por asignatura (cycling)
$coloresPorAsignatura = [];
$colorIdx = 0;
foreach ($horarios as $h) {
    $idAsig = $h['id_asignatura'];
    if (!isset($coloresPorAsignatura[$idAsig])) {
        $coloresPorAsignatura[$idAsig] = HorarioModel::$colores[$colorIdx % count(HorarioModel::$colores)];
        $colorIdx++;
    }
}

// Alerta de sesión
$alerta = $_SESSION['horario_alerta'] ?? null;
unset($_SESSION['horario_alerta']);

require BASE_PATH . '/app/views/dashboard/administrador/panel-horarios.php';
