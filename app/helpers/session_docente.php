<?php

/**
 * Session Docente - Valida que el usuario sea un docente
 * Incluye el archivo principal de sesión helper para usar sus funciones
 */

require_once __DIR__ . '/session_helper.php';

// Iniciar sesión si no está activa
initSession();

// Verificamos que haya una sesión activa
if (!isset($_SESSION['user']) || empty($_SESSION['user'])) {
    header('Location: ' . BASE_URL . '/login');
    exit();
}

// Validamos que el rol sea Docente
if (!isset($_SESSION['user']['rol']) || $_SESSION['user']['rol'] !== 'Docente') {
    header('Location: ' . BASE_URL . '/login');
    exit();
}

?>
