<?php
    // Inicia sesión si no está activa
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">

<style>
    body{
        margin: 30px 0;
        font-size: 13px;
        color: #1E1E1E;
    }

    /* ENCABEZADO */
    .header{
        width: 100%;
        text-align: center;
        margin-bottom: 25px;
        padding: 10px 0;
        border-bottom: 3px solid #0A1D56;
    }

    .header h1{
        margin: 0;
        font-size: 24px;
        color: #0A1D56;
        font-weight: bold;
    }

    /* TEXTO DESCRIPTIVO */
    .contenido{
        margin-bottom: 20px;
        line-height: 1.5;
        text-align: justify;
    }

    /* TABLA */
    table{
        width: 740px;
        margin-left: -35px;
        border-collapse: collapse;
        margin-top: 20px;
        table-layout: fixed; /* MUY IMPORTANTE: evita que la tabla crezca fuera del ancho */
    }

    thead{
        background: #0075F2;
        color: white;
    }

    th, td{
        border: 1px solid #dcdcdc;
        padding: 7px;
        text-align: center;
        word-wrap: break-word; /* permite que textos largos se ajusten */
        font-size: 12px;
    }

    tbody tr:nth-child(even){
        background: #f5f5f5;
    }

    /* Ajuste especial para la columna de la foto */
    td img{
        width: 50px;
        height: 50px;
        object-fit: cover;
        border-radius: 8px;
    }

    /* FOOTER */
    .footer{
        margin-top: 20px;
        text-align: center;
        font-size: 11px;
        color: #555;
        padding-top: 10px;
        border-top: 1px solid #ccc;
    }
</style>

</head>
<body>

    <img src="<?= BASE_URL ?>/public/uploads/logofinal.png" alt="Logo" width="150px">

<!-- ENCABEZADO -->
<div class="header">
    <h1>Reporte de Docentes Inscritos</h1>
</div>

<!-- PÁRRAFO DESCRIPTIVO -->
<div class="contenido">
    Este reporte presenta un resumen detallado de los docentes registrados en el institucion. 
    Su propósito es ofrecer una visión clara del estado actual del personal académico y 
    la información más relevante para la toma de decisiones administrativas.
</div>

<!-- TABLA GENERADA -->
<table>
    <thead>
        <tr>
            <th>Foto</th>
            <th>Nombres</th>
            <th>Apellidos</th>
            <th>Especialidad</th>
            <th>Tipo de documento</th>
            <th>Documento</th>
            <th>Correo</th>
            <th>Telefono</th>
            <th>Estado</th>

        </tr>
    </thead>
   <tbody>

    <?php if (!empty($docentes)): ?>
        <?php foreach ($docentes as $doc): ?>
        <tr>
            <td>
                <img src="<?= BASE_URL?>/public/uploads/docentes/<?= $doc['foto'] ?>" width="50px" style="border-radius: 10px;">
            </td>

            <td><?= $doc['nombres'] ?></td>
            <td><?= $doc['apellidos'] ?></td>
            <td><?= $doc['profesion'] ?></td>
            <td><?= $doc['tipo_documento'] ?></td>
            <td><?= $doc['documento'] ?></td>
            <td><?= $doc['correo'] ?></td>
            <td><?= $doc['telefono'] ?></td>
            <td><?= $doc['estado'] ?></td>

        </tr>
        <?php endforeach; ?>

    <?php else: ?>
        <tr>
            <td colspan="9" style="text-align:center;">No hay docentes registrados</td>
        </tr>
    <?php endif; ?>

    </tbody>
</table>

<div class="footer">
    <p>Reporte generado automaticamente por SIADEMY</p>
</div>
</body>
</html>
