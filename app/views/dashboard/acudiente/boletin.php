<?php
/**
 * Vista: Boletín Académico (Acudiente)
 * Variables disponibles (del controlador):
 *   $estudiantesAsociados    — estudiantes asociados al acudiente
 *   $estudianteSeleccionado  — estudiante activo (ya validado: pertenece al acudiente)
 *   $boletin_estudiante      — datos del estudiante (nombres, grado, curso, jornada, documento…)
 *   $boletin_periodos        — lista de períodos configurados para el año
 *   $boletin_por_periodo     — array: [ { periodo, materias, asistencia, promedio_periodo }, … ]
 *   $boletin_sin_datos       — bool: true cuando no hay períodos o estudiante no encontrado
 *   $periodoActivoDefault    — número del período que debe mostrarse abierto
 *   $anio                    — año lectivo actual
 */

// ── Helpers de clase para los badges ─────────────────────────────────────────
function bol_notaBadgeClass(string $estado): string {
    return match ($estado) {
        'superior' => 'nota-superior',
        'alto'     => 'nota-alto',
        'basico'   => 'nota-basico',
        'bajo'     => 'nota-bajo',
        default    => 'nota-sin',
    };
}

function bol_promClase(?float $promedio): string {
    if ($promedio === null) return 'prom-sinnota';
    if ($promedio >= 4.5)  return 'prom-superior';
    if ($promedio >= 4.0)  return 'prom-alto';
    if ($promedio > 3.0)   return 'prom-basico';
    return 'prom-bajo';
}
?>
<!doctype html>
<html lang="es">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>SIADEMY • Boletines</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/remixicon@4.3.0/fonts/remixicon.css">
  <?php include_once __DIR__ . '/../../layouts/header_coordinador.php' ?>
  <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/dashboard/css/styles-boletin.css">
  <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/dashboard/css/modo-claro-acudiente.css">
  <style>
    .card {
      background: #11193a;
      border: 1px solid var(--border);
      border-radius: 18px;
      padding: 20px;
      box-shadow: 0 12px 32px rgba(0, 0, 0, .25);
      margin-bottom: 24px;
    }

    .muted {
      color: #c6cada;
      font-size: 13px;
      margin: 0 2px 12px;
    }

    .students-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
      gap: 14px;
    }

    .student-card-form {
      display: contents;
    }

    .student-card {
      display: flex;
      align-items: center;
      gap: 12px;
      width: 100%;
      background: #171a28;
      border: 1px solid var(--border);
      border-radius: 14px;
      padding: 14px;
      text-align: left;
      color: #e6e9f4;
      font-family: inherit;
      cursor: pointer;
      transition: border-color .2s ease;
    }

    .student-card:hover {
      border-color: #4f46e5;
    }

    .student-card.active {
      border-color: #4f46e5;
      background: linear-gradient(135deg, rgba(79, 70, 229, .18), rgba(99, 102, 241, .08));
      cursor: default;
    }

    .student-card-avatar {
      width: 48px;
      height: 48px;
      border-radius: 50%;
      object-fit: cover;
      flex-shrink: 0;
      background: #2d3353;
    }

    .student-card-info {
      display: flex;
      flex-direction: column;
      gap: 2px;
      flex: 1;
      min-width: 0;
    }

    .student-card-info strong {
      font-size: 14px;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }

    .student-card-info small {
      font-size: 12px;
      color: #a4b1ff;
    }

    .student-card-badge {
      font-size: 11px;
      font-weight: 600;
      color: #22c55e;
      display: flex;
      align-items: center;
      gap: 4px;
      flex-shrink: 0;
      white-space: nowrap;
    }

    .empty-state {
      text-align: center;
      padding: 40px 20px;
      color: #c7cbe1;
    }

    .empty-state i {
      font-size: 48px;
      color: #4f46e5;
      margin-bottom: 12px;
      display: block;
    }

  </style>
</head>

