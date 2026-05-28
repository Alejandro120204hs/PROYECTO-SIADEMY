// ============================================================
//  CALIFICACIONES — Professional List View
// ============================================================

const materias      = window.calificacionesData?.materias || {};
let   periodoActivo = Number(window.calificacionesData?.periodoActual || 1);

// ── Helpers ───────────────────────────────────────────────────────────────────

function badgeClass(nota) {
    if (nota === null || nota === undefined) return 'pendiente';
    if (nota >= 4.5) return 'nb-excelente';
    if (nota >= 4.0) return 'nb-bueno';
    if (nota >= 3.0) return 'nb-regular';
    return 'nb-bajo';
}
function gradientFor(nota) {
    if (nota >= 4.5) return 'linear-gradient(135deg,#059669,#10b981)';
    if (nota >= 4.0) return 'linear-gradient(135deg,#1d4ed8,#3b82f6)';
    if (nota >= 3.0) return 'linear-gradient(135deg,#b45309,#f59e0b)';
    return 'linear-gradient(135deg,#b91c1c,#ef4444)';
}
function labelFor(nota) {
    if (nota >= 4.5) return 'Excelente';
    if (nota >= 4.0) return 'Bueno';
    if (nota >= 3.0) return 'Regular';
    return 'En riesgo';
}
function esc(s) {
    return String(s ?? '')
        .replace(/&/g,'&amp;').replace(/</g,'&lt;')
        .replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

// ── Render evaluaciones ───────────────────────────────────────────────────────

function renderEval(container, materiaId, numPeriodo) {
    const materia = materias[materiaId];
    if (!materia) return;

    const periodo = (materia.periodos || {})[numPeriodo] || { notaFinal: null, estado: null, evaluaciones: [] };
    const evals   = periodo.evaluaciones || [];
    let html = '';

    // Banner nota del período
    if (periodo.notaFinal !== null) {
        const nota = periodo.notaFinal;
        html += `
        <div class="eval-banner" style="background:${gradientFor(nota)}">
            <div class="eb-left">
                <span class="eb-periodo">Período ${numPeriodo}</span>
                <span class="eb-label">${labelFor(nota)}</span>
            </div>
            <span class="eb-nota">${nota.toFixed(1)}</span>
        </div>`;
    }

    if (evals.length === 0) {
        html += `<div class="eval-empty"><i class="ri-file-list-3-line"></i><p>Sin evaluaciones registradas en este período</p></div>`;
        container.innerHTML = html;
        return;
    }

    const rows = evals.map(ev => {
        const pend    = ev.nota === null || ev.nota === undefined;
        const nc      = pend ? 'pendiente' : badgeClass(ev.nota);
        const notaStr = pend ? 'Pendiente' : ev.nota.toFixed(1);
        const peso    = parseFloat(ev.ponderacion || 0);
        const pesoDisp = ev.peso || (peso > 0 ? peso.toFixed(0) + '%' : '—');
        const pesoPct  = Math.min(100, peso);

        return `<tr>
            <td>
                <span class="ev-nombre">${esc(ev.nombre)}</span>
                <span class="ev-fecha">${esc(ev.fecha || '—')}</span>
            </td>
            <td style="text-align:center">
                <div class="ev-peso-wrap">
                    <div class="ev-peso-bar"><div class="ev-peso-fill" style="width:${pesoPct}%"></div></div>
                    <span class="ev-peso-text">${esc(pesoDisp)}</span>
                </div>
            </td>
            <td style="text-align:center">
                <span class="ev-badge ${nc}">${notaStr}</span>
            </td>
        </tr>`;
    }).join('');

    html += `
    <table class="eval-table">
        <thead><tr><th>Evaluación</th><th>Peso</th><th>Nota</th></tr></thead>
        <tbody>${rows}</tbody>
    </table>`;

    container.innerHTML = html;
}

// ── Gestión de tabs ───────────────────────────────────────────────────────────

function activarTab(row, numPeriodo) {
    row.querySelectorAll('.dtab').forEach(t =>
        t.classList.toggle('active', parseInt(t.dataset.periodo) === numPeriodo)
    );
}

// ── Inicializar filas ─────────────────────────────────────────────────────────

function initRows() {
    document.querySelectorAll('.grade-row').forEach(row => {
        const materiaId = row.dataset.materiaId;
        const mainRow   = row.querySelector('.gr-main');
        const container = row.querySelector('.evaluaciones-container');

        // Click en la fila principal → expandir/colapsar
        mainRow.addEventListener('click', () => {
            const isOpen = row.classList.contains('open');

            // Cerrar todas
            document.querySelectorAll('.grade-row.open').forEach(r => r.classList.remove('open'));

            if (!isOpen) {
                row.classList.add('open');
                activarTab(row, periodoActivo);
                renderEval(container, materiaId, periodoActivo);
            }
        });

        // Click en tabs de período
        row.querySelectorAll('.dtab').forEach(tab => {
            tab.addEventListener('click', e => {
                e.stopPropagation();
                const p = parseInt(tab.dataset.periodo);
                activarTab(row, p);
                renderEval(container, materiaId, p);
            });
        });
    });
}

// ── Búsqueda ──────────────────────────────────────────────────────────────────

function initSearch() {
    const input = document.getElementById('searchInput');
    if (!input) return;
    input.addEventListener('input', e => {
        const q = e.target.value.toLowerCase().trim();
        document.querySelectorAll('.grade-row').forEach(row => {
            const m = materias[row.dataset.materiaId];
            const t = m ? (m.nombre + ' ' + m.profesor).toLowerCase() : '';
            row.style.display = !q || t.includes(q) ? '' : 'none';
        });
    });
}

// ── Sidebar toggle ────────────────────────────────────────────────────────────

function initSidebar() {
    const btn  = document.getElementById('toggleLeft');
    const side = document.getElementById('leftSidebar');
    const app  = document.getElementById('appGrid');
    if (!btn || !side || !app) return;
    let vis = localStorage.getItem('leftSidebarVisible') !== 'false';
    if (!vis) { side.classList.add('hidden'); app.classList.add('hide-left'); app.classList.remove('hide-right'); }
    btn.addEventListener('click', () => {
        vis = !vis;
        side.classList.toggle('hidden', !vis);
        app.classList.toggle('hide-left', !vis);
        app.classList.toggle('hide-right', vis);
        localStorage.setItem('leftSidebarVisible', vis);
    });
}

// ── Boot ──────────────────────────────────────────────────────────────────────

document.addEventListener('DOMContentLoaded', () => {
    initRows();
    initSearch();
    initSidebar();
});
