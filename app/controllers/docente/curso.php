<?php

    // IMPORTAMOS LAS DEPENDENCIAS NECESARIAS
       require_once __DIR__ . '/../../helpers/alert_helper.php';
       require_once __DIR__ . '/../../models/docente/curso.php';

    // CAPTURAMOS EN UNA VARIABLE EL METODO O SOLICITUD HECHA AL SERVIDOR
    $method = $_SERVER['REQUEST_METHOD'];

    switch($method){
        case 'GET':
            mostrarCursos();
            break;

         default;
            http_response_code(405);
            echo"Metodo no permitido";
            break;
    }

    function mostrarCursos(){
        // VERIFICAMOS SI LA SESIÓN YA ESTÁ INICIADA
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }


            // CAPTURAMOS EL ID DE LA INSTITUCIÓN DEL DOCENTE LOGUEADO
            $id_institucion = $_SESSION['user']['id_institucion'];
            $id_docente = $_SESSION['user']['id'] ;

            // INSTANCEAMOS LA CLASE
            $objetoCurso = new Curso_docente();

            // LISTAMOS LOS CURSOS DE LA INSTITUCION
            $curso = $objetoCurso -> listar($id_institucion, $id_docente);

            return $curso;
    }
    

?>