<?php
    require_once BASE_PATH . '/app/helpers/session_administrador.php';
    require_once BASE_PATH . '/app/controllers/perfil.php';
    $id      = $_SESSION['user']['id'] ?? 0;
    $usuario = mostrarPerfil($id);

    // Garantía de variables del controlador
    $cursos            = $cursos            ?? [];
    $idCurso           = $idCurso           ?? 0;
    $horarios          = $horarios          ?? [];
    $horariosPorDia    = $horariosPorDia    ?? array_fill(1, 6, []);
    $dacOptions        = $dacOptions        ?? [];
    $stats             = $stats             ?? [];
    $coloresPorAsignatura = $coloresPorAsignatura ?? [];
    $alerta            = $alerta            ?? null;

    $dias = HorarioModel::$dias;

    // Curso seleccionado
    $cursoSeleccionado = null;
    foreach ($cursos as $c) {
        if ((int) $c['id'] === $idCurso) { $cursoSeleccionado = $c; break; }
    }

    $totalBloques    = (int) ($stats['total_bloques']    ?? 0);
    $totalDocentes   = (int) ($stats['total_docentes']   ?? 0);
    $totalAsigs      = (int) ($stats['total_asignaturas'] ?? 0);
    $minutosSemanales= (int) ($stats['minutos_semana']   ?? 0);
    $horasSemanales  = $minutosSemanales > 0 ? round($minutosSemanales / 60, 1) : 0;
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SIADEMY • Horarios</title>
    <?php 
        include_once __DIR__ . '/../../layouts/header_coordinador.php'; 
    ?>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Montserrat:wght@600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/remixicon@4.3.0/fonts/remixicon.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/dashboard/css/styles-horarios.css">
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
                <button class="toggle-btn" id="toggleLeft" title="Mostrar/Ocultar menú">
                    <i class="ri-menu-2-line"></i>
                </button>
                <div class="title">Horarios Académicos</div>
            </div>
            <?php include_once BASE_PATH . '/app/views/layouts/boton_perfil_solo.php'; ?>
        </div>

        <?php if ($alerta): ?>
        <div class="alert alert-<?= htmlspecialchars($alerta['tipo']) ?>">
            <i class="ri-<?= $alerta['tipo'] === 'success' ? 'checkbox-circle' : 'error-warning' ?>-line"></i>
            <span><?= $alerta['mensaje'] ?></span>
            <button class="btn-close-alert" onclick="this.parentElement.remove()"><i class="ri-close-line"></i></button>
        </div>
        <?php endif; ?>

        <?php if (empty($cursos)): ?>
        <!-- Sin cursos registrados -->
        <div class="empty-state">
            <i class="ri-calendar-close-line"></i>
            <h3>Sin cursos disponibles</h3>
            <p>Primero registra y activa cursos en el módulo de Cursos antes de gestionar horarios.</p>
            <a href="<?= BASE_URL ?>/administrador-panel-cursos" class="btn-save" style="text-decoration:none; display:inline-flex; align-items:center; gap:8px;">
                <i class="ri-arrow-right-line"></i> Ir a Cursos
            </a>
        </div>

        <?php else: ?>

        <!-- FILTER BAR -->
        <div class="filter-bar">
            <span class="filter-label"><i class="ri-filter-3-line"></i> Curso:</span>
            <select class="filter-select" id="cursoSelect" onchange="cambiarCurso(this.value)">
                <?php foreach ($cursos as $c): ?>
                    <option value="<?= $c['id'] ?>" <?= (int) $c['id'] === $idCurso ? 'selected' : '' ?>>
                        <?= htmlspecialchars($c['grado'] . '-' . $c['curso']) ?>
                        <?php if ($c['jornada']): ?> — <?= htmlspecialchars($c['jornada']) ?><?php endif; ?>
                        (<?= $c['anio'] ?>)
                    </option>
                <?php endforeach; ?>
            </select>
            <button class="btn-add-horario" id="btnAgregarHorario" onclick="abrirModal()">
                <i class="ri-add-line"></i> Agregar Bloque
            </button>
        </div>

        <!-- STATS -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon blue"><i class="ri-calendar-2-line"></i></div>
                <div class="stat-content">
                    <h3><?= $totalBloques ?></h3>
                    <p>Bloques semanales</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon green"><i class="ri-user-3-line"></i></div>
                <div class="stat-content">
                    <h3><?= $totalDocentes ?></h3>
                    <p>Docentes activos</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon amber"><i class="ri-book-2-line"></i></div>
                <div class="stat-content">
                    <h3><?= $totalAsigs ?></h3>
                    <p>Asignaturas</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon purple"><i class="ri-time-line"></i></div>
                <div class="stat-content">
                    <h3><?= $horasSemanales ?>h</h3>
                    <p>Horas por semana</p>
                </div>
            </div>
        </div>

        <!-- WEEKLY GRID HEADER -->
        <div class="week-header">
            <span class="week-title">
                <i class="ri-calendar-line"></i>
                Horario semanal
                <?php if ($cursoSeleccionado): ?>
                    — <span style="color: #818cf8;"><?= htmlspecialchars($cursoSeleccionado['grado'] . '-' . $cursoSeleccionado['curso']) ?></span>
                <?php endif; ?>
                <?php if ($totalBloques > 0): ?>
                    <span class="week-badge"><?= $totalBloques ?> bloques</span>
                <?php endif; ?>
            </span>
        </div>

        <!-- WEEKLY GRID -->
        <?php if (empty($horarios) && !empty($dacOptions)): ?>
        <div class="empty-state">
            <i class="ri-calendar-todo-line"></i>
            <h3>Sin horario configurado</h3>
            <p>Este curso aún no tiene bloques de horario. Agrega el primer bloque con el botón de arriba.</p>
        </div>

        <?php elseif (empty($dacOptions)): ?>
        <div class="empty-state">
            <i class="ri-user-unfollow-line"></i>
            <h3>Sin docentes asignados</h3>
            <p>Primero asigna docentes a las asignaturas de este curso para poder crear el horario.</p>
            <a href="<?= BASE_URL ?>/administrador/asignar-docentes?curso=<?= $idCurso ?>" class="btn-save" style="text-decoration:none; display:inline-flex; align-items:center; gap:8px;">
                <i class="ri-arrow-right-line"></i> Asignar Docentes
            </a>
        </div>

        <?php else: ?>
        <div class="schedule-grid">
            <?php foreach ($dias as $numDia => $nombreDia): ?>
            <?php $bloquesDia = $horariosPorDia[$numDia] ?? []; ?>
            <div class="day-column">
                <div class="day-header">
                    <span class="day-name"><?= $nombreDia ?></span>
                    <span class="day-blocks-count">
                        <?= count($bloquesDia) ?> <?= count($bloquesDia) === 1 ? 'bloque' : 'bloques' ?>
                    </span>
                </div>
                <div class="day-body">
                    <?php if (empty($bloquesDia)): ?>
                    <div class="day-empty">
                        <i class="ri-calendar-2-line"></i>
                        <span>Sin clases</span>
                    </div>
                    <?php else: ?>
                    <?php foreach ($bloquesDia as $bloque):
                        $color = $coloresPorAsignatura[$bloque['id_asignatura']] ?? '#4f46e5';
                        $horaI = substr($bloque['hora_inicio'], 0, 5);
                        $horaF = substr($bloque['hora_fin'], 0, 5);
                    ?>
                    <div class="block-card" style="--block-color: <?= $color ?>;"
                         onclick="editarBloque(<?= htmlspecialchars(json_encode([
                             'id'          => $bloque['id'],
                             'id_dac'      => $bloque['id_dac'],
                             'dia_semana'  => $bloque['dia_semana'],
                             'hora_inicio' => substr($bloque['hora_inicio'],0,5),
                             'hora_fin'    => substr($bloque['hora_fin'],0,5),
                             'aula'        => $bloque['aula'] ?? '',
                         ]), ENT_QUOTES) ?>)">

                        <div class="block-time">
                            <i class="ri-time-line"></i>
                            <?= $horaI ?> – <?= $horaF ?>
                        </div>
                        <div class="block-subject"><?= htmlspecialchars($bloque['asignatura_nombre']) ?></div>
                        <div class="block-teacher">
                            <i class="ri-user-3-line"></i>
                            <?= htmlspecialchars($bloque['docente_nombre']) ?>
                        </div>
                        <?php if (!empty($bloque['aula'])): ?>
                        <div class="block-aula">
                            <i class="ri-map-pin-line"></i>
                            <?= htmlspecialchars($bloque['aula']) ?>
                        </div>
                        <?php endif; ?>

                        <!-- Actions -->
                        <div class="block-actions" onclick="event.stopPropagation()">
                            <button class="btn-block-action btn-block-edit"
                                    title="Editar"
                                    onclick="editarBloque(<?= htmlspecialchars(json_encode([
                                        'id'          => $bloque['id'],
                                        'id_dac'      => $bloque['id_dac'],
                                        'dia_semana'  => $bloque['dia_semana'],
                                        'hora_inicio' => substr($bloque['hora_inicio'],0,5),
                                        'hora_fin'    => substr($bloque['hora_fin'],0,5),
                                        'aula'        => $bloque['aula'] ?? '',
                                    ]), ENT_QUOTES) ?>); event.stopPropagation()">
                                <i class="ri-edit-line"></i>
                            </button>
                            <button class="btn-block-action btn-block-delete"
                                    title="Eliminar"
                                    onclick="eliminarBloque(<?= $bloque['id'] ?>, '<?= htmlspecialchars($bloque['asignatura_nombre']) ?>'); event.stopPropagation()">
                                <i class="ri-delete-bin-line"></i>
                            </button>
                        </div>
                    </div>
                    <?php endforeach; ?>
                    <?php endif; ?>

                    <!-- Add button at bottom of each day -->
                    <button class="btn-day-add" onclick="abrirModalDia(<?= $numDia ?>)">
                        <i class="ri-add-line"></i> Agregar
                    </button>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <?php endif; // end if cursos ?>

    </main>
