<?php
require_once BASE_PATH . '/app/models/superAdmin/reportes.php';

$model = new ReportesSuperAdmin();

$kpiMes     = $model->ingresosDelMes();
$kpiMesAnt  = $model->ingresosDelMesAnterior();
$kpiAnio    = $model->ingresosDelAnio();
$pendientes = $model->pagosPendientes();
$hoy        = $model->pagosAprobadosHoy();
$instInfo   = $model->instituciones();
$topInst    = $model->topInstituciones();
$ingresosMes = $model->ingresosPorMes();

$nombre = 'reporte_siademy_' . date('Ymd') . '.csv';
header('Content-Type: text/csv; charset=UTF-8');
header('Content-Disposition: attachment; filename="' . $nombre . '"');
header('Pragma: no-cache');

$out = fopen('php://output', 'w');
fwrite($out, "\xEF\xBB\xBF");

fputcsv($out, ['REPORTE SIADEMY — Generado el ' . date('d/m/Y H:i')], ';');
fputcsv($out, [], ';');

fputcsv($out, ['INDICADORES CLAVE'], ';');
fputcsv($out, ['Indicador', 'Valor'], ';');
fputcsv($out, ['Ingresos del mes (COP)',   number_format($kpiMes / 100, 0, ',', '.')], ';');
fputcsv($out, ['Ingresos del año (COP)',   number_format($kpiAnio / 100, 0, ',', '.')], ';');
fputcsv($out, ['Ingresos mes anterior',    number_format($kpiMesAnt / 100, 0, ',', '.')], ';');
fputcsv($out, ['Pagos pendientes',         $pendientes], ';');
fputcsv($out, ['Pagos aprobados hoy',      $hoy['cantidad']], ';');
fputcsv($out, ['Ingresos hoy (COP)',       number_format($hoy['monto'] / 100, 0, ',', '.')], ';');
fputcsv($out, ['Instituciones activas',    $instInfo['activas']], ';');
fputcsv($out, ['Total instituciones',      $instInfo['total']], ';');

fputcsv($out, [], ';');
fputcsv($out, ['INGRESOS POR MES (últimos 12 meses)'], ';');
fputcsv($out, ['Mes', 'Ingresos (COP)'], ';');
foreach ($ingresosMes as $r) {
    fputcsv($out, [$r['etiqueta'], number_format($r['total'] / 100, 0, ',', '.')], ';');
}

fputcsv($out, [], ';');
fputcsv($out, ['TOP INSTITUCIONES POR INGRESOS'], ';');
fputcsv($out, ['#', 'Institución', 'Pagos', 'Total (COP)'], ';');
foreach ($topInst as $i => $inst) {
    fputcsv($out, [
        $i + 1,
        $inst['nombre'],
        $inst['pagos'],
        number_format($inst['total'] / 100, 0, ',', '.'),
    ], ';');
}

fclose($out);
exit();
