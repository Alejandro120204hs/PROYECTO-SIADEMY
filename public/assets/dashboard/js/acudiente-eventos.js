(function () {
  // ── Datos de eventos desde PHP ────────────────────────────────────────────
  var appGrid = document.getElementById('appGrid');
  var rawEvents = [];
  try { rawEvents = JSON.parse(appGrid.dataset.eventos || '[]'); } catch(e) {}

  // ── Helpers de modal (sin Bootstrap class, compatible con dark theme) ─────
  function showModalSafe(el) {
    if (!el) return;
    try { bootstrap.Modal.getOrCreateInstance(el).show(); } catch(e) {
      el.style.display = 'flex'; el.classList.add('show');
    }
  }
  function hideModalSafe(el) {
    if (!el) return;
    try { bootstrap.Modal.getInstance(el)?.hide(); } catch(e) {
      el.style.display = 'none'; el.classList.remove('show');
    }
  }
  function escapeHtml(str) {
    return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
  }

  // ── Mapear eventos al índice por fecha ────────────────────────────────────
  var iconByCategory = { meetings:'ri-user-voice-line', exams:'ri-file-edit-line', activities:'ri-calendar-event-line' };
  var eventsData = rawEvents.reduce(function(acc, item) {
    var dateKey = String(item.fecha_evento || '').slice(0, 10);
    if (!dateKey) return acc;
    if (!acc[dateKey]) acc[dateKey] = [];
    var cat = item.category || 'activities';
    acc[dateKey].push({
      title:      item.nombre_evento    || 'Evento',
      description:item.descripcion      || '',
      category:   cat,
      icon:       item.icon             || iconByCategory[cat] || 'ri-calendar-event-line',
      time:       item.hora_inicio      || '',
      hora_fin:   item.hora_fin         || '',
      ubicacion:  item.ubicacion        || '',
      responsable:item.responsable      || '',
      correo:     item.correo_contacto  || '',
      source:     item.fuente           || 'evento',
    });
    return acc;
  }, {});

  // ── Modal de día ──────────────────────────────────────────────────────────
  var dayEventsModalEl    = document.getElementById('dayEventsModal');
  var dayEventsModalTitle = document.getElementById('dayEventsModalLabel');
  var dayEventsModalBody  = document.getElementById('dayEventsModalBody');

  if (dayEventsModalEl) {
    dayEventsModalEl.querySelectorAll('[data-bs-dismiss="modal"]').forEach(function(btn) {
      btn.addEventListener('click', function() { hideModalSafe(dayEventsModalEl); });
    });
    dayEventsModalEl.addEventListener('click', function(e) {
      if (e.target === dayEventsModalEl) hideModalSafe(dayEventsModalEl);
    });
  }

  function renderDayEventsModal(dateString, events) {
    if (!dayEventsModalEl) return;

    // Ocultar eventos pasados: si el día ya pasó se descartan todos sus
    // eventos; si es hoy, se descartan los que ya terminaron (hora_fin/hora_inicio).
    var now      = new Date();
    var hoyStr   = now.getFullYear() + '-' + String(now.getMonth() + 1).padStart(2, '0') + '-' + String(now.getDate()).padStart(2, '0');
    var nowHHMM  = String(now.getHours()).padStart(2, '0') + ':' + String(now.getMinutes()).padStart(2, '0');
    events = (events || []).filter(function (ev) {
      if (dateString < hoyStr) return false;
      if (dateString === hoyStr) {
        var fin = (ev.hora_fin || ev.time || '').slice(0, 5);
        if (fin && fin < nowHHMM) return false;
      }
      return true;
    });

    var parts = dateString.split('-');
    dayEventsModalTitle.textContent = 'Eventos del ' + parts[2] + '/' + parts[1] + '/' + parts[0];

    if (!events || events.length === 0) {
      dayEventsModalBody.innerHTML = '<div style="border:1px dashed rgba(255,255,255,.25);border-radius:12px;padding:22px;text-align:center;color:#c5d1ee;background:#171f45;"><i class="ri-calendar-line" style="font-size:24px;"></i><br>No hay eventos para este día.</div>';
      showModalSafe(dayEventsModalEl);
      return;
    }

    var html = events.map(function(ev) {
      var hour = ev.time ? '<span><i class="ri-time-line"></i> ' + escapeHtml(String(ev.time).slice(0,5)) + '</span>' : '<span><i class="ri-time-line"></i> Sin hora</span>';
      var detalleBtn = '<button class="btn-detalle-calendario"' +
        ' data-nombre="'     + escapeHtml(ev.title)       + '"' +
        ' data-descripcion="'+ escapeHtml(ev.description) + '"' +
        ' data-tipo="'       + escapeHtml(ev.category)    + '"' +
        ' data-fecha="'      + escapeHtml(dateString)      + '"' +
        ' data-hora-inicio="'+ escapeHtml(String(ev.time||'').slice(0,5))    + '"' +
        ' data-hora-fin="'   + escapeHtml(String(ev.hora_fin||'').slice(0,5)) + '"' +
        ' data-ubicacion="'  + escapeHtml(ev.ubicacion)   + '"' +
        ' data-responsable="'+ escapeHtml(ev.responsable) + '"' +
        ' data-correo="'     + escapeHtml(ev.correo)      + '"' +
        ' data-fuente="'     + escapeHtml(ev.source)      + '"' +
        ' style="margin-top:10px;background:#6366f1;color:#fff;border:none;border-radius:8px;padding:6px 14px;font-size:12px;cursor:pointer;display:inline-flex;align-items:center;gap:6px;">' +
        '<i class="ri-information-line"></i> Ver detalles</button>';

      return '<article style="background:#171f45;border:1px solid rgba(255,255,255,.08);border-radius:12px;padding:14px;color:#e6e9f4;">' +
        '<div style="display:flex;align-items:center;gap:10px;margin-bottom:6px;">' +
        '<div style="width:34px;height:34px;border-radius:10px;display:grid;place-items:center;font-size:18px;background:#232e60;color:#a4b1ff;"><i class="' + escapeHtml(ev.icon) + '"></i></div>' +
        '<span style="display:inline-flex;align-items:center;padding:4px 10px;border-radius:999px;font-size:12px;font-weight:600;color:#dbe2ff;background:rgba(79,70,229,.25);border:1px solid rgba(164,177,255,.25);">' + escapeHtml(ev.category) + '</span>' +
        '</div>' +
        '<h6 style="margin:0 0 6px;font-size:16px;color:#fff;">' + escapeHtml(ev.title) + '</h6>' +
        '<p style="margin:0;color:#b8c2df;font-size:14px;line-height:1.4;">' + escapeHtml(ev.description || 'Sin descripción') + '</p>' +
        '<div style="margin-top:8px;display:flex;gap:14px;flex-wrap:wrap;font-size:12px;color:#9daccc;">' + hour + '</div>' +
        detalleBtn + '</article>';
    }).join('');

    dayEventsModalBody.innerHTML = '<div style="margin-bottom:10px;color:#c5d1ee;font-size:13px;">Total: ' + events.length + '</div><div style="display:grid;gap:12px;">' + html + '</div>';

    // Delegación: Ver detalles desde el modal de día
    dayEventsModalBody.querySelectorAll('.btn-detalle-calendario').forEach(function(btn) {
      btn.addEventListener('click', function() {
        abrirDetalleEvento(btn.dataset);
        hideModalSafe(dayEventsModalEl);
      });
    });

    showModalSafe(dayEventsModalEl);
  }

  // ── Modal de detalle ──────────────────────────────────────────────────────
  var detalleModal   = document.getElementById('modalDetalleEvento');
  var bsDetalleModal = detalleModal ? bootstrap.Modal.getOrCreateInstance(detalleModal) : null;

  function abrirDetalleEvento(d) {
    if (!detalleModal) return;
    var fecha   = d.fecha || d.date || '';
    var parts   = fecha.split('-');
    var fechaFmt = parts.length === 3 ? parts[2] + '/' + parts[1] + '/' + parts[0] : fecha;
    var horaI   = d.horaInicio || '';
    var horaF   = d.horaFin   || '';
    var horario = horaI && horaF ? horaI + ' — ' + horaF : horaI ? 'Desde ' + horaI : 'Sin hora definida';

    detalleModal.querySelector('#mde-tipo').textContent        = d.tipo        || 'Evento';
    detalleModal.querySelector('#mde-titulo').textContent      = d.nombre      || '';
    detalleModal.querySelector('#mde-descripcion').textContent = d.descripcion || 'Sin descripción.';
    detalleModal.querySelector('#mde-fecha').textContent       = fechaFmt;
    detalleModal.querySelector('#mde-horario').textContent     = horario;

    var ubicWrap = detalleModal.querySelector('#mde-ubicacion-wrap');
    var respWrap = detalleModal.querySelector('#mde-responsable-wrap');
    if (d.ubicacion || d.responsable) {
      detalleModal.querySelector('#mde-ubicacion').textContent   = d.ubicacion   || '—';
      detalleModal.querySelector('#mde-responsable').textContent = d.responsable || '—';
      detalleModal.querySelector('#mde-correo').textContent      = d.correo      || '';
      if (ubicWrap) ubicWrap.style.display = '';
      if (respWrap) respWrap.style.display = '';
    } else {
      if (ubicWrap) ubicWrap.style.display = 'none';
      if (respWrap) respWrap.style.display = 'none';
    }
    if (bsDetalleModal) bsDetalleModal.show();
  }

  // Botón "Ver detalles" en las cards de la lista
  document.querySelectorAll('.btn-ver-detalle-evento').forEach(function(btn) {
    btn.addEventListener('click', function(e) {
      e.stopPropagation();
      var card = btn.closest('.event-card');
      if (!card) return;
      abrirDetalleEvento({
        nombre:      card.dataset.nombre,
        descripcion: card.dataset.descripcion,
        tipo:        card.dataset.tipo,
        fecha:       card.dataset.date,
        horaInicio:  card.dataset.horaInicio,
        horaFin:     card.dataset.horaFin,
        ubicacion:   card.dataset.ubicacion,
        responsable: card.dataset.responsable,
        correo:      card.dataset.correo,
        fuente:      card.dataset.fuente,
      });
    });
  });

  // ── Generador del calendario ──────────────────────────────────────────────
  var now   = new Date();
  var curM  = now.getMonth();
  var curY  = now.getFullYear();
  var months = ['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'];
  var days   = ['Dom','Lun','Mar','Mié','Jue','Vie','Sáb'];

  function generateCalendar(month, year) {
    var grid = document.getElementById('calendarLargeGrid');
    if (!grid) return;

    var firstDay     = new Date(year, month, 1).getDay();
    var daysInMonth  = new Date(year, month + 1, 0).getDate();
    var daysInPrev   = new Date(year, month, 0).getDate();
    var html = '';

    days.forEach(function(d) { html += '<div class="calendar-large-header">' + d + '</div>'; });

    for (var i = firstDay - 1; i >= 0; i--) {
      html += '<div class="calendar-large-day other-month"><div class="calendar-day-number">' + (daysInPrev - i) + '</div></div>';
    }

    for (var day = 1; day <= daysInMonth; day++) {
      var ds = year + '-' + String(month + 1).padStart(2, '0') + '-' + String(day).padStart(2, '0');
      var isToday = day === now.getDate() && month === now.getMonth() && year === now.getFullYear();
      var dayEvs  = eventsData[ds] || [];
      var cls     = 'calendar-large-day' + (isToday ? ' today' : '') + (dayEvs.length > 0 ? ' has-events' : '');
      var evHtml  = '';

      if (dayEvs.length > 0) {
        evHtml = '<div class="calendar-day-events">';
        dayEvs.slice(0, 2).forEach(function(ev) {
          evHtml += '<div class="calendar-mini-event ' + ev.category + '"><i class="' + ev.icon + '"></i><span>' + escapeHtml(ev.title) + '</span></div>';
        });
        if (dayEvs.length > 2) evHtml += '<div class="calendar-mini-event more-events">+' + (dayEvs.length - 2) + ' más</div>';
        evHtml += '</div>';
      }
      html += '<div class="' + cls + '" data-date="' + ds + '"><div class="calendar-day-number">' + day + '</div>' + evHtml + '</div>';
    }

    var totalCells = Math.ceil((firstDay + daysInMonth) / 7) * 7;
    for (var r = 1; r <= totalCells - (firstDay + daysInMonth); r++) {
      html += '<div class="calendar-large-day other-month"><div class="calendar-day-number">' + r + '</div></div>';
    }

    grid.innerHTML = html;
    var mye = document.getElementById('calendarMonthYear');
    if (mye) mye.textContent = months[month] + ' ' + year;

    grid.querySelectorAll('.calendar-large-day[data-date]').forEach(function(el) {
      el.addEventListener('click', function() {
        renderDayEventsModal(el.dataset.date, eventsData[el.dataset.date] || []);
      });
    });
  }

  document.getElementById('prevMonthEvents')?.addEventListener('click', function() {
    curM--; if (curM < 0) { curM = 11; curY--; } generateCalendar(curM, curY);
  });
  document.getElementById('nextMonthEvents')?.addEventListener('click', function() {
    curM++; if (curM > 11) { curM = 0; curY++; } generateCalendar(curM, curY);
  });
  document.getElementById('todayBtn')?.addEventListener('click', function() {
    curM = now.getMonth(); curY = now.getFullYear(); generateCalendar(curM, curY);
  });

  generateCalendar(curM, curY);

  // ── Filtros y búsqueda ────────────────────────────────────────────────────
  var eventCards = document.querySelectorAll('.event-card');
  var activeFilter = 'all';

  function applyFilters() {
    var search = (document.getElementById('searchEvents')?.value || '').toLowerCase().trim();
    eventCards.forEach(function(card) {
      var cat      = (card.dataset.category || '').toLowerCase();
      var upcoming = card.dataset.upcoming === '1';
      var title    = (card.querySelector('h4')?.textContent || '').toLowerCase();
      var desc     = (card.querySelector('p')?.textContent  || '').toLowerCase();
      var passF = activeFilter === 'all' ? true : activeFilter === 'upcoming' ? upcoming : cat === activeFilter;
      var passS = !search || title.includes(search) || desc.includes(search);
      card.style.display = (passF && passS) ? '' : 'none';
    });
  }

  document.querySelectorAll('.filter-tab-event').forEach(function(tab) {
    tab.addEventListener('click', function() {
      document.querySelectorAll('.filter-tab-event').forEach(function(t) { t.classList.remove('active'); });
      tab.classList.add('active');
      activeFilter = tab.dataset.filter || 'all';
      applyFilters();
    });
  });
  document.getElementById('searchEvents')?.addEventListener('input', applyFilters);

  document.querySelectorAll('.btn-view').forEach(function(btn) {
    btn.addEventListener('click', function() {
      document.querySelectorAll('.btn-view').forEach(function(b) { b.classList.remove('active'); });
      btn.classList.add('active');
      var cont = document.getElementById('eventsContainer');
      if (cont) cont.classList.toggle('list-view', btn.dataset.view === 'list');
    });
  });

  applyFilters();
})();
