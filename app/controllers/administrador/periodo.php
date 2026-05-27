<?php

/**
 * Controlador: Período Académico (Administrador)
 *
 * CORRECCIONES APLICADAS:
 *  - actualizarPeriodo(): ya no hardcodea 'planificado'. Lee el estado real del
 *    período actual y lo preserva en el UPDATE.
 *  - editarPeriodo(): pasa id_institucion a listarPeriodoId (fix IDOR).
 *  - eliminarPeriodo(): verifica pertenencia institucional y usa el nuevo retorno
 *    array del modelo que incluye el mensaje de error.
 *  - activarPeriodo(): ya no necesita cambios (el modelo maneja la lógica).
 */

require_once __DIR__ . '/../../helpers/alert_helper.php';
require_once __DIR__ . '/../../models/administradores/periodo.php';

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'POST':
        $accion = $_POST['accion'] ?? '';
        if ($accion === 'actualizar') {
            actualizarPeriodo();
        } else {
            registrarPeriodo();
        }
        break;

    case 'GET':
        $accion = $_GET['accion'] ?? '';

        if ($accion === 'eliminar') {
            eliminarPeriodo($_GET['id'] ?? 0);
        }

        if ($accion === 'activar') {
            activarPeriodo($_GET['id'] ?? 0);
        }

        if ($accion === 'editar' && isset($_GET['id'])) {
            editarPeriodo((int)$_GET['id']);
        }

        if ($accion === 'obtener-activo') {
            obtenerPeriodoActivo();
        }

        if ($accion === 'obtener-anos') {
            obtenerAnosDisponibles();
        } elseif (!isset($_GET['accion'])) {
            mostrarPeriodos();
        }
        break;

    default:
        http_response_code(405);
        echo "Método no permitido";
        break;
}

