<?php

    // IMPORTAMOS LAS DEPENDENCIAS NECESARIAS
    require_once __DIR__ . '/../helpers/alert_helper.php';
    require_once __DIR__ . '/../models/perfil.php';

    function mostrarPerfil($id){
        
        $objetoPerfil = new Perfil();
        $usuario = $objetoPerfil -> mostrarPerfilAdmin($id);
        return $usuario;
    }

?>