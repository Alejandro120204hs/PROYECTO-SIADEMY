<?php

    // IMPORTAMOS LAS DEPENDENCIAS NECESARIAS
    require_once __DIR__ . '/../helpers/alert_helper.php';
    require_once __DIR__ . '/../helpers/session_helper.php';
    require_once __DIR__ . '/../models/perfil.php';

    function mostrarPerfil($id){
        $objetoPerfil = new Perfil();
        $rol = $_SESSION['user']['rol'] ?? '';

        switch ($rol) {
            case 'Administrador':
                $usuario = $objetoPerfil->mostrarPerfilAdministrador($id);
                break;

            case 'superAdmin':
                $usuario = $objetoPerfil->mostrarPerfilSuperAdmin($id);
                break;

            default:
                $usuario = $objetoPerfil->mostrarPerfilGenerico($id);
                break;
        }

        if (!is_array($usuario) || empty($usuario)) {
            return [
                'nombres' => $_SESSION['user']['rol'] ?? 'Usuario',
                'apellidos' => '',
                'foto' => 'default.png',
                'correo' => $_SESSION['user']['correo'] ?? '',
                'rol' => $rol ?: 'Usuario',
                'nombre_institucion' => '',
                'direccion_institucion' => ''
            ];
        }

        return $usuario;
    }

    function actualizarClaveUsuarioActual()
    {
        initSession();
        redirectIfNoSession('/login');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/dashboard-perfil');
            exit();
        }

        $idUsuario = (int) ($_SESSION['user']['id'] ?? 0);
        $claveActual = $_POST['cActual'] ?? '';
        $claveNueva = $_POST['cNueva'] ?? '';
        $confirmacion = $_POST['conClave'] ?? '';

        if ($idUsuario <= 0 || $claveActual === '' || $claveNueva === '' || $confirmacion === '') {
            mostrarSweetAlert('error', 'Campos vacios', 'Por favor complete todos los campos.', '/configuracion?tab=change-password');
            exit();
        }

        if (strlen($claveNueva) < 8) {
            mostrarSweetAlert('warning', 'Contraseña insegura', 'La nueva contraseña debe tener al menos 8 caracteres.', '/configuracion?tab=change-password');
            exit();
        }

        if ($claveNueva !== $confirmacion) {
            mostrarSweetAlert('warning', 'Validación fallida', 'La confirmación no coincide con la nueva contraseña.', '/configuracion?tab=change-password');
            exit();
        }

        $modelo = new Perfil();
        $hashActual = $modelo->obtenerClaveUsuario($idUsuario);

        if (!$hashActual || !password_verify($claveActual, $hashActual)) {
            mostrarSweetAlert('error', 'Error de autenticación', 'La contraseña actual no es correcta.', '/configuracion?tab=change-password');
            exit();
        }

        $nuevoHash = password_hash($claveNueva, PASSWORD_DEFAULT);
        $actualizado = $modelo->actualizarClaveUsuario($idUsuario, $nuevoHash);

        if ($actualizado) {
            mostrarSweetAlert('success', 'Contraseña actualizada', 'Tu contraseña se actualizó correctamente.', '/dashboard-perfil');
            exit();
        }

        mostrarSweetAlert('error', 'Error al actualizar', 'No fue posible actualizar la contraseña. Intenta de nuevo.', '/configuracion?tab=change-password');
        exit();
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && (($_POST['accion'] ?? '') === 'actualizar-clave')) {
        actualizarClaveUsuarioActual();
    }

?>