// ─────────────────────────────────────────────────────────────────────────────
// REGISTRAR (simple o múltiple)
// ─────────────────────────────────────────────────────────────────────────────
function registrarPeriodo() {
    if (session_status() === PHP_SESSION_NONE) { session_start(); }
    if (!isset($_SESSION['user']['id_institucion'])) {
        mostrarSweetAlert('error', 'Error de sesión', 'No se encontró la institución del administrador.');
        exit();
    }
    $id_institucion = (int) $_SESSION['user']['id_institucion'];
    $objetoPeriodo  = new Periodo();

    // Registro múltiple (arrays)
    if (isset($_POST['fecha_inicio']) && is_array($_POST['fecha_inicio'])) {
        $nombres      = $_POST['nombre']         ?? [];
        $tipos        = $_POST['tipo_periodo']   ?? [];
        $numeros      = $_POST['numero_periodo'] ?? [];
        $anos         = $_POST['ano_lectivo']    ?? [];
        $fechasInicio = $_POST['fecha_inicio']   ?? [];
        $fechasFin    = $_POST['fecha_fin']      ?? [];
        $activos      = $_POST['activo']         ?? [];

        $count = count($fechasInicio);
        if ($count === 0) {
            mostrarSweetAlert('error', 'Campos vacíos', 'No se encontraron períodos para registrar.');
            exit();
        }

        for ($i = 0; $i < $count; $i++) {
            if (empty($nombres[$i]) || empty($tipos[$i]) || empty($numeros[$i]) || empty($anos[$i]) || empty($fechasInicio[$i]) || empty($fechasFin[$i])) {
                mostrarSweetAlert('error', 'Campos vacíos', 'Complete todos los campos de los períodos generados.');
                exit();
            }
            if (strtotime($fechasInicio[$i]) >= strtotime($fechasFin[$i])) {
                mostrarSweetAlert('error', 'Fechas inválidas', 'La fecha de inicio debe ser menor a la fecha de fin.');
                exit();
            }
        }

        $allOk = true;
        for ($i = 0; $i < $count; $i++) {
            $activoFlag = (isset($activos[$i]) && $activos[$i] == 'on') ? 1 : 0;
            $res = $objetoPeriodo->registrar([
                'nombre'          => $nombres[$i],
                'tipo_periodo'    => $tipos[$i],
                'numero_periodo'  => $numeros[$i],
                'ano_lectivo'     => $anos[$i],
                'fecha_inicio'    => $fechasInicio[$i],
                'fecha_fin'       => $fechasFin[$i],
                'activo'          => $activoFlag,
                'estado'          => $activoFlag == 1 ? 'en_curso' : 'planificado',
                'institucion_id'  => $id_institucion,
            ]);
            if (!$res) { $allOk = false; }
        }

        if ($allOk) {
            mostrarSweetAlert('success', 'Registro exitoso', 'Se han creado los períodos académicos. Redirigiendo...', '/siademy/administrador-periodo');
        } else {
            mostrarSweetAlert('error', 'Error al registrar', 'Ocurrió un error al crear algunos períodos. Redirigiendo...', '/siademy/administrador-periodo');
        }
        exit();
    }

    // Registro simple
    $nombre        = $_POST['nombre']         ?? '';
    $tipo_periodo  = $_POST['tipo_periodo']   ?? '';
    $numero_periodo= $_POST['numero_periodo'] ?? '';
    $ano_lectivo   = $_POST['ano_lectivo']    ?? '';
    $fecha_inicio  = $_POST['fecha_inicio']   ?? '';
    $fecha_fin     = $_POST['fecha_fin']      ?? '';
    $activo        = (isset($_POST['activo']) && $_POST['activo'] == 'on') ? 1 : 0;

    if (empty($nombre) || empty($tipo_periodo) || empty($numero_periodo) || empty($ano_lectivo) || empty($fecha_inicio) || empty($fecha_fin)) {
        mostrarSweetAlert('error', 'Campos vacíos', 'Por favor complete todos los campos requeridos.');
        exit();
    }
    if (strtotime($fecha_inicio) >= strtotime($fecha_fin)) {
        mostrarSweetAlert('error', 'Fechas inválidas', 'La fecha de inicio debe ser menor a la fecha de fin.');
        exit();
    }

    $resultado = $objetoPeriodo->registrar([
        'nombre'          => $nombre,
        'tipo_periodo'    => $tipo_periodo,
        'numero_periodo'  => $numero_periodo,
        'ano_lectivo'     => $ano_lectivo,
        'fecha_inicio'    => $fecha_inicio,
        'fecha_fin'       => $fecha_fin,
        'activo'          => $activo,
        'estado'          => $activo == 1 ? 'en_curso' : 'planificado',
        'institucion_id'  => $id_institucion,
    ]);

    if ($resultado === true) {
        mostrarSweetAlert('success', 'Registro exitoso', 'Se ha creado un nuevo período académico. Redirigiendo...', '/siademy/administrador-periodo');
    } else {
        mostrarSweetAlert('error', 'Error al registrar', 'No se pudo registrar el período, intente nuevamente.', '/siademy/administrador-periodo');
    }
    exit();
}

// ─────────────────────────────────────────────────────────────────────────────
// MOSTRAR LISTA (JSON)
// ─────────────────────────────────────────────────────────────────────────────
function mostrarPeriodos() {
    if (session_status() === PHP_SESSION_NONE) { session_start(); }
    if (!isset($_SESSION['user'])) {
        http_response_code(401);
        die('No autorizado');
    }
    $id_institucion = (int) $_SESSION['user']['id_institucion'];
    $periodos = (new Periodo())->listar($id_institucion);
    header('Content-Type: application/json');
    echo json_encode($periodos);
}

