<?php

/**
 * Controlador de Ayuda y Soporte
 * Muestra la página de ayuda y soporte
 */

require_once BASE_PATH . '/app/helpers/session_helper.php';

// Verificar que haya sesión activa
redirectIfNoSession('/siademy/login');

// Incluir la vista de ayuda
require BASE_PATH . '/app/views/ayuda.php';

?>
