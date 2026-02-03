<?php
// ESTE ARCHIVO SE CREO PARA EVITAR MAYOR CONFIGURACION EN EL HOSTING

    // CONFIGURACION GLOBAL DEL PROYECTO

    // DETECTAR PROTOCOLO(http o https)
    $protocol = isset($_SERVER['HTTPS']) ? 'https://' : 'http://';

    // NOMBRE DE LA CARPETA DEL PROYECTO EN LOCAL
    $baseFolder = '/siademy';

    // HOST ACTUAL
    $host = $_SERVER['HTTP_HOST'];

    // URL DINAMICA (FUNCIONA EN LOCAL Y HOSTING)
    define('BASE_URL', $protocol . $host . $baseFolder);

    // RUTA DE LA BASE DEL PROYECTO (PARA REQUIRE O INCLUDE)
    define('BASE_PATH', dirname(__DIR__));

?>