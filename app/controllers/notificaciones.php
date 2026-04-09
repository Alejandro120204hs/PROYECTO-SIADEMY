<?php

/**
 * Controlador de Notificaciones
 * Muestra las notificaciones del usuario
 */

require_once BASE_PATH . '/app/helpers/session_helper.php';

// Verificar que haya sesión activa
redirectIfNoSession('/siademy/login');

// Incluir la vista de notificaciones
require BASE_PATH . '/app/views/notificaciones.php';

?>
