const detalleApp = document.getElementById('appGrid');
const baseUrlDetalle = detalleApp ? (detalleApp.dataset.baseUrl || '') : '';
const idCursoDetalle = detalleApp ? Number(detalleApp.dataset.idCurso || 0) : 0;

function parseJsonAttr(key) {
  if (!detalleApp) return {};
  try {
    return JSON.parse(detalleApp.dataset[key] || '{}');
  } catch (error) {
    return {};
  }
}

function parseArrayAttr(key) {
  if (!detalleApp) return [];
  try {
    const val = JSON.parse(detalleApp.dataset[key] || '[]');
    return Array.isArray(val) ? val : [];
  } catch {
    return [];
  }
}

function esc(s) {
  return String(s ?? '')
    .replace(/&/g, '&amp;').replace(/</g, '&lt;')
    .replace(/>/g, '&gt;').replace(/"/g, '&quot;');
}

const detalleActividadesPorAsignatura    = parseJsonAttr('detalleActividades');
const resumenCalificacionesPorAsignatura = parseJsonAttr('resumenCalificaciones');
const perfilAcademicoPorEstudiante       = parseJsonAttr('perfilEstudiantes');
const calificacionesPorEstudiante        = parseJsonAttr('calificacionesEstudiantes');

function formatoFecha(fechaIso) {
  if (!fechaIso) return 'Sin fecha';
  const fecha = new Date(`${fechaIso}T00:00:00`);
  if (Number.isNaN(fecha.getTime())) return 'Sin fecha';
  return fecha.toLocaleDateString('es-CO', { year: 'numeric', month: 'short', day: '2-digit' });
}

function badgeEstado(estado) {
  const valor = (estado || '').toString().toLowerCase();
  if (valor === 'vencida') return { clase: 'bg-danger-subtle text-danger', texto: 'Vencida' };
  if (valor === 'cerrada' || valor === 'inactiva') return { clase: 'bg-secondary-subtle text-secondary', texto: 'Cerrada' };
  return { clase: 'bg-success-subtle text-success', texto: 'Activa' };
}

function formatearFechaLarga(fechaIso) {
  if (!fechaIso) return 'Sin registro';
  const fecha = new Date(fechaIso);
  if (Number.isNaN(fecha.getTime())) return 'Sin registro';
  return fecha.toLocaleDateString('es-CO', { year: 'numeric', month: 'long', day: '2-digit' });
}

function mostrarModalSeguro(modalId) {
  const modalEl = document.getElementById(modalId);
  if (!modalEl) return;

  if (window.bootstrap && window.bootstrap.Modal) {
    window.bootstrap.Modal.getOrCreateInstance(modalEl).show();
    return;
  }

  if (window.jQuery && typeof window.jQuery(modalEl).modal === 'function') {
    window.jQuery(modalEl).modal('show');
  }
}

function abrirModalActividades(idAsignatura, nombreAsignatura) {
  const actividades = detalleActividadesPorAsignatura[idAsignatura] || [];
  const titulo = document.getElementById('modalActividadesAsignaturaLabel');
  const resumen = document.getElementById('actividadesAsignaturaResumen');
  const body = document.getElementById('tablaActividadesAsignaturaBody');
  const btnNuevaActividad = document.getElementById('btnModalNuevaActividad');

  if (!titulo || !resumen || !body || !btnNuevaActividad) return;

  titulo.innerHTML = '<i class="ri-file-list-3-line" style="color: #6366f1; margin-right: 8px;"></i> Actividades - ' + nombreAsignatura;
  btnNuevaActividad.href = `${baseUrlDetalle}/docente/agregar-actividad?id_curso=${idCursoDetalle}&id_asignatura=${encodeURIComponent(idAsignatura)}`;

  const totalEntregas = actividades.reduce((acc, item) => acc + (parseInt(item.total_entregas, 10) || 0), 0);
  const totalCalificadas = actividades.reduce((acc, item) => acc + (parseInt(item.total_calificadas, 10) || 0), 0);

  resumen.innerHTML = [
    '<span style="padding:8px 12px; border-radius:10px; background:rgba(99,102,241,.16); color:#818cf8; font-weight:600;">' + actividades.length + ' actividades</span>',
    '<span style="padding:8px 12px; border-radius:10px; background:rgba(14,165,233,.16); color:#38bdf8; font-weight:600;">' + totalEntregas + ' entregas</span>',
    '<span style="padding:8px 12px; border-radius:10px; background:rgba(16,185,129,.16); color:#34d399; font-weight:600;">' + totalCalificadas + ' calificadas</span>'
  ].join('');

  if (!actividades.length) {
    body.innerHTML = '<tr><td colspan="7" class="text-center" style="padding:24px; color:#94a3b8;">No hay actividades registradas para esta asignatura.</td></tr>';
  } else {
    body.innerHTML = actividades.map((item) => {
      const badge = badgeEstado(item.estado);
      const promedio = item.promedio_notas !== null ? parseFloat(item.promedio_notas).toFixed(2) : 'N/A';
      return '<tr>' +
        '<td><div style="font-weight:600; color:#e2e8f0;">' + (item.titulo || 'Actividad') + '</div><div style="font-size:12px; color:#94a3b8;">Promedio: ' + promedio + '</div></td>' +
        '<td>' + (item.tipo || 'Sin tipo') + '</td>' +
        '<td>' + formatoFecha(item.fecha_entrega) + '</td>' +
        '<td><span class="badge ' + badge.clase + '">' + badge.texto + '</span></td>' +
        '<td style="text-align:center;">' + (parseInt(item.total_entregas, 10) || 0) + '</td>' +
        '<td style="text-align:center;">' + (parseInt(item.total_calificadas, 10) || 0) + '</td>' +
        '<td style="text-align:center;"><a class="btn btn-sm btn-outline-info" href="' + item.url_entregas + '"><i class="ri-eye-line"></i></a></td>' +
      '</tr>';
    }).join('');
  }

  mostrarModalSeguro('modalActividadesAsignatura');
}

function abrirModalCalificaciones(idAsignatura, nombreAsignatura) {
  const resumen = resumenCalificacionesPorAsignatura[idAsignatura] || { total_actividades: 0, total_entregas: 0, total_calificadas: 0, promedio_general: null, actividades: [] };

  const titulo = document.getElementById('modalCalificacionesAsignaturaLabel');
  const resumenContenedor = document.getElementById('calificacionesAsignaturaResumen');
  const detalleContenedor = document.getElementById('calificacionesAsignaturaDetalle');
  const btnVerActividades = document.getElementById('btnModalVerActividades');

  if (!titulo || !resumenContenedor || !detalleContenedor || !btnVerActividades) return;

  titulo.innerHTML = '<i class="ri-bar-chart-box-line" style="color: #10b981; margin-right: 8px;"></i> Calificaciones - ' + nombreAsignatura;
  btnVerActividades.href = `${baseUrlDetalle}/docente/actividades?id_curso=${idCursoDetalle}`;

  const promedioGeneral = resumen.promedio_general !== null ? parseFloat(resumen.promedio_general).toFixed(2) : 'N/A';
  resumenContenedor.innerHTML = [
    '<div style="background:rgba(99,102,241,.15); border:1px solid rgba(99,102,241,.28); padding:14px; border-radius:12px;"><div style="font-size:12px; color:#a5b4fc;">Actividades</div><div style="font-size:22px; font-weight:700;">' + (parseInt(resumen.total_actividades, 10) || 0) + '</div></div>',
    '<div style="background:rgba(14,165,233,.15); border:1px solid rgba(14,165,233,.28); padding:14px; border-radius:12px;"><div style="font-size:12px; color:#7dd3fc;">Entregas recibidas</div><div style="font-size:22px; font-weight:700;">' + (parseInt(resumen.total_entregas, 10) || 0) + '</div></div>',
    '<div style="background:rgba(16,185,129,.15); border:1px solid rgba(16,185,129,.28); padding:14px; border-radius:12px;"><div style="font-size:12px; color:#6ee7b7;">Calificadas</div><div style="font-size:22px; font-weight:700;">' + (parseInt(resumen.total_calificadas, 10) || 0) + '</div></div>',
    '<div style="background:rgba(245,158,11,.15); border:1px solid rgba(245,158,11,.28); padding:14px; border-radius:12px;"><div style="font-size:12px; color:#fcd34d;">Promedio general</div><div style="font-size:22px; font-weight:700;">' + promedioGeneral + '</div></div>'
  ].join('');

  if (!resumen.actividades || !resumen.actividades.length) {
    detalleContenedor.innerHTML = '<div class="text-center" style="padding: 20px; color:#94a3b8; background:rgba(148,163,184,.08); border-radius:12px;">Aun no hay actividades calificadas para esta asignatura.</div>';
  } else {
    detalleContenedor.innerHTML = '<div style="margin-bottom:10px; font-weight:600;">Detalle por actividad</div>' +
      '<div class="table-responsive"><table class="table table-dark table-hover align-middle"><thead><tr><th>Actividad</th><th>Promedio</th><th style="text-align:center;">Entregas</th><th style="text-align:center;">Calificadas</th><th style="text-align:center;">Ver</th></tr></thead><tbody>' +
      resumen.actividades.map((item) => {
        const promedio = item.promedio_notas !== null ? parseFloat(item.promedio_notas).toFixed(2) : 'N/A';
        return '<tr>' +
          '<td>' + (item.titulo || 'Actividad') + '</td>' +
          '<td>' + promedio + '</td>' +
          '<td style="text-align:center;">' + (parseInt(item.total_entregas, 10) || 0) + '</td>' +
          '<td style="text-align:center;">' + (parseInt(item.total_calificadas, 10) || 0) + '</td>' +
          '<td style="text-align:center;"><a class="btn btn-sm btn-outline-success" href="' + item.url_entregas + '"><i class="ri-external-link-line"></i></a></td>' +
        '</tr>';
      }).join('') +
      '</tbody></table></div>';
  }

  mostrarModalSeguro('modalCalificacionesAsignatura');
}

function abrirModalPerfilAcademicoEstudiante(idEstudiante, nombreEstudiante) {
  const perfil = perfilAcademicoPorEstudiante[idEstudiante] || null;
  const titulo = document.getElementById('modalPerfilAcademicoEstudianteLabel');
  const contenido = document.getElementById('perfilAcademicoEstudianteContenido');
  if (!titulo || !contenido) return;

  titulo.innerHTML = '<i class="ri-user-star-line" style="color:#6366f1; margin-right:8px;"></i> Perfil academico - ' + nombreEstudiante;

  if (!perfil) {
    contenido.innerHTML = '<div class="text-center" style="padding:22px; color:#94a3b8;">No hay datos academicos disponibles para este estudiante.</div>';
    mostrarModalSeguro('modalPerfilAcademicoEstudiante');
    return;
  }

  const promedio = perfil.promedio_general !== null ? parseFloat(perfil.promedio_general).toFixed(2) : 'N/A';
  const progreso = perfil.total_actividades > 0 ? Math.round((perfil.total_entregadas / perfil.total_actividades) * 100) : 0;
  const foto = perfil.foto ? perfil.foto : 'default.png';

  contenido.innerHTML =
    '<div style="display:flex; flex-wrap:wrap; gap:16px; align-items:center; margin-bottom:16px;">' +
      '<img src="' + baseUrlDetalle + '/public/uploads/estudiantes/' + foto + '" onerror="this.onerror=null; this.src=\'' + baseUrlDetalle + '/public/uploads/estudiantes/default.png\'" alt="' + (perfil.nombre || nombreEstudiante) + '" style="width:72px; height:72px; border-radius:16px; object-fit:cover; border:2px solid rgba(99,102,241,.3);">' +
      '<div><div style="font-size:20px; font-weight:700;">' + (perfil.nombre || nombreEstudiante) + '</div><div style="color:#94a3b8;">Documento: ' + (perfil.documento || 'N/A') + '</div><div style="color:#94a3b8;">Matricula: ' + formatearFechaLarga(perfil.fecha_matricula) + '</div></div>' +
    '</div>' +
    '<div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(170px, 1fr)); gap: 12px;">' +
      '<div style="background:rgba(99,102,241,.15); border:1px solid rgba(99,102,241,.28); padding:14px; border-radius:12px;"><div style="font-size:12px; color:#a5b4fc;">Actividades</div><div style="font-size:22px; font-weight:700;">' + (parseInt(perfil.total_actividades, 10) || 0) + '</div></div>' +
      '<div style="background:rgba(14,165,233,.15); border:1px solid rgba(14,165,233,.28); padding:14px; border-radius:12px;"><div style="font-size:12px; color:#7dd3fc;">Entregadas</div><div style="font-size:22px; font-weight:700;">' + (parseInt(perfil.total_entregadas, 10) || 0) + '</div></div>' +
      '<div style="background:rgba(16,185,129,.15); border:1px solid rgba(16,185,129,.28); padding:14px; border-radius:12px;"><div style="font-size:12px; color:#6ee7b7;">Calificadas</div><div style="font-size:22px; font-weight:700;">' + (parseInt(perfil.total_calificadas, 10) || 0) + '</div></div>' +
      '<div style="background:rgba(245,158,11,.15); border:1px solid rgba(245,158,11,.28); padding:14px; border-radius:12px;"><div style="font-size:12px; color:#fcd34d;">Promedio</div><div style="font-size:22px; font-weight:700;">' + promedio + '</div></div>' +
    '</div>';

  mostrarModalSeguro('modalPerfilAcademicoEstudiante');
}

function abrirModalCalificacionesEstudiante(idEstudiante, nombreEstudiante) {
  const perfil = perfilAcademicoPorEstudiante[idEstudiante] || null;
  const calificaciones = calificacionesPorEstudiante[idEstudiante] || [];
  const titulo = document.getElementById('modalCalificacionesEstudianteLabel');
  const resumen = document.getElementById('calificacionesEstudianteResumen');
  const detalle = document.getElementById('calificacionesEstudianteDetalle');
  if (!titulo || !resumen || !detalle) return;

  titulo.innerHTML = '<i class="ri-file-edit-line" style="color:#10b981; margin-right:8px;"></i> Calificaciones - ' + nombreEstudiante;

  const totalActividades = perfil ? (parseInt(perfil.total_actividades, 10) || 0) : calificaciones.length;
  const totalEntregadas = perfil ? (parseInt(perfil.total_entregadas, 10) || 0) : calificaciones.filter(item => item.id_entrega).length;
  const totalCalificadas = perfil ? (parseInt(perfil.total_calificadas, 10) || 0) : calificaciones.filter(item => item.nota !== null).length;
  const promedio = perfil && perfil.promedio_general !== null ? parseFloat(perfil.promedio_general).toFixed(2) : 'N/A';

  resumen.innerHTML = [
    '<div style="background:rgba(99,102,241,.15); border:1px solid rgba(99,102,241,.28); padding:14px; border-radius:12px;"><div style="font-size:12px; color:#a5b4fc;">Actividades</div><div style="font-size:22px; font-weight:700;">' + totalActividades + '</div></div>',
    '<div style="background:rgba(14,165,233,.15); border:1px solid rgba(14,165,233,.28); padding:14px; border-radius:12px;"><div style="font-size:12px; color:#7dd3fc;">Entregadas</div><div style="font-size:22px; font-weight:700;">' + totalEntregadas + '</div></div>',
    '<div style="background:rgba(16,185,129,.15); border:1px solid rgba(16,185,129,.28); padding:14px; border-radius:12px;"><div style="font-size:12px; color:#6ee7b7;">Calificadas</div><div style="font-size:22px; font-weight:700;">' + totalCalificadas + '</div></div>',
    '<div style="background:rgba(245,158,11,.15); border:1px solid rgba(245,158,11,.28); padding:14px; border-radius:12px;"><div style="font-size:12px; color:#fcd34d;">Promedio</div><div style="font-size:22px; font-weight:700;">' + promedio + '</div></div>'
  ].join('');

  if (!calificaciones.length) {
    detalle.innerHTML = '<div class="text-center" style="padding: 20px; color:#94a3b8; background:rgba(148,163,184,.08); border-radius:12px;">No hay actividades registradas para mostrar calificaciones.</div>';
    mostrarModalSeguro('modalCalificacionesEstudiante');
    return;
  }

  detalle.innerHTML = '<div class="table-responsive"><table class="table table-dark table-hover align-middle"><thead><tr><th>Actividad</th><th>Asignatura</th><th>Entrega</th><th>Nota</th><th>Observacion</th><th style="text-align:center;">Editar</th></tr></thead><tbody>' +
    calificaciones.map((item) => {
      const estadoEntrega = item.id_entrega ? (item.estado_entrega || 'Entregado') : 'Sin entregar';
      const nota = item.nota !== null ? parseFloat(item.nota).toFixed(2) : 'Sin nota';
      const observacion = item.observacion ? item.observacion : '-';
      const botonEditar = item.id_entrega ? '<a href="' + item.url_entregas + '" class="btn btn-sm btn-outline-success" title="Ir a calificar"><i class="ri-external-link-line"></i></a>' : '<span style="color:#64748b; font-size:12px;">No disponible</span>';

      return '<tr>' +
        '<td><div style="font-weight:600;">' + (item.titulo || 'Actividad') + '</div><div style="font-size:12px; color:#94a3b8;">' + formatoFecha(item.fecha_limite) + '</div></td>' +
        '<td>' + (item.asignatura || 'Sin asignatura') + '</td>' +
        '<td>' + estadoEntrega + '</td>' +
        '<td>' + nota + '</td>' +
        '<td style="max-width:260px; white-space:normal; color:#94a3b8;">' + observacion + '</td>' +
        '<td style="text-align:center;">' + botonEditar + '</td>' +
      '</tr>';
    }).join('') +
    '</tbody></table></div>';

  mostrarModalSeguro('modalCalificacionesEstudiante');
}

function filtrarEstudiantes() {
  const input = document.getElementById('searchStudent');
  const table = document.getElementById('tablaEstudiantes');
  if (!input || !table) return;

  const filter = input.value.toLowerCase();
  const rows = table.getElementsByClassName('student-row');

  for (let i = 0; i < rows.length; i++) {
    const nameCell = rows[i].querySelector('.student-name');
    const docCell = rows[i].querySelector('.student-document');
    const nameText = nameCell ? nameCell.textContent.toLowerCase() : '';
    const docText = docCell ? docCell.textContent.toLowerCase() : '';
    rows[i].style.display = (nameText.indexOf(filter) > -1 || docText.indexOf(filter) > -1) ? '' : 'none';
  }
}

document.addEventListener('click', function(e) {
  const btnActividades = e.target.closest('.btn-open-actividades-modal');
  if (btnActividades) {
    abrirModalActividades(btnActividades.dataset.asignaturaId, btnActividades.dataset.asignaturaNombre || 'Asignatura');
    return;
  }

  const btnCalificaciones = e.target.closest('.btn-open-calificaciones-modal');
  if (btnCalificaciones) {
    abrirModalCalificaciones(btnCalificaciones.dataset.asignaturaId, btnCalificaciones.dataset.asignaturaNombre || 'Asignatura');
    return;
  }

  const btnPerfilEstudiante = e.target.closest('.btn-open-perfil-estudiante-modal');
  if (btnPerfilEstudiante) {
    abrirModalPerfilAcademicoEstudiante(btnPerfilEstudiante.dataset.estudianteId, btnPerfilEstudiante.dataset.estudianteNombre || 'Estudiante');
    return;
  }

  const btnCalificacionesEstudiante = e.target.closest('.btn-open-calificaciones-estudiante-modal');
  if (btnCalificacionesEstudiante) {
    abrirModalCalificacionesEstudiante(btnCalificacionesEstudiante.dataset.estudianteId, btnCalificacionesEstudiante.dataset.estudianteNombre || 'Estudiante');
  }
});

document.addEventListener('DOMContentLoaded', function() {
  const cards = document.querySelectorAll('.stat-card');
  cards.forEach((card, index) => {
    card.style.opacity = '0';
    card.style.transform = 'translateY(20px)';

    setTimeout(() => {
      card.style.transition = 'all 0.5s ease';
      card.style.opacity = '1';
      card.style.transform = 'translateY(0)';
    }, index * 100);
  });
});

// ── Helpers para modales de stat-cards ────────────────────────────────────────

function diasRestantes(fechaIso) {
  if (!fechaIso) return null;
  const hoy = new Date(); hoy.setHours(0, 0, 0, 0);
  const fecha = new Date(`${fechaIso}T00:00:00`);
  if (isNaN(fecha.getTime())) return null;
  return Math.ceil((fecha - hoy) / 86400000);
}

function fechaBadge(fechaIso, dias) {
  const str = formatoFecha(fechaIso);
  if (dias === null) return `<span style="color:#94a3b8;">${str}</span>`;
  const color = dias < 0 ? '#ef4444' : (dias <= 3 ? '#f59e0b' : '#10b981');
  const label = dias < 0
    ? `Venció hace ${Math.abs(dias)}d`
    : (dias === 0 ? 'Vence hoy' : `${dias}d restantes`);
  return `<div style="color:#e2e8f0;">${str}</div><div style="font-size:11px;color:${color};">${label}</div>`;
}

function notaColor(nota) {
  if (nota === null || nota === undefined) return '#94a3b8';
  return nota >= 4.0 ? '#10b981' : (nota >= 3.0 ? '#f59e0b' : '#ef4444');
}

// ── MODAL: Pendientes por Calificar ───────────────────────────────────────────

function abrirModalPendientesCalificar() {
  const datos   = parseArrayAttr('pendientesCalificar');
  const resumen = document.getElementById('pendientesResumen');
  const body    = document.getElementById('pendientesBody');
  if (!resumen || !body) return;

  const totalPend = datos.reduce((s, a) => s + (parseInt(a.pendientes, 10) || 0), 0);
  resumen.innerHTML = [
    `<span style="padding:8px 14px;border-radius:10px;background:rgba(245,87,108,.15);color:#f5576c;font-weight:600;">${datos.length} actividades</span>`,
    `<span style="padding:8px 14px;border-radius:10px;background:rgba(245,158,11,.15);color:#fcd34d;font-weight:600;">${totalPend} entregas sin calificar</span>`,
  ].join('');

  if (!datos.length) {
    body.innerHTML = '<tr><td colspan="7" class="text-center" style="padding:24px;color:#94a3b8;">No hay actividades con entregas pendientes de calificación.</td></tr>';
  } else {
    body.innerHTML = datos.map(a => {
      const dias = diasRestantes(a.fecha_entrega);
      return `<tr>
        <td><div style="font-weight:600;color:#e2e8f0;">${esc(a.titulo)}</div></td>
        <td><span style="color:#94a3b8;font-size:13px;">${esc(a.asignatura)}</span></td>
        <td>${fechaBadge(a.fecha_entrega, dias)}</td>
        <td style="text-align:center;"><span style="color:#7dd3fc;">${a.total_entregas}</span></td>
        <td style="text-align:center;"><span style="color:#6ee7b7;">${a.total_calificadas}</span></td>
        <td style="text-align:center;">
          <span style="background:rgba(245,158,11,.2);color:#fcd34d;padding:4px 10px;border-radius:8px;font-weight:700;">${a.pendientes}</span>
        </td>
        <td style="text-align:center;">
          <a href="${esc(a.url_entregas)}" class="btn btn-sm btn-outline-warning" title="Ir a calificar">
            <i class="ri-edit-line"></i>
          </a>
        </td>
      </tr>`;
    }).join('');
  }

  mostrarModalSeguro('modalPendientesCalificar');
}

// ── MODAL: Estudiantes en Riesgo ──────────────────────────────────────────────

function abrirModalEstudiantesRiesgo() {
  const datos   = parseArrayAttr('estudiantesRiesgo');
  const resumen = document.getElementById('riesgoResumen');
  const body    = document.getElementById('riesgoBody');
  if (!resumen || !body) return;

  const conProm = datos.filter(e => e.promedio_general !== null && e.promedio_general < 3.0).length;
  const sinEntr = datos.filter(e => e.total_actividades > 0 && e.total_entregadas === 0).length;
  resumen.innerHTML = [
    `<span style="padding:8px 14px;border-radius:10px;background:rgba(239,68,68,.15);color:#f87171;font-weight:600;">${datos.length} en riesgo</span>`,
    `<span style="padding:8px 14px;border-radius:10px;background:rgba(245,158,11,.15);color:#fcd34d;font-weight:600;">${conProm} promedio bajo</span>`,
    `<span style="padding:8px 14px;border-radius:10px;background:rgba(99,102,241,.15);color:#a5b4fc;font-weight:600;">${sinEntr} sin entregas</span>`,
  ].join('');

  if (!datos.length) {
    body.innerHTML = '<tr><td colspan="6" class="text-center" style="padding:24px;color:#94a3b8;">No hay estudiantes en situación de riesgo académico.</td></tr>';
  } else {
    body.innerHTML = datos.map(e => {
      const prom  = e.promedio_general !== null ? parseFloat(e.promedio_general).toFixed(2) : 'Sin nota';
      const pc    = notaColor(e.promedio_general);
      const asist = e.porcentaje_asistencia !== null
        ? parseFloat(e.porcentaje_asistencia).toFixed(1) + '%' : 'N/A';
      const ac = e.porcentaje_asistencia === null ? '#94a3b8'
        : (e.porcentaje_asistencia < 70 ? '#ef4444'
          : (e.porcentaje_asistencia < 85 ? '#f59e0b' : '#10b981'));
      const nombreEsc = esc(e.nombre).replace(/'/g, "\\'");
      return `<tr>
        <td>
          <div style="display:flex;align-items:center;gap:12px;">
            <img src="${baseUrlDetalle}/public/uploads/estudiantes/${esc(e.foto)}"
                 onerror="this.onerror=null;this.src='${baseUrlDetalle}/public/uploads/estudiantes/default.png'"
                 style="width:40px;height:40px;border-radius:10px;object-fit:cover;">
            <div>
              <div style="font-weight:600;color:#e2e8f0;">${esc(e.nombre)}</div>
              <div style="font-size:12px;color:#94a3b8;">${esc(e.documento)}</div>
            </div>
          </div>
        </td>
        <td style="text-align:center;"><span style="font-size:18px;font-weight:700;color:${pc};">${prom}</span></td>
        <td style="text-align:center;"><span style="font-weight:600;color:${ac};">${asist}</span></td>
        <td style="text-align:center;"><span style="color:#94a3b8;">${e.total_entregadas}/${e.total_actividades}</span></td>
        <td><span style="background:rgba(239,68,68,.15);color:#f87171;padding:4px 10px;border-radius:8px;font-size:12px;">${esc(e.motivo)}</span></td>
        <td style="text-align:center;">
          <button class="btn btn-sm btn-outline-info"
                  onclick="bootstrap.Modal.getInstance(document.getElementById('modalEstudiantesRiesgo'))?.hide();abrirModalPerfilAcademicoEstudiante(${e.id_estudiante},'${nombreEsc}');"
                  title="Ver perfil académico">
            <i class="ri-user-star-line"></i>
          </button>
        </td>
      </tr>`;
    }).join('');
  }

  mostrarModalSeguro('modalEstudiantesRiesgo');
}

// ── MODAL: Próximas Actividades ───────────────────────────────────────────────

function abrirModalProximasActividades() {
  const datos   = parseArrayAttr('proximasActividadesDet');
  const resumen = document.getElementById('proximasResumen');
  const body    = document.getElementById('proximasBody');
  if (!resumen || !body) return;

  const hoy3     = new Date(Date.now() + 3 * 86400000).toISOString().slice(0, 10);
  const urgentes = datos.filter(a => a.fecha_entrega <= hoy3).length;
  resumen.innerHTML = [
    `<span style="padding:8px 14px;border-radius:10px;background:rgba(0,242,254,.1);color:#38bdf8;font-weight:600;">${datos.length} en los próximos 7 días</span>`,
    `<span style="padding:8px 14px;border-radius:10px;background:rgba(239,68,68,.1);color:#f87171;font-weight:600;">${urgentes} vencen en ≤ 3 días</span>`,
  ].join('');

  if (!datos.length) {
    body.innerHTML = '<tr><td colspan="6" class="text-center" style="padding:24px;color:#94a3b8;">No hay actividades próximas a vencer en los siguientes 7 días.</td></tr>';
  } else {
    body.innerHTML = datos.map(a => {
      const badge = badgeEstado(a.estado);
      const dias  = diasRestantes(a.fecha_entrega);
      return `<tr>
        <td style="font-weight:600;color:#e2e8f0;">${esc(a.titulo)}</td>
        <td><span style="color:#94a3b8;font-size:13px;">${esc(a.asignatura)}</span></td>
        <td>${esc(a.tipo)}</td>
        <td>${fechaBadge(a.fecha_entrega, dias)}</td>
        <td style="text-align:center;"><span class="badge ${badge.clase}">${badge.texto}</span></td>
        <td style="text-align:center;"><span style="color:#7dd3fc;">${a.total_entregas}</span></td>
      </tr>`;
    }).join('');
  }

  mostrarModalSeguro('modalProximasActividades');
}

// ── MODAL: Promedio General ───────────────────────────────────────────────────

function abrirModalPromedioGeneral() {
  const datos   = parseArrayAttr('promedioDetalle');
  const resumen = document.getElementById('promedioResumen');
  const detCont = document.getElementById('promedioDetallePorAsignatura');
  if (!resumen || !detCont) return;

  const totalEst  = Number(detalleApp?.dataset?.totalEstudiantesCurso || 0);
  const evaluados = Number(detalleApp?.dataset?.totalEvaluados || 0);
  const promGen   = Number(detalleApp?.dataset?.promedioGeneralFloat || 0);
  const pc        = notaColor(promGen > 0 ? promGen : null);

  resumen.innerHTML = [
    `<div style="background:rgba(67,233,123,.1);border:1px solid rgba(67,233,123,.25);padding:16px;border-radius:12px;text-align:center;">
       <div style="font-size:12px;color:#6ee7b7;margin-bottom:4px;">Promedio del Curso</div>
       <div style="font-size:30px;font-weight:800;color:${pc};">${promGen > 0 ? promGen.toFixed(2) : 'N/A'}</div>
     </div>`,
    `<div style="background:rgba(99,102,241,.1);border:1px solid rgba(99,102,241,.25);padding:16px;border-radius:12px;text-align:center;">
       <div style="font-size:12px;color:#a5b4fc;margin-bottom:4px;">Total Estudiantes</div>
       <div style="font-size:30px;font-weight:800;">${totalEst}</div>
     </div>`,
    `<div style="background:rgba(16,185,129,.1);border:1px solid rgba(16,185,129,.25);padding:16px;border-radius:12px;text-align:center;">
       <div style="font-size:12px;color:#34d399;margin-bottom:4px;">Evaluados</div>
       <div style="font-size:30px;font-weight:800;">${evaluados}</div>
     </div>`,
  ].join('');

  if (!datos.length) {
    detCont.innerHTML = '<div class="text-center" style="padding:20px;color:#94a3b8;background:rgba(148,163,184,.08);border-radius:12px;">No hay calificaciones registradas aún.</div>';
  } else {
    detCont.innerHTML = '<div style="font-weight:600;margin-bottom:12px;color:#e2e8f0;">Promedio por asignatura</div>' +
      '<div class="table-responsive"><table class="table table-dark table-hover align-middle" style="margin:0;"><thead><tr>' +
      '<th>Asignatura</th><th style="text-align:center;">Promedio</th><th style="text-align:center;">Actividades</th><th style="text-align:center;">Entregas</th><th style="text-align:center;">Calificadas</th>' +
      '</tr></thead><tbody>' +
      datos.map(d => {
        const prom = d.promedio_general !== null ? parseFloat(d.promedio_general).toFixed(2) : 'N/A';
        const dc   = notaColor(d.promedio_general);
        return `<tr>
          <td style="font-weight:600;">${esc(d.asignatura)}</td>
          <td style="text-align:center;"><span style="font-size:18px;font-weight:700;color:${dc};">${prom}</span></td>
          <td style="text-align:center;">${d.total_actividades}</td>
          <td style="text-align:center;">${d.total_entregas}</td>
          <td style="text-align:center;">${d.total_calificadas}</td>
        </tr>`;
      }).join('') +
      '</tbody></table></div>';
  }

  mostrarModalSeguro('modalPromedioGeneral');
}
