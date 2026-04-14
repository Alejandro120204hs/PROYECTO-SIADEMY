<?php

    // IMPORTAMOS LAS DEPENDENCIAS NECESARIAS
    require_once __DIR__ . '/../helpers/alert_helper.php';
    require_once __DIR__ . '/../models/perfil.php';

    
    function mostrarPerfil($id){
        $objetoPerfil = new Perfil();
        $rol = $_SESSION['user']['rol'] ?? '';

        switch ($rol) {
            case 'Administrador':
                $usuario = $objetoPerfil->mostrarPerfilAdministrador($id);
                break;

            case 'superAdmin':
                $usuario = $objetoPerfil->mostrarPerfilSuperAdmin($id);
                break;

            default:
                $usuario = $objetoPerfil->mostrarPerfilGenerico($id);
                break;
        }

        if (!is_array($usuario) || empty($usuario)) {
            return [
                'nombres' => $_SESSION['user']['rol'] ?? 'Usuario',
                'apellidos' => '',
                'foto' => 'default.png',
                'correo' => $_SESSION['user']['correo'] ?? '',
                'rol' => $rol ?: 'Usuario',
                'nombre_institucion' => '',
                'direccion_institucion' => ''
            ];
        }

        return $usuario;
    }

?>