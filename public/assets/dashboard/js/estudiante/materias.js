// ============================================
// PARTE 1: GENERAR HTML DE LAS CARDS
// ============================================

function generarHTMLMaterias() {
    const materiasHTML = [
        {
            id: 1,
            nombre: 'Matemáticas',
            profesor: 'Prof. Carlos Méndez',
            icon: 'ri-calculator-line',
            color: 'linear-gradient(135deg, #f59e0b 0%, #d97706 100%)'
        },
        {
            id: 2,
            nombre: 'Física',
            profesor: 'Prof. Ana Rodríguez',
            icon: 'ri-flask-line',
            color: 'linear-gradient(135deg, #ef4444 0%, #dc2626 100%)'
        },
        {
            id: 3,
            nombre: 'Química',
            profesor: 'Prof. Luis Torres',
            icon: 'ri-test-tube-line',
            color: 'linear-gradient(135deg, #06b6d4 0%, #0891b2 100%)'
        },
        {
            id: 4,
            nombre: 'Inglés',
            profesor: 'Prof. Patricia Gómez',
            icon: 'ri-english-input',
            color: 'linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%)'
        },
        {
            id: 5,
            nombre: 'Programación',
            profesor: 'Prof. Diego Álvarez',
            icon: 'ri-code-s-slash-line',
            color: 'linear-gradient(135deg, #6366f1 0%, #4f46e5 100%)'
        },
        {
            id: 6,
            nombre: 'Historia',
            profesor: 'Prof. María Ramírez',
            icon: 'ri-book-open-line',
            color: 'linear-gradient(135deg, #ec4899 0%, #db2777 100%)'
        }
    ];

    const grid = document.getElementById('calificacionesGrid');
    // grid.innerHTML = ''; // Limpiar el grid

    materiasHTML.forEach(materia => {
        const cardHTML = `
            <div class="calificacion-card" data-materia-id="${materia.id}" data-materia-nombre="${materia.nombre}" data-profesor="${materia.profesor}">
                <div class="card-header">
                    <div class="materia-info">
                        <div class="materia-icon" style="background: ${materia.color};">
                            <i class="${materia.icon}"></i>
                        </div>
                        <div class="materia-details">
                            <h3>${materia.nombre}</h3>
                            <p>${materia.profesor}</p>
                        </div>
                    </div>
                    <div class="expand-icon">
                        <i class="ri-arrow-down-s-line"></i>
                    </div>
                </div>
                <div class="periodos-section">
                    <div class="periodo-buttons">
                        <button class="periodo-btn" data-periodo="1">
                            <i class="ri-calendar-line"></i>
                            <span>Periodo 1</span>
                        </button>
                        <button class="periodo-btn current" data-periodo="2">
                            <i class="ri-calendar-line"></i>
                            <span>Periodo 2</span>
                        </button>
                        <button class="periodo-btn" data-periodo="3">
                            <i class="ri-calendar-line"></i>
                            <span>Periodo 3</span>
                        </button>
                        <button class="periodo-btn" data-periodo="4">
                            <i class="ri-calendar-line"></i>
                            <span>Periodo 4</span>
                        </button>
                    </div>
                </div>
                <div class="evaluaciones-section"></div>
            </div>
        `;
        // grid.innerHTML += cardHTML;
    });
}

// ============================================
// PARTE 2: DATOS DE EVALUACIONES
// ============================================

