<?php

/**
 * Controlador de Logout
 * Maneja el cierre de sesión seguro para todos los roles del sistema
 * (Admin, Docente, Estudiante, Acudiente, Secretaría, etc.)
 */

// Incluir helpers
require_once BASE_PATH . '/app/helpers/session_helper.php';

// Destruir la sesión de forma segura
destroySession();

// Redirigir al login
header('Location: ' . BASE_URL . '/login', true, 302);
exit();

?>
