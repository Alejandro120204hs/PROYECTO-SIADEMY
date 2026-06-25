<?php
require_once BASE_PATH . '/app/controllers/perfil.php';
require_once BASE_PATH . '/app/models/superAdmin/reportes.php';

$usuario = mostrarPerfil($_SESSION['user']['id']);
$model   = new ReportesSuperAdmin();

$kpiMes         = $model->ingresosDelMes();
$kpiMesAnt      = $model->ingresosDelMesAnterior();
$kpiAnio        = $model->ingresosDelAnio();
$pendientes     = $model->pagosPendientes();
$hoy            = $model->pagosAprobadosHoy();
$instInfo       = $model->instituciones();
$instNuevasMes  = $model->institucionesNuevasEsteMes();
$topInst        = $model->topInstituciones();
$ingresosMes    = $model->ingresosPorMes();
$instMes        = $model->institucionesPorMes();
$distribEst     = $model->distribucionEstados();

// Calcular crecimiento mensual
$crecimiento = 0;
if ($kpiMesAnt > 0) {
    $crecimiento = round((($kpiMes - $kpiMesAnt) / $kpiMesAnt) * 100, 1);
}

// Preparar datos para Chart.js
$chartIngresoLabels = json_encode(array_column($ingresosMes, 'etiqueta'));
$chartIngresoData   = json_encode(array_map(fn($r) => round($r['total'] / 100), $ingresosMes));

$chartInstLabels    = json_encode(array_column($instMes, 'etiqueta'));
$chartInstData      = json_encode(array_column($instMes, 'total'));

// Distribución estados
$estadoMap = ['APPROVED' => 'Aprobado', 'PENDING' => 'Pendiente', 'DECLINED' => 'Rechazado', 'VOIDED' => 'Anulado', 'ERROR' => 'Error'];
$distLabels = []; $distData = []; $distColors = [];
$colorMap = ['APPROVED' => '#10b981', 'PENDING' => '#f59e0b', 'DECLINED' => '#ef4444', 'VOIDED' => '#6b7280', 'ERROR' => '#dc2626'];
foreach ($distribEst as $d) {
    $distLabels[] = $estadoMap[$d['estado']] ?? $d['estado'];
    $distData[]   = (int)$d['total'];
    $distColors[] = $colorMap[$d['estado']] ?? '#64748b';
}
$chartDistLabels = json_encode($distLabels);
$chartDistData   = json_encode($distData);
$chartDistColors = json_encode($distColors);

