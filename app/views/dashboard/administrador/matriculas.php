<?php 
  require_once BASE_PATH . '/app/helpers/session_administrador.php';
  require_once BASE_PATH . '/app/controllers/administrador/matricula.php';

  // LLAMAMOS LA FUNCIÓN
  $datos = mostrarMatriculas();
  
?>

<!doctype html>
<html lang="es">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>SIADEMY • Gestión de Matrículas</title>
 <?php 
    include_once __DIR__ . '/../../layouts/header_coordinador.php'
 ?>
  <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/dashboard/css/styles-admin.css">
 <style>
    .table-responsive {
        overflow-x: auto;
    }
    
    .badge {
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 500;
    }
    
    .badge-success {
        background-color: #28a745;
        color: white;
    }
    
    .badge-warning {
        background-color: #ffc107;
        color: #212529;
    }
    
    .badge-info {
        background-color: #17a2b8;
        color: white;
    }
    
    .btn-action {
        padding: 5px 10px;
        margin: 0 2px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-size: 14px;
    }
    
    .btn-edit {
        background-color: #007bff;
        color: white;
    }
    
    .btn-delete {
        background-color: #dc3545;
        color: white;
    }
    
    .btn-edit:hover {
        background-color: #0056b3;
    }
    
    .btn-delete:hover {
        background-color: #c82333;
    }
 </style>
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
          <div class="title cursos">Gestión de Matrículas</div>
        </div>

        <div class="div"></div>

        <button class="btn-agregar-estudiante" onclick="window.location.href='administrador/registrar-matricula'">
            <i class="ri-user-add-line"></i> Matricular Estudiante
        </button>
        
        <div class="search">
          <i class="ri-search-2-line"></i>
          <input type="text" id="searchMatricula" placeholder="Buscar por estudiante, curso o documento...">
        </div>
        <button class="toggle-btn" id="toggleRight" title="Mostrar/Ocultar panel derecho">
          <i class="ri-layout-right-2-line"></i>
        </button>
      </div>

      <!-- KPI CARDS -->
      <div class="kpis">
        <div class="kpi">
          <div class="icon"><i class="ri-graduation-cap-line"></i></div>
          <div>
            <small>Total Matrículas</small>
            <strong><?= count($datos) ?></strong>
          </div>
        </div>
        <div class="kpi">
          <div class="icon"><i class="ri-calendar-line"></i></div>
          <div>
            <small>Año Actual</small>
            <strong><?= date('Y') ?></strong>
          </div>
        </div>
        <div class="kpi">
          <div class="icon"><i class="ri-book-open-line"></i></div>
          <div>
            <small>Cursos Activos</small>
            <strong><?php 
                $cursos_unicos = array_unique(array_column($datos, 'id_curso'));
                echo count($cursos_unicos);
            ?></strong>
          </div>
        </div>
        <div class="kpi">
          <div class="icon"><i class="ri-user-line"></i></div>
          <div>
            <small>Estudiantes Matriculados</small>
            <strong><?php 
                $estudiantes_unicos = array_unique(array_column($datos, 'id_estudiante'));
                echo count($estudiantes_unicos);
            ?></strong>
          </div>
        </div>
      </div>

      <!-- FILTROS -->
      <div class="filters-container" style="background: white; padding: 20px; border-radius: 8px; margin-bottom: 20px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
        <div class="row">
          <div class="col-md-4">
            <label for="filterAnio">Filtrar por Año:</label>
            <select id="filterAnio" class="form-select">
              <option value="">Todos los años</option>
              <?php 
                $anios = array_unique(array_column($datos, 'anio'));
                rsort($anios);
                foreach($anios as $anio): 
              ?>
                <option value="<?= $anio ?>" <?= ($anio == date('Y')) ? 'selected' : '' ?>><?= $anio ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col-md-4">
            <label for="filterCurso">Filtrar por Curso:</label>
            <select id="filterCurso" class="form-select">
              <option value="">Todos los cursos</option>
              <?php 
                $cursos = [];
                foreach($datos as $dato){
                    $key = $dato['grado'] . ' - ' . $dato['nombre_curso'];
                    $cursos[$key] = $dato['id_curso'];
                }
                ksort($cursos);
                foreach($cursos as $nombre => $id): 
              ?>
                <option value="<?= $id ?>"><?= $nombre ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col-md-4">
            <label for="filterNivel">Filtrar por Nivel:</label>
            <select id="filterNivel" class="form-select">
              <option value="">Todos los niveles</option>
              <?php 
                $niveles = array_unique(array_column($datos, 'nivel_academico'));
                sort($niveles);
                foreach($niveles as $nivel): 
              ?>
                <option value="<?= $nivel ?>"><?= $nivel ?></option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>
      </div>

      <!-- TABLA DE MATRÍCULAS -->
      <section class="table-section">
        <div class="table-header">
          <h3>Listado de Matrículas (<?= count($datos) ?>)</h3>
        </div>

        <div class="table-responsive">
          <table class="table table-hover" id="tableMatriculas">
            <thead>
              <tr>
                <th>ID</th>
                <th>Estudiante</th>
                <th>Documento</th>
                <th>Curso</th>
                <th>Nivel</th>
                <th>Año</th>
                <th>Fecha Matrícula</th>
                <th>Estado Curso</th>
                <th>Acciones</th>
              </tr>
            </thead>
            <tbody>
              <?php if (!empty($datos)): ?>
                <?php foreach ($datos as $matricula): ?>
                  <tr data-anio="<?= $matricula['anio'] ?>" 
                      data-curso="<?= $matricula['id_curso'] ?>" 
                      data-nivel="<?= $matricula['nivel_academico'] ?>">
                    <td><?= $matricula['id'] ?></td>
                    <td>
                      <strong><?= htmlspecialchars($matricula['estudiante_nombres'] . ' ' . $matricula['estudiante_apellidos']) ?></strong>
                    </td>
                    <td><?= htmlspecialchars($matricula['estudiante_documento']) ?></td>
                    <td>
                      <span class="badge badge-info">
                        <?= $matricula['grado'] ?>° - <?= htmlspecialchars($matricula['nombre_curso']) ?>
                      </span>
                    </td>
                    <td><?= htmlspecialchars($matricula['nivel_academico']) ?></td>
                    <td><?= $matricula['anio'] ?></td>
                    <td><?= date('d/m/Y', strtotime($matricula['fecha'])) ?></td>
                    <td>
                      <?php if($matricula['estado_curso'] == 'Activo'): ?>
                        <span class="badge badge-success">Activo</span>
                      <?php else: ?>
                        <span class="badge badge-warning">Inactivo</span>
                      <?php endif; ?>
                    </td>
                    <td>
                      <button class="btn-action btn-edit" 
                              onclick="window.location.href='administrador/editar-matricula?id=<?= $matricula['id'] ?>'" 
                              title="Editar matrícula">
                        <i class="ri-edit-line"></i>
                      </button>
                      <button class="btn-action btn-delete" 
                              onclick="confirmarEliminacion(<?= $matricula['id'] ?>)" 
                              title="Eliminar matrícula">
                        <i class="ri-delete-bin-line"></i>
                      </button>
                    </td>
                  </tr>
                <?php endforeach; ?>
              <?php else: ?>
                <tr>
                  <td colspan="9" class="text-center">No hay matrículas registradas</td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </section>

    </main>

    <!-- RIGHT PANEL (opcional) -->
    <aside class="right" id="rightPanel">
      <div class="panel-content">
        <h4>Información de Matrículas</h4>
        <p>Aquí puedes gestionar todas las matrículas de los estudiantes en los diferentes cursos.</p>
        
        <div class="info-section">
          <h5>Acciones disponibles:</h5>
          <ul>
            <li>Ver todas las matrículas</li>
            <li>Matricular nuevo estudiante</li>
            <li>Editar matrícula existente</li>
            <li>Eliminar matrícula</li>
            <li>Filtrar por año, curso o nivel</li>
          </ul>
        </div>
      </div>
    </aside>

  </div>

  <script>
    // Función para confirmar eliminación
    function confirmarEliminacion(id) {
      Swal.fire({
        title: '¿Estás seguro?',
        text: "Esta acción eliminará la matrícula del estudiante",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
      }).then((result) => {
        if (result.isConfirmed) {
          window.location.href = `<?= BASE_URL ?>/administrador/eliminar-matricula?id=${id}&accion=eliminar`;
        }
      });
    }

    // Búsqueda en tiempo real
    document.getElementById('searchMatricula').addEventListener('keyup', function() {
      const searchValue = this.value.toLowerCase();
      const rows = document.querySelectorAll('#tableMatriculas tbody tr');
      
      rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(searchValue) ? '' : 'none';
      });
    });

    // Filtros
    function aplicarFiltros() {
      const anio = document.getElementById('filterAnio').value;
      const curso = document.getElementById('filterCurso').value;
      const nivel = document.getElementById('filterNivel').value;
      const rows = document.querySelectorAll('#tableMatriculas tbody tr');
      
      rows.forEach(row => {
        const rowAnio = row.getAttribute('data-anio');
        const rowCurso = row.getAttribute('data-curso');
        const rowNivel = row.getAttribute('data-nivel');
        
        let mostrar = true;
        
        if (anio && rowAnio !== anio) mostrar = false;
        if (curso && rowCurso !== curso) mostrar = false;
        if (nivel && rowNivel !== nivel) mostrar = false;
        
        row.style.display = mostrar ? '' : 'none';
      });
    }

    document.getElementById('filterAnio').addEventListener('change', aplicarFiltros);
    document.getElementById('filterCurso').addEventListener('change', aplicarFiltros);
    document.getElementById('filterNivel').addEventListener('change', aplicarFiltros);

    // Toggle sidebars
    document.getElementById('toggleLeft').addEventListener('click', function() {
      document.querySelector('.sidebar').classList.toggle('collapsed');
    });

    document.getElementById('toggleRight').addEventListener('click', function() {
      document.getElementById('rightPanel').classList.toggle('collapsed');
    });
  </script>
</body>
</html>
