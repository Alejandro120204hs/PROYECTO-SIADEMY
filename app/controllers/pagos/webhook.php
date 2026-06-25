<?php

// Webhook de Wompi — sin sesión, recibe eventos POST de Wompi
require_once __DIR__ . '/../../../config/env_loader.php';
require_once __DIR__ . '/../../../config/config.php';
require_once BASE_PATH . '/app/services/WompiService.php';
require_once BASE_PATH . '/app/models/pagos/pago.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit();
}

$payload  = file_get_contents('php://input');
$checksum = $_SERVER['HTTP_X_EVENT_CHECKSUM'] ?? '';

if (empty($payload)) {
    http_response_code(400);
    echo json_encode(['error' => 'Empty payload']);
    exit();
}

$wompi = new WompiService();

if (!$wompi->verificarWebhook($payload, $checksum)) {
    error_log("Wompi webhook: firma inválida. Checksum recibido: $checksum");
    http_response_code(401);
    echo json_encode(['error' => 'Invalid signature']);
    exit();
}

$data        = json_decode($payload, true);
$evento      = $data['event'] ?? '';
$transaction = $data['data']['transaction'] ?? [];

if ($evento !== 'transaction.updated' || empty($transaction)) {
    http_response_code(200);
    echo json_encode(['ok' => true]);
    exit();
}

$referencia = $transaction['reference']      ?? '';
$estado     = $transaction['status']         ?? '';
$wompiId    = $transaction['id']             ?? '';

$estadoMap = [
    'APPROVED' => 'APPROVED',
    'DECLINED' => 'DECLINED',
    'VOIDED'   => 'VOIDED',
    'ERROR'    => 'ERROR',
    'PENDING'  => 'PENDING',
];
$estadoNormalizado = $estadoMap[$estado] ?? 'ERROR';

$modelPago = new Pago();
$modelPago->actualizarPorReferencia($referencia, $estadoNormalizado, $wompiId, $transaction);

http_response_code(200);
echo json_encode(['ok' => true]);
exit();
