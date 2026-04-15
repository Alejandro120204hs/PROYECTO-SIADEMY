<?php 
  // require_once BASE_PATH . '/app/helpers/session_administrador.php';
   // ENLAZAMOS LA DEPENDENCIA, EN ESTE CASO EL CONTROLADOR QUE TIENE LA FUNCION DE COSULTAR LOS DATOS
  require_once BASE_PATH . '/app/controllers/docente/curso.php';
  require_once BASE_PATH . '/app/controllers/docente/actividad.php';
  require_once BASE_PATH . '/app/controllers/perfil.php';

  // LLAMAMOS LA FUNCION ESPECIFICA QUE EXISTE EN DICHO CONTROLADOR
  $datos = mostrarCursos();
  $id = $_SESSION['user']['id'] ?? 0;
  $usuario = mostrarPerfil($id);
  
  // Obtener actividades del curso seleccionado
  $actividades = [];
  $id_curso_seleccionado = null;
  $info_curso = null;
  
  if (isset($_GET['id_curso']) && !empty($_GET['id_curso'])) {
      $id_curso_seleccionado = $_GET['id_curso'];
      $actividades = listarActividades();
      
      // Obtener info del curso para mostrar en el header
      if (!empty($actividades)) {
          $info_curso = $actividades[0]; // Tiene grado, curso, nombre_asignatura
      }
  }
  
  // Stats calculados para la info bar y tabs
  $totalActividades = count($actividades);
  $totalActivas     = 0;
  $totalCerradas    = 0;
  foreach ($actividades as $a) {
    if ($a['estado'] === 'activa')  $totalActivas++;
    elseif ($a['estado'] === 'cerrada') $totalCerradas++;
  }
  $nombreDocente  = htmlspecialchars(trim(($usuario['nombres'] ?? '') . ' ' . ($usuario['apellidos'] ?? '')), ENT_QUOTES, 'UTF-8');
  $asignaturaInfo = $info_curso ? htmlspecialchars($info_curso['nombre_asignatura'], ENT_QUOTES, 'UTF-8') : '';
  // Años únicos para el filtro de período
  $anosUnicos = [];
  foreach ($actividades as $a) {
    $anio = date('Y', strtotime($a['fecha_entrega']));
    if (!in_array($anio, $anosUnicos)) $anosUnicos[] = $anio;
  }
  rsort($anosUnicos);

  // Función auxiliar para obtener icono según tipo
  function obtenerIconoTipo($tipo) {
      $iconos = [
          'Taller' => 'ri-file-text-line',
          'Quiz' => 'ri-file-list-3-line',
          'Examen' => 'ri-file-paper-line',
          'Proyecto' => 'ri-folder-line',
          'Exposición' => 'ri-presentation-line',
          'Laboratorio' => 'ri-flask-line',
          'Tarea' => 'ri-file-edit-line'
      ];
      return $iconos[$tipo] ?? 'ri-file-line';
  }
  
  // Función auxiliar para formatear fecha
  function formatearFecha($fecha) {
      $meses = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];
      $timestamp = strtotime($fecha);
      return date('d', $timestamp) . ' ' . $meses[date('n', $timestamp) - 1] . ' ' . date('Y', $timestamp);
  }
