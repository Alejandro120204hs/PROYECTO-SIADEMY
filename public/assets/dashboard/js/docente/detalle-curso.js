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

const detalleActividadesPorAsignatura = parseJsonAttr('detalleActividades');
const resumenCalificacionesPorAsignatura = parseJsonAttr('resumenCalificaciones');
const perfilAcademicoPorEstudiante = parseJsonAttr('perfilEstudiantes');
const calificacionesPorEstudiante = parseJsonAttr('calificacionesEstudiantes');

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
