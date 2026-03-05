<?php

/**
 * Session Helper - Funciones auxiliares para manejar sesiones
 * Proporciona funciones reutilizables para iniciar, verificar y destruir sesiones
 */

/**
 * Inicia la sesión solo si no está iniciada
 * Esta función se ejecuta automáticamente en el punto de entrada
 */
function initSession() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}

/**
 * Verifica si hay una sesión activa
 * @return bool true si hay sesión activa, false en caso contrario
 */
function isSessionActive() {
    return isset($_SESSION['user']) && !empty($_SESSION['user']);
}

/**
 * Verifica si el usuario tiene un rol específico
 * @param string $rol El rol a verificar (e.g., 'Administrador', 'Docente', 'Estudiante')
 * @return bool true si el rol coincide, false en caso contrario
 */
function hasRole($rol) {
    return isSessionActive() && isset($_SESSION['user']['rol']) && $_SESSION['user']['rol'] === $rol;
}

/**
 * Obtiene los datos del usuario actual
 * @return array|null Los datos del usuario o null si no hay sesión
 */
function getCurrentUser() {
    return isSessionActive() ? $_SESSION['user'] : null;
}

/**
 * Establece datos en la sesión del usuario
 * @param array $userData Los datos del usuario a almacenar
 */
function setUserSession($userData) {
    initSession();
    $_SESSION['user'] = $userData;
}

/**
 * Cierra la sesión de forma segura
 * - Elimina todas las variables de sesión
 * - Destruye la sesión completamente
 * - Elimina la cookie de sesión
 * - Limpia cualquier cookie relacionada a autenticación
 */
function destroySession() {
    initSession();
    
    // Obtener el nombre de la cookie de sesión
    $cookieName = session_name();
    
    // Limpiar todas las variables de sesión
    $_SESSION = array();
    
    // Destruir la sesión
    session_destroy();
    
    // Eliminar la cookie de sesión si existe
    if (isset($_COOKIE[$cookieName])) {
        setcookie($cookieName, '', time() - 3600, '/');
        unset($_COOKIE[$cookieName]);
    }
    
    // Limpiar cualquier otra cookie relacionada a la autenticación
    foreach ($_COOKIE as $key => $value) {
        if (strpos($key, 'auth') !== false || strpos($key, 'user') !== false) {
            setcookie($key, '', time() - 3600, '/');
            unset($_COOKIE[$key]);
        }
    }
}

/**
 * Redirige al login si no hay sesión activa
 * @param string $baseUrl URL base para la redirección
 */
function redirectIfNoSession($baseUrl = '/siademy/login') {
    if (!isSessionActive()) {
        header('Location: ' . $baseUrl);
        exit();
    }
}

/**
 * Redirige si el rol del usuario no coincide
 * @param string $requiredRole El rol requerido
 * @param string $redirectUrl URL de redirección (opcional)
 */
function redirectIfNotRole($requiredRole, $redirectUrl = '/siademy/login') {
    if (!hasRole($requiredRole)) {
        header('Location: ' . $redirectUrl);
        exit();
    }
}

?>