const materias = {
    1: { // Matemáticas
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

const currentPeriod = 2;

// ============================================
// PARTE 3: FUNCIONES DE LÓGICA
// ============================================

// Función para obtener la clase CSS según la nota
function getNotaClass(nota) {
    if (nota === null) return '';
    if (nota >= 4.0) return 'alto';
    if (nota >= 3.0) return 'medio';
    return 'bajo';
}

// Función para mostrar las evaluaciones de un periodo
function showEvaluaciones(card, materiaId, numeroPeriodo) {
    const evaluacionesSection = card.querySelector('.evaluaciones-section');
    const periodo = materias[materiaId].periodos[numeroPeriodo];

    if (periodo.evaluaciones.length === 0) {
        evaluacionesSection.innerHTML = `
            <div style="text-align: center; padding: 20px; color: #8b91a3;">
                <i class="ri-information-line" style="font-size: 32px; margin-bottom: 8px;"></i>
                <p>No hay evaluaciones registradas para este periodo</p>
            </div>
        `;
    } else {
        let notaFinalHTML = '';
        if (periodo.notaFinal !== null) {
            notaFinalHTML = `
                <div class="nota-final-display ${periodo.estado}">
                    <span class="nota-final-label">Nota Final del Periodo</span>
                    <span class="nota-final-value">${periodo.notaFinal.toFixed(1)}</span>
                </div>
            `;
        }

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

        evaluacionesSection.innerHTML = `
            ${notaFinalHTML}
            <div class="evaluaciones-list">
                ${evaluacionesHTML}
            </div>
        `;
    }

    evaluacionesSection.classList.add('show');
}

// Inicializar eventos de las cards
function initializeCards() {
    const cards = document.querySelectorAll('.calificacion-card');

    cards.forEach(card => {
        const materiaId = card.dataset.materiaId;
        const header = card.querySelector('.card-header');
        const periodoButtons = card.querySelectorAll('.periodo-btn');

        // Click en el header para expandir/colapsar
        header.addEventListener('click', () => {
            const isActive = header.classList.contains('active');

            // Cerrar todas las cards
            document.querySelectorAll('.card-header').forEach(h => {
                h.classList.remove('active');
            });
            document.querySelectorAll('.periodos-section').forEach(p => {
                p.classList.remove('show');
            });
            document.querySelectorAll('.evaluaciones-section').forEach(e => {
                e.classList.remove('show');
            });

            // Abrir esta card si no estaba activa
            if (!isActive) {
                header.classList.add('active');
                card.querySelector('.periodos-section').classList.add('show');

                // Mostrar el periodo actual por defecto
                showEvaluaciones(card, materiaId, currentPeriod);
            }
        });

        // Click en los botones de periodo
        periodoButtons.forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.stopPropagation();
                const numeroPeriodo = parseInt(btn.dataset.periodo);

                // Remover active de otros botones del mismo grupo
                periodoButtons.forEach(b => {
                    if (!b.classList.contains('current')) {
                        b.classList.remove('active');
                    }
                });

                // Activar este botón
                if (!btn.classList.contains('current')) {
                    btn.classList.add('active');
                }

                // Mostrar evaluaciones
                showEvaluaciones(card, materiaId, numeroPeriodo);
            });
        });
    });
}

// ============================================
// PARTE 4: BÚSQUEDA
// ============================================

document.getElementById('searchInput').addEventListener('input', (e) => {
    const searchTerm = e.target.value.toLowerCase();
    const cards = document.querySelectorAll('.calificacion-card');

    cards.forEach(card => {
        const materiaId = card.dataset.materiaId;
        const materiaNombre = card.dataset.materiaNombre.toLowerCase();
        const profesor = card.dataset.profesor.toLowerCase();

        const evaluacionesText = Object.values(materias[materiaId].periodos)
            .flatMap(p => p.evaluaciones.map(e => e.nombre))
            .join(' ')
            .toLowerCase();

        const matches = materiaNombre.includes(searchTerm) || 
                       profesor.includes(searchTerm) || 
                       evaluacionesText.includes(searchTerm);

        card.style.display = matches ? 'block' : 'none';
    });
});

// ============================================
// PARTE 5: TOGGLE DE SIDEBARS
// ============================================

document.getElementById('toggleLeft').addEventListener('click', () => {
    const sidebar = document.getElementById('leftSidebar');
    const app = document.getElementById('appGrid');

    sidebar.classList.toggle('hidden');

    if (sidebar.classList.contains('hidden')) {
        if (document.getElementById('rightSidebar').classList.contains('hidden')) {
            app.classList.add('hide-both');
            app.classList.remove('hide-left', 'hide-right');
        } else {
            app.classList.add('hide-left');
            app.classList.remove('hide-right', 'hide-both');
        }
    } else {
        if (document.getElementById('rightSidebar').classList.contains('hidden')) {
            app.classList.add('hide-right');
            app.classList.remove('hide-left', 'hide-both');
        } else {
            app.classList.remove('hide-left', 'hide-right', 'hide-both');
        }
    }
});

document.getElementById('toggleRight').addEventListener('click', () => {
    const rightbar = document.getElementById('rightSidebar');
    const app = document.getElementById('appGrid');

    rightbar.classList.toggle('hidden');

    if (rightbar.classList.contains('hidden')) {
        if (document.getElementById('leftSidebar').classList.contains('hidden')) {
            app.classList.add('hide-both');
            app.classList.remove('hide-left', 'hide-right');
        } else {
            app.classList.add('hide-right');
            app.classList.remove('hide-left', 'hide-both');
        }
    } else {
        if (document.getElementById('leftSidebar').classList.contains('hidden')) {
            app.classList.add('hide-left');
            app.classList.remove('hide-right', 'hide-both');
        } else {
            app.classList.remove('hide-left', 'hide-right', 'hide-both');
        }
    }
});

// ============================================
// PARTE 6: INICIALIZACIÓN
// ============================================

document.addEventListener('DOMContentLoaded', () => {
    generarHTMLMaterias(); // Genera las cards en el HTML
    initializeCards();     // Inicializa los eventos
});