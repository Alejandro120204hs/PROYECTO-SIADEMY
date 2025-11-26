<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>SIADEMY • Panel de Administración del Sistema</title>
  <?php 
    include_once __DIR__ . '/../../layouts/header_coordinador.php';
  ?>
  <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/dashboard/css/styles-superAdmin.css">
</head>

<body>
  <div class="app" id="appGrid">
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
          <div class="title">Admin Sistema</div>
        </div>
        <div class="search">
          <i class="ri-search-2-line"></i>
          <input type="text" placeholder="Buscar escuela...">
        </div>
        <button class="toggle-btn" id="toggleRight" title="Mostrar/Ocultar panel derecho">
          <i class="ri-layout-right-2-line"></i>
        </button>
      </div>

      <!-- KPIs -->
      <div class="kpi-grid">
        <div class="kpi-card">
          <div class="kpi-label">Total Escuelas</div>
          <div class="kpi-value">24</div>
          <div class="kpi-info success"><i class="ri-arrow-up-line"></i> 3 nuevas este mes</div>
        </div>
        <div class="kpi-card">
          <div class="kpi-label">Escuelas Activas</div>
          <div class="kpi-value">20</div>
          <div class="kpi-info success"><i class="ri-checkbox-circle-line"></i> 83.3% del total</div>
        </div>
        <div class="kpi-card">
          <div class="kpi-label">Pagos Pendientes</div>
          <div class="kpi-value danger">7</div>
          <div class="kpi-info danger"><i class="ri-error-warning-line"></i> Requieren atención</div>
        </div>
        <div class="kpi-card">
          <div class="kpi-label">Ingresos del Mes</div>
          <div class="kpi-value">$48,500</div>
          <div class="kpi-info success"><i class="ri-arrow-up-line"></i> +12% vs mes anterior</div>
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
                <span class="stat-value success">+45.8%</span>
                <span class="stat-label">Crecimiento anual</span>
            </div>
            <div class="stat-item">
                <span class="stat-value">24</span>
                <span class="stat-label">Total instituciones</span>
            </div>
            <div class="stat-item">
                <span class="stat-value warning">+9</span>
                <span class="stat-label">Nuevas este año</span>
            </div>
        </div>
    </div>
</div>
      

      

    </main>

    <!-- RIGHT SIDEBAR -->
    <aside class="rightbar" id="rightSidebar">
      <div class="user">
        <button class="btn" title="Notificaciones"><i class="ri-notification-3-line"></i></button>
        <button class="btn" title="Configuración"><i class="ri-settings-3-line"></i></button>
        <div class="avatar" title="Super Admin">SA</div>
      </div>

      <div class="panel-title">Actividad Reciente</div>
      <p class="muted">Últimas acciones del sistema</p>

      <div class="msg">
        <div class="avatar">🔒</div>
        <div>
          <strong>Escuela Bloqueada</strong>
          <div class="muted">Colegio Los Andes • Por falta de pago</div>
        </div>
        <span class="time">1h</span>
      </div>

      <div class="msg">
        <div class="avatar">✅</div>
        <div>
          <strong>Nuevo Pago Recibido</strong>
          <div class="muted">Liceo Moderno • $1,800</div>
        </div>
        <span class="time">2h</span>
      </div>

      <div class="msg">
        <div class="avatar">🏫</div>
        <div>
          <strong>Nueva Escuela</strong>
          <div class="muted">Instituto del Pacífico • Yopal</div>
        </div>
        <span class="time">5h</span>
      </div>

      <!-- EVENTS SECTION -->
      <div class="events-section">
        <div class="panel-title">Alertas del Sistema</div>
        <p class="muted">Notificaciones importantes</p>

        <div class="event-item">
          <div class="event-date danger-bg">
            <span class="day">7</span>
            <span class="month">Pendientes</span>
          </div>
          <div class="event-content">
            <h4>Pagos Vencidos</h4>
            <p>Hay 7 escuelas con pagos pendientes que requieren seguimiento</p>
            <div class="event-time">⚠️ Acción requerida</div>
          </div>
        </div>

        <div class="event-item">
          <div class="event-date warning-bg">
            <span class="day">4</span>
            <span class="month">Bloqueadas</span>
          </div>
          <div class="event-content">
            <h4>Escuelas Bloqueadas</h4>
            <p>Instituciones temporalmente suspendidas por falta de pago</p>
            <div class="event-time">🔒 Restablecer acceso tras pago</div>
          </div>
        </div>

        <div class="event-item">
          <div class="event-date success-bg">
            <span class="day">20</span>
            <span class="month">Activas</span>
          </div>
          <div class="event-content">
            <h4>Escuelas Operando</h4>
            <p>Instituciones con acceso completo al sistema</p>
            <div class="event-time">✅ Estado: Normal</div>
          </div>
        </div>

        <a href="#" class="btn-primary">Ver reporte completo</a>
      </div>
    </aside>
  </div>

  <!-- Scripts -->
  <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
  <script src="<?= BASE_URL ?>/public/assets/dashboard/js/superAdmin/main-superAdmin.js"></script>
</body>
</html>