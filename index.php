<?php

    // index.php - Router principal, EN LARAVEL SE TIENE UN ARCHIVO DIFERENTE POR CADA CARPTEA

    define('BASE_PATH', __DIR__);

    // OBTENER LA URI ACTUAL (POR EJEMPLO: /SIADEMY/login)
    $requestUri = $_SERVER['REQUEST_URI'];

    // QUITAR EL PREFIJO DE LA CARPETA DEL PROYECTO
    $request = str_replace('/siademy', '', $requestUri);

    // QUITAR PARAMETROS TIPO ?id=123
    $request = strtok($request, '?');

    // QUITAR LA BARRA FINAL (SI EXISTE)
    $request = rtrim($request, '/');

    // SI LA RUTA QUEDA VACIA, SE INTERPETRA COMO "/"
    if($request === '')$request = '/';
    
    // ENRUTAMIENTO BASICO
    switch($request){
        case '/':
            require BASE_PATH . '/app/views/website/index.html';
            break;

        // INICIO RUTAS LOGIN
        case '/login':
            require BASE_PATH . '/app/views/auth/login.php';
            break;
            
        case '/iniciarSesion':
            require BASE_PATH . '/app/controllers/loginController.php';
            break;
        case '/superAdmin':
            require BASE_PATH . '/app/views/dashboard/superAdmin/superAdmin.html';
            break;
        // FIN RUTAS LOGIN
        default: 
            http_response_code(404);
            require BASE_PATH . '/app/views/auth/404.html';
            break;
    }
?>