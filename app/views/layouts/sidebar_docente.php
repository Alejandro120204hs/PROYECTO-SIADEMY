<?php
  $requestUri = $_SERVER['REQUEST_URI'] ?? '/';
  $basePath = parse_url(BASE_URL, PHP_URL_PATH) ?: '';
  if ($basePath !== '' && $basePath !== '/' && strpos($requestUri, $basePath) === 0) {
    $currentPath = substr($requestUri, strlen($basePath));
  } else {
    $currentPath = $requestUri;
  }
  $currentPath = strtok($currentPath, '?');
  $currentPath = rtrim($currentPath, '/');
  if ($currentPath === '') {
    $currentPath = '/';
  }

  function navDocenteIsActive($currentPath, $paths) {
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
      <a class="brand" href="<?= BASE_URL ?>/docente/dashboard">
                <img width="170px" src="<?= BASE_URL ?>/public/assets/extras/img/LOGO-NEGATIVO 1 (1).png" alt="">

      </a>
      <nav class="nav">
        <a class="<?= navDocenteIsActive($currentPath, ['/docente/dashboard']) ? 'active' : '' ?>" href="<?= BASE_URL ?>/docente/dashboard"><i class="ri-home-5-line"></i> Panel</a>
        <a class="<?= navDocenteIsActive($currentPath, ['/docente-cursos', '/docente/detalle-curso', '/docente/actividades', '/docente/agregar-actividad', '/docente/ver-entregas']) ? 'active' : '' ?>" href="<?= BASE_URL ?>/docente-cursos"><i class="ri-team-line"></i> Cursos</a>
          
        <a class="<?= navDocenteIsActive($currentPath, ['/docente-eventos']) ? 'active' : '' ?>" href="<?= BASE_URL ?>/docente-eventos"><i class="ri-calendar-event-line"></i> Eventos</a>
        <a class="<?= navDocenteIsActive($currentPath, ['/docente/asistencia']) ? 'active' : '' ?>" href="<?= BASE_URL ?>/docente/asistencia"><i class="ri-team-line"></i> Gestion de Asistencia</a>

    
      </nav>
    </aside>