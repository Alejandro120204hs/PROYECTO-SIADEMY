<?php

    // IMPORTAMOS LAS DEPENDENCIAS NECESARIAS
    require_once __DIR__ . '/../helpers/alert_helper.php';
    require_once __DIR__ . '/../models/recoveryPass.php';

    // CAPTURAMOS EN VARIABLES LOS DATOS ENVIADOS A ATRAVEZ DEL METODO POST Y LOS NAME DE LOS CAMPOS
    $correo = $_POST['correo'] ?? '';

     if(empty($correo)){
            mostrarSweetAlert('error', 'Campos vacios', 'Por favor complete el formulario.');
            exit();
        }

    $objetoModelo = new Recovery();
    $resultado = $objetoModelo -> recuperarClave($correo);

    // AGREGAR SWEET ALERT DEL ENVIO O NO ENVIO DEL CORREO
    if($resultado === true){
            mostrarSweetAlert('success', 'Nueva clave generada', 'Se ha enviado a una nueva contraseña a su correo electronico. Redirigiendo...', '/siademy/login');
            exit();
        }else{
            mostrarSweetAlert('error', 'Usuario no encontrado', 'Verifique su correo electronico e intente nuevamente.  Redirigiendo...', '/siademy/login');
            exit();
        }
        exit();



?>