<?php

/**
 * Session Docente - Valida que el usuario sea un docente
 * Incluye el archivo principal de sesión helper para usar sus funciones
 */

require_once __DIR__ . '/session_helper.php';

// Iniciar sesión si no está activa
initSession();

// Verificamos que haya una sesión activa
if (!isSessionActive()) {
    header('Location: /siademy/login');
    exit();
}

// Validamos que el rol sea Docente
if (!hasRole('Docente')) {
    header('Location: /siademy/login');
    exit();
}

?>
