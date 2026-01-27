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

            case '/recuperar-clave':
                require BASE_PATH . '/app/views/auth/ressetpassword.php';
                break;

            case '/generar-clave':
                require BASE_PATH . '/app/controllers/recuperarClave.php';
                break;

            

            // -------------------------------PERFIL-----------------------------------------------
            case '/dashboard-perfil':
                require BASE_PATH . '/app/views/dashboard/usuario/perfil.php';
                break;
            
            case '/actualizar-clave':
                require BASE_PATH . '/app/controllers/perfil.php';
                break;


              // -------------------------GENERRAR PDF-----------------------------------------
                case '/superAdmin-reporte':
                require BASE_PATH . '/app/controllers/reportesPdfController.php';
                    mostrarReportes();
                break;

                case '/administrador-reporte':
                require BASE_PATH . '/app/controllers/reportesPdfController.php';
                    mostrarReportes();
                break;

            // --------------------------------ROL: SUPER ADMIN--------------------------------------------------------------
            case '/superAdmin-dashboard':
                require BASE_PATH . '/app/views/dashboard/superAdmin/superAdmin.php';
                break;

                // --------------------------SUPER ADMIN(MODULO ADMINISTRADOR)---------------------
            case '/superAdmin-registrar-administrador':
                require BASE_PATH . '/app/controllers/superAdmin/administradores.php';
                break;

             case '/superAdmin-panel-administradores':
                require BASE_PATH . '/app/views/dashboard/superAdmin/administradores.php';
                break;

            case '/superAdmin-agregar-administrador':
                require BASE_PATH . '/app/views/dashboard/superAdmin/addAdministrador.php';
                break;

            case '/superAdmin-eliminar-administrador':
                require BASE_PATH . '/app/controllers/superAdmin/administradores.php';
                break;

            case '/superAdmin-editar-administrador':
                require BASE_PATH . '/app/views/dashboard/superAdmin/editarAdministrador.php';
                break;

            case '/superAdmin-actualizar-administrador':
                require BASE_PATH . '/app/controllers/superAdmin/administradores.php';
                break;


            // -------------------------------SUPER ADMIN(MODULO INSTITUCIONES)-----------------------------

            case '/superAdmin-panel-institucion':
                require BASE_PATH . '/app/views/dashboard/superAdmin/instituciones.php';
                break;

            case '/superAdmin-agregar-instituciones':
                require BASE_PATH . '/app/views/dashboard/superAdmin/addInstitucion.php';
                break;

            case '/superAdmin-registrar-institucion':
                require BASE_PATH . '/app/controllers/superAdmin/instituciones.php';
                break;

            case '/superAdmin-panel-instituciones':
                require BASE_PATH . '/app/views/dashboard/superAdmin/instituciones.php';
                break;

            case '/superAdmin-eliminar-institucion':
                require BASE_PATH . '/app/controllers/superAdmin/instituciones.php';
                break;

             case '/superAdmin-editar-institucion':
                require BASE_PATH . '/app/views/dashboard/superAdmin/editarInstitucion.php';
                break;

            case '/superAdmin-actualizar-institucion':
                require BASE_PATH . '/app/controllers/superAdmin/instituciones.php';
                break;

            // --------------------------------SUPER ADMIN(MODULO PAGOS)---------------------------
            case '/superAdmin-panel-pagos':
                require BASE_PATH . '/app/views/dashboard/superAdmin/pagos.php';
                break;

        

            // -----------------------------ROL: ADMINISTRADOR--------------------------------------------------------------

            case '/administrador/dashboard':
                require BASE_PATH . '/app/views/dashboard/administrador/admin.php';
                break;
            // ------------------------------ADMINISTRADOR(MODULO ESTUDIANTES)---------------------
            case '/administrador-panel-estudiantes':
                require BASE_PATH . '/app/views/dashboard/administrador/panel-estudiantes.php';
                break;

            
            case '/administrador/registrar-estudiante':
                require BASE_PATH . '/app/views/dashboard/administrador/addStudent.php';
                break;

            case '/administrador/guardar_estudiante':
                require BASE_PATH . '/app/controllers/administrador/estudiante_controller.php';
                break;

            case '/administrador/detalle-estudiante':
                require BASE_PATH . '/app/views/dashboard/administrador/detalle-estudiante.php';
                break;

            case '/administrador/eliminar-estudiante':
                require BASE_PATH . '/app/controllers/administrador/estudiante_controller.php';
                break;

            case '/administrador/editar-estudiante':
                require BASE_PATH . '/app/views/dashboard/administrador/editarEstudiante.php';
                break;

            case '/administrador/actualizar_estudiante':
                require BASE_PATH . '/app/controllers/administrador/estudiante_controller.php';
                break;

            


                // -------------------------ADMINISTRADOR(MODULO ACUDIENTE)--------------------------

            case '/administrador-panel-acudientes':
                require BASE_PATH . '/app/views/dashboard/administrador/panel-acudientes.php';
                break;
        
            case '/administrador/registrar-acudiente':
                require BASE_PATH . '/app/views/dashboard/administrador/addAcudiente.php';
                break;

            case '/administrador/guardar_acudiente':
                require BASE_PATH . '/app/controllers/administrador/acudiente.php';
                break;

            case '/administrador/detalle-acudiente':
                require BASE_PATH . '/app/views/dashboard/administrador/detalle-acudiente.php';
                break;

            case '/administrador/editar-acudiente':
                require BASE_PATH . '/app/views/dashboard/administrador/editar-acudiente.php';
                break;

            case '/administrador/eliminar-acudiente':
                require BASE_PATH . '/app/controllers/administrador/acudiente.php';
                break;


            case '/administrador/actualizar_acudiente':
                require BASE_PATH . '/app/controllers/administrador/acudiente.php';
                break;

                // --------------------------ADMINISTRADOR(MODULO PROFESORES)--------------------------

            case '/administrador-panel-profesores':
                require BASE_PATH . '/app/views/dashboard/administrador/panel-profesores.php';
                break;

            case '/administrador/registrar-profesores':
                require BASE_PATH . '/app/views/dashboard/administrador/addDocente.php';
                break;

            case '/administrador/guardar_docente':
                require BASE_PATH . '/app/controllers/administrador/docente.php';
                break;    
            
            case '/administrador/editar-docente':
                require BASE_PATH . '/app/views/dashboard/administrador/editar-docente.php';
                break;  
                
            case '/administrador/actualizar-docente':
                require BASE_PATH . '/app/controllers/administrador/docente.php';
                break;    
             
            case '/administrador/eliminar-docente':
                require BASE_PATH . '/app/controllers/administrador/docente.php';
                break;    

            // -----------------------------ADMINISTRADOR(MODULO EVENTOS)-------------------------

            case '/administrador-panel-eventos':
                require BASE_PATH . '/app/views/dashboard/administrador/eventos.php';
                break;

            case '/administrador/registrar-evento':
                require BASE_PATH . '/app/views/dashboard/administrador/addEvento.php';
                break;

            case 'administrador/guardar-evento':
                require BASE_PATH . '/app/controllers/administrador/eventos.php';
                break;

            case '/administrador-panel-asignaturas':
                require BASE_PATH . '/app/views/dashboard/administrador/asignaturas.php';
                break;

            


            // -----------------------------ADMINISTRADOR(MODULO ASIGNATURAS)-------------------------

            case '/administrador/registrar-asignatura':
                require BASE_PATH . '/app/views/dashboard/administrador/addAsignatura.php';
                break;       
                

            case '/administrador/guardar_asignatura':
                require BASE_PATH . '/app/controllers/administrador/asignatura.php';
                break;

            case '/administrador/editar-asignatura':
                require BASE_PATH . '/app/views/dashboard/administrador/editarAsignatura.php';
                break;

            case '/administrador/actualizar-asignatura':
                require BASE_PATH . '/app/controllers/administrador/asignatura.php';
                break;

            case '/administrador/eliminar-asignatura':
                require BASE_PATH . '/app/controllers/administrador/asignatura.php';
                break;

            // -------------------------------ADMINISTRADOR(ASIGNAR DOCENTES A ASIGNATURAS)-------------------------
            case '/administrador/asignar-docentes':
                require BASE_PATH . '/app/controllers/administrador/docente_asignatura.php';
                break;

            // -------------------------------ADMINISTRADOR(MODULO CURSOS)-------------------------
            case '/administrador-panel-cursos':
                require BASE_PATH . '/app/views/dashboard/administrador/cursos.php';
                break;
            case '/administrador/registrar-curso':
                require BASE_PATH . '/app/views/dashboard/administrador/addCurso.php';
                break;

            case '/administrador/guardar-curso':
                require BASE_PATH . '/app/controllers/administrador/curso.php';
                break;

            case '/administrador/eliminar-curso':
                require BASE_PATH . '/app/controllers/administrador/curso.php';
                break;

            case '/administrador/editar-curso':
                require BASE_PATH . '/app/views/dashboard/administrador/editarCurso.php';
                break;

            case '/administrador/actualizar-curso':
                require BASE_PATH . '/app/controllers/administrador/curso.php';
                break;

            case '/administrador/detalle-curso':
                require BASE_PATH . '/app/views/dashboard/administrador/detalle-curso.php';
                break;

            // -------------------------------ADMINISTRADOR(MODULO MATRÍCULAS)-------------------------
            case '/administrador-panel-matriculas':
                require BASE_PATH . '/app/views/dashboard/administrador/matriculas.php';
                break;

            case '/administrador/registrar-matricula':
                require BASE_PATH . '/app/views/dashboard/administrador/addMatricula.php';
                break;

            case '/administrador/guardar-matricula':
                require BASE_PATH . '/app/controllers/administrador/matricula.php';
                break;

            case '/administrador/editar-matricula':
                require BASE_PATH . '/app/views/dashboard/administrador/editarMatricula.php';
                break;

            case '/administrador/actualizar-matricula':
                require BASE_PATH . '/app/controllers/administrador/matricula.php';
                break;

            case '/administrador/eliminar-matricula':
                require BASE_PATH . '/app/controllers/administrador/matricula.php';
                break;

                
                

            // --------------------------------------ROL: DOCENTE---------------------------------------------
            case '/docente/dashboard':
                require BASE_PATH . '/app/views/dashboard/docente/docente.php';
                break;

            case '/docente-cursos':
                require BASE_PATH . '/app/views/dashboard/docente/cursos.php';
                break;

            case '/docente-eventos':
                require BASE_PATH . '/app/views/dashboard/docente/eventos.php';
                break;
            
            // -----------------------------------ROL: ESTUDIANTE------------------------------------------
            case '/estudiante/dashboard':
                require BASE_PATH . '/app/views/dashboard/estudiante/estudiante.php';
                break;

             case '/estudiante-panel-materias':
                require BASE_PATH . '/app/views/dashboard/estudiante/materias.php';
                break;

            case '/estudiante-panel-actividades':
                require BASE_PATH . '/app/views/dashboard/estudiante/actividades.php';
                break;

            case '/estudiante-panel-calificaciones':
                require BASE_PATH . '/app/views/dashboard/estudiante/calificaciones.php';
                break;

            case '/estudiante-panel-profesores':
                require BASE_PATH . '/app/views/dashboard/estudiante/misProfesores.php';
                break;

            // ---------------------------------------ROL:  ACUDIENTE----------------------------------------
            case '/acudiente/dashboard':
                require BASE_PATH . '/app/views/dashboard/acudiente/acudiente.php';
                break;

            case '/super-admin/dashboard':
                require BASE_PATH . '/app/views/dashboard/superAdmin/superAdmin.php';
                break;

            // -----------------------------------ROL: SECRETARÍA------------------------------------------
            case '/secretaria-academica/dashboard':
                require BASE_PATH . '/app/views/dashboard/secretariaAcademica/secretariaAcademica.php';
                break;


            // FIN RUTAS LOGIN
            default: 
                http_response_code(404);
                require BASE_PATH . '/app/views/auth/404.html';
                break;
        }
    ?>