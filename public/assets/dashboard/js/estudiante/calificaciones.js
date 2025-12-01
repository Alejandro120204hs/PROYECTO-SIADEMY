// =============================================
// DATOS DE LAS MATERIAS Y EVALUACIONES
// =============================================

const materias = {
    1: { // Matemáticas
        nombre: 'Matemáticas',
        profesor: 'Prof. Carlos Méndez',
        periodos: {
            1: {
                notaFinal: 3.2,
                estado: 'riesgo',
                evaluaciones: [
                    { nombre: 'Parcial 1', fecha: '15 Ago 2024', nota: 3.0, peso: '30%' },
                    { nombre: 'Taller', fecha: '22 Ago 2024', nota: 3.5, peso: '20%' },
                    { nombre: 'Quiz', fecha: '29 Ago 2024', nota: 3.2, peso: '15%' }
                ]
            },
            2: {
                notaFinal: 2.8,
                estado: 'riesgo',
                evaluaciones: [
                    { nombre: 'Parcial 1 - Álgebra', fecha: '15 Oct 2024', nota: 3.0, peso: '30%' },
                    { nombre: 'Taller Ecuaciones', fecha: '22 Oct 2024', nota: 2.5, peso: '20%' },
                    { nombre: 'Quiz Geometría', fecha: '29 Oct 2024', nota: 2.8, peso: '15%' },
                    { nombre: 'Participación en Clase', fecha: 'Continuo', nota: 3.5, peso: '10%' },
                    { nombre: 'Examen Final', fecha: 'Pendiente', nota: null, peso: '25%' }
                ]
            },
            3: { notaFinal: null, estado: null, evaluaciones: [] },
            4: { notaFinal: null, estado: null, evaluaciones: [] }
        }
    },
    2: { // Física
        nombre: 'Física',
        profesor: 'Prof. Ana Rodríguez',
        periodos: {
            1: {
                notaFinal: 3.0,
                estado: 'riesgo',
                evaluaciones: [
                    { nombre: 'Parcial 1', fecha: '10 Ago 2024', nota: 3.2, peso: '35%' },
                    { nombre: 'Laboratorio', fecha: '18 Ago 2024', nota: 2.8, peso: '20%' }
                ]
            },
            2: {
                notaFinal: 2.5,
                estado: 'critico',
                evaluaciones: [
                    { nombre: 'Parcial 1 - Mecánica', fecha: '10 Oct 2024', nota: 2.3, peso: '35%' },
                    { nombre: 'Laboratorio 1', fecha: '18 Oct 2024', nota: 2.8, peso: '20%' },
                    { nombre: 'Quiz Leyes de Newton', fecha: '25 Oct 2024', nota: 2.2, peso: '15%' },
                    { nombre: 'Proyecto Final', fecha: 'Pendiente', nota: null, peso: '30%' }
                ]
            },
            3: { notaFinal: null, estado: null, evaluaciones: [] },
            4: { notaFinal: null, estado: null, evaluaciones: [] }
        }
    },
    3: { // Química
        nombre: 'Química',
        profesor: 'Prof. Luis Torres',
        periodos: {
            1: {
                notaFinal: 3.5,
                estado: 'medio',
                evaluaciones: [
                    { nombre: 'Parcial', fecha: '12 Ago 2024', nota: 3.8, peso: '30%' },
                    { nombre: 'Laboratorio', fecha: '20 Ago 2024', nota: 3.2, peso: '25%' }
                ]
            },
            2: {
                notaFinal: 3.0,
                estado: 'riesgo',
                evaluaciones: [
                    { nombre: 'Parcial Orgánica', fecha: '12 Oct 2024', nota: 3.2, peso: '30%' },
                    { nombre: 'Laboratorio Reacciones', fecha: '20 Oct 2024', nota: 3.0, peso: '25%' },
                    { nombre: 'Taller Compuestos', fecha: '28 Oct 2024', nota: 2.8, peso: '20%' },
                    { nombre: 'Examen Final', fecha: 'Pendiente', nota: null, peso: '25%' }
                ]
            },
            3: { notaFinal: null, estado: null, evaluaciones: [] },
            4: { notaFinal: null, estado: null, evaluaciones: [] }
        }
    },
    4: { // Inglés
        nombre: 'Inglés',
        profesor: 'Prof. Patricia Gómez',
        periodos: {
            1: {
                notaFinal: 4.3,
                estado: 'bueno',
                evaluaciones: [
                    { nombre: 'Speaking Test', fecha: '08 Ago 2024', nota: 4.5, peso: '25%' },
                    { nombre: 'Writing Essay', fecha: '16 Ago 2024', nota: 4.2, peso: '25%' }
                ]
            },
            2: {
                notaFinal: 4.5,
                estado: 'excelente',
                evaluaciones: [
                    { nombre: 'Speaking Test', fecha: '08 Oct 2024', nota: 4.8, peso: '25%' },
                    { nombre: 'Writing Essay', fecha: '16 Oct 2024', nota: 4.5, peso: '25%' },
                    { nombre: 'Grammar Quiz', fecha: '23 Oct 2024', nota: 4.2, peso: '20%' },
                    { nombre: 'Reading Comprehension', fecha: '30 Oct 2024', nota: 4.6, peso: '20%' },
                    { nombre: 'Oral Presentation', fecha: 'Pendiente', nota: null, peso: '10%' }
                ]
            },
            3: { notaFinal: null, estado: null, evaluaciones: [] },
            4: { notaFinal: null, estado: null, evaluaciones: [] }
        }
    },
    5: { // Programación
        nombre: 'Programación',
        profesor: 'Prof. Diego Álvarez',
        periodos: {
            1: {
                notaFinal: 3.2,
                estado: 'riesgo',
                evaluaciones: [
                    { nombre: 'Taller POO', fecha: '14 Ago 2024', nota: 3.0, peso: '20%' },
                    { nombre: 'Proyecto', fecha: '21 Ago 2024', nota: 3.5, peso: '30%' }
                ]
            },
            2: {
                notaFinal: 2.7,
                estado: 'riesgo',
                evaluaciones: [
                    { nombre: 'Taller POO', fecha: '14 Oct 2024', nota: 2.5, peso: '20%' },
                    { nombre: 'Proyecto MVC', fecha: '21 Oct 2024', nota: 2.8, peso: '30%' },
                    { nombre: 'Quiz Java Avanzado', fecha: '27 Oct 2024', nota: 2.9, peso: '15%' },
                    { nombre: 'Proyecto Final', fecha: 'Pendiente', nota: null, peso: '35%' }
                ]
            },
            3: { notaFinal: null, estado: null, evaluaciones: [] },
            4: { notaFinal: null, estado: null, evaluaciones: [] }
        }
    },
    6: { // Historia
        nombre: 'Historia',
        profesor: 'Prof. María Ramírez',
        periodos: {
            1: {
                notaFinal: 4.0,
                estado: 'bueno',
                evaluaciones: [
                    { nombre: 'Ensayo', fecha: '09 Ago 2024', nota: 4.2, peso: '30%' },
                    { nombre: 'Exposición', fecha: '19 Ago 2024', nota: 3.8, peso: '25%' }
                ]
            },
            2: {
                notaFinal: 4.2,
                estado: 'bueno',
                evaluaciones: [
                    { nombre: 'Ensayo Independencia', fecha: '09 Oct 2024', nota: 4.5, peso: '30%' },
                    { nombre: 'Exposición Oral', fecha: '19 Oct 2024', nota: 4.0, peso: '25%' },
                    { nombre: 'Participación', fecha: 'Continuo', nota: 4.3, peso: '15%' },
                    { nombre: 'Examen Final', fecha: 'Pendiente', nota: null, peso: '30%' }
                ]
            },
            3: { notaFinal: null, estado: null, evaluaciones: [] },
            4: { notaFinal: null, estado: null, evaluaciones: [] }
        }
    }
};

