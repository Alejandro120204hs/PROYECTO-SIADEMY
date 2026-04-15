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

    function obtenerEstadisticasDocenteDashboard(){
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $id_institucion = $_SESSION['user']['id_institucion'] ?? 0;
        $id_docente = $_SESSION['user']['id'] ?? 0;

        if ((int)$id_institucion <= 0 || (int)$id_docente <= 0) {
            return [
                'total_estudiantes' => 0,
                'total_acudientes' => 0,
                'total_cursos' => 0,
                'total_eventos' => 0,
            ];
        }

        $objetoCurso = new Curso_docente();
        return $objetoCurso->obtenerEstadisticasDashboard($id_institucion, $id_docente);
    }

    function listarEstudiantesBajoRendimientoDocente($limite = 20){
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $id_institucion = $_SESSION['user']['id_institucion'] ?? 0;
        $id_docente = $_SESSION['user']['id'] ?? 0;

        if ((int)$id_institucion <= 0 || (int)$id_docente <= 0) {
            return [];
        }

        $objetoCurso = new Curso_docente();
        return $objetoCurso->listarEstudiantesBajoRendimiento($id_institucion, $id_docente, (int)$limite);
    }

    function obtenerEventosCalendarioDocente(){
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $id_institucion = $_SESSION['user']['id_institucion'] ?? 0;
        $id_docente = $_SESSION['user']['id'] ?? 0;

        if ((int)$id_institucion <= 0 || (int)$id_docente <= 0) {
            return [];
        }

        $objetoCurso = new Curso_docente();
        return $objetoCurso->obtenerEventosCalendario((int)$id_institucion, (int)$id_docente);
    }
    

?>