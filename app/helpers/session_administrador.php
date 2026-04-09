<?php 

if (session_status() === PHP_SESSION_NONE) {
    session_start();
    }
    // VALIDAMOS SI HAY UNA SESION ACTIVA
    if(!isset($_SESSION['user'])){
        header('Location: ' . BASE_URL . '/login');
        exit();
    }

    // VALIDAMOS QUE EL ROL SEA EL CORRESPONDIENTE
    if($_SESSION['user']['rol'] != 'Administrador'){
        header('Location: ' . BASE_URL . '/login');
        exit();
    }

?>