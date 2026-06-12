<?php

require_once BASE_PATH . '/app/helpers/session_helper.php';
require_once BASE_PATH . '/app/models/notificaciones.php';
require_once BASE_PATH . '/app/controllers/perfil.php';

redirectIfNoSession('/login');

$idUsuario     = (int)$_SESSION['user']['id'];
$idInstitucion = (int)($_SESSION['user']['id_institucion'] ?? 0);

$notifModel      = new Notificacion();
$notificaciones  = $notifModel->listarParaUsuario($idUsuario, $idInstitucion, 100);
$totalNoLeidas   = $notifModel->contarNoLeidas($idUsuario, $idInstitucion);

$usuario = mostrarPerfil($idUsuario);

require BASE_PATH . '/app/views/notificaciones.php';
