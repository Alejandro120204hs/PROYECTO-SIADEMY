<!doctype html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SIADEMY • Calificaciones</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/remixicon@4.3.0/fonts/remixicon.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/dashboard/css/styles-calificaciones.css">
</head>

<body>
    <div class="app" id="appGrid">
        <!-- LEFT SIDEBAR -->
        <?php
        include_once __DIR__ . '/../../layouts/sidebar_estudiante.php'
        ?>

        <!-- MAIN CONTENT -->
        <main class="main">
            <div class="topbar">
                <div class="topbar-left">
                    <button class="toggle-btn" id="toggleLeft" title="Mostrar/Ocultar menú lateral">
                        <i class="ri-menu-2-line"></i>
                    </button>
                    <div class="title">Calificaciones</div>
                </div>

                <div class="search">
                    <i class="ri-search-2-line"></i>
                    <input type="text" id="searchInput" placeholder="Buscar materia o evaluación...">
                </div>

                <button class="toggle-btn" id="toggleRight" title="Mostrar/Ocultar panel derecho">
                    <i class="ri-layout-right-2-line"></i>
                </button>
            </div>

            <!-- ACCIONES -->
            <div class="actiones">
                <div class="actions-bar">
                    <button class="btn-progreso" id="progreso">
                        <i class="ri-line-chart-line"></i>
                        <span>Progreso</span>
                    </button>
                </div>

                <div class="actions-bar">
                    <button class="btn-download" id="downloadBoletin">
                        <i class="ri-survey-line"></i>
                        <span>Generar Boletín</span>
                    </button>
                </div>
            </div>

            <!-- STATS CARDS -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon blue">
                        <i class="ri-award-line"></i>
                    </div>
                    <div class="stat-content">
                        <h3>3.8</h3>
                        <p>Promedio General</p>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon green">
                        <i class="ri-book-open-line"></i>
                    </div>
                    <div class="stat-content">
                        <h3>6</h3>
                        <p>Materias Cursando</p>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon purple">
                        <i class="ri-file-list-3-line"></i>
                    </div>
                    <div class="stat-content">
                        <h3>24</h3>
                        <p>Evaluaciones</p>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon orange">
                        <i class="ri-time-line"></i>
                    </div>
                    <div class="stat-content">
                        <h3>5</h3>
                        <p>Pendientes</p>
                    </div>
                </div>
            </div>

            <!-- CALIFICACIONES GRID -->
            <div class="calificaciones-grid" id="calificacionesGrid">

                <!-- CARD: MATEMÁTICAS -->
                <div class="calificacion-card" data-materia-id="1">
                    <div class="card-header">
                        <div class="materia-info">
                            <div class="materia-icon" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);">
                                <i class="ri-calculator-line"></i>
                            </div>
                            <div class="materia-details">
                                <h3>Matemáticas</h3>
                                <p>Prof. Carlos Méndez</p>
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

                <!-- CARD: FÍSICA -->
                <div class="calificacion-card" data-materia-id="2">
                    <div class="card-header">
                        <div class="materia-info">
                            <div class="materia-icon" style="background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);">
                                <i class="ri-flask-line"></i>
                            </div>
                            <div class="materia-details">
                                <h3>Física</h3>
                                <p>Prof. Ana Rodríguez</p>
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

                <!-- CARD: QUÍMICA -->
                <div class="calificacion-card" data-materia-id="3">
                    <div class="card-header">
                        <div class="materia-info">
                            <div class="materia-icon" style="background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%);">
                                <i class="ri-test-tube-line"></i>
                            </div>
                            <div class="materia-details">
                                <h3>Química</h3>
                                <p>Prof. Luis Torres</p>
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

                <!-- CARD: INGLÉS -->
                <div class="calificacion-card" data-materia-id="4">
                    <div class="card-header">
                        <div class="materia-info">
                            <div class="materia-icon" style="background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);">
                                <i class="ri-english-input"></i>
                            </div>
                            <div class="materia-details">
                                <h3>Inglés</h3>
                                <p>Prof. Patricia Gómez</p>
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

                <!-- CARD: PROGRAMACIÓN -->
                <div class="calificacion-card" data-materia-id="5">
                    <div class="card-header">
                        <div class="materia-info">
                            <div class="materia-icon" style="background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);">
                                <i class="ri-code-s-slash-line"></i>
                            </div>
                            <div class="materia-details">
                                <h3>Programación</h3>
                                <p>Prof. Diego Álvarez</p>
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

                <!-- CARD: HISTORIA -->
                <div class="calificacion-card" data-materia-id="6">
                    <div class="card-header">
                        <div class="materia-info">
                            <div class="materia-icon" style="background: linear-gradient(135deg, #ec4899 0%, #db2777 100%);">
                                <i class="ri-book-open-line"></i>
                            </div>
                            <div class="materia-details">
                                <h3>Historia</h3>
                                <p>Prof. María Ramírez</p>
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

            </div>
        </main>

        <!-- RIGHT SIDEBAR -->
        <aside class="rightbar" id="rightSidebar">
            <div class="user">
                <button class="btn" title="Notificaciones"><i class="ri-notification-3-line"></i></button>
                <button class="btn" title="Configuración"><i class="ri-settings-3-line"></i></button>
                <div class="avatar" title="Diego A.">DA</div>
            </div>

            <div class="panel-title">Rendimiento Académico</div>
            <p class="muted">Análisis de tu desempeño</p>

            <div class="rendimiento-card">
                <div class="rendimiento-header">
                    <i class="ri-line-chart-line"></i>
                    <div>
                        <strong>Tendencia General</strong>
                        <small>Último periodo</small>
                    </div>
                </div>
                <div class="tendencia mejorando">
                    <i class="ri-arrow-up-line"></i>
                    <span>Mejorando +0.3</span>
                </div>
            </div>

            <div class="panel-title" style="margin-top:24px">Mejores Materias</div>
            <p class="muted">Tus fortalezas académicas</p>

            <div class="ranking-list">
                <div class="ranking-item">
                    <div class="ranking-position first">1</div>
                    <div class="ranking-info">
                        <strong>Inglés</strong>
                        <small>Prof. Patricia Gómez</small>
                    </div>
                    <div class="ranking-nota excelente">4.5</div>
                </div>

                <div class="ranking-item">
                    <div class="ranking-position second">2</div>
                    <div class="ranking-info">
                        <strong>Historia</strong>
                        <small>Prof. María Ramírez</small>
                    </div>
                    <div class="ranking-nota bueno">4.2</div>
                </div>

                <div class="ranking-item">
                    <div class="ranking-position third">3</div>
                    <div class="ranking-info">
                        <strong>Química</strong>
                        <small>Prof. Luis Torres</small>
                    </div>
                    <div class="ranking-nota medio">3.0</div>
                </div>
            </div>

            <div class="panel-title" style="margin-top:24px">Requieren Atención</div>
            <p class="muted">Materias para mejorar</p>

            <div class="atencion-list">
                <div class="atencion-item urgente">
                    <div class="atencion-icon">
                        <i class="ri-error-warning-line"></i>
                    </div>
                    <div class="atencion-info">
                        <strong>Física</strong>
                        <small>Nota: 2.5 - Crítico</small>
                    </div>
                </div>

                <div class="atencion-item alerta">
                    <div class="atencion-icon">
                        <i class="ri-alert-line"></i>
                    </div>
                    <div class="atencion-info">
                        <strong>Programación</strong>
                        <small>Nota: 2.7 - En riesgo</small>
                    </div>
                </div>

                <div class="atencion-item alerta">
                    <div class="atencion-icon">
                        <i class="ri-alert-line"></i>
                    </div>
                    <div class="atencion-info">
                        <strong>Matemáticas</strong>
                        <small>Nota: 2.8 - En riesgo</small>
                    </div>
                </div>
            </div>

            <div class="panel-title" style="margin-top:24px">Estadísticas</div>
            <p class="muted">Resumen del periodo</p>

            <div class="estadisticas-card">
                <div class="estadistica-item">
                    <span class="estadistica-label">Evaluaciones</span>
                    <span class="estadistica-value">24</span>
                </div>
                <div class="estadistica-item">
                    <span class="estadistica-label">Pendientes</span>
                    <span class="estadistica-value text-warning">5</span>
                </div>
                <div class="estadistica-item">
                    <span class="estadistica-label">Aprobadas</span>
                    <span class="estadistica-value text-success">18</span>
                </div>
                <div class="estadistica-item">
                    <span class="estadistica-label">Reprobadas</span>
                    <span class="estadistica-value text-danger">1</span>
                </div>
            </div>

            <button class="btn-primary">Ver Historial Completo</button>

            <div class="tips-card">
                <h4><i class="ri-lightbulb-line"></i> Consejo Académico</h4>
                <p>Enfócate en mejorar Física y Programación. Solicita asesorías con tus profesores para fortalecer estos temas antes del examen final.</p>
            </div>
        </aside>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?= BASE_URL ?>/public/assets/dashboard/js/estudiante/calificaciones.js"></script>
</body>

</html>