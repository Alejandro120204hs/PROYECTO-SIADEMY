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
        margin: 30px;
        font-size: 12px;
        color: #1E1E1E;
    }

    /* ENCABEZADO */
    .header{
        display: flex;
        align-items: center;
        background: #0A1D56;
        padding: 15px;
        border-radius: 8px;
        color: white;
        margin-bottom: 25px;
    }

    .header img{
        width: 70px;
        margin-right: 15px;
    }

    h1{
        margin: 0;
        font-size: 22px;
        font-weight: bold;
        color: #1E1E1E;
        text-align: center;
        margin-top: 50px;
    }

    /* TEXTO DESCRIPTIVO */
    .contenido{
        margin-bottom: 25px;
        line-height: 1.5;
    }

    /* TABLA */
    table{
        width: 100%;
        border-collapse: collapse;
        margin-top: 10px;
        font-size: 12px;
    }

    thead{
        background: #0075F2;
        color: white;
    }

    th, td{
        border: 1px solid #ddd;
        padding: 8px;
        text-align: left;
    }

    tbody tr:nth-child(even){
        background: #f2f2f2;
    }

    /* FOOTER */
    .footer{
        position: fixed;
        bottom: -10px;
        left: 0;
        right: 0;
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
    <h1>Reporte de Acudientes Inscritos</h1>
</div>

<!-- PÁRRAFO DESCRIPTIVO -->
<div class="contenido">
    Este reporte presenta un resumen detallado de los acudientes inscritos en el institucion. 
    Su propósito es ofrecer una visión clara del estado actual, tipos de instituciones registradas y 
    la información más relevante para la toma de decisiones administrativas.
</div>

<!-- TABLA GENERADA -->
<table>
    <thead>
        <tr>
            <th>Foto</th>
            <th>Nombres</th>
            <th>Apellidos</th>
            <th>Parentesco</th>
            <th>Documento</th>
            <th>Correo</th>
            <th>Telefono</th>
            <th>Edad</th>
            <th>Estado</th>

        </tr>
    </thead>
   <tbody>

    <?php if (!empty($acudientes)): ?>
        <?php foreach ($acudientes as $ac): ?>
        <tr>
            <td>
                <img src="<?= BASE_URL?>/public/uploads/acudientes/<?= $ac['foto'] ?>" width="50px"    style="border-radius: 10px;">
            </td>

            <td><?= $ac['nombres'] ?></td>
            <td><?= $ac['apellidos'] ?></td>
            <td><?= $ac['parentesco'] ?></td>
            <td><?= $ac['documento'] ?></td>
            <td><?= $ac['correo'] ?></td>
            <td><?= $ac['telefono'] ?></td>
            <td><?= $ac['edad'] ?></td>
            <td><?= $ac['estado'] ?></td>
            

        </tr>
        <?php endforeach; ?>

    <?php else: ?>
        <tr>
            <td colspan="7" style="text-align:center;">No hay acudientes registradas</td>
        </tr>
    <?php endif; ?>

    </tbody>
    </table>

    <!-- FOOTER -->
    <div class="footer">
        © 2025 SIADEMY — Todos los derechos reservados
    </div>

    </body>
</html>