// ============================================================
//  MIS MATERIAS — JS
//  Filtros, toggle vista grid/tabla, búsqueda, sidebar
// ============================================================

document.addEventListener('DOMContentLoaded', () => {
    initFilters();
    initViewToggle();
    initSearch();
    initSidebar();
});

// ── Helpers ───────────────────────────────────────────────────────────────────

function allCards() {
    return Array.from(document.querySelectorAll('.materia-card'));
}

function badgeClass(status) {
    if (status === 'excelente') return 'badge-excel';
    if (status === 'bueno')     return 'badge-bueno';
    if (status === 'riesgo')    return 'badge-riesgo';
    if (status === 'critico')   return 'badge-critico';
    return 'badge-sin';
}

function statusLabel(status) {
    const map = { excelente: 'Excelente', bueno: 'Bueno', riesgo: 'En riesgo', critico: 'Crítico', 'sin-nota': 'Sin nota' };
    return map[status] || '—';
}

// ── FILTROS ───────────────────────────────────────────────────────────────────

let filtroActivo = 'todas';
let busquedaActiva = '';

function applyFilters() {
    const cards = allCards();
    cards.forEach(card => {
        const status  = card.dataset.status || '';
        const nombre  = card.querySelector('.materia-title')?.textContent.toLowerCase() || '';
        const prof    = card.querySelector('.profesor-info strong')?.textContent.toLowerCase() || '';
        const text    = nombre + ' ' + prof;

        const matchFiltro = filtroActivo === 'todas' || status === filtroActivo;
        const matchBusqueda = !busquedaActiva || text.includes(busquedaActiva);

        card.style.display = matchFiltro && matchBusqueda ? '' : 'none';
    });

    // Actualizar fila de tabla si está activa
    document.querySelectorAll('.tabla-row').forEach(row => {
        const status  = row.dataset.status || '';
        const nombre  = row.querySelector('.tr-nombre')?.textContent.toLowerCase() || '';
        const prof    = row.querySelector('.tr-prof')?.textContent.toLowerCase() || '';
        const text    = nombre + ' ' + prof;

        const matchFiltro  = filtroActivo === 'todas' || status === filtroActivo;
        const matchBusqueda = !busquedaActiva || text.includes(busquedaActiva);

        row.style.display = matchFiltro && matchBusqueda ? '' : 'none';
    });
}

function initFilters() {
    document.querySelectorAll('.filter-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            filtroActivo = btn.dataset.filter;
            applyFilters();
        });
    });
}

// ── VISTA GRID / TABLA ────────────────────────────────────────────────────────

let vistaActual = 'grid';

