<?php 
  // ENLAZAMOS LAS DEPENDENCIAS NECESARIAS
  require_once BASE_PATH . '/app/helpers/session_administrador.php';
  require_once BASE_PATH . '/app/controllers/administrador/estudiante_controller.php';

  // LLAMAMOS LA FUNCION
  $datos = mostrarEstudiantes();
  
?>

<?php

    //ENLAZAMOS LA DEPENDENCIA DEL CONTROLADOR QUE TIENE LA FUNCION PARA MOSTRAR LOS DATOS
    require_once BASE_PATH . '/app/controllers/perfil.php';
    
    // LLAMAMOS EL ID QUE VIENE ATRAVEZ DEL METODO GET
    $id = $_SESSION['user']['id'];
    // LLAMAMOS LA FUNCION ESPECIFICA DEL CONTROLADOR
    $usuario = mostrarPerfil($id);
?>


<!doctype html>
<html lang="es">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>SIADEMY • Estudiantes</title>
  <?php 
    include_once __DIR__ . '/../../layouts/header_coordinador.php'
  ?>
  <link rel="stylesheet" href="public/assets/dashboard/css/styles-panel-estudiantes.css">

</head>

<body>
  <div class="app" id="appGrid">
    <!-- LEFT SIDEBAR -->
   <?php
      include_once __DIR__ . '/../../layouts/sidebar_coordinador.php'
   ?>

    <!-- MAIN -->
    <main class="main">
      <div class="topbar">
        <div class="topbar-left">
          <button class="toggle-btn" id="toggleLeft" title="Mostrar/Ocultar menú lateral">
            <i class="ri-menu-2-line"></i>
          </button>
          <div class="title">Estudiantes</div>
        </div>
        <div class="search">
          <i class="ri-search-2-line"></i>
          <input type="text" placeholder="Buscar Aquí">
        </div>
        
        <!-- Botón Agregar Estudiante -->
        <button class="btn-agregar-estudiante" onclick="window.location.href='administrador/registrar-estudiante'">
          <i class="ri-add-line"></i> Agregar Estudiante
        </button>
          <a class="btn-pdf" href="<?= BASE_URL ?>/administrador-reporte?reporte=estudiantes" target="_blank">Generar PDF</a>
        
        <div class="user">
          <?php
          include_once __DIR__ . '/../../layouts/boton_perfil.php'
          ?>
        </div>
      </div>

      <!-- Tabla de Estudiantes -->
      <div class="datatable-card">
        <div class="table-wrapper">
          
        <table id="tablaEstudiantes" class="table table-dark table-hover">
          <thead>
            <tr>
              <th width="40">
                <input type="checkbox" class="form-check-input" id="selectAll">
              </th>
              <th>Foto</th>
              <th>Genero</th>
              <th>Nombres</th>
              <th>Apellidos</th>
              <th>N° Identificación</th>
              <th>Correo</th>
              <th>Telefono</th>
              <th>Acudiente</th>
              <th>Fecha de nacimineto</th>
            
              <th>Estado</th>
              <th width="100">Acción</th>
            </tr>
          </thead>
          <tbody>
            <?php if(!empty($datos)): ?>
            <?php foreach($datos as $estudiante): ?>
            <tr>
              <td>
                <input type="checkbox" class="form-check-input row-checkbox">
              </td>
              <td><img src="<?= BASE_URL ?>/public/uploads/estudiantes/<?= $estudiante['foto'] ?>" 
         alt="foto" width="50px" height="50px" style="border-radius: 50%;"></td>
              <td><?= $estudiante['genero'] ?></td>
              <td><?= $estudiante['nombres'] ?></td>
              <td><?= $estudiante['apellidos'] ?></td>
              <td><?= $estudiante['documento'] ?></td>
              <td><?= $estudiante['correo'] ?></td>
              <td><?= $estudiante['telefono'] ?></td>
              <td class="dos-lineas"><?= $estudiante['nombres_acudiente']. ' ' .$estudiante['apellidos_acudiente']?></td>
              <td><?= $estudiante['fecha_de_nacimiento'] ?></td>
              <td><?= $estudiante['estado'] ?></td>
              <td class="acciones">
                  <a class="btn-action" href="">Ver</a>
                  <a class="btn-action" href="<?= BASE_URL ?>/administrador/editar-estudiante?id=<?= $estudiante['id'] ?>">
                    Editar
                  </a>
                  <a class="btn-action" href="<?= BASE_URL ?>/administrador/eliminar-estudiante?accion=eliminar&id=<?= $estudiante['id_usuario'] ?>">
                    <i class="bi bi-trash3-fill"></i>
                  </a>

              </td>
            </tr>

            <?php endforeach; ?>
              <?php else: ?>
                <tr>
                  <td>No hay estudiantes registrados</td>
                </tr>
              <?php endif; ?>
          
          </tbody>
        </table>
        </div>
      </div>

    </main>
  </div>

  <!-- Bootstrap and DataTables Scripts -->
  <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

  <script src="<?= BASE_URL ?>/public/assets/dashboard/js/main-panel-estudiantes.js"></script>
</body>

</html>