const currentPeriod = 2; // Periodo actual

// =============================================
// FUNCIONES AUXILIARES
// =============================================

/**
 * Obtiene la clase CSS según la nota
 * @param {number|null} nota - La nota a evaluar
 * @returns {string} - Clase CSS correspondiente
 */
function getNotaClass(nota) {
    if (nota === null) return '';
    if (nota >= 4.0) return 'alto';
    if (nota >= 3.0) return 'medio';
    return 'bajo';
}

/**
 * Muestra las evaluaciones de un periodo específico
 * @param {HTMLElement} card - La card de la materia
 * @param {string} materiaId - ID de la materia
 * @param {number} numeroPeriodo - Número del periodo (1-4)
 */
function showEvaluaciones(card, materiaId, numeroPeriodo) {
    const evaluacionesSection = card.querySelector('.evaluaciones-section');
    const materia = materias[materiaId];
    const periodo = materia.periodos[numeroPeriodo];

    // Si no hay evaluaciones
    if (periodo.evaluaciones.length === 0) {
        evaluacionesSection.innerHTML = `
            <div style="text-align: center; padding: 20px; color: #8b91a3;">
                <i class="ri-information-line" style="font-size: 32px; margin-bottom: 8px;"></i>
                <p>No hay evaluaciones registradas para este periodo</p>
            </div>
        `;
    } else {
        // Generar HTML de nota final si existe
        let notaFinalHTML = '';
        if (periodo.notaFinal !== null) {
            notaFinalHTML = `
                <div class="nota-final-display ${periodo.estado}">
                    <span class="nota-final-label">Nota Final del Periodo</span>
                    <span class="nota-final-value">${periodo.notaFinal.toFixed(1)}</span>
                </div>
            `;
        }

        // Generar HTML de evaluaciones
        const evaluacionesHTML = periodo.evaluaciones.map(evaluacion => {
            const isPendiente = evaluacion.nota === null;
            const notaClass = isPendiente ? '' : getNotaClass(evaluacion.nota);

            return `
                <div class="evaluacion-item ${isPendiente ? 'pendiente' : ''}">
                    <div class="evaluacion-info">
                        <span class="evaluacion-nombre">${evaluacion.nombre}</span>
                        <span class="evaluacion-fecha">${evaluacion.fecha}</span>
                    </div>
                    <div class="evaluacion-nota ${notaClass}">
                        ${isPendiente ? '-' : evaluacion.nota.toFixed(1)}
                    </div>
                    <div class="evaluacion-peso">${evaluacion.peso}</div>
                </div>
            `;
        }).join('');

        // Insertar contenido completo
        evaluacionesSection.innerHTML = `
            ${notaFinalHTML}
            <div class="evaluaciones-list">
                ${evaluacionesHTML}
            </div>
        `;
    }

    // Mostrar la sección con animación
    evaluacionesSection.classList.add('show');
}

