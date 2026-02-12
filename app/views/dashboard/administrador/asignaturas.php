<?php 
  require_once BASE_PATH . '/app/helpers/session_administrador.php';
  // ENLAZAMOS LA DEPENDENCIA, EN ESTE CASO EL CONTROLADOR QUE TIENE LA FUNCION DE COSULTAR LOS DATOS
  require_once BASE_PATH . '/app/controllers/administrador/asignatura.php';
    //ENLAZAMOS LA DEPENDENCIA DEL CONTROLADOR QUE TIENE LA FUNCION PARA MOSTRAR LOS DATOS
    require_once BASE_PATH . '/app/controllers/perfil.php';
    
    // IMPORTAMOS LOS MODELOS NECESARIOS
    require_once BASE_PATH . '/app/models/administradores/asignatura.php';
    require_once BASE_PATH . '/app/models/administradores/docente.php';
    
    // LLAMAMOS EL ID QUE VIENE ATRAVEZ DEL METODO GET
    $id = $_SESSION['user']['id'];
    // LLAMAMOS LA FUNCION ESPECIFICA DEL CONTROLADOR
    $usuario = mostrarPerfil($id);

    // OBTENEMOS LA INSTITUCIÓN DEL ADMIN
    $id_institucion = $_SESSION['user']['id_institucion'];

    // INSTANCIAMOS LOS MODELOS
    $objAsignatura = new Asignatura();
    $objDocente = new Docente();

    // CONTAMOS LOS REGISTROS POR INSTITUCIÓN
    $totalAsignaturas = $objAsignatura->contar($id_institucion);
    $totalProfesores = $objDocente->contar($id_institucion);

  // LLAMAMOS LA FUNCION ESPECIFICA QUE EXISTE EN DICHO CONTROLADOR
  $asignaturas = mostrarAsignaturas();
?>





<!doctype html>
<html lang="es">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>SIADEMY • Gestión de Asignaturas</title>
  <?php 
    include_once __DIR__ . '/../../layouts/header_coordinador.php'
  ?>
  <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/dashboard/css/styles-admin.css">
  
</head>

<body>
  <div class="app hide-right" id="appGrid">
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
          <div class="title">Gestión de Asignaturas</div>
        </div>
        <div class="search">
          <i class="ri-search-2-line"></i>
          <input type="text" placeholder="Buscar asignatura o profesor...">
        </div>        
        <div class="topbar-buttons" style="display: flex; gap: 10px;">
          <button class="btn-agregar-estudiante" onclick="window.location.href='administrador/registrar-asignatura'">
            <i class="ri-add-line"></i> Agregar Asignatura
          </button>
          <button class="btn-agregar-estudiante" onclick="window.location.href='<?= BASE_URL ?>/administrador/asignar-docentes'" >
            <i class="ri-user-add-line"></i> Asignar Docentes
          </button>
        </div>

        
        <?php
  include_once BASE_PATH . '/app/views/layouts/boton_perfil_solo.php'
?>
      </div>

      <!-- KPI CARDS -->
      <div class="kpis">
        <div class="kpi">
          <div class="icon"><i class="ri-booklet-line"></i></div>
          <div>
            <small>Total Asignaturas</small>
            <strong><?php echo $totalAsignaturas; ?></strong>
          </div>
        </div>
        <div class="kpi">
          <div class="icon"><i class="ri-user-star-line"></i></div>
          <div>
            <small>Profesores</small>
            <strong><?php echo $totalProfesores; ?></strong>
          </div>
        </div>
        <div class="kpi">
          <div class="icon"><i class="ri-line-chart-line"></i></div>
          <div>
            <small>Promedio General</small>
            <strong>3.8</strong>
          </div>
        </div>
      </div>

   

      <!-- SUBJECTS GRID -->
      <section class="subjects-section">
        <div class="subjects-header">
          
          <h3>Asignaturas Activas (18)</h3>
          <div class="view-toggle">
            <button class="view-btn active" data-view="grid"><i class="ri-grid-line"></i></button>
            <button class="view-btn" data-view="list"><i class="ri-list-check"></i></button>
          </div>
        </div>

        <div class="subjects-grid">
          <!-- Subject Card 1 -->

          <?php if(!empty($asignaturas)): ?>
          <?php foreach($asignaturas as $asignaturas): ?>

          <div class="subject-card">
            <div class="subject-header">
              <div class="subject-icon" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <i class="ri-calculator-line"></i>
              </div>
              <div class="subject-status status-active <?= $asignaturas['estado'] ?>"><?= $asignaturas['estado'] ?></div>
            </div>
            
            <h4><?= $asignaturas['nombre'] ?></h4>
            <p class="subject-area"><?= $asignaturas['descripcion'] ?></p>
            
            <div class="subject-info">
              <div class="info-item">
                <i class="ri-user-line"></i>
                <div>
                  <span class="info-label">Profesores</span>
                  <strong>4</strong>
                </div>
              </div>
              <div class="info-item">
                <i class="ri-time-line"></i>
                <div>
                  <span class="info-label">Horas/Semana</span>
                  <strong>5</strong>
                </div>
              </div>
            </div>

            <div class="subject-stats">
              <div class="stat-box">
                <span class="stat-label">Promedio</span>
                <strong class="stat-value grade-good">4.1</strong>
              </div>
              <div class="stat-box">
                <span class="stat-label">Estudiantes</span>
                <strong class="stat-value">842</strong>
              </div>
            </div>

            <div class="subject-actions">
              <button class="btn-secondary"><i class="bi bi-eye"></i></button>
              <button class="btn-secondary"><a href="<?= BASE_URL ?>/administrador/editar-asignatura?id=<?= $asignaturas['id'] ?>"><i class="bi bi-pencil-square"></i></a></button>
              <button class="btn-secondary"><a href="<?= BASE_URL ?>/administrador/eliminar-asignatura?accion=eliminar&id=<?= $asignaturas['id'] ?>"><i class="bi bi-trash3-fill"></i></a></button>

            </div>
          </div>
              <?php endforeach; ?>
              <?php else: ?>

                  <h3>No hay asignaturas registrados</h3>
                
              <?php endif; ?>


        </div>
      </section>

    </main>

    
  </div>

  <!-- FOOTER -->
  <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="<?= BASE_URL ?>/public/assets/dashboard/js/main-admin.js"></script>
 
</body>

</html>