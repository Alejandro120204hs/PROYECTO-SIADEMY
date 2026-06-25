<?php
require_once BASE_PATH . '/app/models/superAdmin/reportes.php';

$model  = new ReportesSuperAdmin();
$estado = $_GET['estado'] ?? '';
$desde  = $_GET['desde']  ?? '';
$hasta  = $_GET['hasta']  ?? '';

$pagos = $model->listarPagos($estado, $desde, $hasta);

$nombre = 'pagos_siademy_' . date('Ymd_His') . '.csv';
header('Content-Type: text/csv; charset=UTF-8');
header('Content-Disposition: attachment; filename="' . $nombre . '"');
header('Pragma: no-cache');

$out = fopen('php://output', 'w');
// BOM para que Excel abra correctamente con UTF-8
fwrite($out, "\xEF\xBB\xBF");
fputcsv($out, ['Referencia', 'Institución', 'Concepto', 'Monto (COP)', 'Estado', 'Fecha'], ';');

$estadoMap = ['APPROVED' => 'Aprobado', 'PENDING' => 'Pendiente', 'DECLINED' => 'Rechazado', 'VOIDED' => 'Anulado', 'ERROR' => 'Error'];

foreach ($pagos as $p) {
    fputcsv($out, [
        $p['referencia'],
        $p['nombre_institucion'],
        $p['concepto'],
        number_format($p['monto_cents'] / 100, 0, ',', '.'),
        $estadoMap[$p['estado']] ?? $p['estado'],
        date('d/m/Y H:i', strtotime($p['created_at'])),
    ], ';');
}
fclose($out);
exit();
