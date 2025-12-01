// ===========================================
// TOGGLE SIDEBARS CON LOCALSTORAGE
// ===========================================
document.addEventListener('DOMContentLoaded', function() {
    const leftSidebar = document.getElementById('leftSidebar');
    const rightSidebar = document.getElementById('rightSidebar');
    const appGrid = document.getElementById('appGrid');
    const toggleLeft = document.getElementById('toggleLeft');
    const toggleRight = document.getElementById('toggleRight');

    // Cargar estado desde localStorage
    let leftVisible = localStorage.getItem('leftSidebarVisible') !== 'false';
    let rightVisible = localStorage.getItem('rightSidebarVisible') !== 'false';

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
        localStorage.setItem('leftSidebarVisible', leftVisible);
        updateGridState();
    }

    function toggleRightSidebar() {
        rightVisible = !rightVisible;
        rightSidebar.classList.toggle('hidden', !rightVisible);
        localStorage.setItem('rightSidebarVisible', rightVisible);
        updateGridState();
    }

    // Event listeners
    if (toggleLeft) {
        toggleLeft.addEventListener('click', toggleLeftSidebar);
    }
    
    if (toggleRight) {
        toggleRight.addEventListener('click', toggleRightSidebar);
    }

    // Aplicar estado inicial
    if (!leftVisible && leftSidebar) leftSidebar.classList.add('hidden');
    if (!rightVisible && rightSidebar) rightSidebar.classList.add('hidden');
    updateGridState();

    // ===========================================
    // FILTROS DE CATEGORÍA
    // ===========================================
    const filterButtons = document.querySelectorAll('.filter-btn');
    const profesorCards = document.querySelectorAll('.profesor-card');

    filterButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            // Remover active de todos los botones
            filterButtons.forEach(b => b.classList.remove('active'));
            
            // Agregar active al botón clickeado
            this.classList.add('active');

            const filter = this.getAttribute('data-filter');

            // Filtrar las cards
            profesorCards.forEach(card => {
                if (filter === 'todos') {
                    card.style.display = 'block';
                    setTimeout(() => {
                        card.style.opacity = '1';
                        card.style.transform = 'scale(1)';
                    }, 10);
                } else {
                    if (card.getAttribute('data-categoria') === filter) {
                        card.style.display = 'block';
                        setTimeout(() => {
                            card.style.opacity = '1';
                            card.style.transform = 'scale(1)';
                        }, 10);
                    } else {
                        card.style.opacity = '0';
                        card.style.transform = 'scale(0.9)';
                        setTimeout(() => {
                            card.style.display = 'none';
                        }, 300);
                    }
                }
            });
        });
    });

    // ===========================================
    // BÚSQUEDA
    // ===========================================
    const searchInput = document.getElementById('searchInput');
    
    if (searchInput) {
        searchInput.addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase().trim();

            profesorCards.forEach(card => {
                // Buscar en nombre del profesor
                const nombreElement = card.querySelector('.profesor-info h3');
                const nombre = nombreElement ? nombreElement.textContent.toLowerCase() : '';

                // Buscar en materia
                const materiaElement = card.querySelector('.profesor-materia span');
                const materia = materiaElement ? materiaElement.textContent.toLowerCase() : '';

                // Buscar en título
                const tituloElement = card.querySelector('.profesor-titulo');
                const titulo = tituloElement ? tituloElement.textContent.toLowerCase() : '';

                const matches = nombre.includes(searchTerm) || 
                               materia.includes(searchTerm) || 
                               titulo.includes(searchTerm);

                if (matches || searchTerm === '') {
                    card.style.display = 'block';
                    setTimeout(() => {
                        card.style.opacity = '1';
                        card.style.transform = 'scale(1)';
                    }, 10);
                } else {
                    card.style.opacity = '0';
                    card.style.transform = 'scale(0.9)';
                    setTimeout(() => {
                        card.style.display = 'none';
                    }, 300);
                }
            });

            // Si hay búsqueda activa, activar filtro "Todos"
            if (searchTerm !== '') {
                filterButtons.forEach(b => b.classList.remove('active'));
                filterButtons[0]?.classList.add('active');
            }
        });
    }

    // ===========================================
    // ACCIONES DE BOTONES
    // ===========================================
    
    // Enviar Mensaje
    document.querySelectorAll('.btn-profesor.primary').forEach(btn => {
        btn.addEventListener('click', function() {
            const card = this.closest('.profesor-card');
            const nombre = card.querySelector('.profesor-info h3').textContent;
            
            console.log('Enviar mensaje a:', nombre);
            alert(`Abriendo chat con ${nombre}...`);
            // Aquí iría la lógica para abrir el sistema de mensajería
            // window.location.href = `mensajes.html?profesor=${encodeURIComponent(nombre)}`;
        });
    });

    // Agendar Cita
    document.querySelectorAll('.btn-profesor.secondary').forEach(btn => {
        btn.addEventListener('click', function() {
            const card = this.closest('.profesor-card');
            const nombre = card.querySelector('.profesor-info h3').textContent;
            const materia = card.querySelector('.profesor-materia span').textContent;
            
            console.log('Agendar cita con:', nombre, '-', materia);
            alert(`Agendando cita con ${nombre} - ${materia}...`);
            // Aquí iría la lógica para abrir el formulario de agendamiento
            // window.location.href = `agendar-cita.html?profesor=${encodeURIComponent(nombre)}`;
        });
    });

    // Email links
    document.querySelectorAll('.contact-item').forEach(item => {
        const email = item.querySelector('span')?.textContent;
        if (email && email.includes('@')) {
            item.style.cursor = 'pointer';
            item.addEventListener('click', function() {
                window.location.href = `mailto:${email}`;
            });
        }
    });

    // ===========================================
    // ANIMACIONES AL CARGAR
    // ===========================================
    profesorCards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        
        setTimeout(() => {
            card.style.transition = 'all 0.3s ease';
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, index * 50);
    });

    console.log('✅ Módulo de Profesores cargado correctamente');
});