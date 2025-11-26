<?php

    // IMPORTAMOS LAS DEPENDENCIAS NECESARIAS
    require_once __DIR__ . '/../helpers/alert_helper.php';
    require_once __DIR__ . '/../models/login.php';
    

    // $clave = 'Alejo1202';
    // echo password_hash($clave, PASSWORD_DEFAULT);

    // EJECUTAR SEGUN LA SOILICTUD AL SERVIDOR "POST"  
    if($_SERVER['REQUEST_METHOD'] === 'POST'){

        // CAPTURAMOS EN VARIABLES LOS DATOS ENVIADOS A TRAVAEZ DEL METODO POST Y LOS NAME DE LOS CAMPOS
        $correo = $_POST['correo'] ?? '';
        $clave = $_POST['clave'] ?? '';

        // VALIDAMOS QUE LOS CAMPOS NO ESTEN VACIOS
        if(empty($correo) || empty($clave)){
            mostrarSweetAlert('error', 'Campos vacios', 'Por favor complete el formulario.');
            exit();
        }

        // PROGRAMACION ORIENTADA A OBJETOS
        // INSTANCEAMOS LA CLASE PARA ACCEDER A UNA FUNCION EN ESPECIFICO
        $login = new Login();
        $resultado = $login->autenticar($correo,$clave);

        // VERIFICAR SI EL MODELO DEVOLVIO UN ERROR
        if(isset($resultado['error'])){
            mostrarSweetAlert('error', 'Error de autenticacion', $resultado['error']);
            exit();
        }

        // SI PASA ESTA LINEA, EL USUARIO ES VALIDO
        session_start();
        $_SESSION['user']=[
            'id' => $resultado['id'],
            'rol' => $resultado['rol']
        ];

        // REDIRIGIENDO SEGUN EL ROL
        $redireccionar = '/siademy/login';
        $mensaje = 'Rol inexistente. Redirigiendo al inicio de sesion....';

        switch($resultado['rol']){
            case 'Administrador':
                $redireccionar = '/siademy/administrador/dashboard';
                $mensaje = 'Bienvenido Administrador';
                break;

            case 'Docente':
                $redireccionar = '/siademy/docente/dashboard';
                $mensaje = 'Bienvenido docente academmico';
                break;

            case 'Estudiante':
                $redireccionar = '/siademy/estudiante/dashboard';
                $mensaje = 'Bienvenido estudiante academico';
                break;

            case 'Acudiente':
                $redireccionar = '/siademy/acudiente/dashboard';
                $mensaje = 'Bienvenido acudiente';
                break;

            case 'superAdmin':
                $redireccionar = '/siademy/super-admin/dashboard';
                $mensaje = 'Bienvenido super admin';
        }

        mostrarSweetAlert('success', 'Ingreso exitoso', $mensaje, $redireccionar);
        exit();

    }else{
        http_response_code(405);
        echo"Método no permitido";
        exit();
    }

?>