// Top instituciones para barra horizontal
$topLabels = json_encode(array_column($topInst, 'nombre'));
$topData   = json_encode(array_map(fn($r) => round($r['total'] / 100), $topInst));
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>SIADEMY • Reportes</title>
  <?php include_once __DIR__ . '/../../layouts/header_coordinador.php' ?>
  <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/dashboard/css/styles-superAdmin.css">
  <style>
    /* KPI grid */
    .kpi-grid { display: grid; grid-template-columns: repeat(4,1fr); gap: 16px; margin-bottom: 28px; }
    @media(max-width:1100px){ .kpi-grid { grid-template-columns: repeat(2,1fr); } }
    @media(max-width:600px) { .kpi-grid { grid-template-columns: 1fr; } }

    .kpi-card {
      background: #11193a;
      border: 1px solid rgba(255,255,255,.08);
      border-radius: 16px;
      padding: 22px 24px;
      position: relative;
      overflow: hidden;
      transition: transform .2s;
    }
    .kpi-card:hover { transform: translateY(-2px); }
    .kpi-card::before {
      content:''; position:absolute; top:0; left:0; right:0; height:3px;
      background: var(--kpi-accent, linear-gradient(90deg,#667eea,#764ba2));
    }
    .kpi-card.green::before  { background: linear-gradient(90deg,#10b981,#059669); }
    .kpi-card.amber::before  { background: linear-gradient(90deg,#f59e0b,#d97706); }
    .kpi-card.red::before    { background: linear-gradient(90deg,#ef4444,#dc2626); }
    .kpi-card.blue::before   { background: linear-gradient(90deg,#818cf8,#6366f1); }

    .kpi-icon { font-size: 24px; margin-bottom: 12px; }
    .kpi-icon.green  { color: #10b981; }
    .kpi-icon.amber  { color: #f59e0b; }
    .kpi-icon.red    { color: #ef4444; }
    .kpi-icon.blue   { color: #818cf8; }

    .kpi-label { font-size: 11px; color: #64748b; text-transform: uppercase; letter-spacing: .07em; margin-bottom: 6px; }
    .kpi-value { font-size: 28px; font-weight: 700; color: #e6e9f4; line-height: 1.1; }
    .kpi-value small { font-size: 14px; font-weight: 400; color: #64748b; }
    .kpi-sub   { font-size: 12px; color: #64748b; margin-top: 6px; display: flex; align-items: center; gap: 4px; }
    .kpi-sub.up   { color: #10b981; }
    .kpi-sub.down { color: #ef4444; }

    /* Sección de gráficas */
    .charts-grid { display: grid; grid-template-columns: 2fr 1fr; gap: 20px; margin-bottom: 24px; }
    @media(max-width:1000px){ .charts-grid { grid-template-columns: 1fr; } }

    .charts-row { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 24px; }
    @media(max-width:900px){ .charts-row { grid-template-columns: 1fr; } }

    .chart-card {
      background: #11193a;
      border: 1px solid rgba(255,255,255,.08);
      border-radius: 16px;
      padding: 22px 24px;
    }
    .chart-card h4 {
      font-size: 14px; font-weight: 600; color: #e6e9f4;
      margin: 0 0 18px; display: flex; align-items: center; gap: 8px;
    }
    .chart-card h4 i { color: #818cf8; }
    .chart-wrap { position: relative; height: 240px; }
    .chart-wrap-sm { position: relative; height: 200px; }

    /* Top instituciones */
    .top-list { list-style: none; margin: 0; padding: 0; }
    .top-item {
      display: flex; align-items: center; gap: 12px;
      padding: 10px 0; border-bottom: 1px solid rgba(255,255,255,.05);
    }
    .top-item:last-child { border-bottom: none; }
    .top-rank {
      width: 26px; height: 26px; border-radius: 50%;
      background: rgba(129,140,248,.15);
      display: flex; align-items: center; justify-content: center;
      font-size: 11px; font-weight: 700; color: #818cf8;
      flex-shrink: 0;
    }
    .top-rank.gold   { background: rgba(251,191,36,.15); color: #fbbf24; }
    .top-rank.silver { background: rgba(156,163,175,.15); color: #9ca3af; }
    .top-rank.bronze { background: rgba(180,83,9,.15); color: #b45309; }
    .top-name  { flex: 1; font-size: 13px; color: #b8c2df; }
    .top-total { font-size: 13px; font-weight: 600; color: #e6e9f4; white-space: nowrap; }
    .top-bar-bg { height: 4px; border-radius: 2px; background: rgba(255,255,255,.06); margin-top: 4px; }
    .top-bar    { height: 4px; border-radius: 2px; background: linear-gradient(90deg,#667eea,#764ba2); }

    /* Indicadores estratégicos */
    .indicadores-grid { display: grid; grid-template-columns: repeat(3,1fr); gap: 16px; margin-bottom: 24px; }
    @media(max-width:800px){ .indicadores-grid { grid-template-columns: 1fr 1fr; } }
    .indicador-card {
      background: #11193a; border: 1px solid rgba(255,255,255,.08);
      border-radius: 12px; padding: 18px 20px;
    }
    .indicador-card .label { font-size: 11px; color: #64748b; text-transform: uppercase; letter-spacing: .06em; margin-bottom: 8px; }
    .indicador-card .value { font-size: 22px; font-weight: 700; color: #e6e9f4; }
    .indicador-card .desc  { font-size: 11px; color: #64748b; margin-top: 4px; }

    /* Botones exportar */
    .export-bar { display: flex; gap: 10px; margin-bottom: 24px; flex-wrap: wrap; }
    .btn-export {
      display: inline-flex; align-items: center; gap: 6px;
      padding: 9px 16px; border-radius: 8px; font-size: 12px; font-weight: 600;
      cursor: pointer; border: none; text-decoration: none; transition: opacity .2s;
    }
    .btn-export:hover { opacity: .8; }
    .btn-green  { background: rgba(16,185,129,.15); color: #10b981; border: 1px solid rgba(16,185,129,.3); }
    .btn-purple { background: rgba(129,140,248,.15); color: #818cf8; border: 1px solid rgba(129,140,248,.3); }

    .section-title {
      font-size: 13px; font-weight: 600; color: #64748b;
      text-transform: uppercase; letter-spacing: .1em;
      margin: 0 0 14px; padding-bottom: 10px;
      border-bottom: 1px solid rgba(255,255,255,.06);
    }

    @media print {
      .sidebar, .topbar .toggle-btn, .export-bar { display: none !important; }
      .app { grid-template-columns: 1fr !important; }
    }
  </style>
</head>
<body>
<div class="app hide-right" id="appGrid">
  <?php include_once __DIR__ . '/../../layouts/sidebar_superAdmin.php' ?>

  <main class="main">
    <div class="topbar">
      <div class="topbar-left">
        <button class="toggle-btn" id="toggleLeft"><i class="ri-menu-2-line"></i></button>
        <div class="title">Reportes & Analítica</div>
      </div>
      <?php include_once BASE_PATH . '/app/views/layouts/boton_perfil_solo.php' ?>
    </div>

    <!-- Botones exportar -->
    <div class="export-bar">
      <a href="<?= BASE_URL ?>/superAdmin/exportar-reporte-csv" class="btn-export btn-green">
        <i class="ri-file-excel-line"></i> Exportar CSV
      </a>
      <button onclick="window.print()" class="btn-export btn-purple">
        <i class="ri-printer-line"></i> Imprimir
      </button>
    </div>

    <!-- KPIs principales -->
    <p class="section-title">Indicadores Clave</p>
    <div class="kpi-grid">
      <div class="kpi-card green">
        <div class="kpi-icon green"><i class="ri-money-dollar-circle-line"></i></div>
        <div class="kpi-label">Ingresos del Mes</div>
        <div class="kpi-value">$ <?= number_format($kpiMes / 100, 0, ',', '.') ?> <small>COP</small></div>
        <div class="kpi-sub <?= $crecimiento >= 0 ? 'up' : 'down' ?>">
          <i class="ri-arrow-<?= $crecimiento >= 0 ? 'up' : 'down' ?>-line"></i>
          <?= $crecimiento >= 0 ? '+' : '' ?><?= $crecimiento ?>% vs mes anterior
        </div>
      </div>

      <div class="kpi-card blue">
        <div class="kpi-icon blue"><i class="ri-calendar-line"></i></div>
        <div class="kpi-label">Ingresos del Año</div>
        <div class="kpi-value">$ <?= number_format($kpiAnio / 100, 0, ',', '.') ?> <small>COP</small></div>
        <div class="kpi-sub"><i class="ri-checkbox-circle-line"></i> Solo pagos aprobados</div>
      </div>

      <div class="kpi-card <?= $pendientes > 0 ? 'amber' : 'green' ?>">
        <div class="kpi-icon <?= $pendientes > 0 ? 'amber' : 'green' ?>"><i class="ri-time-line"></i></div>
        <div class="kpi-label">Pagos Pendientes</div>
        <div class="kpi-value"><?= $pendientes ?></div>
        <div class="kpi-sub <?= $pendientes > 0 ? '' : 'up' ?>">
          <i class="ri-<?= $pendientes > 0 ? 'error-warning' : 'checkbox-circle' ?>-line"></i>
          <?= $pendientes > 0 ? 'Requieren seguimiento' : 'Todo al día' ?>
        </div>
      </div>

      <div class="kpi-card blue">
        <div class="kpi-icon blue"><i class="ri-school-line"></i></div>
        <div class="kpi-label">Instituciones Activas</div>
        <div class="kpi-value"><?= $instInfo['activas'] ?> <small>/ <?= $instInfo['total'] ?></small></div>
        <div class="kpi-sub">
          <i class="ri-checkbox-circle-line"></i> de <?= $instInfo['total'] ?> registradas
        </div>
      </div>
    </div>

    <!-- Gráfica ingresos + distribución estados -->
    <p class="section-title">Análisis Financiero</p>
    <div class="charts-grid">
      <div class="chart-card">
        <h4><i class="ri-bar-chart-2-line"></i> Ingresos por Mes (últimos 12 meses)</h4>
        <div class="chart-wrap">
          <canvas id="chartIngresos"></canvas>
        </div>
      </div>
      <div class="chart-card">
        <h4><i class="ri-pie-chart-line"></i> Estado de Pagos</h4>
        <div class="chart-wrap">
          <canvas id="chartEstados"></canvas>
        </div>
      </div>
    </div>

    <!-- Gráfica instituciones + top instituciones -->
    <p class="section-title">Crecimiento e Instituciones</p>
    <div class="charts-row">
      <div class="chart-card">
        <h4><i class="ri-line-chart-line"></i> Instituciones con Pagos por Mes</h4>
        <div class="chart-wrap-sm">
          <canvas id="chartInstituciones"></canvas>
        </div>
      </div>

      <div class="chart-card">
        <h4><i class="ri-trophy-line"></i> Top Instituciones (ingresos)</h4>
        <?php if (empty($topInst)): ?>
        <div style="text-align:center; padding:40px; color:#64748b; font-size:13px;">
          <i class="ri-inbox-line" style="font-size:32px; display:block; margin-bottom:8px;"></i>
          Sin datos de pagos aprobados
        </div>
        <?php else:
          $maxTotal = max(array_column($topInst, 'total')) ?: 1;
          $rankClasses = ['gold','silver','bronze','',''];
        ?>
        <ul class="top-list">
          <?php foreach ($topInst as $i => $inst): ?>
          <li class="top-item">
            <div class="top-rank <?= $rankClasses[$i] ?? '' ?>"><?= $i + 1 ?></div>
            <div style="flex:1; min-width:0;">
              <div class="top-name"><?= htmlspecialchars($inst['nombre']) ?></div>
              <div class="top-bar-bg"><div class="top-bar" style="width:<?= round(($inst['total'] / $maxTotal) * 100) ?>%"></div></div>
            </div>
            <div class="top-total">$ <?= number_format($inst['total'] / 100, 0, ',', '.') ?></div>
          </li>
          <?php endforeach; ?>
        </ul>
        <?php endif; ?>
      </div>
    </div>

    <!-- Indicadores estratégicos -->
    <p class="section-title">Indicadores Estratégicos</p>
    <div class="indicadores-grid">
      <div class="indicador-card">
        <div class="label">Ingresos hoy</div>
        <div class="value">$ <?= number_format($hoy['monto'] / 100, 0, ',', '.') ?></div>
        <div class="desc"><?= $hoy['cantidad'] ?> pago(s) aprobado(s)</div>
      </div>
      <div class="indicador-card">
        <div class="label">Crecimiento mensual</div>
        <div class="value" style="color:<?= $crecimiento >= 0 ? '#10b981' : '#ef4444' ?>">
          <?= $crecimiento >= 0 ? '+' : '' ?><?= $crecimiento ?>%
        </div>
        <div class="desc">vs mes anterior</div>
      </div>
      <div class="indicador-card">
        <div class="label">Tasa de éxito</div>
        <?php
          $totalPagos = array_sum(array_column($distribEst, 'total')) ?: 1;
          $aprobados  = array_sum(array_map(fn($d) => $d['estado'] === 'APPROVED' ? $d['total'] : 0, $distribEst));
          $tasa = round(($aprobados / $totalPagos) * 100, 1);
        ?>
        <div class="value"><?= $tasa ?>%</div>
        <div class="desc"><?= $aprobados ?> de <?= $totalPagos ?> transacciones</div>
      </div>
      <div class="indicador-card">
        <div class="label">Total instituciones</div>
        <div class="value"><?= $instInfo['total'] ?></div>
        <div class="desc"><?= $instInfo['activas'] ?> activas</div>
      </div>
      <div class="indicador-card">
        <div class="label">Ingresos año</div>
        <div class="value">$ <?= number_format($kpiAnio / 100, 0, ',', '.') ?></div>
        <div class="desc">Solo pagos aprobados</div>
      </div>
      <div class="indicador-card">
        <div class="label">Pagos pendientes</div>
        <div class="value" style="color:<?= $pendientes > 0 ? '#f59e0b' : '#10b981' ?>"><?= $pendientes ?></div>
        <div class="desc"><?= $pendientes > 0 ? 'Requieren acción' : 'Sin pendientes' ?></div>
      </div>
    </div>

  </main>
</div>

<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script src="<?= BASE_URL ?>/public/assets/dashboard/js/superAdmin/main-superAdmin.js"></script>
<script>
Chart.defaults.color = '#64748b';
Chart.defaults.borderColor = 'rgba(255,255,255,0.06)';

// Ingresos por mes
const ctxIngresos = document.getElementById('chartIngresos').getContext('2d');
new Chart(ctxIngresos, {
  type: 'bar',
  data: {
    labels: <?= $chartIngresoLabels ?>,
    datasets: [{
      label: 'Ingresos (COP)',
      data: <?= $chartIngresoData ?>,
      backgroundColor: 'rgba(129,140,248,0.7)',
      borderColor: '#818cf8',
      borderWidth: 1,
      borderRadius: 6,
    }]
  },
  options: {
    responsive: true, maintainAspectRatio: false,
    plugins: { legend: { display: false } },
    scales: {
      y: {
        grid: { color: 'rgba(255,255,255,0.05)' },
        ticks: { callback: v => '$ ' + v.toLocaleString('es-CO') }
      },
      x: { grid: { display: false } }
    }
  }
});

// Distribución estados
const ctxEstados = document.getElementById('chartEstados').getContext('2d');
new Chart(ctxEstados, {
  type: 'doughnut',
  data: {
    labels: <?= $chartDistLabels ?>,
    datasets: [{
      data: <?= $chartDistData ?>,
      backgroundColor: <?= $chartDistColors ?>,
      borderWidth: 2,
      borderColor: '#0b1229',
    }]
  },
  options: {
    responsive: true, maintainAspectRatio: false,
    plugins: {
      legend: { position: 'bottom', labels: { padding: 12, font: { size: 12 } } }
    },
    cutout: '60%'
  }
});

// Instituciones por mes
const ctxInst = document.getElementById('chartInstituciones').getContext('2d');
new Chart(ctxInst, {
  type: 'line',
  data: {
    labels: <?= $chartInstLabels ?>,
    datasets: [{
      label: 'Nuevas instituciones',
      data: <?= $chartInstData ?>,
      borderColor: '#10b981',
      backgroundColor: 'rgba(16,185,129,0.1)',
      tension: 0.4,
      fill: true,
      pointBackgroundColor: '#10b981',
      pointRadius: 4,
    }]
  },
  options: {
    responsive: true, maintainAspectRatio: false,
    plugins: { legend: { display: false } },
    scales: {
      y: {
        grid: { color: 'rgba(255,255,255,0.05)' },
        ticks: { stepSize: 1 },
        beginAtZero: true,
      },
      x: { grid: { display: false } }
    }
  }
});
</script>
</body>
</html>
