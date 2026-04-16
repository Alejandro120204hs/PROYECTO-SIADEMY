let cambiosPendientes = {};
let estadoOriginal = {};
let confirmAcceptCallback = null;

const appGrid = document.getElementById('appGrid');
const baseUrl = appGrid ? (appGrid.dataset.baseUrl || '') : '';
const cursoSeleccionado = Number(appGrid ? (appGrid.dataset.cursoId || 0) : 0);
const asignaturaSeleccionada = Number(appGrid ? (appGrid.dataset.asignaturaId || 0) : 0);
const fechaSeleccionada = appGrid ? (appGrid.dataset.fecha || '') : '';

let cursosData = [];
if (appGrid && appGrid.dataset.cursos) {
  try {
    cursosData = JSON.parse(appGrid.dataset.cursos);
  } catch (error) {
    cursosData = [];
  }
}

function actualizarAsignaturas() {
  const selectCurso = document.getElementById('selectCurso');
  const selectAsignatura = document.getElementById('selectAsignatura');
  if (!selectCurso || !selectAsignatura) return;

  const cursoId = selectCurso.value;
  selectAsignatura.innerHTML = '<option value="">-- Todas las asignaturas --</option>';

  if (cursoId) {
    const cursoData = cursosData.find(c => String(c.id_curso) === String(cursoId));

    if (cursoData && Array.isArray(cursoData.asignaturas)) {
      cursoData.asignaturas.forEach(asig => {
        const option = document.createElement('option');
        option.value = asig.id;
        const horario = (asig.horario || '').toString().trim();
        option.textContent = horario ? `${asig.nombre} - ${horario}` : asig.nombre;
        selectAsignatura.appendChild(option);
      });
    }

    selectAsignatura.disabled = false;
  } else {
    selectAsignatura.disabled = true;
  }
}

document.addEventListener('DOMContentLoaded', function() {
  const filas = document.querySelectorAll('.student-row');
  filas.forEach(fila => {
    const studentId = fila.getAttribute('data-student-id');
    const statusElement = fila.querySelector('.current-status');
    if (!studentId || !statusElement) return;
    estadoOriginal[studentId] = statusElement.getAttribute('data-status');
  });
});

function construirEstadoHTML(tipo) {
  switch (tipo) {
    case 'P': return '<span class="status-pill s-P"><i class="ri-checkbox-circle-fill"></i> Presente</span>';
    case 'A': return '<span class="status-pill s-A"><i class="ri-close-circle-fill"></i> Ausente</span>';
    case 'T': return '<span class="status-pill s-T"><i class="ri-time-fill"></i> Tardanza</span>';
    case 'E': return '<span class="status-pill s-E"><i class="ri-file-text-fill"></i> Excusa</span>';
    default: return '<span class="status-pill s-null"><i class="ri-question-fill"></i> Sin marcar</span>';
  }
}

function abrirModalConfirmacion(mensaje, accionAceptar, esPeligro = false) {
  const modal = document.getElementById('confirmModal');
  const messageEl = document.getElementById('confirmModalMessage');
  const acceptBtn = document.getElementById('confirmAcceptBtn');

  if (!modal || !messageEl || !acceptBtn) {
    if (typeof accionAceptar === 'function') accionAceptar();
    return;
  }

  messageEl.textContent = mensaje;
  acceptBtn.classList.remove('primary', 'danger');
  acceptBtn.classList.add(esPeligro ? 'danger' : 'primary');
  confirmAcceptCallback = accionAceptar;
  modal.classList.add('visible');
  modal.setAttribute('aria-hidden', 'false');
}

function cerrarModalConfirmacion() {
  const modal = document.getElementById('confirmModal');
  if (!modal) return;
  modal.classList.remove('visible');
  modal.setAttribute('aria-hidden', 'true');
  confirmAcceptCallback = null;
}

function marcarAsistencia(studentId, tipo, boton) {
  const fila = boton.closest('.student-row');
  if (!fila) return;

  const statusElement = fila.querySelector('.current-status');
  const botonesAsistencia = fila.querySelectorAll('.att-btn');

  botonesAsistencia.forEach(btn => btn.classList.remove('active'));
  boton.classList.add('active');

  if (statusElement) {
    statusElement.innerHTML = construirEstadoHTML(tipo);
    statusElement.setAttribute('data-status', tipo);
  }

  if (estadoOriginal[studentId] !== tipo) {
    cambiosPendientes[studentId] = tipo;
  } else {
    delete cambiosPendientes[studentId];
  }

  actualizarEstadisticas();
  mostrarBotonGuardar();
}

function ejecutarMarcarTodosPresentes() {
  const filas = document.querySelectorAll('.student-row');
  filas.forEach(fila => {
    const studentId = fila.getAttribute('data-student-id');
    const botonPresente = fila.querySelector('.att-btn.presente');
    if (studentId && botonPresente) {
      marcarAsistencia(studentId, 'P', botonPresente);
    }
  });
  mostrarToast('Todos los estudiantes quedaron en estado Presente', 'success');
}