<body>
<div class="app hide-right" id="appGrid">

  <!-- ── SIDEBAR ──────────────────────────────────────────────────────────── -->
  <?php include_once __DIR__ . '/../../layouts/sidebar_acudiente.php' ?>

  <!-- ── MAIN ─────────────────────────────────────────────────────────────── -->
  <main class="main">

    <!-- TOPBAR -->
    <div class="topbar">
      <div class="topbar-left">
        <button class="toggle-btn" id="toggleLeft" title="Mostrar/Ocultar menú lateral">
          <i class="ri-menu-2-line"></i>
        </button>
        <div class="title">Boletines</div>
      </div>
      <?php include_once BASE_PATH . '/app/views/layouts/boton_perfil_solo.php'; ?>
    </div>

    <?php if (empty($estudiantesAsociados)): ?>
      <section class="card">
        <div class="empty-state">
          <i class="ri-user-search-line"></i>
          <h3>No tienes estudiantes asociados</h3>
          <p>Si crees que esto es un error, comunícate con la institución para verificar la vinculación.</p>
        </div>
      </section>

    <?php else: ?>

      <?php include __DIR__ . '/../../layouts/mis_estudiantes_acudiente.php'; ?>

      <!-- ═══════════════════════════════════════════════════════════════════ -->
      <!-- SIN DATOS                                                          -->
      <!-- ═══════════════════════════════════════════════════════════════════ -->
      <?php if ($boletin_sin_datos): ?>
        <section class="card">
          <div class="empty-state">
            <i class="ri-file-paper-2-line"></i>
            <h3>Boletín no disponible</h3>
            <p>
              <?php if ($boletin_estudiante === null): ?>
                No se encontró la matrícula activa de <?= htmlspecialchars(trim($estudianteSeleccionado['nombres'] . ' ' . $estudianteSeleccionado['apellidos'])) ?> para el año <?= (int)$anio ?>.<br>
                Contacta al administrador de la institución.
              <?php else: ?>
                La institución aún no ha configurado los períodos académicos para <?= (int)$anio ?>.<br>
                Vuelve a consultar cuando estén listos.
              <?php endif; ?>
            </p>
          </div>
        </section>

      <?php else: ?>
      <!-- ═══════════════════════════════════════════════════════════════════ -->
      <!-- CON DATOS                                                          -->
      <!-- ═══════════════════════════════════════════════════════════════════ -->

        <!-- CABECERA DEL ESTUDIANTE -->
        <div class="student-header">

          <div class="student-avatar">
            <img src="<?= BASE_URL ?>/public/uploads/estudiantes/<?= htmlspecialchars($estudianteSeleccionado['foto'] ?: 'default.png') ?>" alt="" onerror="this.onerror=null; this.src='<?= BASE_URL ?>/public/uploads/estudiantes/default.png'" style="width:100%; height:100%; border-radius:50%; object-fit:cover;">
          </div>

          <div class="student-info">
            <h2><?= htmlspecialchars(trim($boletin_estudiante['nombres'] . ' ' . $boletin_estudiante['apellidos'])) ?></h2>
            <div class="student-meta">
              <span><i class="ri-graduation-cap-line"></i> Grado <?= htmlspecialchars($boletin_estudiante['grado']) ?></span>
              <span><i class="ri-group-line"></i> <?= htmlspecialchars($boletin_estudiante['curso']) ?></span>
              <?php if (!empty($boletin_estudiante['jornada'])): ?>
                <span><i class="ri-sun-line"></i> Jornada <?= htmlspecialchars($boletin_estudiante['jornada']) ?></span>
              <?php endif; ?>
              <?php if (!empty($boletin_estudiante['documento'])): ?>
                <span><i class="ri-id-card-line"></i> <?= htmlspecialchars($boletin_estudiante['documento']) ?></span>
              <?php endif; ?>
              <span><i class="ri-calendar-line"></i> Año lectivo <?= (int)$anio ?></span>
            </div>
          </div>

          <button class="btn-print" onclick="imprimirBoletin()">
            <i class="ri-printer-line"></i>
            <span>Imprimir Boletín</span>
          </button>

        </div>

        <!-- TABS DE PERÍODO -->
        <div class="period-tabs">
          <?php foreach ($boletin_por_periodo as $bloque): ?>
            <?php
              $numP    = (int)$bloque['periodo']['numero_periodo'];
              $promP   = $bloque['promedio_periodo'] !== null ? (float)$bloque['promedio_periodo'] : null;
              $isActive = ($numP === $periodoActivoDefault);
            ?>
            <button
              class="period-tab <?= $isActive ? 'active' : '' ?>"
              data-periodo="<?= $numP ?>"
              onclick="cambiarPeriodo(<?= $numP ?>)"
            >
              <i class="ri-calendar-2-line"></i>
              <?= htmlspecialchars($bloque['periodo']['nombre'] ?: 'Período ' . $numP) ?>
              <?php if ($promP !== null): ?>
                <span class="tab-avg"><?= number_format($promP, 1) ?></span>
              <?php endif; ?>
            </button>
          <?php endforeach; ?>
        </div>

        <!-- CONTENIDO POR PERÍODO -->
        <?php foreach ($boletin_por_periodo as $bloque): ?>
          <?php
            $numP       = (int)$bloque['periodo']['numero_periodo'];
            $periodo    = $bloque['periodo'];
            $materias   = $bloque['materias'];
            $asistencia = $bloque['asistencia'];
            $promP      = $bloque['promedio_periodo'] !== null ? (float)$bloque['promedio_periodo'] : null;
            $isActive   = ($numP === $periodoActivoDefault);
          ?>
          <div
            class="periodo-content <?= $isActive ? 'active' : '' ?>"
            id="periodo-<?= $numP ?>"
          >

            <!-- STATS DEL PERÍODO -->
            <div class="periodo-stats">

              <div class="stat-pill">
                <i class="ri-bar-chart-2-line"></i>
                <div>
                  <span class="stat-label">Promedio del período</span>
                  <strong class="stat-val <?= bol_promClase($promP) ?>">
                    <?= $promP !== null ? number_format($promP, 1) : 'Sin datos' ?>
                  </strong>
                </div>
              </div>

              <div class="stat-pill">
                <i class="ri-user-follow-line"></i>
                <div>
                  <span class="stat-label">Asistencia</span>
                  <strong class="stat-val">
                    <?php if ($asistencia['porcentaje_asistencia'] !== null): ?>
                      <?= number_format($asistencia['porcentaje_asistencia'], 1) ?>%
                    <?php else: ?>
                      Sin registro
                    <?php endif; ?>
                  </strong>
                </div>
              </div>

              <div class="stat-pill">
                <i class="ri-calendar-check-line"></i>
                <div>
                  <span class="stat-label">Clases / Presentes</span>
                  <strong class="stat-val">
                    <?= (int)$asistencia['presentes'] ?> / <?= (int)$asistencia['total_registros'] ?>
                  </strong>
                </div>
              </div>

              <div class="stat-pill">
                <i class="ri-calendar-event-line"></i>
                <div>
                  <span class="stat-label">Fechas del período</span>
                  <strong class="stat-val period-dates">
                    <?= date('d M', strtotime($periodo['fecha_inicio'])) ?>
                    —
                    <?= date('d M Y', strtotime($periodo['fecha_fin'])) ?>
                  </strong>
                </div>
              </div>

            </div><!-- /.periodo-stats -->

            <!-- TABLA DE MATERIAS -->
            <?php if (empty($materias)): ?>
              <div class="empty-period">
                <i class="ri-inbox-line"></i>
                <p>No hay actividades registradas en este período.</p>
              </div>
            <?php else: ?>
              <div class="materias-table-wrap">
                <table class="materias-table">
                  <thead>
                    <tr>
                      <th>Materia</th>
                      <th>Docente</th>
                      <th class="text-center">Actividades</th>
                      <th class="text-center">Promedio</th>
                      <th class="text-center">Estado</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($materias as $m): ?>
                      <?php
                        $prom        = $m['promedio'] !== null ? (float)$m['promedio'] : null;
                        $estadoNota  = $m['estado_nota']  ?? 'sin-nota';
                        $estadoLabel = $m['estado_label'] ?? 'Sin Nota';
                      ?>
                      <tr>
                        <td class="td-materia"><?= htmlspecialchars($m['materia']) ?></td>
                        <td class="td-docente"><?= htmlspecialchars($m['docente_nombre'] ?: '—') ?></td>
                        <td class="text-center">
                          <div class="eval-counts">
                            <span class="ec-ok"    title="Calificadas"><i class="ri-check-line"></i><?= (int)$m['actividades_calificadas'] ?></span>
                            <span class="ec-fail"  title="Vencidas sin entregar"><i class="ri-time-line"></i><?= (int)$m['actividades_vencidas'] ?></span>
                            <span class="ec-pending" title="Pendientes"><i class="ri-hourglass-line"></i><?= (int)$m['actividades_pendientes'] ?></span>
                          </div>
                        </td>
                        <td class="text-center">
                          <?php if ($prom !== null): ?>
                            <span class="nota-badge <?= bol_notaBadgeClass($estadoNota) ?>">
                              <?= number_format($prom, 1) ?>
                            </span>
                          <?php else: ?>
                            <span class="nota-badge nota-sin">S/N</span>
                          <?php endif; ?>
                        </td>
                        <td class="text-center">
                          <span class="estado-badge <?= htmlspecialchars($estadoNota) ?>">
                            <?= htmlspecialchars($estadoLabel) ?>
                          </span>
                        </td>
                      </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
              </div><!-- /.materias-table-wrap -->
            <?php endif; ?>

          </div><!-- /.periodo-content -->
        <?php endforeach; ?>

      <?php endif; /* $boletin_sin_datos */ ?>

    <?php endif; /* empty($estudiantesAsociados) */ ?>

  </main>
