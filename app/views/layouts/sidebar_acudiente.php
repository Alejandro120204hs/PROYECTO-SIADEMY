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
        <span class="logo"><i class="ri-shield-star-line"></i></span>
        <span>Siademy</span>
    </a>
    <nav class="nav">
        <a class="<?= navAcudienteIsActive($currentPath, ['/acudiente/dashboard']) ? 'active' : '' ?>" href="<?= BASE_URL ?>/acudiente/dashboard">
            <i class="ri-home-5-line"></i> Panel
        </a>
        <a class="<?= navAcudienteIsActive($currentPath, ['/acudiente/calificaciones']) ? 'active' : '' ?>" href="<?= BASE_URL ?>/acudiente/calificaciones">
            <i class="ri-file-text-line"></i> Calificaciones
        </a>
    </nav>
</aside>
