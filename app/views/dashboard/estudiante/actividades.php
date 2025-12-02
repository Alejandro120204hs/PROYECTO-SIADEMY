<!doctype html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SIADEMY • Actividades</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/remixicon@4.3.0/fonts/remixicon.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/dashboard/css/styles-actividades.css">
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
                    <div class="title">Mis Actividades</div>
                </div>

                <div class="search">
                    <i class="ri-search-2-line"></i>
                    <input type="text" id="searchInput" placeholder="Buscar actividades...">
                </div>

                <button class="toggle-btn" id="toggleRight" title="Mostrar/Ocultar panel derecho">
                    <i class="ri-layout-right-2-line"></i>
                </button>
            </div>

            <!-- STATS CARDS -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon blue">
                        <i class="ri-file-list-3-line"></i>
                    </div>
                    <div class="stat-content">
                        <h3>15</h3>
                        <p>Total Actividades</p>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon orange">
                        <i class="ri-time-line"></i>
                    </div>
                    <div class="stat-content">
                        <h3>3</h3>
                        <p>Pendientes</p>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon green">
                        <i class="ri-checkbox-circle-line"></i>
                    </div>
                    <div class="stat-content">
                        <h3>10</h3>
                        <p>Completadas</p>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon red">
                        <i class="ri-error-warning-line"></i>
                    </div>
                    <div class="stat-content">
                        <h3>2</h3>
                        <p>Atrasadas</p>
                    </div>
                </div>
            </div>

            <!-- FILTERS -->
            <div class="filter-section">
                <div class="filter-group">
                    <button class="filter-btn active" data-filter="todas">
                        <i class="ri-apps-line"></i> Todas
                    </button>
                    <button class="filter-btn" data-filter="pendientes">
                        <i class="ri-time-line"></i> Pendientes
                    </button>
                    <button class="filter-btn" data-filter="completadas">
                        <i class="ri-checkbox-circle-line"></i> Completadas
                    </button>
                    <button class="filter-btn" data-filter="atrasadas">
                        <i class="ri-error-warning-line"></i> Atrasadas
                    </button>
                </div>
                <div class="sort-group">
                    <select class="sort-select" id="sortSelect">
                        <option value="fecha">Ordenar por Fecha</option>
                        <option value="materia">Ordenar por Materia</option>
                        <option value="prioridad">Ordenar por Prioridad</option>
                    </select>
                </div>
            </div>

            <!-- ACTIVIDADES LIST -->
            <div class="actividades-container" id="actividadesContainer">

                <!-- ACTIVIDAD 1 - ATRASADA -->
                <div class="actividad-card atrasada" data-status="atrasada" data-materia="fisica" data-fecha="2024-11-20">
                    <div class="actividad-priority urgent"></div>
                    <div class="actividad-header">
                        <div class="actividad-icon" style="background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);">
                            <i class="ri-flask-line"></i>
                        </div>
                        <div class="actividad-info">
                            <div class="actividad-badge atrasada">
                                <i class="ri-error-warning-line"></i> Atrasada
                            </div>
                            <h3 class="actividad-title">Taller de Mecánica Clásica</h3>
                            <p class="actividad-materia">Física • Prof. Ana Rodríguez</p>
                        </div>
                        <div class="actividad-status">
                            <div class="progress-circle urgent">
                                <span>0%</span>
                            </div>
                        </div>
                    </div>

                    <p class="actividad-description">
                        Resolver 15 problemas sobre movimiento parabólico, caída libre y lanzamiento vertical. Incluir procedimiento completo.
                    </p>

                    <div class="actividad-meta">
                        <div class="meta-item">
                            <i class="ri-calendar-line"></i>
                            <span>Vencimiento: <strong>20 Nov, 2024</strong></span>
                        </div>
                        <div class="meta-item urgent">
                            <i class="ri-time-line"></i>
                            <span>Venció hace 1 día</span>
                        </div>
                        <div class="meta-item">
                            <i class="ri-file-text-line"></i>
                            <span>Taller escrito</span>
                        </div>
                    </div>

                    <div class="actividad-actions">
                        <button class="btn-actividad primary">
                            <i class="ri-upload-line"></i> Entregar Tarea
                        </button>
                        <button class="btn-actividad secondary">
                            <i class="ri-eye-line"></i> Ver Detalles
                        </button>
                    </div>
                </div>

                <!-- ACTIVIDAD 2 - PENDIENTE URGENTE -->
                <div class="actividad-card pendiente" data-status="pendiente" data-materia="matematicas" data-fecha="2024-11-22">
                    <div class="actividad-priority high"></div>
                    <div class="actividad-header">
                        <div class="actividad-icon" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);">
                            <i class="ri-calculator-line"></i>
                        </div>
                        <div class="actividad-info">
                            <div class="actividad-badge urgente">
                                <i class="ri-alarm-warning-line"></i> Vence mañana
                            </div>
                            <h3 class="actividad-title">Ejercicios de Álgebra Lineal</h3>
                            <p class="actividad-materia">Matemáticas • Prof. Carlos Méndez</p>
                        </div>
                        <div class="actividad-status">
                            <div class="progress-circle pending">
                                <span>25%</span>
                            </div>
                        </div>
                    </div>

                    <p class="actividad-description">
                        Completar ejercicios del capítulo 5: Sistemas de ecuaciones lineales, matrices y determinantes. Páginas 120-135.
                    </p>

                    <div class="actividad-meta">
                        <div class="meta-item">
                            <i class="ri-calendar-line"></i>
                            <span>Vencimiento: <strong>22 Nov, 2024</strong></span>
                        </div>
                        <div class="meta-item warning">
                            <i class="ri-time-line"></i>
                            <span>Vence en 1 día</span>
                        </div>
                        <div class="meta-item">
                            <i class="ri-file-text-line"></i>
                            <span>Ejercicios prácticos</span>
                        </div>
                    </div>

                    <div class="actividad-actions">
                        <button class="btn-actividad primary">
                            <i class="ri-upload-line"></i> Continuar Tarea
                        </button>
                        <button class="btn-actividad secondary">
                            <i class="ri-eye-line"></i> Ver Detalles
                        </button>
                    </div>
                </div>

                <!-- ACTIVIDAD 3 - PENDIENTE -->
                <div class="actividad-card pendiente" data-status="pendiente" data-materia="programacion" data-fecha="2024-11-28">
                    <div class="actividad-priority medium"></div>
                    <div class="actividad-header">
                        <div class="actividad-icon" style="background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);">
                            <i class="ri-code-s-slash-line"></i>
                        </div>
                        <div class="actividad-info">
                            <div class="actividad-badge pendiente">
                                <i class="ri-time-line"></i> Pendiente
                            </div>
                            <h3 class="actividad-title">Proyecto Final - Sistema de Gestión</h3>
                            <p class="actividad-materia">Programación • Prof. Diego Álvarez</p>
                        </div>
                        <div class="actividad-status">
                            <div class="progress-circle pending">
                                <span>60%</span>
                            </div>
                        </div>
                    </div>

                    <p class="actividad-description">
                        Desarrollar un sistema de gestión de biblioteca usando Java. Debe incluir: registro de libros, préstamos, devoluciones y reportes.
                    </p>

                    <div class="actividad-meta">
                        <div class="meta-item">
                            <i class="ri-calendar-line"></i>
                            <span>Vencimiento: <strong>28 Nov, 2024</strong></span>
                        </div>
                        <div class="meta-item">
                            <i class="ri-time-line"></i>
                            <span>Quedan 7 días</span>
                        </div>
                        <div class="meta-item">
                            <i class="ri-code-box-line"></i>
                            <span>Proyecto de código</span>
                        </div>
                    </div>

                    <div class="actividad-actions">
                        <button class="btn-actividad primary">
                            <i class="ri-upload-line"></i> Subir Avance
                        </button>
                        <button class="btn-actividad secondary">
                            <i class="ri-eye-line"></i> Ver Detalles
                        </button>
                    </div>
                </div>

                <!-- ACTIVIDAD 4 - COMPLETADA -->
                <div class="actividad-card completada" data-status="completada" data-materia="historia" data-fecha="2024-11-18">
                    <div class="actividad-priority low"></div>
                    <div class="actividad-header">
                        <div class="actividad-icon" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                            <i class="ri-book-open-line"></i>
                        </div>
                        <div class="actividad-info">
                            <div class="actividad-badge completada">
                                <i class="ri-checkbox-circle-line"></i> Completada
                            </div>
                            <h3 class="actividad-title">Ensayo sobre la Independencia</h3>
                            <p class="actividad-materia">Historia • Prof. María Ramírez</p>
                        </div>
                        <div class="actividad-status">
                            <div class="progress-circle complete">
                                <i class="ri-check-line"></i>
                            </div>
                        </div>
                    </div>

                    <p class="actividad-description">
                        Ensayo de 5 páginas sobre las causas y consecuencias de la independencia de Colombia. Formato APA.
                    </p>

                    <div class="actividad-meta">
                        <div class="meta-item">
                            <i class="ri-calendar-check-line"></i>
                            <span>Entregado: <strong>18 Nov, 2024</strong></span>
                        </div>
                        <div class="meta-item success">
                            <i class="ri-thumb-up-line"></i>
                            <span>Calificación: <strong>4.5/5.0</strong></span>
                        </div>
                        <div class="meta-item">
                            <i class="ri-file-word-line"></i>
                            <span>Ensayo académico</span>
                        </div>
                    </div>

                    <div class="actividad-actions">
                        <button class="btn-actividad secondary">
                            <i class="ri-eye-line"></i> Ver Retroalimentación
                        </button>
                        <button class="btn-actividad secondary">
                            <i class="ri-download-line"></i> Descargar
                        </button>
                    </div>
                </div>

                <!-- ACTIVIDAD 5 - COMPLETADA -->
                <div class="actividad-card completada" data-status="completada" data-materia="quimica" data-fecha="2024-11-15">
                    <div class="actividad-priority low"></div>
                    <div class="actividad-header">
                        <div class="actividad-icon" style="background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%);">
                            <i class="ri-test-tube-line"></i>
                        </div>
                        <div class="actividad-info">
                            <div class="actividad-badge completada">
                                <i class="ri-checkbox-circle-line"></i> Completada
                            </div>
                            <h3 class="actividad-title">Informe de Laboratorio</h3>
                            <p class="actividad-materia">Química • Prof. Luis Torres</p>
                        </div>
                        <div class="actividad-status">
                            <div class="progress-circle complete">
                                <i class="ri-check-line"></i>
                            </div>
                        </div>
                    </div>

                    <p class="actividad-description">
                        Informe completo de la práctica de laboratorio sobre reacciones químicas orgánicas. Incluir análisis de resultados.
                    </p>

                    <div class="actividad-meta">
                        <div class="meta-item">
                            <i class="ri-calendar-check-line"></i>
                            <span>Entregado: <strong>15 Nov, 2024</strong></span>
                        </div>
                        <div class="meta-item success">
                            <i class="ri-thumb-up-line"></i>
                            <span>Calificación: <strong>4.2/5.0</strong></span>
                        </div>
                        <div class="meta-item">
                            <i class="ri-file-pdf-line"></i>
                            <span>Informe PDF</span>
                        </div>
                    </div>

                    <div class="actividad-actions">
                        <button class="btn-actividad secondary">
                            <i class="ri-eye-line"></i> Ver Retroalimentación
                        </button>
                        <button class="btn-actividad secondary">
                            <i class="ri-download-line"></i> Descargar
                        </button>
                    </div>
                </div>

                <!-- ACTIVIDAD 6 - ATRASADA -->
                <div class="actividad-card atrasada" data-status="atrasada" data-materia="ingles" data-fecha="2024-11-19">
                    <div class="actividad-priority urgent"></div>
                    <div class="actividad-header">
                        <div class="actividad-icon" style="background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);">
                            <i class="ri-english-input"></i>
                        </div>
                        <div class="actividad-info">
                            <div class="actividad-badge atrasada">
                                <i class="ri-error-warning-line"></i> Atrasada
                            </div>
                            <h3 class="actividad-title">Reading Comprehension Test</h3>
                            <p class="actividad-materia">Inglés • Prof. Patricia Gómez</p>
                        </div>
                        <div class="actividad-status">
                            <div class="progress-circle urgent">
                                <span>0%</span>
                            </div>
                        </div>
                    </div>

                    <p class="actividad-description">
                        Complete the reading comprehension test about "Environmental Issues". Answer all 20 questions in the online platform.
                    </p>

                    <div class="actividad-meta">
                        <div class="meta-item">
                            <i class="ri-calendar-line"></i>
                            <span>Vencimiento: <strong>19 Nov, 2024</strong></span>
                        </div>
                        <div class="meta-item urgent">
                            <i class="ri-time-line"></i>
                            <span>Venció hace 2 días</span>
                        </div>
                        <div class="meta-item">
                            <i class="ri-questionnaire-line"></i>
                            <span>Evaluación online</span>
                        </div>
                    </div>

                    <div class="actividad-actions">
                        <button class="btn-actividad primary">
                            <i class="ri-play-circle-line"></i> Realizar Test
                        </button>
                        <button class="btn-actividad secondary">
                            <i class="ri-eye-line"></i> Ver Detalles
                        </button>
                    </div>
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

            <div class="panel-title">Calendario de Entregas</div>
            <p class="muted">Próximas fechas importantes</p>

            <div class="calendar-mini">
                <div class="calendar-event urgent">
                    <div class="event-date">
                        <span class="day">22</span>
                        <span class="month">Nov</span>
                    </div>
                    <div class="event-info">
                        <strong>Ejercicios de Álgebra</strong>
                        <small>Matemáticas • Mañana</small>
                    </div>
                </div>

                <div class="calendar-event">
                    <div class="event-date">
                        <span class="day">25</span>
                        <span class="month">Nov</span>
                    </div>
                    <div class="event-info">
                        <strong>Presentación Oral</strong>
                        <small>Inglés • En 4 días</small>
                    </div>
                </div>

                <div class="calendar-event">
                    <div class="event-date">
                        <span class="day">28</span>
                        <span class="month">Nov</span>
                    </div>
                    <div class="event-info">
                        <strong>Proyecto Final</strong>
                        <small>Programación • En 7 días</small>
                    </div>
                </div>
            </div>

            <div class="panel-title" style="margin-top:24px">Progreso Semanal</div>
            <p class="muted">Actividades completadas</p>

            <div class="progress-card">
                <div class="progress-header">
                    <span>Esta semana</span>
                    <strong>5/8</strong>
                </div>
                <div class="progress-bar-container">
                    <div class="progress-bar" style="width: 62.5%"></div>
                </div>
                <small class="progress-label">62.5% completado</small>
            </div>

            <div class="progress-card">
                <div class="progress-header">
                    <span>Este mes</span>
                    <strong>10/15</strong>
                </div>
                <div class="progress-bar-container">
                    <div class="progress-bar" style="width: 66.6%"></div>
                </div>
                <small class="progress-label">66.6% completado</small>
            </div>

            <div class="panel-title" style="margin-top:24px">Recordatorios</div>
            <p class="muted">No olvides...</p>

            <div class="reminder-list">
                <div class="reminder-item">
                    <div class="reminder-icon urgent">
                        <i class="ri-alarm-warning-line"></i>
                    </div>
                    <div class="reminder-content">
                        <strong>Taller de Física</strong>
                        <small>Vencido - Contactar al profesor</small>
                    </div>
                </div>

                <div class="reminder-item">
                    <div class="reminder-icon warning">
                        <i class="ri-time-line"></i>
                    </div>
                    <div class="reminder-content">
                        <strong>Ejercicios Matemáticas</strong>
                        <small>Vence mañana a las 11:59 PM</small>
                    </div>
                </div>

                <div class="reminder-item">
                    <div class="reminder-icon info">
                        <i class="ri-information-line"></i>
                    </div>
                    <div class="reminder-content">
                        <strong>Revisar feedback</strong>
                        <small>2 actividades con calificaciones</small>
                    </div>
                </div>
            </div>

            <button class="btn-primary">Ver todas las actividades</button>

            <div class="tips-card">
                <h4><i class="ri-lightbulb-line"></i> Consejo de Productividad</h4>
                <p>Organiza tu tiempo: dedica al menos 2 horas diarias para completar tus actividades pendientes. Prioriza las más urgentes.</p>
            </div>
        </aside>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?= BASE_URL ?>/public/assets/dashboard/js/estudiante/actividades.js"></script>
</body>

</html>