// ===========================================
// TOGGLE SIDEBARS
// ===========================================
const leftSidebar = document.getElementById('leftSidebar');
const rightSidebar = document.getElementById('rightSidebar');
const appGrid = document.getElementById('appGrid');
const toggleLeft = document.getElementById('toggleLeft');
const toggleRight = document.getElementById('toggleRight');

// Estado inicial de los sidebars
let leftVisible = true;
let rightVisible = true;

function updateGridState() {
    appGrid.classList.remove('hide-left', 'hide-right', 'hide-both');

    if (!leftVisible && !rightVisible) {
        appGrid.classList.add('hide-both');
    } else if (!leftVisible) {
        appGrid.classList.add('hide-left');
    } else if (!rightVisible) {
        appGrid.classList.add('hide-right');
    }
}

function toggleLeftSidebar() {
    leftVisible = !leftVisible;
    leftSidebar.classList.toggle('hidden', !leftVisible);
    updateGridState();
}

function toggleRightSidebar() {
    rightVisible = !rightVisible;
    rightSidebar.classList.toggle('hidden', !rightVisible);
    updateGridState();
}

// Event listeners
toggleLeft?.addEventListener('click', toggleLeftSidebar);
toggleRight?.addEventListener('click', toggleRightSidebar);

// Aplicar estado inicial
updateGridState();

// ===========================================
// FILTER FUNCTIONALITY
// ===========================================
const filterButtons = document.querySelectorAll('.filter-btn');
const actividadCards = document.querySelectorAll('.actividad-card');

filterButtons.forEach(btn => {
    btn.addEventListener('click', function () {
        // Remove active from all buttons
        filterButtons.forEach(b => b.classList.remove('active'));

        // Add active to clicked button
        this.classList.add('active');

        const filter = this.getAttribute('data-filter');

        // Filter cards
        actividadCards.forEach(card => {
            if (filter === 'todas') {
                card.style.display = '';
                setTimeout(() => {
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                }, 10);
            } else {
                if (card.getAttribute('data-status') === filter) {
                    card.style.display = '';
                    setTimeout(() => {
                        card.style.opacity = '1';
                        card.style.transform = 'translateY(0)';
                    }, 10);
                } else {
                    card.style.opacity = '0';
                    card.style.transform = 'translateY(10px)';
                    setTimeout(() => {
                        card.style.display = 'none';
                    }, 300);
                }
            }
        });

        // Update counter after filtering
        updateVisibleCounter();
    });
});

// ===========================================
// SEARCH FUNCTIONALITY
// ===========================================
const searchInput = document.getElementById('searchInput');

searchInput?.addEventListener('input', function (e) {
    const searchTerm = e.target.value.toLowerCase().trim();

    actividadCards.forEach(card => {
        const title = card.querySelector('.actividad-title')?.textContent.toLowerCase() || '';
        const materia = card.querySelector('.actividad-materia')?.textContent.toLowerCase() || '';
        const description = card.querySelector('.actividad-description')?.textContent.toLowerCase() || '';

        const matches = title.includes(searchTerm) ||
            materia.includes(searchTerm) ||
            description.includes(searchTerm);

        if (matches || searchTerm === '') {
            card.style.display = '';
            setTimeout(() => {
                card.style.opacity = '1';
                card.style.transform = 'translateY(0)';
            }, 10);
        } else {
            card.style.opacity = '0';
            card.style.transform = 'translateY(10px)';
            setTimeout(() => {
                card.style.display = 'none';
            }, 300);
        }
    });

    // If search is active, show all filtered cards
    if (searchTerm !== '') {
        filterButtons.forEach(b => b.classList.remove('active'));
        filterButtons[0]?.classList.add('active'); // Activate "Todas"
    }

    // Update counter after search
    updateVisibleCounter();
});

// ===========================================
// SORT FUNCTIONALITY
// ===========================================
const sortSelect = document.getElementById('sortSelect');
const actividadesContainer = document.getElementById('actividadesContainer');

