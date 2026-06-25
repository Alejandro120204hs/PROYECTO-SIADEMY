<?php
require_once BASE_PATH . '/app/helpers/session_acudiente.php';
require_once BASE_PATH . '/app/models/pagos/pago.php';
require_once BASE_PATH . '/app/controllers/acudiente/view_data.php';

$idUsuario = (int)($_SESSION['user']['id'] ?? 0);
$usuario   = obtenerPerfilAcudienteDesdeSesion();

$modelPago = new Pago();
$historial = $modelPago->listarPorUsuario($idUsuario);

$error = $_GET['error'] ?? '';
$errorMsg = match ($error) {
    'datos_invalidos' => 'Los datos ingresados no son válidos. Verifica el concepto y el monto.',
    'server'          => 'Error al procesar el pago. Intenta de nuevo.',
    default           => '',
};

$estadoLabels = [
    'PENDING'  => ['texto' => 'Pendiente',  'color' => '#f59e0b'],
    'APPROVED' => ['texto' => 'Aprobado',   'color' => '#10b981'],
    'DECLINED' => ['texto' => 'Rechazado',  'color' => '#ef4444'],
    'VOIDED'   => ['texto' => 'Anulado',    'color' => '#6b7280'],
    'ERROR'    => ['texto' => 'Error',       'color' => '#ef4444'],
];
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>SIADEMY • Pagos</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/remixicon@4.3.0/fonts/remixicon.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <?php include_once __DIR__ . '/../../layouts/header_coordinador.php' ?>
  <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/dashboard/css/styles-acudiente.css">
  <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/dashboard/css/modo-claro-acudiente.css">
  <style>
    .pagos-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 24px; margin-bottom: 32px; }
    @media (max-width: 768px) { .pagos-grid { grid-template-columns: 1fr; } }

    .pago-card {
      background: #11193a;
      border: 1px solid rgba(255,255,255,.08);
      border-radius: 16px;
      padding: 28px;
    }
    .pago-card h3 {
      font-size: 16px;
      font-weight: 600;
      color: #e6e9f4;
      margin-bottom: 20px;
      display: flex;
      align-items: center;
      gap: 8px;
    }
    .pago-card h3 i { color: #818cf8; font-size: 18px; }

    .form-group { margin-bottom: 16px; }
    .form-group label { display: block; font-size: 13px; color: #94a3b8; margin-bottom: 6px; font-weight: 500; }
    .form-group select,
    .form-group input[type="number"] {
      width: 100%;
      background: #0e1632;
      border: 1px solid rgba(255,255,255,.1);
      border-radius: 10px;
      padding: 12px 14px;
      color: #e6e9f4;
      font-family: 'Poppins', sans-serif;
      font-size: 14px;
      outline: none;
      transition: border-color .2s;
    }
    .form-group select:focus,
    .form-group input[type="number"]:focus {
      border-color: #818cf8;
    }
    .form-group select option { background: #0e1632; }

    .monto-prefix {
      position: relative;
    }
    .monto-prefix span {
      position: absolute;
      left: 14px;
      top: 50%;
      transform: translateY(-50%);
      color: #94a3b8;
      font-size: 14px;
      font-weight: 600;
    }
    .monto-prefix input { padding-left: 36px !important; }

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
      margin-top: 8px;
      transition: opacity .2s;
    }
    .btn-pagar:hover { opacity: .9; }

    .wompi-badge {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 6px;
      margin-top: 12px;
      font-size: 12px;
      color: #64748b;
    }
    .wompi-badge i { font-size: 14px; color: #10b981; }

    .alert-error {
      background: rgba(239,68,68,.12);
      border: 1px solid rgba(239,68,68,.25);
      border-radius: 10px;
      padding: 12px 16px;
      color: #fca5a5;
      font-size: 13px;
      margin-bottom: 20px;
      display: flex;
      align-items: center;
      gap: 8px;
    }

    /* Historial */
    .historial-title {
      font-size: 16px;
      font-weight: 600;
      color: #e6e9f4;
      margin-bottom: 16px;
      display: flex;
      align-items: center;
      gap: 8px;
    }
    .historial-title i { color: #818cf8; }

    .historial-table {
      width: 100%;
      border-collapse: collapse;
    }
    .historial-table th {
      font-size: 11px;
      font-weight: 600;
      color: #64748b;
      text-transform: uppercase;
      letter-spacing: .06em;
      padding: 10px 14px;
      text-align: left;
      border-bottom: 1px solid rgba(255,255,255,.06);
    }
    .historial-table td {
      padding: 12px 14px;
      font-size: 13px;
      color: #b8c2df;
      border-bottom: 1px solid rgba(255,255,255,.04);
    }
    .historial-table tr:last-child td { border-bottom: none; }

    .estado-badge {
      display: inline-block;
      padding: 3px 10px;
      border-radius: 999px;
      font-size: 11px;
      font-weight: 600;
    }

    .empty-historial {
      text-align: center;
      padding: 32px;
      color: #64748b;
      font-size: 13px;
    }
    .empty-historial i { font-size: 36px; display: block; margin-bottom: 8px; }
  </style>
</head>
<body>
<div class="app hide-right" id="appGrid">
  <?php include_once __DIR__ . '/../../layouts/sidebar_acudiente.php' ?>

  <main class="main">
    <div class="topbar">
      <div class="topbar-left">
        <button class="toggle-btn" id="toggleLeft">
          <i class="ri-menu-2-line"></i>
        </button>
        <div class="title">Pagos</div>
      </div>
      <?php include_once BASE_PATH . '/app/views/layouts/boton_perfil_solo.php'; ?>
    </div>

    <?php if ($errorMsg): ?>
    <div class="alert-error">
      <i class="ri-error-warning-line"></i>
      <?= htmlspecialchars($errorMsg) ?>
    </div>
    <?php endif; ?>

    <div class="pagos-grid">

      <!-- FORMULARIO DE PAGO -->
      <div class="pago-card">
        <h3><i class="ri-bank-card-line"></i> Realizar un pago</h3>

        <form method="POST" action="<?= BASE_URL ?>/acudiente/iniciar-pago">
          <div class="form-group">
            <label>Concepto de pago</label>
            <select name="concepto" required>
              <option value="" disabled selected>Selecciona un concepto</option>
              <option value="Matrícula">Matrícula</option>
              <option value="Pensión mensual">Pensión mensual</option>
              <option value="Uniforme">Uniforme</option>
              <option value="Material escolar">Material escolar</option>
              <option value="Otro">Otro</option>
            </select>
          </div>
          <div class="form-group">
            <label>Monto a pagar (COP)</label>
            <div class="monto-prefix">
              <span>$</span>
              <input type="number" name="monto" min="1000" step="1000"
                     placeholder="ej: 150000" required>
            </div>
          </div>
          <button type="submit" class="btn-pagar">
            <i class="ri-shield-check-line"></i> Pagar con Wompi
          </button>
          <div class="wompi-badge">
            <i class="ri-lock-line"></i> Pago seguro procesado por Wompi
          </div>
        </form>
      </div>

      <!-- INFO -->
      <div class="pago-card">
        <h3><i class="ri-information-line"></i> Información</h3>
        <p style="font-size:13px; color:#94a3b8; line-height:1.7; margin-bottom:16px;">
          Al hacer clic en <strong style="color:#e6e9f4">Pagar con Wompi</strong> serás redirigido a la
          plataforma de pagos segura de Wompi donde podrás completar tu transacción con:
        </p>
        <div style="display:flex; flex-direction:column; gap:10px;">
          <?php foreach ([
            ['ri-bank-card-line',   'Tarjeta de crédito o débito'],
            ['ri-bank-line',        'Transferencia bancaria (PSE)'],
            ['ri-money-dollar-box-line', 'Nequi'],
            ['ri-store-line',       'Efectivo en corresponsales'],
          ] as [$icon, $label]): ?>
          <div style="display:flex; align-items:center; gap:10px; font-size:13px; color:#b8c2df;">
            <i class="<?= $icon ?>" style="color:#818cf8; font-size:16px; width:20px;"></i>
            <?= $label ?>
          </div>
          <?php endforeach; ?>
        </div>
        <div style="margin-top:20px; padding:12px 14px; background:#0e1632; border-radius:10px; font-size:12px; color:#64748b; line-height:1.6;">
          <i class="ri-shield-check-line" style="color:#10b981;"></i>
          Todos los pagos son procesados de forma segura. SIADEMY no almacena datos de tarjetas.
        </div>
      </div>

    </div>

    <!-- HISTORIAL -->
    <div class="pago-card">
      <div class="historial-title">
        <i class="ri-history-line"></i> Historial de pagos
      </div>

      <?php if (empty($historial)): ?>
        <div class="empty-historial">
          <i class="ri-receipt-line"></i>
          No tienes pagos registrados aún.
        </div>
      <?php else: ?>
        <div style="overflow-x: auto;">
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
                        style="background: <?= $info['color'] ?>22; color: <?= $info['color'] ?>; border: 1px solid <?= $info['color'] ?>44;">
                    <?= $info['texto'] ?>
                  </span>
                </td>
                <td style="font-size:11px; color:#64748b; font-family:monospace;">
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

  </main>
</div>

<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= BASE_URL ?>/public/assets/dashboard/js/main-acudiente.js"></script>
</body>
</html>
