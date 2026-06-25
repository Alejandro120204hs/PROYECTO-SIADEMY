<?php
// Redirigir si se enviaron filtros vacíos (limpia ?estado=&desde=&hasta= de la URL)
if (array_key_exists('estado', $_GET) && $_GET['estado'] === '' && ($_GET['desde'] ?? '') === '' && ($_GET['hasta'] ?? '') === '') {
    header('Location: ' . BASE_URL . '/superAdmin-panel-pagos');
    exit();
}

require_once BASE_PATH . '/app/controllers/perfil.php';
require_once BASE_PATH . '/app/models/superAdmin/reportes.php';

$usuario = mostrarPerfil($_SESSION['user']['id']);
$model   = new ReportesSuperAdmin();

$estadoFiltro = $_GET['estado'] ?? '';
$desdeFiltro  = $_GET['desde']  ?? '';
$hastaFiltro  = $_GET['hasta']  ?? '';

$pagos     = $model->listarPagos($estadoFiltro, $desdeFiltro, $hastaFiltro);
$kpiMes    = $model->ingresosDelMes();
$kpiHoy    = $model->pagosAprobadosHoy();
$pendientes = $model->pagosPendientes();
$instInfo  = $model->instituciones();

$estadoConfig = [
    'APPROVED' => ['label' => 'Aprobado', 'class' => 'bg-success'],
    'PENDING'  => ['label' => 'Pendiente', 'class' => 'bg-warning text-dark'],
    'DECLINED' => ['label' => 'Rechazado', 'class' => 'bg-danger'],
    'VOIDED'   => ['label' => 'Anulado',   'class' => 'bg-secondary'],
    'ERROR'    => ['label' => 'Error',     'class' => 'bg-danger'],
];
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>SIADEMY • Gestión de Pagos</title>
  <?php include_once __DIR__ . '/../../layouts/header_coordinador.php' ?>
  <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/dashboard/css/styles-superAdmin.css">
  <style>
    .kpi-grid { display: grid; grid-template-columns: repeat(4,1fr); gap: 16px; margin-bottom: 24px; }
    @media(max-width:900px){ .kpi-grid { grid-template-columns: repeat(2,1fr); } }
    .kpi-card { background:#11193a; border:1px solid rgba(255,255,255,.08); border-radius:14px; padding:20px 22px; }
    .kpi-label { font-size:12px; color:#64748b; text-transform:uppercase; letter-spacing:.06em; margin-bottom:6px; }
    .kpi-value { font-size:26px; font-weight:700; color:#e6e9f4; }
    .kpi-value.danger { color:#ef4444; }
    .kpi-info  { font-size:12px; margin-top:6px; color:#64748b; display:flex; align-items:center; gap:4px; }
    .kpi-info.success { color:#10b981; }
    .kpi-info.danger  { color:#ef4444; }

    .filters-bar { display:flex; flex-wrap:wrap; gap:12px; margin-bottom:20px; align-items:flex-end; }
    .filters-bar .fg { display:flex; flex-direction:column; gap:4px; }
    .filters-bar label { font-size:11px; color:#64748b; text-transform:uppercase; letter-spacing:.05em; }
    .filters-bar select,
    .filters-bar input[type=date] {
      background:#11193a; border:1px solid rgba(255,255,255,.1);
      border-radius:8px; padding:8px 12px; color:#e6e9f4;
      font-family:'Poppins',sans-serif; font-size:13px; outline:none;
    }
    .btn-filter { padding:9px 18px; background:linear-gradient(135deg,#667eea,#764ba2); border:none; border-radius:8px; color:#fff; font-size:13px; font-weight:600; cursor:pointer; display:flex; align-items:center; gap:6px; }
    .btn-reset  { padding:9px 14px; background:rgba(255,255,255,.06); border:1px solid rgba(255,255,255,.1); border-radius:8px; color:#94a3b8; font-size:13px; cursor:pointer; text-decoration:none; }

    .datatable-card { background:#11193a; border:1px solid rgba(255,255,255,.08); border-radius:16px; overflow:hidden; min-width:0; }
    .table-header   { display:flex; justify-content:space-between; align-items:center; padding:20px 24px; border-bottom:1px solid rgba(255,255,255,.06); }
    .table-header h3 { font-size:15px; font-weight:600; color:#e6e9f4; margin:0; display:flex; align-items:center; gap:8px; }
    .table-header h3 i { color:#818cf8; }
    .table-header p  { font-size:12px; color:#64748b; margin:2px 0 0; }

    .table-wrap { width:100%; overflow-x:auto; -webkit-overflow-scrolling:touch; }
    table { width:100%; border-collapse:collapse; table-layout:fixed; min-width:580px; }
    table colgroup .col-ref      { width:20%; }
    table colgroup .col-inst     { width:11%; }
    table colgroup .col-concepto { width:21%; }
    table colgroup .col-monto    { width:16%; }
    table colgroup .col-estado   { width:16%; }
    table colgroup .col-fecha    { width:16%; }
    thead th {
      font-size:11px; font-weight:600; color:#64748b; text-transform:uppercase;
      letter-spacing:.06em; padding:10px 12px; border-bottom:1px solid rgba(255,255,255,.06);
      text-align:left; white-space:nowrap; overflow:hidden;
    }
    tbody td {
      padding:10px 12px; font-size:13px; color:#b8c2df;
      border-bottom:1px solid rgba(255,255,255,.04); vertical-align:middle;
      overflow:hidden; text-overflow:ellipsis; white-space:nowrap;
    }
    tbody tr:last-child td { border-bottom:none; }
    tbody tr:hover td { background:rgba(255,255,255,.02); }

    .badge-estado { display:inline-block; padding:3px 9px; border-radius:999px; font-size:11px; font-weight:600; white-space:nowrap; }
    .estado-APPROVED { background:rgba(16,185,129,.15); color:#10b981; border:1px solid rgba(16,185,129,.3); }
    .estado-PENDING  { background:rgba(245,158,11,.15);  color:#f59e0b; border:1px solid rgba(245,158,11,.3); }
    .estado-DECLINED { background:rgba(239,68,68,.15);   color:#ef4444; border:1px solid rgba(239,68,68,.3); }
    .estado-VOIDED   { background:rgba(107,114,128,.15); color:#9ca3af; border:1px solid rgba(107,114,128,.3); }
    .estado-ERROR    { background:rgba(239,68,68,.15);   color:#ef4444; border:1px solid rgba(239,68,68,.3); }

    .ref-code { font-family:monospace; font-size:11px; color:#818cf8; }
    .empty-row td { text-align:center; padding:48px; color:#64748b; white-space:normal; }

    .btn-export { display:inline-flex; align-items:center; gap:6px; padding:8px 14px; border-radius:8px; font-size:12px; font-weight:600; cursor:pointer; border:none; text-decoration:none; }
    .btn-csv   { background:rgba(16,185,129,.15); color:#10b981; border:1px solid rgba(16,185,129,.3); }
    .btn-print { background:rgba(129,140,248,.15); color:#818cf8; border:1px solid rgba(129,140,248,.3); }
    .export-row { display:flex; gap:8px; padding:14px 24px; border-top:1px solid rgba(255,255,255,.06); flex-wrap:wrap; }
  </style>
</head>
<body>
<div class="app hide-right" id="appGrid">
  <?php include_once __DIR__ . '/../../layouts/sidebar_superAdmin.php' ?>

  <main class="main">
    <div class="topbar">
      <div class="topbar-left">
        <button class="toggle-btn" id="toggleLeft"><i class="ri-menu-2-line"></i></button>
        <div class="title">Gestión de Pagos</div>
      </div>
      <?php include_once BASE_PATH . '/app/views/layouts/boton_perfil_solo.php' ?>
    </div>

    <!-- KPIs -->
    <div class="kpi-grid">
      <div class="kpi-card">
        <div class="kpi-label">Ingresos del Mes</div>
        <div class="kpi-value">$ <?= number_format($kpiMes / 100, 0, ',', '.') ?></div>
        <div class="kpi-info success"><i class="ri-checkbox-circle-line"></i> Pagos aprobados</div>
      </div>
      <div class="kpi-card">
        <div class="kpi-label">Pagos Pendientes</div>
        <div class="kpi-value <?= $pendientes > 0 ? 'danger' : '' ?>"><?= $pendientes ?></div>
        <div class="kpi-info <?= $pendientes > 0 ? 'danger' : '' ?>">
          <i class="ri-time-line"></i> <?= $pendientes > 0 ? 'Requieren atención' : 'Sin pendientes' ?>
        </div>
      </div>
      <div class="kpi-card">
        <div class="kpi-label">Pagos Hoy</div>
        <div class="kpi-value"><?= $kpiHoy['cantidad'] ?></div>
        <div class="kpi-info success">
          <i class="ri-money-dollar-circle-line"></i>
          $ <?= number_format($kpiHoy['monto'] / 100, 0, ',', '.') ?> recibidos
        </div>
      </div>
      <div class="kpi-card">
        <div class="kpi-label">Instituciones Activas</div>
        <div class="kpi-value"><?= $instInfo['activas'] ?></div>
        <div class="kpi-info success">
          <i class="ri-school-line"></i> de <?= $instInfo['total'] ?> registradas
        </div>
      </div>
    </div>

    <!-- Filtros -->
    <form method="GET" action="">
      <div class="filters-bar">
        <div class="fg">
          <label>Estado</label>
          <select name="estado">
            <option value="">Todos</option>
            <?php foreach (['APPROVED' => 'Aprobado', 'PENDING' => 'Pendiente', 'DECLINED' => 'Rechazado', 'VOIDED' => 'Anulado'] as $val => $lbl): ?>
            <option value="<?= $val ?>" <?= $estadoFiltro === $val ? 'selected' : '' ?>><?= $lbl ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="fg">
          <label>Desde</label>
          <input type="date" name="desde" value="<?= htmlspecialchars($desdeFiltro) ?>">
        </div>
        <div class="fg">
          <label>Hasta</label>
          <input type="date" name="hasta" value="<?= htmlspecialchars($hastaFiltro) ?>">
        </div>
        <button type="submit" class="btn-filter"><i class="ri-filter-line"></i> Filtrar</button>
        <a href="<?= BASE_URL ?>/superAdmin-panel-pagos" class="btn-reset">Limpiar</a>
      </div>
    </form>

    <!-- Tabla -->
    <div class="datatable-card">
      <div class="table-header">
        <div>
          <h3><i class="ri-money-dollar-circle-line"></i> Historial de Pagos</h3>
          <p><?= count($pagos) ?> registros encontrados</p>
        </div>
      </div>

      <div class="table-wrap">
        <table id="tablaPagos">
          <colgroup>
            <col class="col-ref">
            <col class="col-inst">
            <col class="col-concepto">
            <col class="col-monto">
            <col class="col-estado">
            <col class="col-fecha">
          </colgroup>
          <thead>
            <tr>
              <th>Referencia</th>
              <th>Institución</th>
              <th>Concepto</th>
              <th>Monto</th>
              <th>Estado</th>
              <th>Fecha</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($pagos)): ?>
            <tr class="empty-row"><td colspan="6"><i class="ri-inbox-line" style="font-size:32px;display:block;margin-bottom:8px;"></i>Sin registros</td></tr>
            <?php else: foreach ($pagos as $p):
              $cfg = $estadoConfig[$p['estado']] ?? ['label' => $p['estado'], 'class' => 'bg-secondary'];
            ?>
            <tr>
              <td title="<?= htmlspecialchars($p['referencia']) ?>">
                <span class="ref-code"><?= htmlspecialchars($p['referencia']) ?></span>
              </td>
              <td><?= htmlspecialchars($p['nombre_institucion']) ?></td>
              <td title="<?= htmlspecialchars($p['concepto']) ?>"><?= htmlspecialchars($p['concepto']) ?></td>
              <td><strong>$ <?= number_format($p['monto_cents'] / 100, 0, ',', '.') ?></strong></td>
              <td><span class="badge-estado estado-<?= $p['estado'] ?>"><?= $cfg['label'] ?></span></td>
              <td><?= date('d/m/Y', strtotime($p['created_at'])) ?></td>
            </tr>
            <?php endforeach; endif; ?>
          </tbody>
        </table>
      </div>

      <div class="export-row">
        <a href="<?= BASE_URL ?>/superAdmin/exportar-pagos-csv?estado=<?= urlencode($estadoFiltro) ?>&desde=<?= urlencode($desdeFiltro) ?>&hasta=<?= urlencode($hastaFiltro) ?>"
           class="btn-export btn-csv">
          <i class="ri-file-excel-line"></i> Exportar CSV
        </a>
        <button onclick="window.print()" class="btn-export btn-print">
          <i class="ri-printer-line"></i> Imprimir
        </button>
      </div>
    </div>

  </main>
</div>

<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= BASE_URL ?>/public/assets/dashboard/js/superAdmin/main-superAdmin.js"></script>
</body>
</html>
