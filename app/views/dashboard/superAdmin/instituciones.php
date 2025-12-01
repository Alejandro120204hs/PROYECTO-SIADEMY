<?php

  // IMPORTAMOS LAS DEPENDENCIAS NECESARIAS
  require_once BASE_PATH . '/app/controllers/superAdmin/instituciones.php';

  // LLAMAMOS LA FUNCION ESPECIFICA
  $datos = mostrarInstituciones();

?>


<!doctype html>
<html lang="es">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>SIADEMY • Instituciones</title>
  <?php 
    include_once __DIR__ . '/../../layouts/header_coordinador.php'
  ?>
  <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/dashboard/css/styles-panel-estudiantes.css">

</head>

<body>
  <div class="app" id="appGrid">
    <!-- LEFT SIDEBAR -->
    <?php 
      include_once __DIR__ . '/../../layouts/sidebar_superAdmin.php'
    ?>

    <!-- MAIN -->
    <main class="main">
      <div class="topbar">
        <div class="topbar-left">
          <button class="toggle-btn" id="toggleLeft" title="Mostrar/Ocultar menú lateral">
            <i class="ri-menu-2-line"></i>
          </button>
          <div class="title">Instituciones</div>
        </div>
        <div class="search">
          <i class="ri-search-2-line"></i>
          <input type="text" placeholder="Buscar Aquí">
        </div>
        
        <!-- Botón Agregar Estudiante -->
        <button class="btn-agregar-estudiante" onclick="window.location.href='<?= BASE_URL ?>/superAdmin-agregar-instituciones'">
          <i class="ri-add-line"></i> Agregar Institución
        </button>
          <a class="btn-pdf" href="<?= BASE_URL ?>/superAdmin-reporte?reporte=instituciones" target="_blank">Generar PDF</a>
        
        
        <!-- Dropdown Más Nuevo -->
        <div class="dropdown-custom">
          <button class="dropdown-toggle-custom">
            Más Nuevo <i class="ri-arrow-down-s-line"></i>
          </button>
        </div>
        
        <div class="user">
          <button class="btn" title="Notificaciones"><i class="ri-notification-3-line"></i></button>
          <button class="btn" title="Configuración"><i class="ri-settings-3-line"></i></button>
          <div class="avatar" title="Diego A.">DA</div>
        </div>
      </div>

      <!-- Tabla de Estudiantes -->
      <div class="datatable-card">
        <table id="tablaEstudiantes" class="table table-dark table-hover">
          <thead>
            
            <tr>
              <th width="40">
                <input type="checkbox" class="form-check-input" id="selectAll">
              </th>
              <th>Logo</th>
              <th>Nombre</th>
              <th>Ciudad</th>
              <th>Direccion</th>
              <th>Telefono</th>
              <th>Correo</th>
              <th>Tipo</th>
              <th>Estado</th>
              <th width="100">Acción</th>
            </tr>
          </thead>
          <tbody>
            <?php if(!empty($datos)): ?>
            <?php foreach($datos as $institucion): ?>
            <tr>
              <td>
                <input type="checkbox" class="form-check-input row-checkbox">
              </td>
              <td><img src="<?= BASE_URL ?>/public/uploads/instituciones/<?= $institucion['logo'] ?>" 
              alt="logo" width="50px" height="50px" style="border-radius: 50%;"></td>
              <td><?= $institucion['nombre'] ?></td>
              <td><?= $institucion['ciudad'] ?></td>
              <td><?= $institucion['direccion'] ?></td>
              <td><?= $institucion['telefono'] ?></td>
              <td><?= $institucion['correo'] ?></td>
              <td><?= $institucion['tipo'] ?></td>
              <td><?= $institucion['estado'] ?></td>

              <td class="acciones">
        
                <button class="btn-action"><a href="<?= BASE_URL ?>/superAdmin-editar-institucion?id=<?= $institucion['id'] ?>">Editar</a></button>
                <button class="btn-action"><a href="<?= BASE_URL ?>/superAdmin-eliminar-institucion?accion=eliminar&id=<?= $institucion['id'] ?>"><i class="bi bi-trash3-fill"></i></a></button>
              </td>
            </tr>

            <?php endforeach; ?>
            <?php else: ?>
           <tr>
            <td>No hay instituciones registradas</td>
           </tr>
           <?php endif; ?>
           
          </tbody>
        </table>
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