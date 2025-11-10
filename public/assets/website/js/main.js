// Funcionalidad del menú móvil
const mobileMenuToggle = document.querySelector('.mobile-menu-toggle');
const navMenu = document.querySelector('.nav-menu');
const navButtons = document.querySelector('.nav-buttons');

if (mobileMenuToggle) {
    mobileMenuToggle.addEventListener('click', () => {
        navMenu.classList.toggle('active');
        navButtons.classList.toggle('active');
        
        // Cambiar ícono
        const icon = mobileMenuToggle.querySelector('i');
        if (icon.classList.contains('fa-bars')) {
            icon.classList.remove('fa-bars');
            icon.classList.add('fa-times');
        } else {
            icon.classList.remove('fa-times');
            icon.classList.add('fa-bars');
        }
    });
}

// Animación al hacer scroll
const heroImage = document.querySelector('.hero-image');
const statsCard = document.querySelector('.stats-card');

window.addEventListener('scroll', () => {
    const scrolled = window.pageYOffset;
    
    if (heroImage) {
        heroImage.style.transform = `translateY(${scrolled * 0.3}px)`;
    }
    
    if (statsCard) {
        statsCard.style.transform = `translateY(${scrolled * 0.2}px)`;
    }
});

// Animación de entrada de la sección hero al recargar la página
window.addEventListener('load', () => {
    const heroLeft = document.querySelector('.hero-left');
    const heroRight = document.querySelector('.hero-right');
    
    if (heroLeft) {
        heroLeft.style.opacity = '0';
        heroLeft.style.transform = 'translateX(-50px)';
        
        setTimeout(() => {
            heroLeft.style.transition = 'all 0.8s ease';
            heroLeft.style.opacity = '1';
            heroLeft.style.transform = 'translateX(0)';
        }, 100);
    }
    
    if (heroRight) {
        heroRight.style.opacity = '0';
        heroRight.style.transform = 'translateX(50px)';
        
        setTimeout(() => {
            heroRight.style.transition = 'all 0.8s ease';
            heroRight.style.opacity = '1';
            heroRight.style.transform = 'translateX(0)';
        }, 300);
    }
});

// Contador animado para las estadísticas
function animateCounter(element, target, duration) {
    let start = 0;
    const increment = target / (duration / 16);
    
    const timer = setInterval(() => {
        start += increment;
        if (start >= target) {
            element.textContent = `+${target}`;
            clearInterval(timer);
        } else {
            element.textContent = `+${Math.floor(start)}`;
        }
    }, 16);
}

// Iniciar contador cuando el elemento sea visible
const observerOptions = {
    threshold: 0.5
};

const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            const statsNumber = entry.target.querySelector('.stats-number');
            if (statsNumber && !statsNumber.dataset.animated) {
                animateCounter(statsNumber, 1000, 2000);
                statsNumber.dataset.animated = 'true';
            }
        }
    });
}, observerOptions);

if (statsCard) {
    observer.observe(statsCard);
}

// Funcionalidad del slider de módulos
const modulosTrack = document.querySelector('.modulos-track');
const prevArrow = document.querySelector('.prev-arrow');
const nextArrow = document.querySelector('.next-arrow');
const moduloCards = document.querySelectorAll('.modulo-card');

if (modulosTrack && prevArrow && nextArrow) {
    let currentIndex = 0;
    let cardsPerView = 4;
    
    // Función para calcular cuántas cards mostrar según el ancho de pantalla
    function updateCardsPerView() {
        const width = window.innerWidth;
        if (width <= 768) {
            cardsPerView = 1;
        } else if (width <= 1024) {
            cardsPerView = 2;
        } else {
            cardsPerView = 4;
        }
        updateSlider();
    }
    
    // Función para actualizar la posición del slider
    function updateSlider() {
        const cardWidth = moduloCards[0].offsetWidth;
        const gap = 30;
        const totalWidth = cardWidth + gap;
        const maxIndex = Math.max(0, moduloCards.length - cardsPerView);
        
        // Limitar el índice
        currentIndex = Math.max(0, Math.min(currentIndex, maxIndex));
        
        // Aplicar transformación
        modulosTrack.style.transform = `translateX(-${currentIndex * totalWidth}px)`;
        
        // Actualizar estado de los botones
        prevArrow.disabled = currentIndex === 0;
        nextArrow.disabled = currentIndex >= maxIndex;
    }
    
    // Event listeners para las flechas
    prevArrow.addEventListener('click', () => {
        if (currentIndex > 0) {
            currentIndex--;
            updateSlider();
        }
    });
    
    nextArrow.addEventListener('click', () => {
        const maxIndex = Math.max(0, moduloCards.length - cardsPerView);
        if (currentIndex < maxIndex) {
            currentIndex++;
            updateSlider();
        }
    });
    
    // Actualizar al cambiar tamaño de ventana
    window.addEventListener('resize', updateCardsPerView);
    
    // Inicializar
    updateCardsPerView();
}

