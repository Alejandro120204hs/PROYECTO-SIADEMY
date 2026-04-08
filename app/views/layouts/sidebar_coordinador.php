<?php
  $requestUri = $_SERVER['REQUEST_URI'] ?? '/';
  $currentPath = str_replace('/siademy', '', $requestUri);
  $currentPath = strtok($currentPath, '?');
  $currentPath = rtrim($currentPath, '/');
  if ($currentPath === '') {
    $currentPath = '/';
  }

  function navIsActive($currentPath, $paths) {
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
      <a class="brand" href="#">
        <img width="170px" src="<?= BASE_URL ?>/public/assets/extras/img/LOGO-NEGATIVO 1 (1).png" alt="">

      </a>
      <nav class="nav">
        <a class="<?= navIsActive($currentPath, ['/administrador/dashboard']) ? 'active' : '' ?>" href="<?= BASE_URL ?>/administrador/dashboard"><i class="ri-home-5-line"></i> Panel</a>
        <a class="<?= navIsActive($currentPath, ['/administrador-panel-acudientes', '/administrador/registrar-acudiente', '/administrador/editar-acudiente', '/administrador/detalle-acudiente']) ? 'active' : '' ?>" href="<?= BASE_URL ?>/administrador-panel-acudientes"><i class="ri-team-line"></i> Acudientes</a>
        <a class="<?= navIsActive($currentPath, ['/administrador-panel-estudiantes', '/administrador/registrar-estudiante', '/administrador/editar-estudiante', '/administrador/detalle-estudiante']) ? 'active' : '' ?>" href="<?= BASE_URL ?>/administrador-panel-estudiantes"><i class="ri-team-line"></i> Estudiantes</a>
        <a class="<?= navIsActive($currentPath, ['/administrador-panel-profesores', '/administrador/registrar-profesores', '/administrador/editar-docente', '/administrador/detalle-profesor']) ? 'active' : '' ?>" href="<?= BASE_URL ?>/administrador-panel-profesores"><i class="ri-user-3-line"></i> Profesores</a>
        <a class="<?= navIsActive($currentPath, ['/administrador-panel-eventos', '/administrador-eventos', '/administrador/registrar-evento', '/administrador/editar-evento']) ? 'active' : '' ?>" href="<?= BASE_URL ?>/administrador-panel-eventos"><i class="ri-calendar-event-line"></i> Eventos</a>
        
        <div class="spacer"></div>
        <div class="section">Cursos / Asignaturas</div>
         <a class="<?= navIsActive($currentPath, ['/administrador-panel-cursos', '/administrador/registrar-curso', '/administrador/editar-curso', '/administrador/detalle-curso']) ? 'active' : '' ?>" href="<?= BASE_URL ?>/administrador-panel-cursos"><i class="ri-book-2-line"></i> Cursos</a>
        <a class="<?= navIsActive($currentPath, ['/administrador-panel-asignaturas', '/administrador/registrar-asignatura', '/administrador/editar-asignatura']) ? 'active' : '' ?>" href="<?= BASE_URL ?>/administrador-panel-asignaturas"><i class="ri-booklet-line"></i> Asignaturas</a>
        <a class="<?= navIsActive($currentPath, ['/administrador-panel-matriculas', '/administrador/registrar-matricula', '/administrador/editar-matricula']) ? 'active' : '' ?>" href="<?= BASE_URL ?>/administrador-panel-matriculas"><i class="ri-graduation-cap-line"></i> Matrículas</a>
        <a class="<?= navIsActive($currentPath, ['/administrador-periodo', '/administrador/editar-periodo']) ? 'active' : '' ?>" href="<?= BASE_URL ?>/administrador-periodo"><i class="ri-repeat-line"></i> Periodos</a>

      </nav>
    </aside>