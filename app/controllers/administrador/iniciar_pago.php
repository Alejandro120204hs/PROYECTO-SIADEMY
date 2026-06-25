<?php

require_once BASE_PATH . '/app/helpers/session_administrador.php';
require_once BASE_PATH . '/app/services/WompiService.php';
require_once BASE_PATH . '/app/models/pagos/pago.php';
require_once BASE_PATH . '/app/models/administradores/estudiante.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . BASE_URL . '/administrador/pagos');
    exit();
}

$idUsuario     = (int)($_SESSION['user']['id']             ?? 0);
$idInstitucion = (int)($_SESSION['user']['id_institucion'] ?? 0);
$email         = $_SESSION['user']['correo'] ?? '';

// Recalcular en servidor — no confiar en el monto enviado por POST
$modelEstudiante  = new Estudiante();
$totalEstudiantes = (int)$modelEstudiante->contar($idInstitucion);

if ($totalEstudiantes === 0) {
    header('Location: ' . BASE_URL . '/administrador/pagos?error=sin_estudiantes');
    exit();
}

$montoPesos = $totalEstudiantes * 5000;
$concepto   = 'Suscripción mensual SIADEMY';

$wompi      = new WompiService();
$referencia = $wompi->generarReferencia();
$redirectUrl = BASE_URL . '/administrador/pago-resultado';

$modelPago = new Pago();
$creado    = $modelPago->crear([
    'id_institucion' => $idInstitucion,
    'id_usuario'     => $idUsuario,
    'referencia'     => $referencia,
    'concepto'       => $concepto . ' (' . $totalEstudiantes . ' estudiantes)',
    'monto_cents'    => $montoPesos * 100,
    'moneda'         => 'COP',
]);

if (!$creado) {
    header('Location: ' . BASE_URL . '/administrador/pagos?error=server');
    exit();
}

$urlCheckout = $wompi->urlCheckout($referencia, $montoPesos * 100, $redirectUrl, $email);
header('Location: ' . $urlCheckout);
exit();