// Funcionalidad del carrusel de paquetes
const packagesTrack = document.querySelector('.packages-track');
const packageCards = document.querySelectorAll('.package-card');
const prevBtn = document.querySelector('.packages-carousel .prev-btn');
const nextBtn = document.querySelector('.packages-carousel .next-btn');
const indicators = document.querySelectorAll('.indicator');

if (packagesTrack && packageCards.length > 0) {
    let currentSlide = 0;
    const totalSlides = packageCards.length;

    // Función para actualizar el carrusel
    function updateCarousel() {
        const cardWidth = packageCards[0].offsetWidth;
        const gap = 30;
        const totalWidth = cardWidth + gap;
        
        packagesTrack.style.transform = `translateX(-${currentSlide * totalWidth}px)`;
        
        // Actualizar indicadores
        indicators.forEach((indicator, index) => {
            if (index === currentSlide) {
                indicator.classList.add('active');
            } else {
                indicator.classList.remove('active');
            }
        });
        
        // Actualizar estado de los botones
        if (prevBtn) prevBtn.disabled = currentSlide === 0;
        if (nextBtn) nextBtn.disabled = currentSlide === totalSlides - 1;
    }

    // Event listener para botón anterior
    if (prevBtn) {
        prevBtn.addEventListener('click', () => {
            if (currentSlide > 0) {
                currentSlide--;
                updateCarousel();
            }
        });
    }

    // Event listener para botón siguiente
    if (nextBtn) {
        nextBtn.addEventListener('click', () => {
            if (currentSlide < totalSlides - 1) {
                currentSlide++;
                updateCarousel();
            }
        });
    }

    // Event listeners para los indicadores
    indicators.forEach((indicator, index) => {
        indicator.addEventListener('click', () => {
            currentSlide = index;
            updateCarousel();
        });
    });

    // Actualizar al cambiar tamaño de ventana
    window.addEventListener('resize', updateCarousel);

    // Inicializar
    updateCarousel();

    // Auto-play (opcional)
    let autoPlayInterval = setInterval(() => {
        if (currentSlide < totalSlides - 1) {
            currentSlide++;
        } else {
            currentSlide = 0;
        }
        updateCarousel();
    }, 5000);

    // Pausar auto-play al interactuar
    packagesTrack.addEventListener('mouseenter', () => {
        clearInterval(autoPlayInterval);
    });

    packagesTrack.addEventListener('mouseleave', () => {
        autoPlayInterval = setInterval(() => {
            if (currentSlide < totalSlides - 1) {
                currentSlide++;
            } else {
                currentSlide = 0;
            }
            updateCarousel();
        }, 5000);
    });
}

// Newsletter form
const newsletterForm = document.querySelector('.newsletter-form');
if (newsletterForm) {
    newsletterForm.addEventListener('submit', (e) => {
        e.preventDefault();
        const emailInput = newsletterForm.querySelector('.newsletter-input');
        const email = emailInput.value;
        
        // Aquí podrías enviar el email a tu servidor
        console.log('Email suscrito:', email);
        
        // Mostrar mensaje de éxito
        alert('¡Gracias por suscribirte! Te mantendremos al día con las novedades de SIADEMY.');
        
        // Limpiar el input
        emailInput.value = '';
    });
}