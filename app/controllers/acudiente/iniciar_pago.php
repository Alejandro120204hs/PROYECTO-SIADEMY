<?php

require_once BASE_PATH . '/app/helpers/session_acudiente.php';
require_once BASE_PATH . '/app/services/WompiService.php';
require_once BASE_PATH . '/app/models/pagos/pago.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . BASE_URL . '/acudiente/pagos');
    exit();
}

$idUsuario     = (int)($_SESSION['user']['id']              ?? 0);
$idInstitucion = (int)($_SESSION['user']['id_institucion']  ?? 0);
$concepto      = trim($_POST['concepto'] ?? '');
$montoStr      = trim($_POST['monto']    ?? '');
$email         = $_SESSION['user']['correo'] ?? '';

$conceptosValidos = ['Matrícula', 'Pensión mensual', 'Uniforme', 'Material escolar', 'Otro'];

if (!in_array($concepto, $conceptosValidos, true) || !ctype_digit($montoStr) || (int)$montoStr < 1000) {
    header('Location: ' . BASE_URL . '/acudiente/pagos?error=datos_invalidos');
    exit();
}

$montoCents  = (int)$montoStr * 100;
$wompi       = new WompiService();
$referencia  = $wompi->generarReferencia();
$redirectUrl = BASE_URL . '/acudiente/pago-resultado';

$modelPago = new Pago();
$creado    = $modelPago->crear([
    'id_institucion' => $idInstitucion,
    'id_usuario'     => $idUsuario,
    'referencia'     => $referencia,
    'concepto'       => $concepto,
    'monto_cents'    => $montoCents,
    'moneda'         => 'COP',
]);

if (!$creado) {
    header('Location: ' . BASE_URL . '/acudiente/pagos?error=server');
    exit();
}

$urlCheckout = $wompi->urlCheckout($referencia, $montoCents, $redirectUrl, $email);
header('Location: ' . $urlCheckout);
exit();