function marcarTodosPresentes() {
  abrirModalConfirmacion(
    'Se marcarán todos los estudiantes como presentes. ¿Deseas continuar?',
    ejecutarMarcarTodosPresentes,
    false
  );
}

function actualizarEstadisticas() {
  const filas = document.querySelectorAll('.student-row');
  let presentes = 0, ausentes = 0, tardanzas = 0, excusas = 0, sinMarcar = 0;

  filas.forEach(fila => {
    const statusElement = fila.querySelector('.current-status');
    const status = statusElement ? statusElement.getAttribute('data-status') : '';
    switch (status) {
      case 'P': presentes++; break;
      case 'A': ausentes++; break;
      case 'T': tardanzas++; break;
      case 'E': excusas++; break;
      default: sinMarcar++; break;
    }
  });

  const total = filas.length;
  const porcentaje = total > 0 ? Math.round((presentes / total) * 100 * 10) / 10 : 0;

  const presentesNode = document.querySelector('.kpi-att.presentes .kpi-att-content strong');
  const ausentesNode = document.querySelector('.kpi-att.ausentes .kpi-att-content strong');
  const tardanzasNode = document.querySelector('.kpi-att.tardanzas .kpi-att-content strong');
  const excusasNode = document.querySelector('.kpi-att.excusas .kpi-att-content strong');
  const sinMarcarNode = document.querySelector('.kpi-att.sin-marcar .kpi-att-content strong');

  if (presentesNode) presentesNode.innerHTML = `${presentes} <span class="kpi-att-pct">(${porcentaje}%)</span>`;
  if (ausentesNode) ausentesNode.textContent = String(ausentes);
  if (tardanzasNode) tardanzasNode.textContent = String(tardanzas);
  if (excusasNode) excusasNode.textContent = String(excusas);
  if (sinMarcarNode) sinMarcarNode.textContent = String(sinMarcar);
}

function mostrarBotonGuardar() {
  const saveButton = document.getElementById('saveButton');
  const changesCount = document.getElementById('changesCount');
  if (!saveButton || !changesCount) return;

  const numCambios = Object.keys(cambiosPendientes).length;

  if (numCambios > 0) {
    saveButton.classList.add('visible');
    changesCount.textContent = String(numCambios);
  } else {
    saveButton.classList.remove('visible');
  }
}

const saveBtn = document.getElementById('saveButton');
if (saveBtn) {
  saveBtn.addEventListener('click', function() {
    if (Object.keys(cambiosPendientes).length === 0) {
      mostrarToast('No hay cambios pendientes por guardar.', 'info');
      return;
    }

    const payload = {
      curso_id: cursoSeleccionado,
      asignatura_id: asignaturaSeleccionada,
      fecha: fechaSeleccionada,
      asistencias: cambiosPendientes
    };

    saveBtn.disabled = true;
    saveBtn.innerHTML = '<i class="ri-loader-4-line"></i> Guardando...';

    fetch(`${baseUrl}/docente/guardar-asistencia`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(payload)
    })
      .then(r => r.json())
      .then(data => {
        if (data.success) {
          Object.assign(estadoOriginal, cambiosPendientes);
          cambiosPendientes = {};
          mostrarBotonGuardar();
          mostrarToast(data.message || 'Asistencia guardada correctamente', 'success');
        } else {
          mostrarToast(data.message || 'Error al guardar', 'error');
        }
      })
      .catch(() => {
        mostrarToast('Error de conexión al guardar', 'error');
      })
      .finally(() => {
        saveBtn.disabled = false;
        saveBtn.innerHTML = '<i class="ri-save-line"></i> Guardar Cambios <span class="changes-count" id="changesCount">0</span>';
        mostrarBotonGuardar();
      });
  });
}

function filtrarEstudiantes() {
  const input = document.getElementById('searchStudent');
  if (!input) return;

  const filter = input.value.toLowerCase();
  const filas = document.querySelectorAll('.student-row');

  filas.forEach(fila => {
    const nombreNode = fila.querySelector('.student-name');
    const docNode = fila.querySelector('.student-doc');
    const nombre = nombreNode ? nombreNode.textContent.toLowerCase() : '';
    const documento = docNode ? docNode.textContent.toLowerCase() : '';

    fila.style.display = (nombre.includes(filter) || documento.includes(filter)) ? '' : 'none';
  });
}

function cerrarModalHistorial() {
  const modal = document.getElementById('historyModal');
  if (!modal) return;
  modal.classList.remove('visible');
  modal.setAttribute('aria-hidden', 'true');
}

function formatearFecha(fechaIso) {
  const parts = (fechaIso || '').split('-');
  if (parts.length !== 3) return fechaIso;
  return `${parts[2]}/${parts[1]}/${parts[0]}`;
}