sortSelect?.addEventListener('change', function () {
    const criterio = this.value;
    const cardsArray = Array.from(actividadCards);

    cardsArray.sort((a, b) => {
        switch (criterio) {
            case 'fecha':
                const fechaA = new Date(a.dataset.fecha);
                const fechaB = new Date(b.dataset.fecha);
                return fechaA - fechaB;

            case 'materia':
                const materiaA = a.dataset.materia.toLowerCase();
                const materiaB = b.dataset.materia.toLowerCase();
                return materiaA.localeCompare(materiaB);

            case 'prioridad':
                const getPriority = (card) => {
                    const priorityEl = card.querySelector('.actividad-priority');
                    if (priorityEl.classList.contains('urgent')) return 1;
                    if (priorityEl.classList.contains('high')) return 2;
                    if (priorityEl.classList.contains('medium')) return 3;
                    if (priorityEl.classList.contains('low')) return 4;
                    return 5;
                };
                return getPriority(a) - getPriority(b);

            default:
                return 0;
        }
    });

    // Reordenar en el DOM
    cardsArray.forEach(card => {
        actividadesContainer.appendChild(card);
    });

    // Animación de reordenamiento
    actividadCards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        setTimeout(() => {
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, index * 50);
    });
});

// ===========================================
// ACTIVIDAD CARD ACTIONS
// ===========================================
document.querySelectorAll('.btn-actividad.primary').forEach(btn => {
    btn.addEventListener('click', function (e) {
        e.stopPropagation();
        const card = this.closest('.actividad-card');
        const actividad = card.querySelector('.actividad-title')?.textContent;
        const status = card.getAttribute('data-status');

        console.log('Acción principal:', actividad);

        if (status === 'completada') {
            alert(`Ver retroalimentación de: ${actividad}`);
        } else {
            alert(`Entregar/Continuar: ${actividad}`);
        }
    });
});

document.querySelectorAll('.btn-actividad.secondary').forEach(btn => {
    btn.addEventListener('click', function (e) {
        e.stopPropagation();
        const card = this.closest('.actividad-card');
        const actividad = card.querySelector('.actividad-title')?.textContent;

        console.log('Ver detalles de:', actividad);
        alert(`Ver detalles de: ${actividad}`);
    });
});

// ===========================================
// CALENDAR EVENTS (Right Sidebar)
// ===========================================
document.querySelectorAll('.calendar-event').forEach(event => {
    event.addEventListener('click', function () {
        const titulo = this.querySelector('strong')?.textContent;
        console.log('Ver evento:', titulo);
        alert(`Ver detalles de: ${titulo}`);
    });
});

// ===========================================
// REMINDER ITEMS
// ===========================================
document.querySelectorAll('.reminder-item').forEach(item => {
    item.addEventListener('click', function () {
        const titulo = this.querySelector('strong')?.textContent;
        console.log('Ver recordatorio:', titulo);
        alert(`Ver recordatorio: ${titulo}`);
    });
});

// ===========================================
// STATS UPDATE
// ===========================================
function updateStats() {
    const stats = {
        total: actividadCards.length,
        pendientes: document.querySelectorAll('.actividad-card[data-status="pendientes"]').length,
        completadas: document.querySelectorAll('.actividad-card[data-status="completadas"]').length,
        atrasadas: document.querySelectorAll('.actividad-card[data-status="atrasadas"]').length
    };

    const statElements = {
        total: document.querySelector('.stat-card:nth-child(1) h3'),
        pendientes: document.querySelector('.stat-card:nth-child(2) h3'),
        completadas: document.querySelector('.stat-card:nth-child(3) h3'),
        atrasadas: document.querySelector('.stat-card:nth-child(4) h3')
    };

    if (statElements.total) statElements.total.textContent = stats.total;
    if (statElements.pendientes) statElements.pendientes.textContent = stats.pendientes;
    if (statElements.completadas) statElements.completadas.textContent = stats.completadas;
    if (statElements.atrasadas) statElements.atrasadas.textContent = stats.atrasadas;

    return stats;
}

// ===========================================
// UPDATE VISIBLE COUNTER
// ===========================================
function updateVisibleCounter() {
    const visibleCards = Array.from(actividadCards).filter(card => {
        return card.style.display !== 'none' && card.style.opacity !== '0';
    });

    // Show empty message if no cards visible
    showEmptyMessage(visibleCards.length);

    return visibleCards.length;
}

