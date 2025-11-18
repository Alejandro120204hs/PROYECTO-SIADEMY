<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>SIADEMY • Gestión de Pagos</title>
  <?php 
    include_once __DIR__ . '/../../layouts/header_coordinador.php'
  ?>
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/dashboard/css/styles-superAdmin.css">

</head>

<body>
  <div class="app" id="appGrid">
    <!-- LEFT SIDEBAR -->
    <?php 
        include_once __DIR__ . '/../../layouts/sidebar_superAdmin.php';
    ?>

    <!-- MAIN -->
    <main class="main">
      <div class="topbar">
        <div class="topbar-left">
          <button class="toggle-btn" id="toggleLeft" title="Mostrar/Ocultar menú lateral">
            <i class="ri-menu-2-line"></i>
          </button>
          <div class="title">Gestión de Pagos</div>
        </div>
        <div class="search">
          <i class="ri-search-2-line"></i>
          <input type="text" placeholder="Buscar escuela o referencia...">
        </div>
        <button class="toggle-btn" id="toggleRight" title="Mostrar/Ocultar panel derecho">
          <i class="ri-layout-right-2-line"></i>
        </button>
      </div>

      <!-- KPIs -->
      <div class="kpi-grid">
        <div class="kpi-card">
          <div class="kpi-label">Ingresos del Mes</div>
          <div class="kpi-value">$48,500</div>
          <div class="kpi-info success"><i class="ri-arrow-up-line"></i> +12% vs mes anterior</div>
        </div>
        <div class="kpi-card">
          <div class="kpi-label">Pagos Pendientes</div>
          <div class="kpi-value danger">7</div>
          <div class="kpi-info danger"><i class="ri-error-warning-line"></i> Requieren atención</div>
        </div>
        <div class="kpi-card">
          <div class="kpi-label">Pagos del Día</div>
          <div class="kpi-value">5</div>
          <div class="kpi-info success"><i class="ri-checkbox-circle-line"></i> $12,800 recibidos</div>
        </div>
        <div class="kpi-card">
          <div class="kpi-label">Escuelas al Día</div>
          <div class="kpi-value">18</div>
          <div class="kpi-info success"><i class="ri-shield-check-line"></i> 75% del total</div>
        </div>
      </div>

        <!-- Filtros -->
    <div class="filters-grid">
    <div class="filter-group">
        <label>Estado de Pago</label>
        <select class="form-select">
        <option>Todos los estados</option>
        <option>Pagado</option>
        <option>Pendiente</option>
        <option>Vencido</option>
        <option>Rechazado</option>
        </select>
    </div>
    <div class="filter-group">
        <label>Plan</label>
        <select class="form-select">
        <option>Todos los planes</option>
        <option>Premium</option>
        <option>Básico</option>
        </select>
    </div>
    <div class="filter-group">
        <label>Fecha</label>
        <select class="form-select">
        <option>Últimos 30 días</option>
        <option>Este mes</option>
        <option>Mes anterior</option>
        <option>Personalizado</option>
        </select>
    </div>
    <button class="filter-btn">
        <i class="ri-filter-line"></i>
        <span>Aplicar Filtros</span>
    </button>
    </div>
      

      <!-- Tabla de Pagos -->
      <div class="datatable-card">
        <div class="table-header">
          <div>
            <h3><i class="ri-money-dollar-circle-line"></i> Historial de Pagos</h3>
            <p class="card-subtitle">Seguimiento de transacciones y estados de pago</p>
          </div>
          <div class="records-selector">
            <label>Mostrar</label>
            <select class="form-select">
              <option>10 registros</option>
              <option>25 registros</option>
              <option>50 registros</option>
              <option>100 registros</option>
            </select>
          </div>
        </div>
        
        <div class="table-responsive">
          <table id="paymentsTable" class="table table-dark table-hover">
            <thead>
              <tr>
                <th>REFERENCIA</th>
                <th>ESCUELA</th>
                <th>PLAN</th>
                <th>FECHA PAGO</th>
                <th>VENCIMIENTO</th>
                <th>MONTO</th>
                <th>ESTADO</th>
                <th>ACCIÓN</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>
                  <strong>REF-001-2024</strong>
                  <div class="muted">Colegio San José</div>
                </td>
                <td>Colegio San José</td>
                <td><span class="badge bg-primary">Premium</span></td>
                <td>2024-10-15</td>
                <td>2024-11-15</td>
                <td>
                  <div class="amount">$2,500</div>
                  <div class="muted">Mensual</div>
                </td>
                <td><span class="badge bg-success">Pagado</span></td>
                <td>
                  <div class="action-buttons">
                    <button class="btn btn-sm btn-info" title="Ver detalles">
                      <i class="ri-eye-line"></i> Ver
                    </button>
                    <button class="btn btn-sm btn-warning" title="Descargar">
                      <i class="ri-download-line"></i>
                    </button>
                  </div>
                </td>
              </tr>
              <tr>
                <td>
                  <strong>REF-002-2024</strong>
                  <div class="muted">Instituto Técnico Nacional</div>
                </td>
                <td>Instituto Técnico Nacional</td>
                <td><span class="badge bg-primary">Premium</span></td>
                <td>-</td>
                <td>2024-09-20</td>
                <td>
                  <div class="amount">$3,800</div>
                  <div class="muted">Mensual</div>
                </td>
                <td><span class="badge bg-danger">Vencido</span></td>
                <td>
                  <div class="action-buttons">
                    <button class="btn btn-sm btn-info" title="Ver detalles">
                      <i class="ri-eye-line"></i> Ver
                    </button>
                    <button class="btn btn-sm btn-danger" title="Recordar">
                      <i class="ri-mail-send-line"></i>
                    </button>
                  </div>
                </td>
              </tr>
              <tr>
                <td>
                  <strong>REF-003-2024</strong>
                  <div class="muted">Liceo Moderno</div>
                </td>
                <td>Liceo Moderno</td>
                <td><span class="badge bg-secondary">Básico</span></td>
                <td>2024-10-28</td>
                <td>2024-11-28</td>
                <td>
                  <div class="amount">$1,800</div>
                  <div class="muted">Mensual</div>
                </td>
                <td><span class="badge bg-success">Pagado</span></td>
                <td>
                  <div class="action-buttons">
                    <button class="btn btn-sm btn-info" title="Ver detalles">
                      <i class="ri-eye-line"></i> Ver
                    </button>
                    <button class="btn btn-sm btn-warning" title="Descargar">
                      <i class="ri-download-line"></i>
                    </button>
                  </div>
                </td>
              </tr>
              <tr>
                <td>
                  <strong>REF-004-2024</strong>
                  <div class="muted">Colegio Los Andes</div>
                </td>
                <td>Colegio Los Andes</td>
                <td><span class="badge bg-primary">Premium</span></td>
                <td>-</td>
                <td>2024-10-05</td>
                <td>
                  <div class="amount">$2,200</div>
                  <div class="muted">Mensual</div>
                </td>
                <td><span class="badge bg-warning">Pendiente</span></td>
                <td>
                  <div class="action-buttons">
                    <button class="btn btn-sm btn-info" title="Ver detalles">
                      <i class="ri-eye-line"></i> Ver
                    </button>
                    <button class="btn btn-sm btn-danger" title="Recordar">
                      <i class="ri-mail-send-line"></i>
                    </button>
                  </div>
                </td>
              </tr>
              <tr>
                <td>
                  <strong>REF-005-2024</strong>
                  <div class="muted">Academia Santa María</div>
                </td>
                <td>Academia Santa María</td>
                <td><span class="badge bg-primary">Premium</span></td>
                <td>2024-10-20</td>
                <td>2024-11-20</td>
                <td>
                  <div class="amount">$2,800</div>
                  <div class="muted">Mensual</div>
                </td>
                <td><span class="badge bg-success">Pagado</span></td>
                <td>
                  <div class="action-buttons">
                    <button class="btn btn-sm btn-info" title="Ver detalles">
                      <i class="ri-eye-line"></i> Ver
                    </button>
                    <button class="btn btn-sm btn-warning" title="Descargar">
                      <i class="ri-download-line"></i>
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Footer de la tabla -->
        <div class="table-footer">
          <div class="table-info">
            Mostrando registros del 1 al 5 de un total de 24 registros
          </div>
          <nav>
            <ul class="pagination">
              <li class="page-item disabled">
                <a class="page-link" href="#">Anterior</a>
              </li>
              <li class="page-item active"><a class="page-link" href="#">1</a></li>
              <li class="page-item"><a class="page-link" href="#">2</a></li>
              <li class="page-item"><a class="page-link" href="#">3</a></li>
              <li class="page-item"><a class="page-link" href="#">4</a></li>
              <li class="page-item"><a class="page-link" href="#">5</a></li>
              <li class="page-item">
                <a class="page-link" href="#">Siguiente</a>
              </li>
            </ul>
          </nav>
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

      <div class="panel-title">Detalles de Pago</div>
      <p class="muted">Información completa de la transacción</p>

      <div class="payment-detail">
        <div class="payment-header">
          <div class="payment-icon">
            <i class="ri-bill-line"></i>
          </div>
          <div>
            <h4>REF-001-2024</h4>
            <div class="muted">Colegio San José</div>
          </div>
        </div>

        <div class="detail-grid">
          <div class="detail-item">
            <div class="detail-label">Estado</div>
            <div class="detail-value"><span class="badge bg-success">Pagado</span></div>
          </div>
          <div class="detail-item">
            <div class="detail-label">Fecha de Pago</div>
            <div class="detail-value">2024-10-15</div>
          </div>
          <div class="detail-item">
            <div class="detail-label">Vencimiento</div>
            <div class="detail-value">2024-11-15</div>
          </div>
          <div class="detail-item">
            <div class="detail-label">Plan</div>
            <div class="detail-value"><span class="badge bg-primary">Premium</span></div>
          </div>
          <div class="detail-item">
            <div class="detail-label">Monto</div>
            <div class="detail-value amount-large">$2,500</div>
          </div>
          <div class="detail-item">
            <div class="detail-label">Método</div>
            <div class="detail-value">Transferencia Bancaria</div>
          </div>
          <div class="detail-item">
            <div class="detail-label">Referencia</div>
            <div class="detail-value">TRF-789456123</div>
          </div>
        </div>

        <div class="payment-actions">
          <button class="btn btn-warning btn-sm full-width">
            <i class="ri-download-line"></i> Descargar Factura
          </button>
          <button class="btn btn-info btn-sm full-width">
            <i class="ri-eye-line"></i> Ver Comprobante
          </button>
        </div>
      </div>

      <!-- ALERTAS DE PAGOS -->
      <div class="panel-title" style="margin-top: 30px;">Alertas de Pagos</div>
      <p class="muted">Pagos que requieren atención</p>

      <div class="alert-item danger">
        <div class="alert-icon">
          <i class="ri-error-warning-line"></i>
        </div>
        <div>
          <strong>Pago Vencido</strong>
          <div class="muted">Instituto Técnico Nacional • $3,800</div>
        </div>
        <span class="time">5 días</span>
      </div>

      <div class="alert-item warning">
        <div class="alert-icon">
          <i class="ri-time-line"></i>
        </div>
        <div>
          <strong>Vence Pronto</strong>
          <div class="muted">Colegio Los Andes • $2,200</div>
        </div>
        <span class="time">2 días</span>
      </div>

      <div class="alert-item info">
        <div class="alert-icon">
          <i class="ri-information-line"></i>
        </div>
        <div>
          <strong>Pago Pendiente</strong>
          <div class="muted">Colegio Moderno • $1,500</div>
        </div>
        <span class="time">7 días</span>
      </div>

      <!-- RESUMEN MENSUAL -->
      <div class="summary-card">
        <div class="panel-title">Resumen Mensual</div>
        <div class="summary-item">
          <div class="summary-label">Total Recibido</div>
          <div class="summary-value success">$48,500</div>
        </div>
        <div class="summary-item">
          <div class="summary-label">Pendiente</div>
          <div class="summary-value warning">$12,300</div>
        </div>
        <div class="summary-item">
          <div class="summary-label">Escuelas al Día</div>
          <div class="summary-value">18/24</div>
        </div>
      </div>
    </aside>
  </div>

  <!-- Scripts -->
  <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
  <script src="<?= BASE_URL ?>/public/assets/dashboard/js/superAdmin/main-superAdmin-pagos.js"></script>
</body>
</html>