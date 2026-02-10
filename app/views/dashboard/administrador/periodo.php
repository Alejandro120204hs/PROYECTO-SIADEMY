<?php 
  require_once BASE_PATH . '/app/helpers/session_administrador.php';
  require_once BASE_PATH . '/app/models/administradores/periodo.php';
  require_once BASE_PATH . '/app/controllers/perfil.php';
  
  // LLAMAMOS EL ID QUE VIENE ATRAVEZ DEL METODO GET
  $id = $_SESSION['user']['id'];
  // LLAMAMOS LA FUNCION ESPECIFICA DEL CONTROLADOR
  $usuario = mostrarPerfil($id);

  // Obtener datos de periodos
  $id_institucion = $_SESSION['user']['id_institucion'];
  $objetoPeriodo = new Periodo();
  
  // Obtener KPIs
  $kpis = $objetoPeriodo->obtenerKPIs($id_institucion);
  
  // Obtener periodo activo
  $periodoActivo = $objetoPeriodo->obtenerPeriodoActivo($id_institucion);
  
  // Obtener años lectivos disponibles
  $todosLosPeriodos = $objetoPeriodo->listar($id_institucion);
  $anosDisponibles = [];
  foreach($todosLosPeriodos as $periodo){
    if(!in_array($periodo['ano_lectivo'], $anosDisponibles)){
      $anosDisponibles[] = $periodo['ano_lectivo'];
    }
  }
  sort($anosDisponibles, SORT_NUMERIC);
  $anosDisponibles = array_reverse($anosDisponibles);
  
  // Año por defecto es el actual o el más reciente
  $anoActual = isset($_GET['ano']) ? $_GET['ano'] : (end($anosDisponibles) ?: date('Y'));
  
  // Filtrar periodos por año
  $periodosDelAno = array_filter($todosLosPeriodos, function($p) use ($anoActual) {
    return $p['ano_lectivo'] == $anoActual;
  });
?>

<!doctype html>
<html lang="es">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>SIADEMY • Periodos Académicos</title>
  <?php include_once __DIR__ . '/../../layouts/header_coordinador.php' ?>
  <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/dashboard/css/styles-admin.css">
  <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/dashboard/css/styles-periodos.css">
</head>