// ===========================================
// EMPTY MESSAGE
// ===========================================
function showEmptyMessage(count) {
    const container = document.getElementById('actividadesContainer');
    let emptyMessage = document.getElementById('empty-message');

    if (count === 0) {
        if (!emptyMessage) {
            emptyMessage = document.createElement('div');
            emptyMessage.id = 'empty-message';
            emptyMessage.style.cssText = `
        text-align: center;
        padding: 60px 20px;
        color: #8b91a3;
        font-size: 16px;
      `;
            emptyMessage.innerHTML = `
        <i class="ri-inbox-line" style="font-size: 80px; color: #4f46e5; opacity: 0.3; display: block; margin-bottom: 16px;"></i>
        <p style="margin: 0; font-weight: 500; font-size: 18px;">No se encontraron actividades</p>
        <p style="margin: 8px 0 0 0; font-size: 14px;">Intenta con otro filtro o búsqueda</p>
      `;
            container.appendChild(emptyMessage);
        }
    } else {
        if (emptyMessage) {
            emptyMessage.remove();
        }
    }
}

// ===========================================
// SMOOTH TRANSITIONS ON LOAD
// ===========================================
document.addEventListener('DOMContentLoaded', function () {
    // Animate cards on load
    actividadCards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';

        setTimeout(() => {
            card.style.transition = 'all 0.3s ease';
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, index * 50);
    });

    // Animate stats cards
    const statCards = document.querySelectorAll('.stat-card');
    statCards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';

        setTimeout(() => {
            card.style.transition = 'all 0.3s ease';
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, index * 100);
    });

    // Update initial stats
    updateStats();
});

// ===========================================
// HANDLE CARD HOVER EFFECTS
// ===========================================
actividadCards.forEach(card => {
    card.addEventListener('mouseenter', function () {
        this.style.transition = 'all 0.3s cubic-bezier(0.4, 0, 0.2, 1)';
    });
});

// ===========================================
// UTILITIES
// ===========================================

// Function to get actividad data (for future use with API)
function getActividadData(actividadElement) {
    return {
        titulo: actividadElement.querySelector('.actividad-title')?.textContent,
        materia: actividadElement.querySelector('.actividad-materia')?.textContent,
        descripcion: actividadElement.querySelector('.actividad-description')?.textContent,
        status: actividadElement.getAttribute('data-status'),
        fecha: actividadElement.getAttribute('data-fecha'),
        materiaKey: actividadElement.getAttribute('data-materia')
    };
}

// Function to filter by multiple criteria
function filterActividades(criteria) {
    actividadCards.forEach(card => {
        let shouldShow = true;

        if (criteria.status && criteria.status !== 'todas') {
            shouldShow = card.getAttribute('data-status') === criteria.status;
        }

        if (criteria.search && shouldShow) {
            const title = card.querySelector('.actividad-title')?.textContent.toLowerCase() || '';
            const materia = card.querySelector('.actividad-materia')?.textContent.toLowerCase() || '';
            const description = card.querySelector('.actividad-description')?.textContent.toLowerCase() || '';
            shouldShow = title.includes(criteria.search.toLowerCase()) ||
                materia.includes(criteria.search.toLowerCase()) ||
                description.includes(criteria.search.toLowerCase());
        }

        if (criteria.materia && shouldShow) {
            shouldShow = card.getAttribute('data-materia') === criteria.materia;
        }

        if (shouldShow) {
            card.style.display = '';
            setTimeout(() => {
                card.style.opacity = '1';
                card.style.transform = 'translateY(0)';
            }, 10);
        } else {
            card.style.opacity = '0';
            card.style.transform = 'translateY(10px)';
            setTimeout(() => {
                card.style.display = 'none';
            }, 300);
        }
    });

    updateVisibleCounter();
}

// Export functions for external use
window.ActividadesModule = {
    getActividadData,
    filterActividades,
    updateStats,
    updateVisibleCounter
};

console.log('✅ Módulo de Actividades cargado correctamente');
console.log(`📊 Total actividades: ${actividadCards.length}`);