function buildTable() {
    const existing = document.getElementById('tablaMaterias');
    if (existing) return existing;

    const cards = allCards();
    const tbody = cards.map(card => {
        const status  = card.dataset.status || 'sin-nota';
        const url     = card.dataset.url    || '#';
        const nombre  = card.querySelector('.materia-title')?.textContent.trim() || '—';
        const desc    = card.querySelector('.materia-subtitle')?.textContent.trim() || '—';
        const prof    = card.querySelector('.profesor-info strong')?.textContent.trim() || '—';
        const correo  = card.querySelector('.profesor-info small')?.textContent.trim() || '';
        const nota    = card.querySelector('.materia-nota')?.textContent.trim() || '—';
        const acts    = card.querySelector('.stat-item:nth-child(1) span')?.textContent.trim() || '—';
        const pend    = card.querySelector('.stat-item:nth-child(2) span')?.textContent.trim() || '—';
        const iconEl  = card.querySelector('.materia-icon');
        const iconBg  = iconEl?.style.background || '#4f46e5';
        const iconI   = iconEl?.querySelector('i')?.className || 'ri-book-line';
        const bc      = badgeClass(status);
        const slbl    = statusLabel(status);

        return `<tr class="tabla-row" data-status="${status}" data-url="${url}">
            <td>
                <div style="display:flex;align-items:center;gap:12px;">
                    <div style="width:38px;height:38px;border-radius:10px;background:${iconBg};display:grid;place-items:center;flex-shrink:0;">
                        <i class="${iconI}" style="color:#fff;font-size:18px;"></i>
                    </div>
                    <div>
                        <div class="tr-nombre" style="font-weight:600;color:#fff;">${nombre}</div>
                        <div style="font-size:12px;color:#8b91a3;">${desc}</div>
                    </div>
                </div>
            </td>
            <td>
                <div class="tr-prof" style="font-weight:500;color:#e2e8f0;">${prof}</div>
                <div style="font-size:12px;color:#8b91a3;">${correo}</div>
            </td>
            <td style="text-align:center;">
                <span class="tabla-nota-badge ${bc}">${nota}</span>
            </td>
            <td style="text-align:center;color:#cbd5e1;">${acts}</td>
            <td style="text-align:center;">
                <span style="color:${pend.startsWith('0') ? '#10b981' : '#f59e0b'};">${pend}</span>
            </td>
            <td style="text-align:center;">
                <span class="tabla-estado-badge ${bc}">${slbl}</span>
            </td>
            <td style="text-align:center;">
                <button class="btn-tabla-accion" onclick="window.location.href='${url}'" title="Ver actividades">
                    <i class="ri-eye-line"></i>
                </button>
            </td>
        </tr>`;
    }).join('');

    const wrap = document.createElement('div');
    wrap.id = 'tablaMaterias';
    wrap.className = 'datatable-card table-scroll-x';
    wrap.style.display = 'none';
    wrap.innerHTML = `
        <table class="table table-dark table-hover" style="margin:0;">
            <thead>
                <tr>
                    <th><i class="ri-book-2-line"></i> Materia</th>
                    <th><i class="ri-user-line"></i> Profesor</th>
                    <th style="text-align:center;"><i class="ri-award-line"></i> Nota</th>
                    <th style="text-align:center;"><i class="ri-file-list-line"></i> Actividades</th>
                    <th style="text-align:center;"><i class="ri-time-line"></i> Pendientes</th>
                    <th style="text-align:center;"><i class="ri-bar-chart-line"></i> Estado</th>
                    <th style="text-align:center;"><i class="ri-settings-line"></i> Acción</th>
                </tr>
            </thead>
            <tbody>${tbody}</tbody>
        </table>`;

    document.getElementById('materiasContainer').after(wrap);
    return wrap;
}

function initViewToggle() {
    const btns = document.querySelectorAll('.view-btn');
    btns.forEach(btn => {
        btn.addEventListener('click', () => {
            btns.forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            vistaActual = btn.dataset.view;

            const container = document.getElementById('materiasContainer');
            const tabla     = buildTable();

            if (vistaActual === 'list') {
                container.style.display = 'none';
                tabla.style.display     = '';
            } else {
                container.style.display = '';
                tabla.style.display     = 'none';
            }
            applyFilters();
        });
    });
}

// ── BÚSQUEDA ──────────────────────────────────────────────────────────────────

function initSearch() {
    const input = document.getElementById('searchInput');
    if (!input) return;
    input.addEventListener('input', e => {
        busquedaActiva = e.target.value.toLowerCase().trim();
        applyFilters();
    });
}

// ── SIDEBAR ───────────────────────────────────────────────────────────────────

function initSidebar() {
    const btn  = document.getElementById('toggleLeft');
    const side = document.getElementById('leftSidebar');
    const app  = document.getElementById('appGrid');
    if (!btn || !side || !app) return;

    btn.addEventListener('click', () => {
        side.classList.toggle('hidden');
        const hidden = side.classList.contains('hidden');
        app.classList.toggle('hide-left',  hidden);
        app.classList.toggle('hide-right', !hidden);
        app.classList.remove('hide-both');
    });
}