<body>
  <div class="app hide-right" id="appGrid">

    <!-- LEFT SIDEBAR -->
    <?php include_once __DIR__ . '/../../layouts/sidebar_coordinador.php'; ?>

    <!-- MAIN -->
    <main class="main">

      <!-- TOPBAR -->
      <div class="topbar">
        <div class="topbar-left">
          <button class="toggle-btn" id="toggleLeft" title="Mostrar/Ocultar menú lateral">
            <i class="ri-menu-2-line"></i>
          </button>
          <div class="title">Periodos Académicos</div>
        </div>
        <div class="search">
          <i class="ri-search-2-line"></i>
          <input type="text" placeholder="Buscar periodo...">
        </div>
        <button class="btn-agregar-periodo" onclick="abrirModalCrear()">
          <i class="ri-add-line"></i> Agregar Periodo
        </button>
          <?php
          include_once BASE_PATH . '/app/views/layouts/boton_perfil_solo.php'
        ?>
    
      </div>

      <!-- KPI CARDS -->
      <section class="kpis">
        <div class="kpi">
          <div class="icon"><i class="ri-calendar-2-line"></i></div>
          <div>
            <small>Total Periodos</small>
            <strong><?php echo $kpis['total']; ?></strong>
          </div>
        </div>
        <div class="kpi">
          <div class="icon"><i class="ri-play-circle-line"></i></div>
          <div>
            <small>Periodo Activo</small>
            <strong><?php echo $kpis['activos']; ?></strong>
          </div>
        </div>
        <div class="kpi">
          <div class="icon"><i class="ri-time-line"></i></div>
          <div>
            <small>Próximos</small>
            <strong><?php echo $kpis['proximos']; ?></strong>
          </div>
        </div>
        <div class="kpi">
          <div class="icon"><i class="ri-checkbox-circle-line"></i></div>
          <div>
            <small>Finalizados</small>
            <strong><?php echo $kpis['finalizados']; ?></strong>
          </div>
        </div>
      </section>

      <!-- PERIODO ACTIVO DESTACADO -->
      <section class="periodo-activo-banner">
        <?php if($periodoActivo): 
          $inicio = new DateTime($periodoActivo['fecha_inicio']);
          $fin = new DateTime($periodoActivo['fecha_fin']);
          $ahora = new DateTime();
          $intervalo = $ahora->diff($fin);
          $diasRestantes = $intervalo->days;
          
          $totalDias = $inicio->diff($fin)->days;
          $diasRecorridos = $inicio->diff($ahora)->days;
          $porcentaje = round(($diasRecorridos / $totalDias) * 100);
        ?>
        <div class="banner-left">
          <div class="banner-icon">
            <i class="ri-calendar-check-line"></i>
          </div>
          <div class="banner-info">
            <span class="banner-label">Periodo Activo Actualmente</span>
            <h3 class="banner-title"><?php echo htmlspecialchars($periodoActivo['nombre']); ?> · <?php echo $periodoActivo['ano_lectivo']; ?></h3>
            <div class="banner-fechas">
              <i class="ri-calendar-line"></i>
              <span><?php echo date('j M Y', strtotime($periodoActivo['fecha_inicio'])); ?> &nbsp;→&nbsp; <?php echo date('j M Y', strtotime($periodoActivo['fecha_fin'])); ?></span>
              <span class="banner-duracion"><?php echo $totalDias; ?> días</span>
            </div>
          </div>
        </div>
        <div class="banner-right">
          <div class="banner-progress-wrap">
            <div class="banner-progress-label">
              <span>Progreso del periodo</span>
              <strong><?php echo $porcentaje; ?>%</strong>
            </div>
            <div class="banner-progress-bar">
              <div class="banner-progress-fill" style="width: <?php echo $porcentaje; ?>%"></div>
            </div>
            <span class="banner-dias-restantes"><?php echo $diasRestantes; ?> días restantes</span>
          </div>
        </div>
        <?php else: ?>
        <div style="padding: 20px; text-align: center;">
          <p>No hay un periodo activo actualmente. Por favor activa uno.</p>
        </div>
        <?php endif; ?>
      </section>

      <!-- FILTRO AÑO -->
      <section class="periodos-filter-bar">
        <div class="filter-year-group">
          <label><i class="ri-filter-3-line"></i> Año lectivo:</label>
          <select class="periodo-select" id="selectAno" onchange="cambiarAno(this.value)">
            <?php 
              foreach($anosDisponibles as $ano):
                $selected = $ano == $anoActual ? 'selected' : '';
            ?>
              <option value="<?php echo $ano; ?>" <?php echo $selected; ?>><?php echo $ano; ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="filter-status-group">
          <button class="filter-chip active" data-filter="todos">Todos</button>
          <button class="filter-chip" data-filter="en_curso">Activo</button>
          <button class="filter-chip" data-filter="planificado">Próximos</button>
          <button class="filter-chip" data-filter="finalizado">Finalizados</button>
        </div>
      </section>

      <!-- LISTA DE PERIODOS -->
      <section class="periodos-section">
        <div class="periodos-header">
          <h3>Periodos Registrados <span class="periodos-count"><?php echo count($periodosDelAno); ?></span></h3>
        </div>

        <div class="periodos-list" id="periodosList">
          <?php 
            if(count($periodosDelAno) > 0):
              foreach($periodosDelAno as $periodo):
                $estado = $periodo['estado'];
                $activo = $periodo['activo'] == 1;
                $inicio = new DateTime($periodo['fecha_inicio']);
                $fin = new DateTime($periodo['fecha_fin']);
                $diasDiferencia = $inicio->diff($fin)->days;
          ?>
          <div class="periodo-card <?php echo $activo ? 'activo' : ''; ?>" data-estado="<?php echo $estado; ?>" data-id="<?php echo $periodo['id']; ?>">
            <?php if($activo): ?>
            <div class="periodo-activo-indicator"></div>
            <?php endif; ?>
            <div class="periodo-card-left">
              <div class="periodo-numero <?php echo $estado; ?>">
                <span><?php echo $periodo['numero_periodo']; ?></span>
              </div>
              <div class="periodo-info">
                <div class="periodo-nombre-row">
                  <h4><?php echo htmlspecialchars($periodo['nombre']); ?></h4>
                  <span class="periodo-badge <?php echo $estado; ?>">
                    <?php 
                      if($estado == 'en_curso'): 
                        echo '<i class="ri-radio-button-line"></i> Activo';
                      elseif($estado == 'planificado'):
                        echo '<i class="ri-time-line"></i> Próximo';
                      else:
                        echo '<i class="ri-checkbox-circle-fill"></i> Finalizado';
                      endif;
                    ?>
                  </span>
                </div>
                <div class="periodo-meta">
                  <span><i class="ri-calendar-line"></i> <?php echo date('j M Y', strtotime($periodo['fecha_inicio'])); ?> &nbsp;→&nbsp; <?php echo date('j M Y', strtotime($periodo['fecha_fin'])); ?></span>
                  <span class="periodo-sep">·</span>
                  <span><i class="ri-time-line"></i> <?php echo $diasDiferencia; ?> días</span>
                  <span class="periodo-sep">·</span>
                  <span><i class="ri-book-open-line"></i> <?php echo $periodo['tipo_periodo']; ?></span>
                </div>
                <?php if($activo): ?>
                <div class="periodo-progress-mini">
                  <div class="periodo-progress-fill-mini" style="width: <?php echo $porcentaje ?? 50; ?>%"></div>
                </div>
                <?php endif; ?>
              </div>
            </div>
            <div class="periodo-card-right">
              <div class="periodo-actions">
                <button class="btn-periodo-action btn-ver" title="Ver detalles" onclick="verDetallesPeriodo(<?php echo $periodo['id']; ?>)">
                  <i class="ri-eye-line"></i>
                </button>
                <button class="btn-periodo-action btn-editar" title="Editar" onclick="abrirModalEditar(<?php echo $periodo['id']; ?>)">
                  <i class="ri-edit-line"></i>
                </button>
                <?php if(!$activo && $estado != 'finalizado'): ?>
                <button class="btn-periodo-action btn-activar" title="Activar periodo" onclick="abrirModalActivar(<?php echo $periodo['id']; ?>, '<?php echo htmlspecialchars($periodo['nombre']); ?>')">
                  <i class="ri-play-circle-line"></i> Activar
                </button>
                <?php endif; ?>
                <button class="btn-periodo-action btn-eliminar" title="Eliminar" onclick="confirmarEliminacion(<?php echo $periodo['id']; ?>, '<?php echo htmlspecialchars($periodo['nombre']); ?>')">
                  <i class="ri-delete-bin-line"></i>
                </button>
              </div>
            </div>
          </div>
          <?php 
              endforeach;
            else:
          ?>
          <div style="padding: 40px; text-align: center; color: #999;">
            <i class="ri-inbox-line" style="font-size: 48px; display: block; margin-bottom: 10px;"></i>
            <p>No hay periodos registrados para el año <?php echo $anoActual; ?></p>
          </div>
          <?php endif; ?>
        </div>
      </section>

    </main>
  </div>

  <!-- ============================= -->
  <!-- MODAL: AGREGAR / EDITAR PERIODO -->
  <!-- ============================= -->
  <div class="periodo-modal-overlay" id="modalOverlay">
    <div class="periodo-modal">
      <div class="periodo-modal-header">
        <h3 id="modalTitulo"><i class="ri-add-circle-line"></i> Agregar Periodo</h3>
        <button class="modal-close-btn" onclick="cerrarModal()">
          <i class="ri-close-line"></i>
        </button>
      </div>

      <div class="periodo-modal-body">
        <form id="formPeriodo" action="<?= BASE_URL ?>/administrador/guardar-periodo" method="POST">
          <input type="hidden" name="id" id="inputId">
          <input type="hidden" name="accion" id="inputAccion" value="">

          <!-- Tipo de periodo -->
          <div class="form-group-periodo">
            <label for="inputTipo">Tipo de Periodo <span class="req">*</span></label>
            <select name="tipo_periodo" id="inputTipo" class="form-input-periodo" required onchange="actualizarNombre()">
              <option value="" disabled selected>Selecciona el tipo</option>
              <option value="bimestre">Bimestre</option>
              <option value="trimestre">Trimestre</option>
              <option value="semestre">Semestre</option>
              <option value="anual">Anual</option>
            </select>
          </div>

          <!-- Número de periodo -->
          <div class="form-group-periodo">
            <label for="inputNumero">Número del Periodo <span class="req">*</span></label>
            <select name="numero_periodo" id="inputNumero" class="form-input-periodo" required onchange="actualizarNombre()">
              <option value="" disabled selected>Selecciona el número</option>
              <option value="1">1</option>
              <option value="2">2</option>
              <option value="3">3</option>
              <option value="4">4</option>
              <option value="5">5</option>
              <option value="6">6</option>
            </select>
          </div>

          <!-- Vista previa del nombre generado -->
          <div class="nombre-preview" id="nombrePreview" style="display: none;">
            <i class="ri-eye-line"></i>
            <span>Nombre generado: <strong id="nombreGenerado"></strong></span>
          </div>

          <!-- Nombre custom (oculto pero enviable) -->
          <input type="hidden" name="nombre" id="inputNombre">

          <!-- Año lectivo -->
          <div class="form-group-periodo">
            <label for="inputAno">Año Lectivo <span class="req">*</span></label>
            <select name="ano_lectivo" id="inputAno" class="form-input-periodo" required>
              <option value="2025">2025</option>
              <option value="2026" selected>2026</option>
              <option value="2027">2027</option>
              <option value="2028">2028</option>
              <option value="2029">2029</option>
              <option value="2030">2030</option>
            </select>
          </div>

          <!-- Fechas -->
          <div class="form-row-periodo">
            <div class="form-group-periodo">
              <label for="inputInicio">Fecha de Inicio <span class="req">*</span></label>
              <input type="date" name="fecha_inicio" id="inputInicio" class="form-input-periodo" required onchange="calcularDuracion()">
            </div>
            <div class="form-group-periodo">
              <label for="inputFin">Fecha de Fin <span class="req">*</span></label>
              <input type="date" name="fecha_fin" id="inputFin" class="form-input-periodo" required onchange="calcularDuracion()">
            </div>
          </div>

          <!-- Duración calculada -->
          <div class="duracion-info" id="duracionInfo" style="display: none;">
            <i class="ri-information-line"></i>
            <span id="duracionTexto"></span>
          </div>

          <!-- Activar inmediatamente -->
          <div class="form-check-periodo" id="checkActivoContainer" style="display: none;">
            <label class="check-label">
              <input type="checkbox" name="activo" id="inputActivo">
              <span class="check-custom"></span>
              Activar este periodo inmediatamente
            </label>
            <small>Si lo activas, el periodo actual será desactivado automáticamente.</small>
          </div>

        </form>
      </div>

      <div class="periodo-modal-footer">
        <button class="btn-modal-cancelar" onclick="cerrarModal()">Cancelar</button>
        <button class="btn-modal-guardar" onclick="guardarPeriodo()">
          <i class="ri-save-line"></i> Guardar Periodo
        </button>
      </div>
    </div>
  </div>

  <!-- ============================= -->
  <!-- MODAL: CONFIRMAR ACTIVAR      -->
  <!-- ============================= -->
  <div class="periodo-modal-overlay" id="modalActivarOverlay">
    <div class="periodo-modal periodo-modal-sm">
      <div class="periodo-modal-header">
        <h3><i class="ri-play-circle-line"></i> Activar Periodo</h3>
        <button class="modal-close-btn" onclick="cerrarModalActivar()">
          <i class="ri-close-line"></i>
        </button>
      </div>
      <div class="periodo-modal-body">
        <div class="activar-confirm-content">
          <div class="activar-icon">
            <i class="ri-alert-line"></i>
          </div>
          <p>¿Deseas activar el <strong id="nombreActivar">Tercer Bimestre</strong>?</p>
          <div class="activar-consecuencias">
            <div class="consecuencia-item desactivar">
              <i class="ri-close-circle-line"></i>
              <span>Se desactivará: <strong>Segundo Bimestre</strong> (activo actualmente)</span>
            </div>
            <div class="consecuencia-item activar">
              <i class="ri-check-circle-line"></i>
              <span>Se activará: <strong id="nombreActivarConfirm">Tercer Bimestre</strong></span>
            </div>
            <div class="consecuencia-item info">
              <i class="ri-information-line"></i>
              <span>Los profesores podrán registrar notas en el nuevo periodo.</span>
            </div>
          </div>
        </div>
      </div>
      <div class="periodo-modal-footer">
        <button class="btn-modal-cancelar" onclick="cerrarModalActivar()">Cancelar</button>
        <button class="btn-modal-activar">
          <i class="ri-play-circle-line"></i> Sí, Activar
        </button>
      </div>
    </div>
  </div>

  <!-- Scripts -->
  <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="<?= BASE_URL ?>/public/assets/dashboard/js/main-admin.js"></script>

  <script>
    const baseUrl = '<?= BASE_URL ?>';
    
    // --- GUARDAR PERIODO ---
    function guardarPeriodo() {
      // Validar que el nombre esté lleno
      if(!document.getElementById('inputNombre').value) {
        alert('Por favor selecciona el tipo y número del período');
        return;
      }
      
      // Enviar formulario
      document.getElementById('formPeriodo').submit();
    }
    
    // --- MODAL CREAR/EDITAR ---
    function abrirModalCrear() {
      document.getElementById('modalTitulo').innerHTML = '<i class="ri-add-circle-line"></i> Agregar Periodo';
      document.getElementById('inputId').value = '';
      document.getElementById('inputAccion').value = '';
      document.getElementById('formPeriodo').reset();
      document.getElementById('formPeriodo').action = baseUrl + '/administrador/guardar-periodo';
      document.getElementById('nombrePreview').style.display = 'none';
      document.getElementById('duracionInfo').style.display = 'none';
      document.getElementById('checkActivoContainer').style.display = 'block';
      document.getElementById('inputNombre').value = '';
      document.getElementById('modalOverlay').classList.add('active');
    }

    function abrirModalEditar(id) {
      // Hacer petición AJAX para obtener los datos del período
      fetch(baseUrl + '/administrador/editar-periodo?accion=editar&id=' + id)
        .then(response => response.json())
        .then(data => {
          document.getElementById('modalTitulo').innerHTML = '<i class="ri-edit-circle-line"></i> Editar Periodo';
          document.getElementById('inputId').value = data.id;
          document.getElementById('inputTipo').value = data.tipo_periodo;
          document.getElementById('inputNumero').value = data.numero_periodo;
          document.getElementById('inputAno').value = data.ano_lectivo;
          document.getElementById('inputInicio').value = data.fecha_inicio;
          document.getElementById('inputFin').value = data.fecha_fin;
          document.getElementById('inputAccion').value = 'actualizar';
          document.getElementById('inputNombre').value = data.nombre;
          document.getElementById('formPeriodo').action = baseUrl + '/administrador/actualizar-periodo';
          document.getElementById('checkActivoContainer').style.display = 'none';
          
          actualizarNombre();
          calcularDuracion();
          document.getElementById('modalOverlay').classList.add('active');
        })
        .catch(error => {
          console.error('Error:', error);
          alert('Error al obtener los datos del período');
        });
    }

    function cerrarModal() {
      document.getElementById('modalOverlay').classList.remove('active');
    }

    // --- MODAL ACTIVAR ---
    function abrirModalActivar(id, nombre) {
      document.getElementById('nombreActivar').textContent = nombre;
      document.getElementById('nombreActivarConfirm').textContent = nombre;
      document.getElementById('modalActivarOverlay').classList.add('active');
      
      // Guardar el ID del período a activar
      document.querySelector('.btn-modal-activar').onclick = function() {
        window.location.href = baseUrl + '/administrador/activar-periodo?accion=activar&id=' + id;
      };
    }

    function cerrarModalActivar() {
      document.getElementById('modalActivarOverlay').classList.remove('active');
    }

    // --- CONFIRMAR ELIMINACIÓN ---
    function confirmarEliminacion(id, nombre) {
      if(confirm(`¿Estás seguro que deseas eliminar el período "${nombre}"?`)) {
        window.location.href = baseUrl + '/administrador/eliminar-periodo?accion=eliminar&id=' + id;
      }
    }

    function verDetallesPeriodo(id) {
      alert('Detalles del período ' + id);
      // Aquí puede implementarse una vista detallada en un modal
    }

    function cambiarAno(ano) {
      window.location.href = '<?= BASE_URL ?>/administrador-periodo?ano=' + ano;
    }

    // --- GENERAR NOMBRE PREVIEW ---
    function actualizarNombre() {
      const tipo = document.getElementById('inputTipo').value;
      const numero = document.getElementById('inputNumero').value;

      const numTexto = {
        '1': 'Primer', '2': 'Segundo', '3': 'Tercer',
        '4': 'Cuarto', '5': 'Quinto', '6': 'Sexto'
      };
      const tipoTexto = {
        'bimestre': 'Bimestre', 'trimestre': 'Trimestre',
        'semestre': 'Semestre', 'anual': 'Año Lectivo'
      };

      if (tipo && numero) {
        const nombre = `${numTexto[numero]} ${tipoTexto[tipo]}`;
        document.getElementById('nombreGenerado').textContent = nombre;
        document.getElementById('inputNombre').value = nombre;
        document.getElementById('nombrePreview').style.display = 'flex';
      }
    }

    // --- CALCULAR DURACIÓN ---
    function calcularDuracion() {
      const inicioVal = document.getElementById('inputInicio').value;
      const finVal = document.getElementById('inputFin').value;
      
      if(inicioVal && finVal) {
        const inicio = new Date(inicioVal);
        const fin = new Date(finVal);

        if (fin > inicio) {
          const dias = Math.floor((fin - inicio) / (1000 * 60 * 60 * 24));
          document.getElementById('duracionTexto').textContent = `Duración: ${dias} días`;
          document.getElementById('duracionInfo').style.display = 'flex';
        } else {
          document.getElementById('duracionInfo').style.display = 'none';
        }
      }
    }

    // --- CERRAR AL HACER CLICK FUERA ---
    document.getElementById('modalOverlay').addEventListener('click', function(e) {
      if (e.target === this) cerrarModal();
    });
    document.getElementById('modalActivarOverlay').addEventListener('click', function(e) {
      if (e.target === this) cerrarModalActivar();
    });

    // --- FILTROS ---
    document.querySelectorAll('.filter-chip').forEach(chip => {
      chip.addEventListener('click', function() {
        document.querySelectorAll('.filter-chip').forEach(c => c.classList.remove('active'));
        this.classList.add('active');

        const filter = this.dataset.filter;
        document.querySelectorAll('.periodo-card').forEach(card => {
          if (filter === 'todos' || card.dataset.estado === filter) {
            card.style.display = '';
          } else {
            card.style.display = 'none';
          }
        });
      });
    });
  </script>

</body>
</html>