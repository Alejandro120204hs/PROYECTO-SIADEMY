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

      <!-- Tabla 1: Estado de Escuelas -->
      <div class="datatable-card">
        <h3><i class="ri-shield-check-line"></i> Gestión de Estado de Escuelas</h3>
        <p class="card-subtitle">Control de acceso al sistema: Activar o bloquear instituciones</p>
        <div class="table-responsive">
          <table id="statusTable" class="table table-dark table-hover">
            <thead>
              <tr>
                <th>ID</th>
                <th>Escuela</th>
                <th>Ciudad</th>
                <th>Estudiantes</th>
                <th>Estado</th>
                <th>Acciones</th>
              </tr>
            </thead>
            <tbody>
              <tr data-id="001">
                <td>001</td>
                <td><strong>Colegio San José</strong></td>
                <td>Bogotá</td>
                <td>450</td>
                <td><span class="badge bg-success">Activo</span></td>
                <td>
                  <button class="btn btn-sm btn-danger toggle-status" data-status="active">
                    <i class="ri-lock-line"></i> Bloquear
                  </button>
                </td>
              </tr>
              <tr data-id="002">
                <td>002</td>
                <td><strong>Instituto Técnico Nacional</strong></td>
                <td>Medellín</td>
                <td>780</td>
                <td><span class="badge bg-success">Activo</span></td>
                <td>
                  <button class="btn btn-sm btn-danger toggle-status" data-status="active">
                    <i class="ri-lock-line"></i> Bloquear
                  </button>
                </td>
              </tr>
              <tr data-id="003">
                <td>003</td>
                <td><strong>Liceo Moderno</strong></td>
                <td>Cali</td>
                <td>320</td>
                <td><span class="badge bg-success">Activo</span></td>
                <td>
                  <button class="btn btn-sm btn-danger toggle-status" data-status="active">
                    <i class="ri-lock-line"></i> Bloquear
                  </button>
                </td>
              </tr>
              <tr data-id="004">
                <td>004</td>
                <td><strong>Colegio Los Andes</strong></td>
                <td>Barranquilla</td>
                <td>590</td>
                <td><span class="badge bg-danger">Bloqueado</span></td>
                <td>
                  <button class="btn btn-sm btn-success toggle-status" data-status="blocked">
                    <i class="ri-lock-unlock-line"></i> Activar
                  </button>
                </td>
              </tr>
              <tr data-id="005">
                <td>005</td>
                <td><strong>Academia Santa María</strong></td>
                <td>Cartagena</td>
                <td>410</td>
                <td><span class="badge bg-success">Activo</span></td>
                <td>
                  <button class="btn btn-sm btn-danger toggle-status" data-status="active">
                    <i class="ri-lock-line"></i> Bloquear
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Tabla 2: Gestión de Pagos -->
      <div class="datatable-card">
        <h3><i class="ri-money-dollar-circle-line"></i> Gestión de Pagos</h3>
        <p class="card-subtitle">Seguimiento de pagos y transacciones de las instituciones</p>
        <div class="table-responsive">
          <table id="paymentsTable" class="table table-dark table-hover">
            <thead>
              <tr>
                <th>ID</th>
                <th>Escuela</th>
                <th>Ciudad</th>
                <th>Plan</th>
                <th>Estado de Pago</th>
                <th>Último Pago</th>
                <th>Próximo Vencimiento</th>
                <th>Monto Mensual</th>
                <th>Acciones</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>001</td>
                <td><strong>Colegio San José</strong></td>
                <td>Bogotá</td>
                <td><span class="badge bg-primary">Premium</span></td>
                <td><span class="badge bg-success">Pagado</span></td>
                <td>2024-10-15</td>
                <td>2024-11-15</td>
                <td>$2,500</td>
                <td>
                  <button class="btn btn-sm btn-info">
                    <i class="ri-eye-line"></i> Ver
                  </button>
                </td>
              </tr>
              <tr>
                <td>002</td>
                <td><strong>Instituto Técnico Nacional</strong></td>
                <td>Medellín</td>
                <td><span class="badge bg-primary">Premium</span></td>
                <td><span class="badge bg-danger">Pendiente</span></td>
                <td>2024-08-20</td>
                <td>2024-09-20</td>
                <td>$3,800</td>
                <td>
                  <button class="btn btn-sm btn-warning">
                    <i class="ri-mail-send-line"></i> Recordar
                  </button>
                </td>
              </tr>
              <tr>
                <td>003</td>
                <td><strong>Liceo Moderno</strong></td>
                <td>Cali</td>
                <td><span class="badge bg-secondary">Básico</span></td>
                <td><span class="badge bg-success">Pagado</span></td>
                <td>2024-10-28</td>
                <td>2024-11-28</td>
                <td>$1,800</td>
                <td>
                  <button class="btn btn-sm btn-info">
                    <i class="ri-eye-line"></i> Ver
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
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