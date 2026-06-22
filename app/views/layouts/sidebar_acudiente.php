<?php
    $requestUri = $_SERVER['REQUEST_URI'] ?? '/';
    $basePath = parse_url(BASE_URL, PHP_URL_PATH) ?: '';
    $currentPath = parse_url($requestUri, PHP_URL_PATH) ?: '/';

    if ($basePath !== '' && $basePath !== '/' && strpos($currentPath, $basePath) === 0) {
        $currentPath = substr($currentPath, strlen($basePath));
    }

    if ($currentPath === '' || $currentPath === false) {
        $currentPath = '/';
    }

    if ($currentPath[0] !== '/') {
        $currentPath = '/' . $currentPath;
    }

    $currentPath = rtrim($currentPath, '/');
    if ($currentPath === '') {
        $currentPath = '/';
    }

    function navAcudienteIsActive($currentPath, $paths) {
        foreach ($paths as $path) {
            if ($currentPath === $path) {
                return true;
            }
            if ($path !== '/' && strpos($currentPath, $path . '/') === 0) {
                return true;
            }
        }
        return false;
    }
?>

<aside class="sidebar" id="leftSidebar">
     <a class="brand" href="<?= BASE_URL ?>/acudiente/dashboard">
                <img width="170px" src="<?= BASE_URL ?>/public/assets/extras/img/LOGO-NEGATIVO 1 (1).png" alt="">

      </a>
    <nav class="nav">
        <a class="<?= navAcudienteIsActive($currentPath, ['/acudiente/dashboard']) ? 'active' : '' ?>" href="<?= BASE_URL ?>/acudiente/dashboard">
            <i class="ri-home-5-line"></i> Panel
        </a>
        <a class="<?= navAcudienteIsActive($currentPath, ['/acudiente/calificaciones']) ? 'active' : '' ?>" href="<?= BASE_URL ?>/acudiente/calificaciones">
            <i class="ri-file-text-line"></i> Calificaciones
        </a>
        <a class="<?= navAcudienteIsActive($currentPath, ['/acudiente/boletines']) ? 'active' : '' ?>" href="<?= BASE_URL ?>/acudiente/boletines">
            <i class="ri-file-paper-2-line"></i> Boletines
        </a>
        <a class="<?= navAcudienteIsActive($currentPath, ['/acudiente/asistencia']) ? 'active' : '' ?>" href="<?= BASE_URL ?>/acudiente/asistencia">
            <i class="ri-calendar-check-line"></i> Asistencia
        </a>
        <a class="<?= navAcudienteIsActive($currentPath, ['/acudiente/horario']) ? 'active' : '' ?>" href="<?= BASE_URL ?>/acudiente/horario">
            <i class="ri-book-2-line"></i> Horario
        </a>
        <a class="<?= navAcudienteIsActive($currentPath, ['/acudiente/actividades']) ? 'active' : '' ?>" href="<?= BASE_URL ?>/acudiente/actividades">
            <i class="ri-task-line"></i> Actividades
        </a>
        <a class="<?= navAcudienteIsActive($currentPath, ['/acudiente/profesores']) ? 'active' : '' ?>" href="<?= BASE_URL ?>/acudiente/profesores">
            <i class="ri-user-3-line"></i> Profesores
        </a>
        <a class="<?= navAcudienteIsActive($currentPath, ['/acudiente/eventos']) ? 'active' : '' ?>" href="<?= BASE_URL ?>/acudiente/eventos">
            <i class="ri-calendar-event-line"></i> Eventos
        </a>
        
    </nav>
</aside>