?>
<!doctype html>
  <html lang="es">

  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SIADEMY • Gestión de Actividades</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/remixicon@4.3.0/fonts/remixicon.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/dashboard/css/styles-docente.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/dashboard/css/docente/actividades.css">

  </head>

  <body>
    <div class="app hide-right" id="appGrid">
      
      <!-- LEFT SIDEBAR -->
      <?php 
        include_once __DIR__ . '/../../layouts/sidebar_docente.php'
      ?>

      <!-- MAIN -->
      <main class="main">
        
        <!-- TOPBAR -->
        <div class="topbar">
          <div class="topbar-left">
            <button class="toggle-btn" id="toggleLeft" title="Mostrar/Ocultar menú lateral">
              <i class="ri-menu-2-line"></i>
            </button>
            <div class="title">Gestión de Actividades</div>
          </div>
          <div class="topbar-actions">
            <button class="btn-action" id="btnNuevaActividad">
              <a href="<?= BASE_URL ?>/docente/agregar-actividad<?= isset($_GET['id_curso']) ? '?id_curso='.$_GET['id_curso'] : '' ?>"><i class="ri-add-line"></i>
              Nueva Actividad</a>
            </button>
          </div>
          <?php
            include_once BASE_PATH . '/app/views/layouts/boton_perfil_solo.php';
          ?>
        </div>

        <!-- TEACHER INFO BAR -->
        <div class="teacher-info-bar">
          <div class="teacher-profile">
            <div>
              <strong><?= $nombreDocente ?></strong>
              <small><?= $asignaturaInfo ? 'Docente · ' . $asignaturaInfo : 'Docente' ?></small>
            </div>
          </div>
          <div class="teacher-stats">
            <div class="stat-item">
              <i class="ri-file-list-3-line"></i>
              <div>
                <strong id="totalActividades"><?= (int)$totalActividades ?></strong>
                <small>Actividades creadas</small>
              </div>
            </div>
            <div class="stat-item">
              <i class="ri-time-line"></i>
              <div>
                <strong id="actividadesPendientes"><?= (int)$totalActivas ?></strong>
                <small>Activas</small>
              </div>
            </div>
            <div class="stat-item">
              <i class="ri-check-double-line"></i>
              <div>
                <strong id="actividadesCalificadas"><?= (int)$totalCerradas ?></strong>
                <small>Cerradas</small>
              </div>
            </div>
          </div>
        </div>

       <!-- FILTER SECTION -->
      <div class="actividades-filter-section">
        <div class="actividades-filter-tabs">
          <button class="filter-tab-actividad active" data-estado="todas">
            <i class="ri-file-list-line"></i>
            Todas
            <span class="badge-count"><?= (int)$totalActividades ?></span>
          </button>
          <button class="filter-tab-actividad" data-estado="activa">
            <i class="ri-time-line"></i>
            Activas
            <span class="badge-count"><?= (int)$totalActivas ?></span>
          </button>
          <button class="filter-tab-actividad" data-estado="cerrada">
            <i class="ri-lock-line"></i>
            Cerradas
            <span class="badge-count"><?= (int)$totalCerradas ?></span>
          </button>
        </div>

        <div class="actividades-filter-controls">
          <div class="actividades-filter-select-wrapper">
            <i class="ri-calendar-line actividades-filter-icon"></i>
            <select id="periodFilter" class="actividades-filter-select">
              <option value="todos">Todos los años</option>
              <?php foreach ($anosUnicos as $anio): ?>
              <option value="<?= $anio ?>"><?= $anio ?></option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="actividades-filter-select-wrapper">
            <i class="ri-book-line actividades-filter-icon"></i>
            <select id="cursoFilter" class="actividades-filter-select">
              <option value="todos">Todos los cursos</option>
              <?php if (!empty($datos)): ?>
                <?php foreach ($datos as $cursoOpt): ?>
                  <option value="<?= $cursoOpt['grado'] . strtolower($cursoOpt['curso']) ?>"
                    <?= ($id_curso_seleccionado == $cursoOpt['id']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($cursoOpt['grado'] . '° ' . $cursoOpt['curso'] . ' · ' . $cursoOpt['nombre_asignatura'], ENT_QUOTES, 'UTF-8') ?>
                  </option>
                <?php endforeach; ?>
              <?php endif; ?>
            </select>
          </div>

          <div class="actividades-filter-search">
            <i class="ri-search-line"></i>
            <input type="text" id="searchActividades" placeholder="Buscar actividad...">
          </div>

          <!-- NUEVO: Botones de Vista -->
          <div class="view-toggle-buttons">
            <button class="btn-view-toggle active" id="btnVistaCards" data-view="cards" title="Vista de Tarjetas">
              <i class="ri-layout-grid-line"></i>
            </button>
            <button class="btn-view-toggle" id="btnVistaTabla" data-view="table" title="Vista de Tabla">
              <i class="ri-table-line"></i>
            </button>
          </div>
        </div>
      </div>

        <!-- ACTIVIDADES GRID -->
        <section class="actividades-grid" id="actividadesGrid">
          
          <?php if (empty($actividades)): ?>
            <div class="empty-state" style="text-align: center; padding: 60px 20px; color: #97a1b6; grid-column: 1/-1;">
              <i class="ri-file-list-line" style="font-size: 64px; opacity: 0.5;"></i>
              <h3 style="margin-top: 24px; color: #fff;">No hay actividades registradas</h3>
              <p style="margin-top: 12px; font-size: 16px;">Crea tu primera actividad para este curso</p>
              <a href="<?= BASE_URL ?>/docente/agregar-actividad<?= isset($_GET['id_curso']) ? '?id_curso='.$_GET['id_curso'] : '' ?>" 
                 class="btn btn-primary" style="margin-top: 24px; display: inline-flex; align-items: center; gap: 8px;">
                <i class="ri-add-line"></i> Crear primera actividad
              </a>
            </div>
          <?php else: ?>
            <?php foreach ($actividades as $actividad): ?>
              <?php 
                $estadoClase = strtolower($actividad['estado']);
                $tipoClase = strtolower($actividad['tipo']);
                $tipoTexto = strtoupper($actividad['tipo']);
              ?>
              <div class="actividad-card" 
                   data-estado="<?= $estadoClase ?>" 
                   data-periodo="<?= date('Y', strtotime($actividad['fecha_entrega'])) ?>" 
                   data-curso="<?= $actividad['grado'] . strtolower($actividad['curso']) ?>" 
                   data-titulo="<?= htmlspecialchars(strtolower($actividad['titulo'])) ?>" 
                   data-descripcion="<?= htmlspecialchars(strtolower($actividad['descripcion'])) ?>">
                <div class="actividad-card-header">
                  <div class="actividad-tipo-badge <?= $tipoClase ?>">
                    <i class="<?= obtenerIconoTipo($actividad['tipo']) ?>"></i>
                    <?= $tipoTexto ?>
                  </div>
                  <div class="actividad-estado-badge <?= $estadoClase ?>">
                    <?= ucfirst($actividad['estado']) ?>
                  </div>
                </div>

                <div class="actividad-card-body">
                  <h3><?= htmlspecialchars($actividad['titulo']) ?></h3>
                  <p><?= htmlspecialchars($actividad['descripcion']) ?></p>

                  <div class="actividad-meta-grid">
                    <div class="actividad-meta-item">
                      <i class="ri-book-line"></i>
                      <span><strong><?= $actividad['grado'] ?>° <?= $actividad['curso'] ?></strong></span>
                    </div>
                    <div class="actividad-meta-item">
                      <i class="ri-calendar-line"></i>
                      <span><strong><?= date('Y', strtotime($actividad['fecha_entrega'])) ?></strong></span>
                    </div>
                    <div class="actividad-meta-item">
                      <i class="ri-calendar-check-line"></i>
                      <span>Cierre: <strong><?= formatearFecha($actividad['fecha_entrega']) ?></strong></span>
                    </div>
                    <div class="actividad-meta-item">
                      <i class="ri-percent-line"></i>
                      <span>Valor: <strong><?= number_format($actividad['ponderacion'], 0) ?>%</strong></span>
                    </div>
                  </div>

                  <div class="actividad-progreso-section">
                    <div class="actividad-progreso-header">
                      <small>Entregas recibidas</small>
                      <div class="actividad-progreso-stats">
                        <span class="entregadas">0/0</span>
                        <span class="pendientes">0 pendientes</span>
                      </div>
                    </div>
                    <div class="actividad-progreso-bar">
                      <div class="actividad-progreso-fill" style="width: 0%"></div>
                    </div>
                  </div>
                </div>

                <div class="actividad-card-footer">
                  <button class="btn-actividad-primary btn-ver-entregas" data-id="<?= $actividad['id'] ?>">
                    <i class="ri-file-list-3-line"></i>
                    Ver Entregas
                  </button>
                  <button class="btn-actividad-secondary btn-editar-actividad"
                          data-id="<?= $actividad['id'] ?>"
                          data-titulo="<?= htmlspecialchars($actividad['titulo'], ENT_QUOTES, 'UTF-8') ?>"
                          data-tipo="<?= htmlspecialchars($actividad['tipo'], ENT_QUOTES, 'UTF-8') ?>"
                          data-descripcion="<?= htmlspecialchars($actividad['descripcion'], ENT_QUOTES, 'UTF-8') ?>"
                          data-ponderacion="<?= $actividad['ponderacion'] ?>"
                          data-fecha="<?= $actividad['fecha_entrega'] ?>"
                      data-estado-act="<?= $actividad['estado'] ?>"
                      data-archivo="<?= htmlspecialchars($actividad['archivo'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                    <i class="ri-edit-line"></i>
                    Editar
                  </button>
                </div>
              </div>
            <?php endforeach; ?>
          <?php endif; ?>

        </section>

        <!-- TABLA DE ACTIVIDADES (inicialmente oculta) -->
        <section class="actividades-tabla-container" id="actividadesTabla" style="display: none;">
          <div class="datatable-card">
            <div class="table-responsive">
              <table class="table table-dark table-hover align-middle" id="tablaActividades">
                <thead>
                  <tr>
                    <th style="width: 80px;">Tipo</th>
                    <th>Título</th>
                    <th style="width: 100px;">Curso</th>
                    <th style="width: 120px;">Período</th>
                    <th style="width: 120px;">Fecha Cierre</th>
                    <th style="width: 100px;" class="text-center">Valor</th>
                    <th style="width: 140px;" class="text-center">Entregas</th>
                    <th style="width: 120px;" class="text-center">Estado</th>
                    <th style="width: 150px;" class="text-center">Acciones</th>
                  </tr>
                </thead>
                <tbody id="tbodyActividades">
                  
                  <?php if (empty($actividades)): ?>
                    <tr>
                      <td colspan="9" class="text-center" style="padding: 40px; color: #97a1b6;">
                        <i class="ri-file-list-line" style="font-size: 48px; opacity: 0.5;"></i>
                        <p style="margin-top: 16px; font-size: 16px;">No hay actividades registradas para este curso</p>
                        <a href="<?= BASE_URL ?>/docente/agregar-actividad?id_curso=<?= $id_curso_seleccionado ?>" class="btn btn-primary mt-3">
                          <i class="ri-add-line"></i> Crear primera actividad
                        </a>
                      </td>
                    </tr>
                  <?php else: ?>
                    <?php foreach ($actividades as $actividad): ?>
                      <tr data-estado="<?= strtolower($actividad['estado']) ?>" 
                          data-periodo="<?= date('Y', strtotime($actividad['fecha_entrega'])) ?>"
                          data-curso="<?= $actividad['grado'] . strtolower($actividad['curso']) ?>"
                          data-titulo="<?= htmlspecialchars(strtolower($actividad['titulo'])) ?>">
                        <td>
                          <span class="tabla-tipo-badge <?= strtolower($actividad['tipo']) ?>">
                            <i class="<?= obtenerIconoTipo($actividad['tipo']) ?>"></i>
                          </span>
                        </td>
                        <td>
                          <strong style="color: #fff;"><?= htmlspecialchars($actividad['titulo']) ?></strong>
                          <br>
                          <small style="color: #97a1b6;"><?= htmlspecialchars(substr($actividad['descripcion'], 0, 60)) ?><?= strlen($actividad['descripcion']) > 60 ? '...' : '' ?></small>
                        </td>
                        <td><strong style="color: #a4b1ff;"><?= $actividad['grado'] ?>° <?= $actividad['curso'] ?></strong></td>
                        <td><small style="color: #c7cbe1;"><?= date('Y', strtotime($actividad['fecha_entrega'])) ?></small></td>
                        <td><small style="color: #c7cbe1;"><?= formatearFecha($actividad['fecha_entrega']) ?></small></td>
                        <td class="text-center">
                          <span class="tabla-valor-badge"><?= number_format($actividad['ponderacion'], 0) ?>%</span>
                        </td>
                        <td class="text-center">
                          <div style="display: flex; flex-direction: column; gap: 4px; align-items: center;">
                            <span style="color: #97a1b6; font-weight: 600;">0/0</span>
                            <small style="color: #97a1b6;">--%</small>
                          </div>
                        </td>
                        <td class="text-center">
                          <?php 
                            $estadoClase = strtolower($actividad['estado']);
                            $estadoTexto = ucfirst($actividad['estado']);
                          ?>
                          <span class="actividad-estado-badge <?= $estadoClase ?>"><?= $estadoTexto ?></span>
                        </td>
                        <td class="text-center">
                          <button class="btn-tabla-action btn-ver-entregas" title="Ver entregas" data-id="<?= $actividad['id'] ?>">
                            <i class="ri-file-list-3-line"></i>
                          </button>
                          <button class="btn-tabla-action btn-editar-actividad" title="Editar"
                                  data-id="<?= $actividad['id'] ?>"
                                  data-titulo="<?= htmlspecialchars($actividad['titulo'], ENT_QUOTES, 'UTF-8') ?>"
                                  data-tipo="<?= htmlspecialchars($actividad['tipo'], ENT_QUOTES, 'UTF-8') ?>"
                                  data-descripcion="<?= htmlspecialchars($actividad['descripcion'], ENT_QUOTES, 'UTF-8') ?>"
                                  data-ponderacion="<?= $actividad['ponderacion'] ?>"
                                  data-fecha="<?= $actividad['fecha_entrega'] ?>"
                              data-estado-act="<?= $actividad['estado'] ?>"
                              data-archivo="<?= htmlspecialchars($actividad['archivo'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                            <i class="ri-edit-line"></i>
                          </button>
                        </td>
                      </tr>
                    <?php endforeach; ?>
                  <?php endif; ?>
                  
                  <?php /* EJEMPLOS ESTÁTICOS COMENTADOS - BORRAR DESPUÉS
                  <!-- FILA 1 -->
                  <tr data-estado="activa" data-periodo="1" data-curso="10a" 
                      data-titulo="taller de ecuaciones cuadráticas" 
                      data-descripcion="resolver 20 ejercicios de ecuaciones cuadráticas">
                    <td>
                      <span class="tabla-tipo-badge taller">
                        <i class="ri-file-text-line"></i>
                      </span>
                    </td>
                    <td>
                      <strong style="color: #fff;">Taller de Ecuaciones Cuadráticas</strong>
                      <br>
                      <small style="color: #97a1b6;">Resolver 20 ejercicios aplicando fórmula general</small>
                    </td>
                    <td><strong style="color: #a4b1ff;">10° A</strong></td>
                    <td><small style="color: #c7cbe1;">Período 1 - 2025</small></td>
                    <td><small style="color: #c7cbe1;">25 Ene 2025</small></td>
                    <td class="text-center">
                      <span class="tabla-valor-badge">20%</span>
                    </td>
                    <td class="text-center">
                      <div style="display: flex; flex-direction: column; gap: 4px; align-items: center;">
                        <span style="color: #4ade80; font-weight: 600;">28/32</span>
                        <small style="color: #97a1b6;">87%</small>
                      </div>
                    </td>
                    <td class="text-center">
                      <span class="actividad-estado-badge activa">Activa</span>
                    </td>
                    <td class="text-center">
                      <button class="btn-tabla-action btn-ver-detalle" title="Ver detalle">
                        <i class="ri-eye-line"></i>
                      </button>
                      <button class="btn-tabla-action btn-editar-actividad" title="Editar">
                        <i class="ri-edit-line"></i>
                      </button>
                    </td>
                  </tr>

                  <!-- FILA 2 -->
                  <tr data-estado="activa" data-periodo="1" data-curso="10b" 
                      data-titulo="proyecto final aplicaciones de derivadas" 
                      data-descripcion="investigar y presentar 3 aplicaciones reales">
                    <td>
                      <span class="tabla-tipo-badge proyecto">
                        <i class="ri-folder-line"></i>
                      </span>
                    </td>
                    <td>
                      <strong style="color: #fff;">Proyecto Final: Aplicaciones de Derivadas</strong>
                      <br>
                      <small style="color: #97a1b6;">Investigar 3 aplicaciones reales en ingeniería</small>
                    </td>
                    <td><strong style="color: #a4b1ff;">10° B</strong></td>
                    <td><small style="color: #c7cbe1;">Período 1 - 2025</small></td>
                    <td><small style="color: #c7cbe1;">10 Feb 2025</small></td>
                    <td class="text-center">
                      <span class="tabla-valor-badge">35%</span>
                    </td>
                    <td class="text-center">
                      <div style="display: flex; flex-direction: column; gap: 4px; align-items: center;">
                        <span style="color: #4ade80; font-weight: 600;">22/30</span>
                        <small style="color: #97a1b6;">73%</small>
                      </div>
                    </td>
                    <td class="text-center">
                      <span class="actividad-estado-badge activa">Activa</span>
                    </td>
                    <td class="text-center">
                      <button class="btn-tabla-action btn-ver-detalle" title="Ver detalle">
                        <i class="ri-eye-line"></i>
                      </button>
                      <button class="btn-tabla-action btn-editar-actividad" title="Editar">
                        <i class="ri-edit-line"></i>
                      </button>
                    </td>
                  </tr>

                  <!-- FILA 3 -->
                  <tr data-estado="cerradas" data-periodo="1" data-curso="9a" 
                      data-titulo="quiz trigonometría" 
                      data-descripcion="evaluación de identidades trigonométricas">
                    <td>
                      <span class="tabla-tipo-badge quiz">
                        <i class="ri-file-list-3-line"></i>
                      </span>
                    </td>
                    <td>
                      <strong style="color: #fff;">Quiz Trigonometría</strong>
                      <br>
                      <small style="color: #97a1b6;">Evaluación de identidades y resolución de triángulos</small>
                    </td>
                    <td><strong style="color: #a4b1ff;">9° A</strong></td>
                    <td><small style="color: #c7cbe1;">Período 1 - 2025</small></td>
                    <td><small style="color: #c7cbe1;">20 Ene 2025</small></td>
                    <td class="text-center">
                      <span class="tabla-valor-badge">15%</span>
                    </td>
                    <td class="text-center">
                      <div style="display: flex; flex-direction: column; gap: 4px; align-items: center;">
                        <span style="color: #4ade80; font-weight: 600;">28/28</span>
                        <small style="color: #97a1b6;">100%</small>
                      </div>
                    </td>
                    <td class="text-center">
                      <span class="actividad-estado-badge cerrada">Cerrada</span>
                    </td>
                    <td class="text-center">
                      <button class="btn-tabla-action btn-ver-detalle" title="Ver detalle">
                        <i class="ri-eye-line"></i>
                      </button>
                      <button class="btn-tabla-action btn-editar-actividad" title="Editar">
                        <i class="ri-edit-line"></i>
                      </button>
                    </td>
                  </tr>

                  <!-- FILA 4 -->
                  <tr data-estado="activas" data-periodo="1" data-curso="10a" 
                      data-titulo="tarea gráficas de funciones" 
                      data-descripcion="graficar 10 funciones diferentes">
                    <td>
                      <span class="tabla-tipo-badge tarea">
                        <i class="ri-file-edit-line"></i>
                      </span>
                    </td>
                    <td>
                      <strong style="color: #fff;">Tarea: Gráficas de Funciones</strong>
                      <br>
                      <small style="color: #97a1b6;">Graficar 10 funciones identificando dominio y rango</small>
                    </td>
                    <td><strong style="color: #a4b1ff;">10° A</strong></td>
                    <td><small style="color: #c7cbe1;">Período 1 - 2025</small></td>
                    <td><small style="color: #c7cbe1;">29 Ene 2025</small></td>
                    <td class="text-center">
                      <span class="tabla-valor-badge">10%</span>
                    </td>
                    <td class="text-center">
                      <div style="display: flex; flex-direction: column; gap: 4px; align-items: center;">
                        <span style="color: #fbbf24; font-weight: 600;">15/32</span>
                        <small style="color: #97a1b6;">47%</small>
                      </div>
                    </td>
                    <td class="text-center">
                      <span class="actividad-estado-badge activa">Activa</span>
                    </td>
                    <td class="text-center">
                      <button class="btn-tabla-action btn-ver-detalle" title="Ver detalle">
                        <i class="ri-eye-line"></i>
                      </button>
                      <button class="btn-tabla-action btn-editar-actividad" title="Editar">
                        <i class="ri-edit-line"></i>
                      </button>
                    </td>
                  </tr>

                  <!-- FILA 5 -->
                  <tr data-estado="por-calificar" data-periodo="1" data-curso="10b" 
                      data-titulo="examen parcial álgebra y funciones" 
                      data-descripcion="evaluación escrita sobre todos los temas">
                    <td>
                      <span class="tabla-tipo-badge examen">
                        <i class="ri-file-paper-line"></i>
                      </span>
                    </td>
                    <td>
                      <strong style="color: #fff;">Examen Parcial: Álgebra y Funciones</strong>
                      <br>
                      <small style="color: #97a1b6;">Evaluación escrita del primer período</small>
                    </td>
                    <td><strong style="color: #a4b1ff;">10° B</strong></td>
                    <td><small style="color: #c7cbe1;">Período 1 - 2025</small></td>
                    <td><small style="color: #c7cbe1;">28 Ene 2025</small></td>
                    <td class="text-center">
                      <span class="tabla-valor-badge">40%</span>
                    </td>
                    <td class="text-center">
                      <div style="display: flex; flex-direction: column; gap: 4px; align-items: center;">
                        <span style="color: #4ade80; font-weight: 600;">30/30</span>
                        <small style="color: #97a1b6;">100%</small>
                      </div>
                    </td>
                    <td class="text-center">
                      <span class="actividad-estado-badge por-calificar">Por Calificar</span>
                    </td>
                    <td class="text-center">
                      <button class="btn-tabla-action btn-ver-detalle" title="Ver detalle">
                        <i class="ri-eye-line"></i>
                      </button>
                      <button class="btn-tabla-action btn-editar-actividad" title="Editar">
                        <i class="ri-edit-line"></i>
                      </button>
                    </td>
                  </tr>

                  <!-- FILA 6 -->
                  <tr data-estado="calificadas" data-periodo="1" data-curso="9a" 
                      data-titulo="taller geometría analítica" 
                      data-descripcion="ejercicios sobre rectas circunferencias">
                    <td>
                      <span class="tabla-tipo-badge taller">
                        <i class="ri-file-text-line"></i>
                      </span>
                    </td>
                    <td>
                      <strong style="color: #fff;">Taller de Geometría Analítica</strong>
                      <br>
                      <small style="color: #97a1b6;">Ejercicios sobre rectas y parábolas</small>
                    </td>
                    <td><strong style="color: #a4b1ff;">9° A</strong></td>
                    <td><small style="color: #c7cbe1;">Período 1 - 2025</small></td>
                    <td><small style="color: #c7cbe1;">26 Ene 2025</small></td>
                    <td class="text-center">
                      <span class="tabla-valor-badge">25%</span>
                    </td>
                    <td class="text-center">
                      <div style="display: flex; flex-direction: column; gap: 4px; align-items: center;">
                        <span style="color: #4ade80; font-weight: 600;">26/28</span>
                        <small style="color: #97a1b6;">93%</small>
                      </div>
                    </td>
                    <td class="text-center">
                      <span class="actividad-estado-badge calificada">Calificada</span>
                    </td>
                    <td class="text-center">
                      <button class="btn-tabla-action btn-ver-detalle" title="Ver detalle">
                        <i class="ri-eye-line"></i>
                      </button>
                      <button class="btn-tabla-action btn-editar-actividad" title="Editar">
                        <i class="ri-edit-line"></i>
                      </button>
                    </td>
                  </tr>
                  */ ?>

                </tbody>
              </table>
            </div>
          </div>
        </section>

      </main>

    </div>

    

    <!-- MODAL EDITAR ACTIVIDAD -->
    <div class="modal fade" id="modalEditarActividad" tabindex="-1" aria-labelledby="modalEditarLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg">
        <div class="modal-content" style="background:#11193a;border:1px solid #1e2d5a;color:#e6e9f4;">
          <div class="modal-header" style="border-bottom:1px solid #1e2d5a;">
            <h5 class="modal-title" id="modalEditarLabel"><i class="ri-edit-line"></i> Editar Actividad</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
          </div>
          <form id="formEditarActividad" action="<?= BASE_URL ?>/docente/actualizar_actividad" method="POST" enctype="multipart/form-data">
            <div class="modal-body">
              <input type="hidden" name="id" id="editId">
              <input type="hidden" name="id_curso" id="editIdCurso" value="<?= htmlspecialchars($id_curso_seleccionado ?? '', ENT_QUOTES, 'UTF-8') ?>">
              <input type="hidden" name="archivo_actual" id="editArchivoActual" value="">
              <div class="row g-3">
                <div class="col-md-8">
                  <label class="form-label">Título *</label>
                  <input type="text" class="form-control" name="titulo_actividad" id="editTitulo" required>
                </div>
                <div class="col-md-4">
                  <label class="form-label">Tipo *</label>
                  <select class="form-select" name="tipo_actividad" id="editTipo" required>
                    <option value="Taller">Taller</option>
                    <option value="Quiz">Quiz</option>
                    <option value="Examen">Examen</option>
                    <option value="Proyecto">Proyecto</option>
                    <option value="Exposición">Exposición</option>
                    <option value="Laboratorio">Laboratorio</option>
                    <option value="Tarea">Tarea</option>
                  </select>
                </div>
                <div class="col-md-12">
                  <label class="form-label">Descripción</label>
                  <textarea class="form-control" name="descripcion" id="editDescripcion" rows="3"></textarea>
                </div>
                <div class="col-md-4">
                  <label class="form-label">Ponderación (%) *</label>
                  <input type="number" class="form-control" name="ponderacion" id="editPonderacion" min="0" max="100" step="0.01" required>
                </div>
                <div class="col-md-4">
                  <label class="form-label">Fecha de entrega *</label>
                  <input type="date" class="form-control" name="fecha_entrega" id="editFechaEntrega" required>
                </div>
                <div class="col-md-4">
                  <label class="form-label">Estado *</label>
                  <select class="form-select" name="estado" id="editEstado" required>
                    <option value="activa">Activa</option>
                    <option value="cerrada">Cerrada</option>
                  </select>
                </div>
                <div class="col-md-12">
                  <label class="form-label">Archivo adjunto (opcional)</label>
                  <input type="file" class="form-control" name="archivo_actividad" id="editArchivoActividad" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx">
                  <small class="text-muted" id="editArchivoNombre">Archivo actual: ninguno</small>
                </div>
              </div>
            </div>
            <div class="modal-footer" style="border-top:1px solid #1e2d5a;">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
              <button type="submit" class="btn btn-primary"><i class="ri-save-line"></i> Guardar cambios</button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
      <script src="<?= BASE_URL ?>/public/assets/dashboard/js/docente/actividades.js"></script>


  </body>
</html>