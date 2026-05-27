<?php

/**
 * Controlador: Matrícula (Administrador)
 *
 * CORRECCIONES APLICADAS:
 *  - mostrarMatriculaId(): pasa id_institucion al modelo (fix IDOR); redirige si
 *    la matrícula no existe o no pertenece a la institución.
 *  - actualizarMatricula(): pasa id_institucion para el ownership check del modelo.
 *  - eliminarMatricula(): pasa id_institucion al nuevo soft-delete del modelo.
 */

require_once __DIR__ . '/../../helpers/alert_helper.php';
require_once __DIR__ . '/../../models/administradores/matricula.php';

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'POST':
        $accion = $_POST['accion'] ?? '';
        if ($accion === 'actualizar') {
            actualizarMatricula();
        } else {
            registrarMatricula();
        }
        break;

    case 'GET':
        $accion = $_GET['accion'] ?? '';

        if ($accion === 'eliminar') {
            eliminarMatricula((int)($_GET['id'] ?? 0));
            break;
        }

        if (isset($_GET['id'])) {
            mostrarMatriculaId((int)$_GET['id']);
        } else {
            mostrarMatriculas();
        }
        break;

    default:
        http_response_code(405);
        echo "Método no permitido";
        break;
}

// ─────────────────────────────────────────────────────────────────────────────
// REGISTRAR
// ─────────────────────────────────────────────────────────────────────────────
function registrarMatricula() {
    $id_estudiante = $_POST['id_estudiante'] ?? '';
    $id_curso      = $_POST['id_curso']      ?? '';
    $anio          = $_POST['anio']          ?? date('Y');
    $fecha         = $_POST['fecha']         ?? date('Y-m-d');

    if (empty($id_estudiante) || empty($id_curso)) {
        mostrarSweetAlert('error', 'Campos vacíos', 'Por favor seleccione estudiante y curso.');
        exit();
    }

    if (session_status() === PHP_SESSION_NONE) { session_start(); }
    if (!isset($_SESSION['user']['id_institucion'])) {
        mostrarSweetAlert('error', 'Error de sesión', 'No se encontró la institución del administrador.');
        exit();
    }
    $id_institucion = (int) $_SESSION['user']['id_institucion'];

    $data = [
        'id_institucion' => $id_institucion,
        'id_estudiante'  => (int) $id_estudiante,
        'id_curso'       => (int) $id_curso,
        'anio'           => (int) $anio,
        'fecha'          => $fecha,
    ];

    $resultado = (new Matricula())->registrar($data);

    if ($resultado['success'] === true) {
        mostrarSweetAlert('success', 'Matrícula exitosa', 'El estudiante ha sido matriculado correctamente. Redirigiendo...', '/siademy/administrador-panel-matriculas');
    } else {
        mostrarSweetAlert('error', 'Error al matricular', $resultado['message'], '/siademy/administrador/registrar-matricula');
    }
    exit();
}

// ─────────────────────────────────────────────────────────────────────────────
// LISTAR TODAS (panel)
// ─────────────────────────────────────────────────────────────────────────────
function mostrarMatriculas() {
    if (session_status() === PHP_SESSION_NONE) { session_start(); }
    $id_institucion = (int) ($_SESSION['user']['id_institucion'] ?? 0);
    return (new Matricula())->listar($id_institucion);
}

// ─────────────────────────────────────────────────────────────────────────────
// LISTAR POR ID — con ownership check: redirige si no pertenece a la institución
// ─────────────────────────────────────────────────────────────────────────────
function mostrarMatriculaId($id) {
    if (session_status() === PHP_SESSION_NONE) { session_start(); }
    $id_institucion = (int) ($_SESSION['user']['id_institucion'] ?? 0);

    $resultado = (new Matricula())->listarMatriculaId($id, $id_institucion);

    if (!$resultado) {
        // La matrícula no existe o no pertenece a esta institución
        header('Location: ' . BASE_URL . '/administrador-panel-matriculas');
        exit();
    }

    return $resultado;
}

// ─────────────────────────────────────────────────────────────────────────────
// ACTUALIZAR
// ─────────────────────────────────────────────────────────────────────────────
function actualizarMatricula() {
    $id            = (int) ($_POST['id']            ?? 0);
    $id_estudiante = (int) ($_POST['id_estudiante'] ?? 0);
    $id_curso      = (int) ($_POST['id_curso']      ?? 0);
    $anio          = (int) ($_POST['anio']          ?? 0);
    $fecha         = $_POST['fecha']  ?? '';
    $estado        = $_POST['estado'] ?? 'Activa';

    if ($id === 0 || $id_estudiante === 0 || $id_curso === 0 || $anio === 0) {
        mostrarSweetAlert('error', 'Campos vacíos', 'Por favor complete todos los campos.');
        exit();
    }

    if (session_status() === PHP_SESSION_NONE) { session_start(); }
    if (!isset($_SESSION['user']['id_institucion'])) {
        mostrarSweetAlert('error', 'Error de sesión', 'No se encontró la institución del administrador.');
        exit();
    }
    $id_institucion = (int) $_SESSION['user']['id_institucion'];

    $data = [
        'id'             => $id,
        'id_institucion' => $id_institucion,   // ← ownership check en el modelo
        'id_estudiante'  => $id_estudiante,
        'id_curso'       => $id_curso,
        'anio'           => $anio,
        'fecha'          => $fecha,
        'estado'         => $estado,
    ];

    $resultado = (new Matricula())->actualizar($data);

    if ($resultado['success'] === true) {
        mostrarSweetAlert('success', 'Actualización exitosa', 'La matrícula ha sido actualizada. Redirigiendo...', '/siademy/administrador-panel-matriculas');
    } else {
        mostrarSweetAlert('error', 'Error al actualizar', $resultado['message'], '/siademy/administrador-panel-matriculas');
    }
    exit();
}

// ─────────────────────────────────────────────────────────────────────────────
// ELIMINAR (soft delete — cambia estado a 'Retirada')
// ─────────────────────────────────────────────────────────────────────────────
function eliminarMatricula($id) {
    if ($id <= 0) {
        mostrarSweetAlert('error', 'ID inválido', 'No se pudo identificar la matrícula.');
        exit();
    }

    if (session_status() === PHP_SESSION_NONE) { session_start(); }
    $id_institucion = (int) ($_SESSION['user']['id_institucion'] ?? 0);

    $resultado = (new Matricula())->eliminar($id, $id_institucion);

    if ($resultado === true) {
        mostrarSweetAlert('success', 'Matrícula retirada', 'La matrícula ha sido marcada como retirada. Redirigiendo...', '/siademy/administrador-panel-matriculas');
    } else {
        mostrarSweetAlert('error', 'Error al retirar', 'No se pudo retirar la matrícula o no pertenece a su institución.', '/siademy/administrador-panel-matriculas');
    }
    exit();
}