// ─────────────────────────────────────────────────────────────────────────────
// EDITAR — retorna JSON del período; verifica pertenencia institucional (fix IDOR)
// ─────────────────────────────────────────────────────────────────────────────
function editarPeriodo($id) {
    if (session_status() === PHP_SESSION_NONE) { session_start(); }
    if (!isset($_SESSION['user'])) {
        http_response_code(401);
        header('Content-Type: application/json');
        echo json_encode(['error' => 'No autorizado']);
        exit();
    }
    $id_institucion = (int) $_SESSION['user']['id_institucion'];
    $periodo = (new Periodo())->listarPeriodoId($id, $id_institucion);
    header('Content-Type: application/json');
    echo json_encode($periodo ?: ['error' => 'Período no encontrado o no pertenece a su institución']);
}

// ─────────────────────────────────────────────────────────────────────────────
// ACTUALIZAR — preserva el estado actual del período (no hardcodea 'planificado')
// ─────────────────────────────────────────────────────────────────────────────
function actualizarPeriodo() {
    $id             = (int) ($_POST['id']             ?? 0);
    $nombre         = $_POST['nombre']         ?? '';
    $tipo_periodo   = $_POST['tipo_periodo']   ?? '';
    $numero_periodo = $_POST['numero_periodo'] ?? '';
    $ano_lectivo    = $_POST['ano_lectivo']    ?? '';
    $fecha_inicio   = $_POST['fecha_inicio']   ?? '';
    $fecha_fin      = $_POST['fecha_fin']      ?? '';

    if ($id === 0 || empty($nombre) || empty($tipo_periodo) || empty($numero_periodo) || empty($ano_lectivo) || empty($fecha_inicio) || empty($fecha_fin)) {
        mostrarSweetAlert('error', 'Campos vacíos', 'Por favor complete todos los campos requeridos.');
        exit();
    }
    if (strtotime($fecha_inicio) >= strtotime($fecha_fin)) {
        mostrarSweetAlert('error', 'Fechas inválidas', 'La fecha de inicio debe ser menor a la fecha de fin.');
        exit();
    }

    if (session_status() === PHP_SESSION_NONE) { session_start(); }
    if (!isset($_SESSION['user']['id_institucion'])) {
        mostrarSweetAlert('error', 'Error de sesión', 'No se encontró la institución del administrador.');
        exit();
    }
    $id_institucion = (int) $_SESSION['user']['id_institucion'];

    $objetoPeriodo = new Periodo();

    // ── CORRECCIÓN CRÍTICA: leer el estado real del período antes de actualizar
    //    (antes se hardcodeaba 'planificado', lo que rompía el período activo)
    $periodoActual = $objetoPeriodo->listarPeriodoId($id, $id_institucion);
    if (!$periodoActual) {
        mostrarSweetAlert('error', 'Período no encontrado', 'El período no existe o no pertenece a su institución.');
        exit();
    }
    $estado_real = $periodoActual['estado']; // preservar: 'en_curso', 'planificado' o 'finalizado'

    $resultado = $objetoPeriodo->actualizar([
        'id'              => $id,
        'institucion_id'  => $id_institucion,
        'nombre'          => $nombre,
        'tipo_periodo'    => $tipo_periodo,
        'numero_periodo'  => $numero_periodo,
        'ano_lectivo'     => $ano_lectivo,
        'fecha_inicio'    => $fecha_inicio,
        'fecha_fin'       => $fecha_fin,
        'estado'          => $estado_real,   // ← estado correcto, no hardcodeado
    ]);

    if ($resultado === true) {
        mostrarSweetAlert('success', 'Actualización exitosa', 'Se ha actualizado el período académico. Redirigiendo...', '/siademy/administrador-periodo');
    } else {
        mostrarSweetAlert('error', 'Error al actualizar', 'No se pudo actualizar el período, intente nuevamente.', '/siademy/administrador-periodo');
    }
    exit();
}

