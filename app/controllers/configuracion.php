<?php

require_once BASE_PATH . '/app/helpers/session_helper.php';
require_once BASE_PATH . '/app/helpers/alert_helper.php';
require_once BASE_PATH . '/app/controllers/perfil.php';
require_once BASE_PATH . '/app/models/perfil.php';

initSession();
redirectIfNoSession('/login');

$rolUsuario = $_SESSION['user']['rol'] ?? '';
if ($rolUsuario !== 'Administrador' && $rolUsuario !== 'superAdmin' && $rolUsuario !== 'Docente') {
    header('Location: ' . BASE_URL . '/login');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && (($_POST['accion'] ?? '') === 'actualizar-perfil')) {
    $idUsuario = (int) ($_SESSION['user']['id'] ?? 0);
    $nombres = trim($_POST['nombres'] ?? '');
    $apellidos = trim($_POST['apellidos'] ?? '');
    $correo = trim($_POST['correo'] ?? '');
    $telefono = trim($_POST['telefono'] ?? '');
    $edad = trim($_POST['edad'] ?? '');

    if ($idUsuario <= 0 || $nombres === '' || $apellidos === '' || $correo === '' || $telefono === '' || $edad === '') {
        mostrarSweetAlert('error', 'Campos vacíos', 'Por favor completa todos los campos.', '/configuracion');
        exit();
    }

    if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        mostrarSweetAlert('warning', 'Correo inválido', 'Ingresa un correo electrónico válido.', '/configuracion');
        exit();
    }

    if (!ctype_digit($edad) || (int)$edad < 1 || (int)$edad > 120) {
        mostrarSweetAlert('warning', 'Edad inválida', 'La edad debe estar entre 1 y 120.', '/configuracion');
        exit();
    }

    $modelo = new Perfil();
    $okCorreo = $modelo->actualizarCorreoUsuario($idUsuario, $correo);

    // Para superAdmin no existe tabla administrador asociada, solo se actualiza correo.
    $okDatos = true;
    if ($rolUsuario === 'Administrador') {
        $okDatos = $modelo->actualizarDatosAdministradorPorUsuario($idUsuario, [
            'nombres' => $nombres,
            'apellidos' => $apellidos,
            'telefono' => $telefono,
            'edad' => (int)$edad,
        ]);
    }

    if ($okCorreo && $okDatos) {
        mostrarSweetAlert('success', 'Perfil actualizado', 'Los datos del perfil se actualizaron correctamente.', '/dashboard-perfil');
        exit();
    }

    mostrarSweetAlert('error', 'Error al actualizar', 'No fue posible actualizar el perfil. Intenta de nuevo.', '/configuracion');
    exit();
}

$id = (int)($_SESSION['user']['id'] ?? 0);
$usuario = mostrarPerfil($id);

if (!$usuario) {
    header('Location: ' . BASE_URL . '/login');
    exit();
}

$tab = $_GET['tab'] ?? 'edit-profile';
$activeTab = ($tab === 'change-password') ? 'change-password' : 'edit-profile';
require BASE_PATH . '/app/views/dashboard/usuario/perfil.php';

?>
