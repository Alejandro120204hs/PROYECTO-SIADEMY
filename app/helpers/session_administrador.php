<?php

/**
 * Session Administrador - Valida que el usuario sea un administrador
 * Incluye el archivo principal de sesión helper para usar sus funciones
 */

require_once __DIR__ . '/session_helper.php';

// Iniciar sesión si no está activa
initSession();

// Validamos si hay una sesión activa
if (!isSessionActive()) {
    header('Location: /siademy/login');
    exit();
}

// Validamos que el rol sea el correspondiente (Administrador)
if (!hasRole('Administrador')) {
    header('Location: /siademy/login');
    exit();
}

?>