// ─────────────────────────────────────────────────────────────────────────────
// ELIMINAR — usa el nuevo retorno array del modelo (con mensaje de error)
// ─────────────────────────────────────────────────────────────────────────────
function eliminarPeriodo($id) {
    $id = (int) $id;
    if (session_status() === PHP_SESSION_NONE) { session_start(); }
    if (!isset($_SESSION['user'])) {
        http_response_code(401);
        mostrarSweetAlert('error', 'No autorizado', 'No tienes permiso para realizar esta acción.');
        exit();
    }
    $id_institucion = (int) $_SESSION['user']['id_institucion'];

    $resultado = (new Periodo())->eliminar($id, $id_institucion);

    if ($resultado['success'] === true) {
        mostrarSweetAlert('success', 'Eliminación exitosa', 'Se ha eliminado el período académico. Redirigiendo...', '/siademy/administrador-periodo');
    } else {
        mostrarSweetAlert('error', 'Error al eliminar', $resultado['message'] ?? 'No se pudo eliminar el período.', '/siademy/administrador-periodo');
    }
    exit();
}

// ─────────────────────────────────────────────────────────────────────────────
// ACTIVAR
// ─────────────────────────────────────────────────────────────────────────────
function activarPeriodo($id) {
    $id = (int) $id;
    if (session_status() === PHP_SESSION_NONE) { session_start(); }
    if (!isset($_SESSION['user'])) {
        http_response_code(401);
        mostrarSweetAlert('error', 'No autorizado', 'No tienes permiso para realizar esta acción.');
        exit();
    }
    $id_institucion = (int) $_SESSION['user']['id_institucion'];

    $resultado = (new Periodo())->activar($id, $id_institucion);

    if ($resultado === true) {
        mostrarSweetAlert('success', 'Activación exitosa', 'Se ha activado el período académico. Redirigiendo...', '/siademy/administrador-periodo');
    } else {
        mostrarSweetAlert('error', 'Error al activar', 'No se pudo activar el período. Verifique que pertenece a su institución.', '/siademy/administrador-periodo');
    }
    exit();
}

// ─────────────────────────────────────────────────────────────────────────────
// OBTENER PERÍODO ACTIVO (JSON)
// ─────────────────────────────────────────────────────────────────────────────
function obtenerPeriodoActivo() {
    if (session_status() === PHP_SESSION_NONE) { session_start(); }
    if (!isset($_SESSION['user'])) {
        http_response_code(401);
        header('Content-Type: application/json');
        echo json_encode(['error' => 'No autorizado']);
        exit();
    }
    $id_institucion = (int) $_SESSION['user']['id_institucion'];
    $periodoActivo  = (new Periodo())->obtenerPeriodoActivo($id_institucion);
    header('Content-Type: application/json');
    echo json_encode($periodoActivo);
}

// ─────────────────────────────────────────────────────────────────────────────
// OBTENER AÑOS DISPONIBLES (JSON)
// ─────────────────────────────────────────────────────────────────────────────
function obtenerAnosDisponibles() {
    if (session_status() === PHP_SESSION_NONE) { session_start(); }
    if (!isset($_SESSION['user'])) {
        http_response_code(401);
        header('Content-Type: application/json');
        echo json_encode(['error' => 'No autorizado']);
        exit();
    }
    $id_institucion  = (int) $_SESSION['user']['id_institucion'];
    $todosLosPeriodos = (new Periodo())->listar($id_institucion);

    $anosDisponibles = [];
    foreach ($todosLosPeriodos as $periodo) {
        if (!in_array($periodo['ano_lectivo'], $anosDisponibles, true)) {
            $anosDisponibles[] = $periodo['ano_lectivo'];
        }
    }
    sort($anosDisponibles, SORT_NUMERIC);
    $anosDisponibles = array_reverse($anosDisponibles);

    if (empty($anosDisponibles)) {
        $anoActual       = (int) date('Y');
        $anosDisponibles = [$anoActual, $anoActual + 1, $anoActual + 2, $anoActual + 3, $anoActual + 4];
    }

    header('Content-Type: application/json');
    echo json_encode($anosDisponibles);
}
