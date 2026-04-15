<?php

  require_once BASE_PATH . '/app/controllers/perfil.php';
  require_once BASE_PATH . '/app/models/superAdmin/institucion.php';
    
    // LLAMAMOS EL ID QUE VIENE ATRAVEZ DEL METODO GET
    $id = $_SESSION['user']['id'];
    // LLAMAMOS LA FUNCION ESPECIFICA DEL CONTROLADOR
    $usuario = mostrarPerfil($id);

    $objInstitucion = new Institucion();
    $metricasDashboard = $objInstitucion->obtenerMetricasDashboard();
    $totalesDashboard = $metricasDashboard['totales'] ?? ['total' => 0, 'activas' => 0, 'inactivas' => 0];
    $chartDashboard = $metricasDashboard['chart'] ?? [];
    $porcentajeActivas = ($totalesDashboard['total'] ?? 0) > 0
      ? round((($totalesDashboard['activas'] ?? 0) / $totalesDashboard['total']) * 100, 1)
      : 0;
    $crecimientoDashboard = (float) ($chartDashboard['crecimiento'] ?? 0);
    $nuevasEsteAnio = (int) ($chartDashboard['nuevasEsteAnio'] ?? 0);
    $chartJson = json_encode($chartDashboard, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    if ($chartJson === false) {
      $chartJson = '{}';
    }

    $superAdminCssVersion = @filemtime(BASE_PATH . '/public/assets/dashboard/css/styles-superAdmin.css') ?: time();
    $superAdminJsVersion = @filemtime(BASE_PATH . '/public/assets/dashboard/js/superAdmin/main-superAdmin.js') ?: time();

?>

<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>SIADEMY • Panel de Administración del Sistema</title>
  <?php 
    include_once __DIR__ . '/../../layouts/header_coordinador.php';
  ?>
  <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/dashboard/css/styles-superAdmin.css?v=<?= $superAdminCssVersion ?>">
</head>

<body>
  <div class="app hide-right" id="appGrid">
    <!-- LEFT SIDEBAR -->
    <?php 
      include_once __DIR__ . '/../../layouts/sidebar_superAdmin.php'
    ?>

    <!-- MAIN -->
    <main class="main">
      <div class="topbar">
        <div class="topbar-left">
          <button class="toggle-btn" id="toggleLeft" title="Mostrar/Ocultar menú lateral">
            <i class="ri-menu-2-line"></i>
          </button>
          <div class="title">Super Admin Sistema</div>
        </div>
        <div class="search">
          <i class="ri-search-2-line"></i>
          <input type="text" placeholder="Buscar escuela...">
        </div>
         <?php
          include_once BASE_PATH . '/app/views/layouts/boton_perfil_solo.php'
        ?>
      </div>

      <!-- KPIs -->
      <div class="kpi-grid">
        <div class="kpi-card">
          <div class="kpi-label">Total Escuelas</div>
          <div class="kpi-value"><?= (int) ($totalesDashboard['total'] ?? 0) ?></div>
          <div class="kpi-info success"><i class="ri-building-2-line"></i> Multi institucional</div>
        </div>
        <div class="kpi-card">
          <div class="kpi-label">Escuelas Activas</div>
          <div class="kpi-value"><?= (int) ($totalesDashboard['activas'] ?? 0) ?></div>
          <div class="kpi-info success"><i class="ri-checkbox-circle-line"></i> <?= $porcentajeActivas ?>% del total</div>
        </div>
        <div class="kpi-card">
          <div class="kpi-label">Pagos Pendientes</div>
          <div class="kpi-value danger"><?= (int) ($totalesDashboard['inactivas'] ?? 0) ?></div>
          <div class="kpi-info danger"><i class="ri-error-warning-line"></i> Instituciones inactivas</div>
        </div>
        <div class="kpi-card">
          <div class="kpi-label">Crecimiento Anual</div>
          <div class="kpi-value"><?= $crecimientoDashboard >= 0 ? '+' : '' ?><?= number_format($crecimientoDashboard, 1) ?>%</div>
          <div class="kpi-info success"><i class="ri-arrow-up-line"></i> Vs año anterior</div>
        </div>
      </div>

      <!-- Sección de Gráficos - Agregar después de los KPIs -->
<div class="charts-section">
    <div class="chart-card">
        <div class="chart-header">
            <div class="chart-title-group">
                <h3>Comparativa de Instituciones Registradas</h3>
                <p class="card-subtitle">Crecimiento anual - Impacto del aplicativo</p>
            </div>
            <div class="chart-controls">
                <select id="chartType" class="form-select chart-select">
                    <option value="bar">Gráfico de Barras</option>
                    <option value="line">Gráfico de Líneas</option>
                </select>
            </div>
        </div>
        <div class="chart-container">
            <canvas id="institutionsChart"></canvas>
        </div>
        <div class="chart-stats">
            <div class="stat-item">
              <span class="stat-value success"><?= $crecimientoDashboard >= 0 ? '+' : '' ?><?= number_format($crecimientoDashboard, 1) ?>%</span>
                <span class="stat-label">Crecimiento anual</span>
            </div>
            <div class="stat-item">
              <span class="stat-value"><?= (int) ($totalesDashboard['total'] ?? 0) ?></span>
                <span class="stat-label">Total instituciones</span>
            </div>
            <div class="stat-item">
              <span class="stat-value warning">+<?= $nuevasEsteAnio ?></span>
                <span class="stat-label">Nuevas este año</span>
            </div>
        </div>
    </div>
</div>
      

      

    </main>

    <!-- RIGHT SIDEBAR -->
    
  </div>

  <!-- Scripts -->
  <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
  <script>
    window.superAdminChartData = <?= $chartJson ?>;
  </script>
  <script src="<?= BASE_URL ?>/public/assets/dashboard/js/superAdmin/main-superAdmin.js?v=<?= $superAdminJsVersion ?>"></script>
</body>
</html>