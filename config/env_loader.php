<?php

/**
 * Cargador de variables de entorno desde archivo .env
 *
 * Lee el archivo .env ubicado en la raíz del proyecto y registra cada
 * variable usando putenv() + $_ENV, respetando las ya definidas por el
 * servidor (p.ej. variables de Docker/hosting que ya vienen en $_ENV).
 *
 * Reglas del parser:
 *  - Líneas que comienzan con # son comentarios y se ignoran.
 *  - Líneas vacías se ignoran.
 *  - Formato esperado:  CLAVE=valor
 *  - Los valores pueden ir entre comillas simples o dobles (se eliminan).
 *  - Una variable ya definida en el entorno del servidor NO se sobreescribe
 *    (prioridad: entorno del servidor > .env > fallback hardcodeado).
 *
 * IMPORTANTE: NO incluyas este archivo en src/; está pensado para ser
 * requerido UNA SOLA VEZ desde index.php antes de cualquier otro require.
 */

(function () {
    // Buscar .env en dos ubicaciones:
    //  1. Un nivel ARRIBA de public_html  → producción (fuera del repo git)
    //  2. Raíz del proyecto (public_html) → desarrollo local
    $candidatos = [
        dirname(dirname(__DIR__)) . '/.env',   // /home/usuario/.env  (producción)
        dirname(__DIR__) . '/.env',             // /public_html/.env   (local)
    ];

    $rutaEnv = null;
    foreach ($candidatos as $ruta) {
        if (is_file($ruta)) {
            $rutaEnv = $ruta;
            break;
        }
    }

    if ($rutaEnv === null) {
        // Sin .env en ninguna ubicación: las variables vienen del entorno del servidor.
        return;
    }

    $lineas = file($rutaEnv, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    foreach ($lineas as $linea) {
        $linea = trim($linea);

        // Ignorar comentarios
        if ($linea === '' || $linea[0] === '#') {
            continue;
        }

        // Solo procesar líneas con '='
        if (!str_contains($linea, '=')) {
            continue;
        }

        [$nombre, $valor] = explode('=', $linea, 2);
        $nombre = trim($nombre);
        $valor  = trim($valor);

        // Eliminar comillas envolventes si existen
        if (strlen($valor) >= 2) {
            $primer = $valor[0];
            $ultimo = $valor[strlen($valor) - 1];
            if (($primer === '"' && $ultimo === '"') ||
                ($primer === "'" && $ultimo === "'")) {
                $valor = substr($valor, 1, -1);
            }
        }

        // No sobreescribir variables ya definidas por el entorno del servidor
        if (!array_key_exists($nombre, $_ENV) && getenv($nombre) === false) {
            putenv("$nombre=$valor");
            $_ENV[$nombre]    = $valor;
            $_SERVER[$nombre] = $valor;
        }
    }
})();
