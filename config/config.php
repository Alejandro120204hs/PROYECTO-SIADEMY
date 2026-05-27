<?php
// ESTE ARCHIVO SE CREO PARA EVITAR MAYOR CONFIGURACION EN EL HOSTING

    // ZONA HORARIA: Colombia (UTC-5). Evita que date() retorne fecha UTC
    // y que actividades con fecha_entrega = hoy aparezcan como 'cerrada'.
    date_default_timezone_set('America/Bogota');

    // CONFIGURACION GLOBAL DEL PROYECTO

    // DETECTAR PROTOCOLO (HTTP/HTTPS), incluyendo proxy reverso
    $isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || (isset($_SERVER['SERVER_PORT']) && (int)$_SERVER['SERVER_PORT'] === 443)
        || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https');
    $protocol = $isHttps ? 'https://' : 'http://';

    // DETECTAR CARPETA BASE DINAMICAMENTE (funciona en / y en /siademy)
    $scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? ''));
    $baseFolder = rtrim($scriptDir, '/');
    if ($baseFolder === '/' || $baseFolder === '\\' || $baseFolder === '.') {
        $baseFolder = '';
    }

    // HOST ACTUAL
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';

    // URL DINAMICA (FUNCIONA EN LOCAL Y HOSTING)
    define('BASE_URL', $protocol . $host . $baseFolder);

    // RUTA DE LA BASE DEL PROYECTO (PARA REQUIRE O INCLUDE)
    define('BASE_PATH', dirname(__DIR__));

?>