</div><!-- /#appGrid -->


<!-- ═══════════════════════════════════════════════════════════════════════ -->
<!-- SECCIÓN DE IMPRESIÓN — invisible en pantalla, visible en @media print  -->
<!-- ═══════════════════════════════════════════════════════════════════════ -->
<?php if (!$boletin_sin_datos): ?>
<div class="print-only">

  <!-- Encabezado -->
  <div class="print-header">
    <img src="<?= BASE_URL ?>/public/assets/extras/img/LOGO-NEGATIVO 1 (1).png"
         class="print-logo"
         alt="Logo SIADEMY"
         style="filter:invert(1) brightness(.3) sepia(1) hue-rotate(190deg);">
    <div class="print-inst-info">
      <div class="print-inst-name">SIADEMY — Sistema Académico</div>
      <div class="print-doc-title">Boletín de Calificaciones</div>
    </div>
    <div class="print-year-badge">Año <?= (int)$anio ?></div>
  </div>

  <!-- Datos del estudiante -->
  <table class="print-student-table">
    <tr>
      <th>Estudiante</th>
      <td><?= htmlspecialchars(trim($boletin_estudiante['nombres'] . ' ' . $boletin_estudiante['apellidos'])) ?></td>
      <th>Documento</th>
      <td><?= htmlspecialchars($boletin_estudiante['documento'] ?? '—') ?></td>
    </tr>
    <tr>
      <th>Grado</th>
      <td><?= htmlspecialchars($boletin_estudiante['grado']) ?></td>
      <th>Curso</th>
      <td><?= htmlspecialchars($boletin_estudiante['curso']) ?></td>
    </tr>
    <?php if (!empty($boletin_estudiante['jornada'])): ?>
    <tr>
      <th>Jornada</th>
      <td><?= htmlspecialchars($boletin_estudiante['jornada']) ?></td>
      <th>Año lectivo</th>
      <td><?= (int)$anio ?></td>
    </tr>
    <?php endif; ?>
  </table>

  <!-- Un bloque por período -->
  <?php foreach ($boletin_por_periodo as $bloque): ?>
    <?php
      $numP       = (int)$bloque['periodo']['numero_periodo'];
      $periodo    = $bloque['periodo'];
      $materias   = $bloque['materias'];
      $asistencia = $bloque['asistencia'];
      $promP      = $bloque['promedio_periodo'] !== null ? (float)$bloque['promedio_periodo'] : null;
    ?>
    <div class="print-period-block" data-periodo="<?= $numP ?>">

      <!-- Título del período -->
      <div class="print-period-title">
        <span><?= htmlspecialchars($periodo['nombre'] ?: 'Período ' . $numP) ?></span>
        <span>
          <?= date('d/m/Y', strtotime($periodo['fecha_inicio'])) ?>
          al
          <?= date('d/m/Y', strtotime($periodo['fecha_fin'])) ?>
          &nbsp;|&nbsp;
          Promedio:
          <strong><?= $promP !== null ? number_format($promP, 1) : 'N/A' ?></strong>
        </span>
      </div>

      <?php if (empty($materias)): ?>
        <p style="font-size:11px; color:#6b7280; margin-bottom:10px;">
          No hay actividades registradas en este período.
        </p>
      <?php else: ?>
        <!-- Tabla de materias (impresión) -->
        <table class="print-grades-table">
          <thead>
            <tr>
              <th style="width:35%">Materia</th>
              <th style="width:25%">Docente</th>
              <th class="tc" style="width:10%">Calif.</th>
              <th class="tc" style="width:10%">Venc.</th>
              <th class="tc" style="width:10%">Promedio</th>
              <th class="tc" style="width:10%">Estado</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($materias as $m): ?>
              <?php
                $prom   = $m['promedio'] !== null ? (float)$m['promedio'] : null;
                $estNota= $m['estado_nota'] ?? 'sin-nota';
                $estLab = $m['estado_label'] ?? 'Sin Nota';
                $pc = match($estNota) {
                    'superior' => 'superior',
                    'alto'     => 'alto',
                    'basico'   => 'basico',
                    'bajo'     => 'bajo',
                    default    => 'sin-nota',
                };
              ?>
              <tr>
                <td><?= htmlspecialchars($m['materia']) ?></td>
                <td><?= htmlspecialchars($m['docente_nombre'] ?: '—') ?></td>
                <td class="tc"><?= (int)$m['actividades_calificadas'] ?></td>
                <td class="tc"><?= (int)$m['actividades_vencidas'] ?></td>
                <td class="tc">
                  <span class="print-prom-badge <?= $pc ?>">
                    <?= $prom !== null ? number_format($prom, 1) : 'S/N' ?>
                  </span>
                </td>
                <td class="tc"><?= htmlspecialchars($estLab) ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>

        <!-- Resumen de asistencia bajo la tabla -->
        <div class="print-attendance">
          Asistencia del período:
          <?= (int)$asistencia['presentes'] ?> presentes •
          <?= (int)$asistencia['ausentes'] ?> ausentes •
          <?= (int)$asistencia['justificados'] ?> justificados •
          <?= (int)$asistencia['tardes'] ?> tardes •
          Total registros: <?= (int)$asistencia['total_registros'] ?>
          <?php if ($asistencia['porcentaje_asistencia'] !== null): ?>
            &nbsp;|&nbsp; <strong><?= number_format($asistencia['porcentaje_asistencia'], 1) ?>%</strong>
          <?php endif; ?>
        </div>
      <?php endif; ?>

    </div><!-- /.print-period-block -->
  <?php endforeach; ?>

  <!-- Firmas -->
  <div class="print-footer">
    <div class="print-signature">
      <div class="print-sig-line">&nbsp;</div>
      <div class="print-sig-label">Director(a) de Grupo</div>
    </div>
    <div class="print-signature">
      <div class="print-sig-line">&nbsp;</div>
      <div class="print-sig-label">Acudiente / Padre de Familia</div>
    </div>
    <div class="print-signature">
      <div class="print-sig-line">&nbsp;</div>
      <div class="print-sig-label">Coordinador(a) Académico</div>
    </div>
  </div>

