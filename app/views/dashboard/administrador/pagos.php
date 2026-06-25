<?php
require_once BASE_PATH . '/app/helpers/session_administrador.php';
require_once BASE_PATH . '/app/models/pagos/pago.php';
require_once BASE_PATH . '/app/models/administradores/estudiante.php';

$idUsuario     = (int)($_SESSION['user']['id']             ?? 0);
$idInstitucion = (int)($_SESSION['user']['id_institucion'] ?? 0);

$modelEstudiante  = new Estudiante();
$totalEstudiantes = (int)$modelEstudiante->contar($idInstitucion);

define('PRECIO_POR_ESTUDIANTE', 5000);
$montoPesos = $totalEstudiantes * PRECIO_POR_ESTUDIANTE;
$concepto   = 'Suscripción mensual SIADEMY';

$modelPago = new Pago();
$historial = $modelPago->listarPorInstitucion($idInstitucion);

$error = $_GET['error'] ?? '';
$errorMsg = match ($error) {
    'sin_estudiantes' => 'No tienes estudiantes activos. Registra al menos un estudiante para realizar el pago.',
    'server'          => 'Error al procesar el pago. Intenta de nuevo.',
    default           => '',
};

$estadoLabels = [
    'PENDING'  => ['texto' => 'Pendiente',  'color' => '#f59e0b'],
    'APPROVED' => ['texto' => 'Aprobado',   'color' => '#10b981'],
    'DECLINED' => ['texto' => 'Rechazado',  'color' => '#ef4444'],
    'VOIDED'   => ['texto' => 'Anulado',    'color' => '#6b7280'],
    'ERROR'    => ['texto' => 'Error',      'color' => '#ef4444'],
];
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>SIADEMY • Pagos</title>
  <?php include_once __DIR__ . '/../../layouts/header_coordinador.php' ?>
  <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/dashboard/css/styles-admin.css">
  <style>
    .pagos-layout { display: grid; grid-template-columns: 1fr 360px; gap: 24px; align-items: start; }
    @media (max-width: 900px) { .pagos-layout { grid-template-columns: 1fr; } }

    .pago-card {
      background: #11193a;
      border: 1px solid rgba(255,255,255,.08);
      border-radius: 16px;
      padding: 24px;
    }
    .pago-card + .pago-card { margin-top: 24px; }
    .pago-card h3 {
      font-size: 15px; font-weight: 600; color: #e6e9f4;
      margin-bottom: 20px; display: flex; align-items: center; gap: 8px;
    }
    .pago-card h3 i { color: #818cf8; }

    /* Resumen de cálculo */
    .calculo-box {
      background: #0e1632;
      border-radius: 14px;
      padding: 24px;
      margin-bottom: 20px;
    }
    .calculo-row {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 10px 0;
      font-size: 14px;
      color: #b8c2df;
      border-bottom: 1px solid rgba(255,255,255,.05);
    }
    .calculo-row:last-child { border-bottom: none; }
    .calculo-label { color: #64748b; }
    .calculo-valor { font-weight: 600; color: #e6e9f4; }
    .calculo-total {
      margin-top: 4px;
      font-size: 28px !important;
      font-weight: 700 !important;
      color: #818cf8 !important;
    }
    .calculo-total small { font-size: 14px; font-weight: 400; color: #64748b; }

    .formula-badge {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      padding: 6px 12px;
      background: rgba(129,140,248,.1);
      border: 1px solid rgba(129,140,248,.2);
      border-radius: 999px;
      font-size: 12px;
      color: #818cf8;
      font-weight: 600;
      margin-bottom: 20px;
    }

    .btn-pagar {
      width: 100%;
      padding: 14px;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      border: none;
      border-radius: 10px;
      color: #fff;
      font-family: 'Poppins', sans-serif;
      font-size: 15px;
      font-weight: 600;
      cursor: pointer;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
      transition: opacity .2s;
    }
    .btn-pagar:hover:not(:disabled) { opacity: .9; }
    .btn-pagar:disabled { opacity: .45; cursor: not-allowed; }

    .wompi-badge {
      display: flex; align-items: center; justify-content: center;
      gap: 6px; margin-top: 10px; font-size: 11px; color: #64748b;
    }
    .wompi-badge i { color: #10b981; }

    .alert-error {
      background: rgba(239,68,68,.12); border: 1px solid rgba(239,68,68,.25);
      border-radius: 10px; padding: 12px 16px; color: #fca5a5;
      font-size: 13px; margin-bottom: 20px;
      display: flex; align-items: center; gap: 8px;
    }
    .alert-warning {
      background: rgba(245,158,11,.1); border: 1px solid rgba(245,158,11,.25);
      border-radius: 10px; padding: 12px 16px; color: #fcd34d;
      font-size: 13px; margin-bottom: 20px;
      display: flex; align-items: center; gap: 8px;
    }

    /* Historial */
    .historial-table { width: 100%; border-collapse: collapse; }
    .historial-table th {
      font-size: 11px; font-weight: 600; color: #64748b;
      text-transform: uppercase; letter-spacing: .06em;
      padding: 10px 12px; text-align: left;
      border-bottom: 1px solid rgba(255,255,255,.06);
    }
    .historial-table td {
      padding: 11px 12px; font-size: 13px; color: #b8c2df;
      border-bottom: 1px solid rgba(255,255,255,.04);
    }
    .historial-table tr:last-child td { border-bottom: none; }
    .estado-badge {
      display: inline-block; padding: 3px 10px;
      border-radius: 999px; font-size: 11px; font-weight: 600;
    }
    .empty-historial { text-align: center; padding: 32px; color: #64748b; font-size: 13px; }
    .empty-historial i { font-size: 36px; display: block; margin-bottom: 8px; }
  </style>
</head>
<body>
<div class="app hide-right" id="appGrid">
  <?php include_once __DIR__ . '/../../layouts/sidebar_coordinador.php' ?>

  <main class="main">
    <div class="topbar">
      <div class="topbar-left">
        <button class="toggle-btn" id="toggleLeft"><i class="ri-menu-2-line"></i></button>
        <div class="title">Pagos</div>
      </div>
    </div>

    <?php if ($errorMsg): ?>
    <div class="alert-error"><i class="ri-error-warning-line"></i> <?= htmlspecialchars($errorMsg) ?></div>
    <?php endif; ?>

    <?php if ($totalEstudiantes === 0): ?>
    <div class="alert-warning">
      <i class="ri-information-line"></i>
      No tienes estudiantes activos registrados. El pago estará disponible cuando registres estudiantes.
    </div>
    <?php endif; ?>

    <div class="pagos-layout">

      <!-- IZQUIERDA -->
      <div>
        <div class="pago-card">
          <h3><i class="ri-calculator-line"></i> Resumen de pago</h3>

          <div class="formula-badge">
            <i class="ri-user-line"></i>
            Precio por estudiante: $ <?= number_format(PRECIO_POR_ESTUDIANTE, 0, ',', '.') ?> COP / mes
          </div>

          <div class="calculo-box">
            <div class="calculo-row">
              <span class="calculo-label">Estudiantes activos</span>
              <span class="calculo-valor"><?= $totalEstudiantes ?></span>
            </div>
            <div class="calculo-row">
              <span class="calculo-label">Precio por estudiante</span>
              <span class="calculo-valor">$ <?= number_format(PRECIO_POR_ESTUDIANTE, 0, ',', '.') ?></span>
            </div>
            <div class="calculo-row" style="padding-top:16px; margin-top:4px;">
              <span class="calculo-label" style="font-size:15px; color:#94a3b8;">Total a pagar</span>
              <span class="calculo-total">
                $ <?= number_format($montoPesos, 0, ',', '.') ?>
                <small>COP</small>
              </span>
            </div>
          </div>

          <form method="POST" action="<?= BASE_URL ?>/administrador/iniciar-pago">
            <input type="hidden" name="concepto"          value="<?= htmlspecialchars($concepto) ?>">
            <input type="hidden" name="monto"             value="<?= $montoPesos ?>">
            <input type="hidden" name="total_estudiantes" value="<?= $totalEstudiantes ?>">
            <button type="submit" class="btn-pagar" <?= $totalEstudiantes === 0 ? 'disabled' : '' ?>>
              <i class="ri-shield-check-line"></i>
              Pagar $ <?= number_format($montoPesos, 0, ',', '.') ?> con Wompi
            </button>
          </form>

          <div class="wompi-badge">
            <i class="ri-lock-line"></i> Pago seguro procesado por Wompi
          </div>
        </div>

        <!-- HISTORIAL -->
        <div class="pago-card">
          <h3><i class="ri-history-line"></i> Historial de pagos</h3>
          <?php if (empty($historial)): ?>
            <div class="empty-historial">
              <i class="ri-receipt-line"></i>
              No hay pagos registrados aún.
            </div>
          <?php else: ?>
          <div style="overflow-x:auto;">
            <table class="historial-table">
              <thead>
                <tr>
                  <th>Concepto</th>
                  <th>Monto</th>
                  <th>Estado</th>
                  <th>Referencia</th>
                  <th>Fecha</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($historial as $pago):
                  $info = $estadoLabels[$pago['estado']] ?? ['texto' => $pago['estado'], 'color' => '#94a3b8'];
                ?>
                <tr>
                  <td><?= htmlspecialchars($pago['concepto']) ?></td>
                  <td>$ <?= number_format($pago['monto_cents'] / 100, 0, ',', '.') ?></td>
                  <td>
                    <span class="estado-badge"
                          style="background:<?= $info['color'] ?>22;color:<?= $info['color'] ?>;border:1px solid <?= $info['color'] ?>44;">
                      <?= $info['texto'] ?>
                    </span>
                  </td>
                  <td style="font-size:11px;color:#64748b;font-family:monospace;">
                    <?= htmlspecialchars($pago['referencia']) ?>
                  </td>
                  <td><?= date('d/m/Y H:i', strtotime($pago['created_at'])) ?></td>
                </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
          <?php endif; ?>
        </div>
      </div>

      <!-- DERECHA: info -->
      <div class="pago-card">
        <h3><i class="ri-information-line"></i> Información</h3>
        <p style="font-size:13px; color:#94a3b8; line-height:1.7; margin-bottom:20px;">
          El costo mensual de SIADEMY se calcula automáticamente según el número de
          <strong style="color:#e6e9f4">estudiantes activos</strong> en tu institución.
        </p>

        <div style="display:flex; flex-direction:column; gap:12px; margin-bottom:20px;">
          <?php foreach ([
            ['ri-user-add-line',   'Cada estudiante activo', '$ 5.000 / mes'],
            ['ri-refresh-line',    'Se recalcula cada mes',  'según estudiantes'],
            ['ri-team-line',       'Tus estudiantes ahora',  $totalEstudiantes . ' activos'],
          ] as [$icon, $label, $val]): ?>
          <div style="display:flex; justify-content:space-between; align-items:center; padding:10px 14px; background:#0e1632; border-radius:10px;">
            <div style="display:flex; align-items:center; gap:8px; font-size:13px; color:#94a3b8;">
              <i class="<?= $icon ?>" style="color:#818cf8; font-size:16px;"></i>
              <?= $label ?>
            </div>
            <span style="font-size:13px; font-weight:600; color:#e6e9f4;"><?= $val ?></span>
          </div>
          <?php endforeach; ?>
        </div>

        <div style="padding:14px; background:#0e1632; border-radius:10px; font-size:12px; color:#64748b; line-height:1.7;">
          <i class="ri-shield-check-line" style="color:#10b981;"></i>
          Puedes pagar con tarjeta de crédito/débito, PSE, Nequi o efectivo en corresponsales.
          SIADEMY no almacena datos de tarjetas.
        </div>
      </div>

    </div>
  </main>
</div>

<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= BASE_URL ?>/public/assets/dashboard/js/main-admin.js"></script>
</body>
</html>