</div>

<!-- ═══════════════════════════════════════════════════════════
     MODAL — Agregar / Editar Horario
══════════════════════════════════════════════════════════════ -->
<div class="modal-overlay" id="modalHorario">
    <div class="modal-box">
        <div class="modal-header">
            <span class="modal-title" id="modalTitulo">
                <i class="ri-calendar-add-line"></i>
                Agregar Bloque
            </span>
            <button class="btn-modal-close" onclick="cerrarModal()">
                <i class="ri-close-line"></i>
            </button>
        </div>

        <form id="formHorario" method="POST" action="<?= BASE_URL ?>/administrador/horarios">
            <input type="hidden" name="accion"   id="inputAccion"   value="guardar">
            <input type="hidden" name="id"        id="inputId"       value="">
            <input type="hidden" name="id_curso"  id="inputIdCurso"  value="<?= $idCurso ?>">

            <!-- DAC (Asignatura + Docente) -->
            <div class="form-group">
                <label><i class="ri-book-2-line"></i> Asignatura — Docente</label>
                <select class="form-control" name="id_dac" id="selectDac" required>
                    <option value="">Selecciona...</option>
                    <?php foreach ($dacOptions as $dac): ?>
                    <option value="<?= $dac['id_dac'] ?>">
                        <?= htmlspecialchars($dac['asignatura_nombre'] . ' — ' . $dac['docente_nombre']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Día -->
            <div class="form-group">
                <label><i class="ri-calendar-line"></i> Día de la semana</label>
                <select class="form-control" name="dia_semana" id="selectDia" required>
                    <option value="">Selecciona un día...</option>
                    <?php foreach ($dias as $num => $nombre): ?>
                    <option value="<?= $num ?>"><?= $nombre ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Horas -->
            <div class="form-row">
                <div class="form-group">
                    <label><i class="ri-time-line"></i> Hora inicio</label>
                    <input type="time" class="form-control" name="hora_inicio" id="inputHoraInicio" required>
                </div>
                <div class="form-group">
                    <label><i class="ri-time-line"></i> Hora fin</label>
                    <input type="time" class="form-control" name="hora_fin" id="inputHoraFin" required>
                </div>
            </div>

            <!-- Aula -->
            <div class="form-group">
                <label><i class="ri-map-pin-line"></i> Aula <span style="color:var(--muted); font-size:11px;">(opcional)</span></label>
                <input type="text" class="form-control" name="aula" id="inputAula"
                       placeholder="Ej: Sala 101, Laboratorio 2..." maxlength="60">
            </div>

            <!-- Indicador de conflictos -->
            <div class="conflict-box" id="conflictBox">
                <i class="ri-information-line"></i>
                <span id="conflictMsg"></span>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn-cancel" onclick="cerrarModal()">Cancelar</button>
                <button type="submit" class="btn-save" id="btnGuardar">
                    <i class="ri-save-line"></i> Guardar
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Form oculto para eliminar (evita GET con datos sensibles) -->
<form id="formEliminar" method="GET" action="<?= BASE_URL ?>/administrador/horarios" style="display:none;">
    <input type="hidden" name="accion"   value="eliminar">
    <input type="hidden" name="id"       id="eliminarId" value="">
    <input type="hidden" name="id_curso" value="<?= $idCurso ?>">
</form>

<script>
const BASE_URL    = '<?= BASE_URL ?>';
const ID_CURSO    = <?= $idCurso ?>;


// ── Cambiar curso ────────────────────────────────────────────────────────────
function cambiarCurso(idCurso) {
    window.location.href = BASE_URL + '/administrador/horarios?id_curso=' + idCurso;
}

// ── Abrir modal para agregar ─────────────────────────────────────────────────
function abrirModal() {
    document.getElementById('modalTitulo').innerHTML = '<i class="ri-calendar-add-line"></i> Agregar Bloque';
    document.getElementById('inputAccion').value = 'guardar';
    document.getElementById('inputId').value     = '';
    document.getElementById('formHorario').reset();
    document.getElementById('inputIdCurso').value = ID_CURSO;
    ocultarConflicto();
    document.getElementById('modalHorario').classList.add('open');
}

// Abrir modal con día preseleccionado
function abrirModalDia(dia) {
    abrirModal();
    document.getElementById('selectDia').value = dia;
}

// ── Abrir modal para editar ──────────────────────────────────────────────────
function editarBloque(data) {
    document.getElementById('modalTitulo').innerHTML = '<i class="ri-edit-line"></i> Editar Bloque';
    document.getElementById('inputAccion').value     = 'actualizar';
    document.getElementById('inputId').value         = data.id;
    document.getElementById('inputIdCurso').value    = ID_CURSO;
    document.getElementById('selectDac').value       = data.id_dac;
    document.getElementById('selectDia').value       = data.dia_semana;
    document.getElementById('inputHoraInicio').value = data.hora_inicio;
    document.getElementById('inputHoraFin').value    = data.hora_fin;
    document.getElementById('inputAula').value       = data.aula || '';
    ocultarConflicto();
    document.getElementById('modalHorario').classList.add('open');
}

// ── Cerrar modal ─────────────────────────────────────────────────────────────
function cerrarModal() {
    document.getElementById('modalHorario').classList.remove('open');
}
document.getElementById('modalHorario').addEventListener('click', function (e) {
    if (e.target === this) cerrarModal();
});

// ── Eliminar bloque ───────────────────────────────────────────────────────────
function eliminarBloque(id, asignatura) {
    if (!confirm('¿Eliminar el bloque de "' + asignatura + '"? Esta acción no se puede deshacer.')) return;
    document.getElementById('eliminarId').value = id;
    document.getElementById('formEliminar').submit();
}

// ── Verificación de conflictos en tiempo real ─────────────────────────────────
let conflictTimer = null;
const campos = ['selectDac', 'selectDia', 'inputHoraInicio', 'inputHoraFin'];
campos.forEach(id => {
    const el = document.getElementById(id);
    if (el) el.addEventListener('change', scheduleConflictCheck);
});

function scheduleConflictCheck() {
    clearTimeout(conflictTimer);
    conflictTimer = setTimeout(verificarConflictos, 500);
}

function verificarConflictos() {
    const idDac     = document.getElementById('selectDac').value;
    const dia       = document.getElementById('selectDia').value;
    const horaI     = document.getElementById('inputHoraInicio').value;
    const horaF     = document.getElementById('inputHoraFin').value;
    const excluirId = document.getElementById('inputId').value;

    if (!idDac || !dia || !horaI || !horaF) { ocultarConflicto(); return; }
    if (horaF <= horaI) {
        mostrarConflicto('warn', 'La hora de fin debe ser mayor que la hora de inicio.');
        return;
    }

    const url = new URL(BASE_URL + '/administrador/horarios');
    url.searchParams.set('accion',       'verificar_conflicto');
    url.searchParams.set('id_dac',       idDac);
    url.searchParams.set('dia',          dia);
    url.searchParams.set('hora_inicio',  horaI);
    url.searchParams.set('hora_fin',     horaF);
    if (excluirId) url.searchParams.set('excluir_id', excluirId);

    fetch(url)
        .then(r => r.json())
        .then(data => {
            if (!data.success) return;
            if (data.conflictos.length === 0) {
                mostrarConflicto('ok', '<i class="ri-checkbox-circle-line"></i> Sin conflictos detectados.');
            } else {
                const msgs = data.conflictos.map(c => c.mensaje).join('<br>');
                mostrarConflicto('warn', msgs);
            }
        })
        .catch(() => ocultarConflicto());
}

function mostrarConflicto(tipo, msg) {
    const box = document.getElementById('conflictBox');
    const txt = document.getElementById('conflictMsg');
    box.className = 'conflict-box show ' + tipo;
    txt.innerHTML = msg;
    document.getElementById('btnGuardar').disabled = (tipo === 'warn');
}
function ocultarConflicto() {
    document.getElementById('conflictBox').className = 'conflict-box';
    document.getElementById('btnGuardar').disabled = false;
}

// ── Tecla Escape cierra el modal ─────────────────────────────────────────────
document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') cerrarModal();
});