</div><!-- /.print-only -->
<?php endif; ?>


<!-- Scripts -->
<script>
// ── Sidebar toggle ───────────────────────────────────────────────────────────
const leftSidebar = document.getElementById('leftSidebar');
const appGrid     = document.getElementById('appGrid');
const toggleLeft  = document.getElementById('toggleLeft');
const bOv = document.querySelector('.sidebar-overlay') || document.createElement('div');
if (!bOv.parentElement) { bOv.className = 'sidebar-overlay'; document.body.appendChild(bOv); }
let leftVisible = localStorage.getItem('leftSidebarVisible') !== 'false';
function isMobile() { return window.innerWidth <= 768; }
function openBDrawer()  { if (!leftSidebar) return; leftSidebar.classList.add('mobile-open'); leftSidebar.classList.remove('hidden'); bOv.classList.add('active'); }
function closeBDrawer() { if (!leftSidebar) return; leftSidebar.classList.remove('mobile-open'); bOv.classList.remove('active'); }
bOv.onclick = closeBDrawer;
window.addEventListener('resize', () => { if (!isMobile()) closeBDrawer(); });
function updateGrid() { if (appGrid) appGrid.classList.toggle('hide-left', !leftVisible); }
if (toggleLeft) {
  toggleLeft.addEventListener('click', () => {
    if (isMobile()) { leftSidebar && leftSidebar.classList.contains('mobile-open') ? closeBDrawer() : openBDrawer(); return; }
    leftVisible = !leftVisible;
    if (leftSidebar) leftSidebar.classList.toggle('hidden', !leftVisible);
    localStorage.setItem('leftSidebarVisible', leftVisible);
    updateGrid();
  });
}
if (!isMobile()) { if (leftSidebar && !leftVisible) leftSidebar.classList.add('hidden'); updateGrid(); }

// ── Cambio de período ────────────────────────────────────────────────────────
function cambiarPeriodo(numPeriodo) {
  // Tabs
  document.querySelectorAll('.period-tab').forEach(btn => {
    btn.classList.toggle('active', parseInt(btn.dataset.periodo) === numPeriodo);
  });
  // Contenidos
  document.querySelectorAll('.periodo-content').forEach(div => {
    div.classList.toggle('active', div.id === 'periodo-' + numPeriodo);
  });
}

// ── Imprimir solo el período activo ─────────────────────────────────────────
function imprimirBoletin() {
  // Detectar el período activo en los tabs
  const activeTab = document.querySelector('.period-tab.active');
  const activePeriodo = activeTab ? parseInt(activeTab.dataset.periodo) : null;

  // Ocultar todos los bloques de impresión que NO sean el período activo
  const bloques = document.querySelectorAll('.print-period-block');
  bloques.forEach(bloque => {
    const numP = parseInt(bloque.dataset.periodo);
    bloque.style.display = (!activePeriodo || numP === activePeriodo) ? '' : 'none';
  });

  window.print();

  // Restaurar visibilidad de todos los bloques después de imprimir
  bloques.forEach(bloque => { bloque.style.display = ''; });
}
</script>

</body>
</html>
