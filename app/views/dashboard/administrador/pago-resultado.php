<?php
require_once BASE_PATH . '/app/helpers/session_administrador.php';
require_once BASE_PATH . '/app/services/WompiService.php';
require_once BASE_PATH . '/app/models/pagos/pago.php';

$transaccionId = trim($_GET['id'] ?? '');
$transaccion   = null;
$pago          = null;

if ($transaccionId !== '') {
    $wompi       = new WompiService();
    $transaccion = $wompi->obtenerTransaccion($transaccionId);

    if ($transaccion) {
        $referencia = $transaccion['reference'] ?? '';
        $estado     = $transaccion['status']    ?? '';
        $wompiId    = $transaccion['id']        ?? '';

        $estadoMap = [
            'APPROVED' => 'APPROVED', 'DECLINED' => 'DECLINED',
            'VOIDED'   => 'VOIDED',   'ERROR'    => 'ERROR', 'PENDING' => 'PENDING',
        ];
        $modelPago = new Pago();
        $modelPago->actualizarPorReferencia($referencia, $estadoMap[$estado] ?? 'ERROR', $wompiId, $transaccion);
        $pago = $modelPago->buscarPorReferencia($referencia);
    }
}

$estado     = $transaccion['status']          ?? 'ERROR';
$monto      = isset($transaccion['amount_in_cents']) ? $transaccion['amount_in_cents'] / 100 : 0;
$referencia = $transaccion['reference']       ?? '—';
$concepto   = $pago['concepto']               ?? '—';
$fecha      = isset($transaccion['created_at_secs'])
    ? date('d/m/Y H:i', $transaccion['created_at_secs'])
    : date('d/m/Y H:i');

$config = match ($estado) {
    'APPROVED' => ['icon' => 'ri-checkbox-circle-fill', 'color' => '#10b981',
                   'titulo' => '¡Pago exitoso!',
                   'mensaje' => 'Tu pago fue procesado correctamente. El plan de tu institución ha sido activado.',
                   'border' => 'rgba(16,185,129,.2)'],
    'DECLINED' => ['icon' => 'ri-close-circle-fill', 'color' => '#ef4444',
                   'titulo' => 'Pago rechazado',
                   'mensaje' => 'El pago fue rechazado. Verifica los datos de tu método de pago e intenta de nuevo.',
                   'border' => 'rgba(239,68,68,.2)'],
    'PENDING'  => ['icon' => 'ri-time-fill', 'color' => '#f59e0b',
                   'titulo' => 'Pago pendiente',
                   'mensaje' => 'Tu pago está siendo procesado. Te notificaremos cuando se confirme.',
                   'border' => 'rgba(245,158,11,.2)'],
    default    => ['icon' => 'ri-error-warning-fill', 'color' => '#6b7280',
                   'titulo' => 'Estado desconocido',
                   'mensaje' => 'No pudimos obtener el estado del pago. Revisa el historial en unos minutos.',
                   'border' => 'rgba(107,114,128,.2)'],
};
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>SIADEMY • Resultado del pago</title>
  <?php include_once __DIR__ . '/../../layouts/header_coordinador.php' ?>
  <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/dashboard/css/styles-admin.css">
  <style>
    .resultado-wrap {
      max-width: 520px; margin: 40px auto;
      background: #11193a; border: 1px solid <?= $config['border'] ?>;
      border-radius: 20px; padding: 40px 36px; text-align: center;
    }
    .resultado-icon  { font-size: 64px; color: <?= $config['color'] ?>; margin-bottom: 16px; }
    .resultado-titulo { font-size: 22px; font-weight: 700; color: #e6e9f4; margin-bottom: 8px; }
    .resultado-msg   { font-size: 14px; color: #94a3b8; line-height: 1.6; margin-bottom: 28px; }
    .detalles-grid   { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 28px; text-align: left; }
    .detalle-item    { background: #0e1632; border-radius: 10px; padding: 12px 14px; }
    .detalle-label   { font-size: 11px; color: #64748b; text-transform: uppercase; letter-spacing: .06em; margin-bottom: 4px; }
    .detalle-valor   { font-size: 14px; color: #e6e9f4; font-weight: 600; word-break: break-all; }
    .btn-volver {
      display: inline-flex; align-items: center; gap: 8px; padding: 12px 24px;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      border: none; border-radius: 10px; color: #fff;
      font-family: 'Poppins', sans-serif; font-size: 14px; font-weight: 600;
      text-decoration: none; cursor: pointer;
    }
    .btn-volver:hover { opacity: .9; color: #fff; }
  </style>
</head>
<body>
<div class="app hide-right" id="appGrid">
  <?php include_once __DIR__ . '/../../layouts/sidebar_coordinador.php' ?>

  <main class="main">
    <div class="topbar">
      <div class="topbar-left">
        <button class="toggle-btn" id="toggleLeft"><i class="ri-menu-2-line"></i></button>
        <div class="title">Resultado del pago</div>
      </div>
    </div>

    <div class="resultado-wrap">
      <div class="resultado-icon"><i class="<?= $config['icon'] ?>"></i></div>
      <div class="resultado-titulo"><?= $config['titulo'] ?></div>
      <div class="resultado-msg"><?= $config['mensaje'] ?></div>

      <div class="detalles-grid">
        <div class="detalle-item">
          <div class="detalle-label">Plan</div>
          <div class="detalle-valor"><?= htmlspecialchars($concepto) ?></div>
        </div>
        <div class="detalle-item">
          <div class="detalle-label">Monto</div>
          <div class="detalle-valor">$ <?= number_format($monto, 0, ',', '.') ?> COP</div>
        </div>
        <div class="detalle-item" style="grid-column: 1 / -1;">
          <div class="detalle-label">Referencia</div>
          <div class="detalle-valor" style="font-family:monospace; font-size:12px;">
            <?= htmlspecialchars($referencia) ?>
          </div>
        </div>
        <div class="detalle-item">
          <div class="detalle-label">Fecha</div>
          <div class="detalle-valor"><?= $fecha ?></div>
        </div>
        <div class="detalle-item">
          <div class="detalle-label">Estado</div>
          <div class="detalle-valor" style="color:<?= $config['color'] ?>;"><?= $estado ?></div>
        </div>
      </div>

      <a href="<?= BASE_URL ?>/administrador/pagos" class="btn-volver">
        <i class="ri-arrow-left-line"></i> Volver a Pagos
      </a>
    </div>
  </main>
</div>

<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= BASE_URL ?>/public/assets/dashboard/js/main-admin.js"></script>
</body>
</html>
