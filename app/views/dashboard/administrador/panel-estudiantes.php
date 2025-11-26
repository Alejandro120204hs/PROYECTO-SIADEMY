<?php 
  require_once BASE_PATH . '/app/helpers/session_administrador.php';
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
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/remixicon@4.3.0/fonts/remixicon.css">
  <!-- DataTables CSS -->
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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
        
        <!-- Dropdown Más Nuevo -->
        <div class="dropdown-custom">
          <button class="dropdown-toggle-custom">
            Más Nuevo <i class="ri-arrow-down-s-line"></i>
          </button>
        </div>
        
        <div class="user">
          <?php
          include_once __DIR__ . '/../../layouts/boton_perfil.php'
          ?>
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
              <th>Nombres</th>
              <th>N° Identificación</th>
              <th>Fecha Nacimiento</th>
              <th>Nombre Pariente</th>
              <th>Ciudad</th>
              <th>Contactos</th>
              <th>Grado</th>
              <th width="100">Acción</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>
                <input type="checkbox" class="form-check-input row-checkbox">
              </td>
              <td>
                <div class="student-info">
                  <div class="student-avatar" style="background: #9b59b6;">SW</div>
                  <span class="student-name">Samanta William</span>
                </div>
              </td>
              <td>#463436465</td>
              <td>28 de marzo de 2016</td>
              <td>Maria William</td>
              <td>Jatarta</td>
              <td>
                <div class="contacts">
                  <i class="ri-phone-line"></i>
                  <i class="ri-mail-line"></i>
                </div>
              </td>
              <td>VII A</td>
              <td>
                <button class="btn-action"><a href="administrador/detalle-estudiante">Ver</a></button>
                <button class="btn-more"><i class="ri-more-2-fill"></i></button>
              </td>
            </tr>
          
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