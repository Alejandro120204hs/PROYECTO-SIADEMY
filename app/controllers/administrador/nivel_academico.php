<?php

     // IMPORTAMOS LAS DEPENDENCIAS NECESARIAS
    require_once __DIR__ . '/../../helpers/alert_helper.php';
    require_once __DIR__ . '/../../models/administradores/nivel_academico.php';

    // CAPTURAMOS EN UNA VARIABLE EL METODO O SOLICITUD HECHA AL SERVIDOR
    $method = $_SERVER['REQUEST_METHOD'];

    switch($method){
        case 'GET':

            mostrarNivelAcademico();
            break;

        default;
            http_response_code(405);
            echo"Metodo no permitido";
            break;
    }

    function mostrarNivelAcademico(){
        $objetoNivel = new Nivel();
        $resultado = $objetoNivel -> listar();

        return $resultado;
    }

?>