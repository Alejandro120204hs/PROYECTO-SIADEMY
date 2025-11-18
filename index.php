<?php

    // index.php - Router principal, EN LARAVEL SE TIENE UN ARCHIVO DIFERENTE POR CADA CARPETA

    require_once __DIR__ . '/config/config.php';

    // OBTENER LA URI ACTUAL (POR EJEMPLO: /siademy/login)
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
            
        case '/iniciar-sesion':
            require BASE_PATH . '/app/controllers/loginController.php';
            break;

        // --------------------------------ROL: SUPER ADMIN--------------------------------------------------------------
        case '/superAdmin-dashboard':
            require BASE_PATH . '/app/views/dashboard/superAdmin/superAdmin.php';
            break;

        // -------------------------------MODULO INSTITUCIONES----------------------------------------

        case '/superAdmin-panel-institucion':
            require BASE_PATH . '/app/views/dashboard/superAdmin/instituciones.php';
            break;

          case '/superAdmin-agregar-escuelas':
            require BASE_PATH . '/app/views/dashboard/superAdmin/addInstitucion.php';
            break;

        // ................................MODULO ADMINISTRADORES-----------------------------------

        case '/superAdmin-panel-administradores':
            require BASE_PATH . '/app/views/dashboard/superAdmin/administradores.php';
            break;

        case '/superAdmin-agregar-administrador':
            require BASE_PATH . '/app/views/dashboard/superAdmin/addAdministrador.php';
            break;

        // --------------------------------MODULO PAGOS-----------------------------------------
        case '/superAdmin-panel-pagos':
            require BASE_PATH . '/app/views/dashboard/superAdmin/pagos.php';
            break;

      

        // -----------------------------ROL: COORDINADOR--------------------------------------------------------------

        case '/coordinador/dashboard':
            require BASE_PATH . '/app/views/dashboard/coordinador/admin.php';
            break;
        // ------------------------------MODULO ESTUDIANTES---------------------
        case '/coordinador-panel-estudiantes':
            require BASE_PATH . '/app/views/dashboard/coordinador/panel-estudiantes.php';
            break;

        
        case '/coordinador/registrar-estudiante':
            require BASE_PATH . '/app/views/dashboard/coordinador/addStudent.php';
            break;

        case '/coordinador/guardar_estudiante':
            require BASE_PATH . '/app/controllers/estudiante_controller.php';
            break;

        case '/coordinador/detalle-estudiante':
            require BASE_PATH . '/app/views/dashboard/coordinador/detalle-estudiante.php';
            break;


             // -------------------------MODULO ACUDIENTE--------------------------

        case '/coordinador-panel-acudientes':
            require BASE_PATH . '/app/views/dashboard/coordinador/panel-acudientes.php';
            break;
       
        case '/coordinador/registrar-acudiente':
            require BASE_PATH . '/app/views/dashboard/coordinador/addAcudiente.php';
            break;

        case '/coordinador/guardar_acudiente':
            require BASE_PATH . '/app/controllers/acudiente.php';
            break;

        case '/coordinador/detalle-acudiente':
            require BASE_PATH . '/app/views/dashboard/coordinador/detalle-acudiente.php';
            break;

        case '/coordinador/editar-acudiente':
            require BASE_PATH . '/app/views/dashboard/coordinador/editar-acudiente.php';
            break;

          case '/coordinador/eliminar-acudiente':
            require BASE_PATH . '/app/controllers/acudiente.php';
            break;


        case '/coordinador/actualizar_acudiente':
            require BASE_PATH . '/app/controllers/acudiente.php';
            break;

            // --------------------------MODULO PROFESORES--------------------------

        case '/coordinador-panel-profesores':
            require BASE_PATH . '/app/views/dashboard/coordinador/panel-profesores.php';
            break;

        case '/coordinador/registrar-profesores':
            require BASE_PATH . '/app/views/dashboard/coordinador/addDocente.php';
            break;

        case '/coordinador-panel-eventos':
            require BASE_PATH . '/app/views/dashboard/coordinador/eventos.php';
            break;

        case '/coordinador/registrar-evento':
            require BASE_PATH . '/app/views/dashboard/coordinador/addEvento.php';
            break;

        case '/coordinador-panel-asignaturas':
            require BASE_PATH . '/app/views/dashboard/coordinador/asignaturas.php';
            break;

        case '/coordinador-panel-cursos':
            require BASE_PATH . '/app/views/dashboard/coordinador/cursos.php';
            break;

        // --------------------------------------DOCENTE---------------------------------------------
        case '/docente/dashboard':
            require BASE_PATH . '/app/views/dashboard/docente/docente.php';
            break;
        
        // -----------------------------------ESTUDIANTE------------------------------------------
        case '/estudiante/dashboard':
            require BASE_PATH . '/app/views/dashboard/estudiante/estudiante.php';
            break;

        // ---------------------------------------ACUDIENTE----------------------------------------
        case '/acudiente/dashboard':
            require BASE_PATH . '/app/views/dashboard/acudiente/acudiente.php';
            break;

        case '/super-admin/dashboard':
            require BASE_PATH . '/app/views/dashboard/superAdmin/superAdmin.php';
            break;
        // FIN RUTAS LOGIN
        default: 
            http_response_code(404);
            require BASE_PATH . '/app/views/auth/404.html';
            break;
    }
?>