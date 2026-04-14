<?php
  $requestUriSidebar = strtok($_SERVER['REQUEST_URI'] ?? '', '?');
  $basePathSidebar = parse_url(BASE_URL, PHP_URL_PATH) ?: '';

  if ($basePathSidebar !== '' && $basePathSidebar !== '/' && strpos($requestUriSidebar, $basePathSidebar) === 0) {
    $rutaActualSidebar = substr($requestUriSidebar, strlen($basePathSidebar));
  } else {
    $rutaActualSidebar = $requestUriSidebar;
  }

  $rutaActualSidebar = '/' . ltrim((string) $rutaActualSidebar, '/');

  $isActiveSidebar = function (array $rutas) use ($rutaActualSidebar) {
    foreach ($rutas as $ruta) {
      if ($rutaActualSidebar === $ruta) {
        return true;
      }
    }
    return false;
  };
?>

<aside class="sidebar" id="leftSidebar">
      <a class="brand" href="<?= BASE_URL ?>/superAdmin-dashboard">
        <img width="170px" src="<?= BASE_URL ?>/public/assets/extras/img/LOGO-NEGATIVO 1 (1).png" alt="">
      </a>
      <nav class="nav">
        <a class="<?= $isActiveSidebar(['/superAdmin-dashboard']) ? 'active' : '' ?>" href="<?= BASE_URL ?>/superAdmin-dashboard"><i class="ri-dashboard-line"></i> Panel</a>
        <a class="<?= $isActiveSidebar([
            '/superAdmin-panel-institucion',
            '/superAdmin-panel-instituciones',
            '/superAdmin-agregar-instituciones',
            '/superAdmin-editar-institucion'
          ]) ? 'active' : '' ?>" href="<?= BASE_URL ?>/superAdmin-panel-institucion"><i class="ri-school-line"></i> Instituciones</a>
        <a class="<?= $isActiveSidebar([
            '/superAdmin-panel-administradores',
            '/superAdmin-agregar-administrador',
            '/superAdmin-editar-administrador'
          ]) ? 'active' : '' ?>" href="<?= BASE_URL ?>/superAdmin-panel-administradores"><i class="ri-user-settings-line"></i> Administradores</a>
        <a class="<?= $isActiveSidebar(['/superAdmin-panel-pagos']) ? 'active' : '' ?>" href="<?= BASE_URL ?>/superAdmin-panel-pagos"><i class="ri-bill-line"></i> Pagos</a>
        <a href="#"><i class="ri-bar-chart-box-line"></i> Reportes</a>
        <div class="spacer"></div>
        <div class="section">Configuración</div>
        <a href="#"><i class="ri-settings-3-line"></i> Ajustes</a>
        <a href="#"><i class="ri-shield-check-line"></i> Seguridad</a>
      </nav>
    </aside>