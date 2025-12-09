<?php 
  require_once BASE_PATH . '/app/helpers/session_administrador.php';
  // ENLAZAMOS LA DEPENDENCIA, EN ESTE CASO EL CONTROLADOR QUE TIENE LA FUNCION DE COSULTAR LOS DATOS
  require_once BASE_PATH . '/app/controllers/administrador/acudiente.php';

  // LLAMAMOS LA FUNCION ESPECIFICA QUE EXISTE EN DICHO CONTROLADOR
  $datos = mostrarAcudientes();

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
  <title>SIADEMY • Acudientes</title>
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
          <div class="title">Acudientes</div>
        </div>
        <div class="search">
          <i class="ri-search-2-line"></i>
          <input type="text" placeholder="Buscar Aquí">
        </div>
        
        <!-- Botón Agregar Estudiante -->
        <button class="btn-agregar-estudiante" onclick="window.location.href='administrador/registrar-acudiente'">
          <i class="ri-add-line"></i> Agregar Acudiente
        </button>
          <a class="btn-pdf" href="<?= BASE_URL ?>/administrador-reporte?reporte=acudientes" target="_blank">Generar PDF</a>

        
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
              <th>Foto</th>
              <th>Nombres</th>
              <th>Apellidos</th>
              <th>Parentesco</th>
              <th>N° Identificación</th>
              <th>Correo</th>
              <th>Telefono</th>
              <th>Fecha de nacimiento</th>
              <th>estado</th>
              <th width="100">Acción</th>
            </tr>
          </thead>
          <tbody>
            <?php if(!empty($datos)): ?>
            <?php foreach($datos as $acudiente): ?>
            <tr>
              <td>
                <input type="checkbox" class="form-check-input row-checkbox">
              </td>
              <td><img src="<?= BASE_URL ?>/public/uploads/acudientes/<?= $acudiente['foto'] ?>" 
         alt="foto" width="50px" height="50px" style="border-radius: 50%;"></td>
              <td><?= $acudiente['nombres'] ?></td>
              <td><?= $acudiente['apellidos'] ?></td>
              <td><?= $acudiente['parentesco'] ?></td>
              <td><?= $acudiente['documento'] ?></td>
              <td><?= $acudiente['correo'] ?></td>
              <td><?= $acudiente['telefono'] ?></td>
                 <td><?= $acudiente['fecha_de_nacimiento'] ?></td>
              <td><?= $acudiente['estado'] ?></td>
              
              
           
              <td class="acciones">
                <button class="btn-action"><a href="<?= BASE_URL ?>/administrador/detalle-acudiente">Ver</a></button>
                <button class="btn-action"><a href="<?= BASE_URL ?>/administrador/editar-acudiente?id=<?= $acudiente['id'] ?>">Editar</a></button>
                <button class="btn-action"><a href="<?= BASE_URL ?>/administrador/eliminar-acudiente?accion=eliminar&id=<?= $acudiente['id_usuario'] ?>"><i class="bi bi-trash3-fill"></i></a></button>

                
              </td>
            </tr>
          
              <?php endforeach; ?>
              <?php else: ?>
                <tr>
                  <td>No hay acudientes registrados</td>
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