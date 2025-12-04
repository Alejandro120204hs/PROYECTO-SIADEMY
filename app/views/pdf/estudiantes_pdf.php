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
    <h1>Reporte de Estudiantes Inscritos</h1>
</div>

<!-- PÁRRAFO DESCRIPTIVO -->
<div class="contenido">
    Este reporte presenta un resumen detallado de los estudiantes inscritos en el institucion. 
    Su propósito es ofrecer una visión clara del estado actual, tipos de instituciones registradas y 
    la información más relevante para la toma de decisiones administrativas.
</div>

<!-- TABLA GENERADA -->
<table>
    <thead>
        <tr>
            <th>Foto</th>
            <th>Genero</th>
            <th>Nombres</th>
            <th>Apellidos</th>
            <th>Documento</th>
            <th>Correo</th>
            <th>Telefono</th>
            <th>Acudiente</th>
            <th>Fecha de nacimiento</th>
            <th>Estado</th>

        </tr>
    </thead>
   <tbody>

    <?php if (!empty($estudiantes)): ?>
        <?php foreach ($estudiantes as $es): ?>
        <tr>
            <td>
                <img src="<?= BASE_URL?>/public/uploads/estudiantes/<?= $es['foto'] ?>" width="50px"    style="border-radius: 10px;">
            </td>

            <td><?= $es['genero'] ?></td>
            <td><?= $es['nombres'] ?></td>
            <td><?= $es['apellidos'] ?></td>
            <td><?= $es['documento'] ?></td>
            <td><?= $es['correo'] ?></td>
            <td><?= $es['telefono'] ?></td>
            <td><?= $es['nombres_acudiente']. ' ' .$es['apellidos_acudiente'] ?></td>
            <td><?= $es['fecha_de_nacimiento'] ?></td>
            <td><?= $es['estado'] ?></td>
            

        </tr>
        <?php endforeach; ?>

    <?php else: ?>
        <tr>
            <td colspan="7" style="text-align:center;">No hay estudiantes registradas</td>
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