<?php

/**
 * Controlador de Configuración de Perfil
 * Redirige directamente a la sección de editar perfil del usuario
 */

require_once BASE_PATH . '/app/helpers/session_helper.php';
require_once BASE_PATH . '/app/controllers/perfil.php';

// Verificar que haya sesión activa
redirectIfNoSession('/siademy/login');

// Obtener el ID del usuario desde la sesión
$id = $_SESSION['user']['id'];

// Obtener los datos del usuario
$usuario = mostrarPerfil($id);

// Si no se encuentran los datos del usuario, redirigir
if (!$usuario) {
    header('Location: ' . BASE_URL . '/login');
    exit();
}

// Incluir la vista de perfil con un parámetro para mostrar la sección de configuración
$activeTab = 'edit-profile'; // Esta variable estará disponible en la vista
require BASE_PATH . '/app/views/dashboard/usuario/perfil.php';

?>