// =============================================
// INICIALIZACIÓN DE EVENTOS
// =============================================

/**
 * Inicializa los eventos de todas las cards de materias
 */
function initializeCards() {
    const cards = document.querySelectorAll('.calificacion-card');

    cards.forEach(card => {
        const materiaId = card.dataset.materiaId;
        const header = card.querySelector('.card-header');
        const periodoButtons = card.querySelectorAll('.periodo-btn');

        // Evento: Click en el header para expandir/colapsar
        header.addEventListener('click', () => {
            const isActive = header.classList.contains('active');

            // Cerrar todas las cards primero
            document.querySelectorAll('.card-header').forEach(h => {
                h.classList.remove('active');
            });
            document.querySelectorAll('.periodos-section').forEach(p => {
                p.classList.remove('show');
            });
            document.querySelectorAll('.evaluaciones-section').forEach(e => {
                e.classList.remove('show');
            });

            // Si no estaba activa, abrirla
            if (!isActive) {
                header.classList.add('active');
                card.querySelector('.periodos-section').classList.add('show');

                // Mostrar el periodo actual por defecto
                showEvaluaciones(card, materiaId, currentPeriod);
            }
        });

        // Evento: Click en botones de periodo
        periodoButtons.forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.stopPropagation(); // Evitar que se propague al header
                const numeroPeriodo = parseInt(btn.dataset.periodo);

                // Remover estado activo de otros botones (excepto current)
                periodoButtons.forEach(b => {
                    if (!b.classList.contains('current')) {
                        b.classList.remove('active');
                    }
                });

                // Activar este botón si no es el current
                if (!btn.classList.contains('current')) {
                    btn.classList.add('active');
                }

                // Mostrar evaluaciones del periodo seleccionado
                showEvaluaciones(card, materiaId, numeroPeriodo);
            });
        });
    });
}