// ── Dropdown de perfil de usuario ────────────────────────────────────────────
(function () {
    function initProfileDropdown() {
        const btn      = document.getElementById('userMenuBtn');
        const dropdown = document.getElementById('userDropdown');
        if (!btn || !dropdown || btn.dataset.dropdownInit === '1') return;
        btn.dataset.dropdownInit = '1';

        const overlay = document.createElement('div');
        overlay.className = 'dropdown-overlay';
        document.body.appendChild(overlay);

        function open()  { dropdown.classList.add('show');    overlay.classList.add('show'); }
        function close() { dropdown.classList.remove('show'); overlay.classList.remove('show'); }

        btn.addEventListener('click', function (e) {
            e.stopPropagation();
            dropdown.classList.contains('show') ? close() : open();
        });
        overlay.addEventListener('click', close);
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') close();
        });
    }
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initProfileDropdown);
    } else {
        initProfileDropdown();
    }
})();

// ── Animación de entrada a las cards ─────────────────────────────────────────
document.querySelectorAll('.block-card').forEach((card, i) => {
    card.style.opacity = '0';
    card.style.transform = 'translateY(10px)';
    setTimeout(() => {
        card.style.transition = 'opacity .25s ease, transform .25s ease';
        card.style.opacity    = '1';
        card.style.transform  = 'translateY(0)';
    }, i * 40);
});
</script>
<script src="<?= BASE_URL ?>/public/assets/dashboard/js/main-admin.js"></script>
</body>
</html>