function construirFilaHistorial(item) {
  const presentes = Number(item.presentes || 0);
  const ausentes = Number(item.ausentes || 0);
  const justificados = Number(item.justificados || 0);
  const total = Number(item.total_registrados || 0);

  return `
    <div class="history-item">
      <div class="history-date">${formatearFecha(item.fecha)}</div>
      <div class="history-metrics">
        <span class="metric-chip p"><i class="ri-checkbox-circle-line"></i> ${presentes} Pres.</span>
        <span class="metric-chip a"><i class="ri-close-circle-line"></i> ${ausentes} Aus.</span>
        <span class="metric-chip j"><i class="ri-file-text-line"></i> ${justificados} Just.</span>
        <span class="metric-chip t"><i class="ri-group-line"></i> ${total} Reg.</span>
      </div>
    </div>
  `;
}

function verHistorial() {
  const modal = document.getElementById('historyModal');
  const body = document.getElementById('historyModalBody');
  if (!modal || !body) {
    mostrarToast('No se pudo abrir el historial en este momento.', 'error');
    return;
  }

  modal.classList.add('visible');
  modal.setAttribute('aria-hidden', 'false');
  body.innerHTML = 'Cargando historial...';

  fetch(`${baseUrl}/docente/historial-asistencia?curso=${cursoSeleccionado}&asignatura=${asignaturaSeleccionada}&limite=20`)
    .then(resp => resp.json())
    .then(data => {
      if (!data.success) {
        body.innerHTML = `<div class="history-empty">${data.message || 'No se pudo cargar el historial.'}</div>`;
        return;
      }

      const historial = Array.isArray(data.historial) ? data.historial : [];
      if (historial.length === 0) {
        body.innerHTML = '<div class="history-empty">No hay registros históricos para esta combinación de curso y asignatura.</div>';
        return;
      }

      body.innerHTML = `<div class="history-list">${historial.map(construirFilaHistorial).join('')}</div>`;
    })
    .catch(() => {
      body.innerHTML = '<div class="history-empty">Error de conexión al consultar historial.</div>';
    });
}

function mostrarToast(mensaje, tipo) {
  const toast = document.createElement('div');
  const bg = tipo === 'success' ? '#10b981' : (tipo === 'info' ? '#3b82f6' : '#ef4444');
  toast.style.cssText = `position:fixed;top:20px;right:20px;background:${bg};color:white;padding:14px 20px;border-radius:12px;box-shadow:0 8px 24px rgba(0,0,0,.3);font-weight:500;z-index:10000;display:flex;align-items:center;gap:10px;animation:slideInRight .3s ease`;
  toast.textContent = mensaje;
  document.body.appendChild(toast);
  setTimeout(() => {
    toast.style.animation = 'slideOutRight .3s ease';
    setTimeout(() => toast.remove(), 300);
  }, 3000);
}

document.addEventListener('keydown', function(e) {
  if ((e.ctrlKey || e.metaKey) && e.key === 's') {
    e.preventDefault();
    const button = document.getElementById('saveButton');
    if (button) button.click();
  }

  if ((e.ctrlKey || e.metaKey) && e.key === 'a' && e.shiftKey) {
    e.preventDefault();
    marcarTodosPresentes();
  }
});

document.addEventListener('DOMContentLoaded', function() {
  const confirmModal = document.getElementById('confirmModal');
  const confirmCloseBtn = document.getElementById('confirmCloseBtn');
  const confirmCancelBtn = document.getElementById('confirmCancelBtn');
  const confirmAcceptBtn = document.getElementById('confirmAcceptBtn');

  if (confirmCloseBtn) confirmCloseBtn.addEventListener('click', cerrarModalConfirmacion);
  if (confirmCancelBtn) confirmCancelBtn.addEventListener('click', cerrarModalConfirmacion);
  if (confirmAcceptBtn) {
    confirmAcceptBtn.addEventListener('click', function() {
      const accion = confirmAcceptCallback;
      cerrarModalConfirmacion();
      if (typeof accion === 'function') accion();
    });
  }

  if (confirmModal) {
    confirmModal.addEventListener('click', function(e) {
      if (e.target === confirmModal) cerrarModalConfirmacion();
    });
  }

  const historyModal = document.getElementById('historyModal');
  const historyCloseBtn = document.getElementById('historyCloseBtn');
  const historyAcceptBtn = document.getElementById('historyAcceptBtn');

  if (historyCloseBtn) historyCloseBtn.addEventListener('click', cerrarModalHistorial);
  if (historyAcceptBtn) historyAcceptBtn.addEventListener('click', cerrarModalHistorial);
  if (historyModal) {
    historyModal.addEventListener('click', function(e) {
      if (e.target === historyModal) cerrarModalHistorial();
    });
  }
});

window.addEventListener('beforeunload', function(e) {
  if (Object.keys(cambiosPendientes).length > 0) {
    e.preventDefault();
    e.returnValue = '¿Estás seguro? Tienes cambios sin guardar.';
    return e.returnValue;
  }
});