// =============================================
// FUNCIONALIDAD DE BÚSQUEDA
// =============================================

/**
 * Filtra las cards según el término de búsqueda
 */
function initializeSearch() {
    const searchInput = document.getElementById('searchInput');

    searchInput.addEventListener('input', (e) => {
        const searchTerm = e.target.value.toLowerCase().trim();
        const cards = document.querySelectorAll('.calificacion-card');

        cards.forEach(card => {
            const materiaId = card.dataset.materiaId;
            const materia = materias[materiaId];

            // Buscar en nombre y profesor
            const materiaText = `${materia.nombre} ${materia.profesor}`.toLowerCase();

            // Buscar en nombres de evaluaciones
            const evaluacionesText = Object.values(materia.periodos)
                .flatMap(p => p.evaluaciones.map(e => e.nombre))
                .join(' ')
                .toLowerCase();

            // Mostrar u ocultar según coincidencia
            const matches = materiaText.includes(searchTerm) ||
                evaluacionesText.includes(searchTerm);

            card.style.display = matches ? 'block' : 'none';
        });
    });
}

// =============================================
// TOGGLE DE SIDEBARS
// =============================================

/**
 * Inicializa los botones de toggle para los sidebars
 */
function initializeSidebarToggles() {
    const toggleLeft = document.getElementById('toggleLeft');
    const toggleRight = document.getElementById('toggleRight');
    const leftSidebar = document.getElementById('leftSidebar');
    const rightSidebar = document.getElementById('rightSidebar');
    const app = document.getElementById('appGrid');

    // Toggle del sidebar izquierdo
    toggleLeft.addEventListener('click', () => {
        leftSidebar.classList.toggle('hidden');

        if (leftSidebar.classList.contains('hidden')) {
            if (rightSidebar.classList.contains('hidden')) {
                app.classList.add('hide-both');
                app.classList.remove('hide-left', 'hide-right');
            } else {
                app.classList.add('hide-left');
                app.classList.remove('hide-right', 'hide-both');
            }
        } else {
            if (rightSidebar.classList.contains('hidden')) {
                app.classList.add('hide-right');
                app.classList.remove('hide-left', 'hide-both');
            } else {
                app.classList.remove('hide-left', 'hide-right', 'hide-both');
            }
        }
    });

    // Toggle del sidebar derecho
    toggleRight.addEventListener('click', () => {
        rightSidebar.classList.toggle('hidden');

        if (rightSidebar.classList.contains('hidden')) {
            if (leftSidebar.classList.contains('hidden')) {
                app.classList.add('hide-both');
                app.classList.remove('hide-left', 'hide-right');
            } else {
                app.classList.add('hide-right');
                app.classList.remove('hide-left', 'hide-both');
            }
        } else {
            if (leftSidebar.classList.contains('hidden')) {
                app.classList.add('hide-left');
                app.classList.remove('hide-right', 'hide-both');
            } else {
                app.classList.remove('hide-left', 'hide-right', 'hide-both');
            }
        }
    });
}

// =============================================
// INICIALIZACIÓN AL CARGAR LA PÁGINA
// =============================================

document.addEventListener('DOMContentLoaded', () => {
    console.log('🎓 Inicializando sistema de calificaciones...');

    // Inicializar todas las funcionalidades
    initializeCards();
    initializeSearch();
    initializeSidebarToggles();

    console.log('Sistema de calificaciones listo');
});