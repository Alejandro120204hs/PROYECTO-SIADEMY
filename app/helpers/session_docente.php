<?php
// session_docente.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

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
