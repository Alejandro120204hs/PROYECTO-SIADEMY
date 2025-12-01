<!doctype html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SIADEMY • Mis Profesores</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/remixicon@4.3.0/fonts/remixicon.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/dashboard/css/styles-profesores.css">
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
                    <div class="title">Mis Profesores</div>
                </div>

                <div class="search">
                    <i class="ri-search-2-line"></i>
                    <input type="text" id="searchInput" placeholder="Buscar profesor o materia...">
                </div>

                <button class="toggle-btn" id="toggleRight" title="Mostrar/Ocultar panel derecho">
                    <i class="ri-layout-right-2-line"></i>
                </button>
            </div>

            <!-- STATS CARDS -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon blue">
                        <i class="ri-user-line"></i>
                    </div>
                    <div class="stat-content">
                        <h3>6</h3>
                        <p>Total Profesores</p>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon green">
                        <i class="ri-book-2-line"></i>
                    </div>
                    <div class="stat-content">
                        <h3>6</h3>
                        <p>Materias Activas</p>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon orange">
                        <i class="ri-calendar-line"></i>
                    </div>
                    <div class="stat-content">
                        <h3>28</h3>
                        <p>Horas Semanales</p>
                    </div>
                </div>
            </div>

            <!-- FILTERS -->
            <div class="filter-section">
                <div class="filter-group">
                    <button class="filter-btn active" data-filter="todos">
                        <i class="ri-user-line"></i> Todos
                    </button>
                    <button class="filter-btn" data-filter="matematicas">
                        <i class="ri-calculator-line"></i> Ciencias Exactas
                    </button>
                    <button class="filter-btn" data-filter="humanidades">
                        <i class="ri-book-line"></i> Humanidades
                    </button>
                    <button class="filter-btn" data-filter="tecnologia">
                        <i class="ri-code-line"></i> Tecnología
                    </button>
                </div>
            </div>

            <!-- PROFESORES GRID -->
            <div class="profesores-grid" id="profesoresGrid">

                <!-- PROFESOR 1 -->
                <div class="profesor-card" data-categoria="matematicas">
                    <div class="profesor-header">
                        <div class="profesor-avatar" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);">
                            <span>CM</span>
                        </div>
                        <div class="profesor-status online">
                            <i class="ri-checkbox-circle-fill"></i>
                            <span>Disponible</span>
                        </div>
                    </div>

                    <div class="profesor-info">
                        <h3>Carlos Méndez</h3>
                        <p class="profesor-titulo">Licenciado en Matemáticas</p>
                        <div class="profesor-materia">
                            <i class="ri-book-2-line"></i>
                            <span>Matemáticas</span>
                        </div>
                    </div>

                    <div class="profesor-stats">
                        <div class="stat-item">
                            <i class="ri-award-line"></i>
                            <div>
                                <strong>2.8</strong>
                                <small>Tu nota</small>
                            </div>
                        </div>
                        <div class="stat-item">
                            <i class="ri-time-line"></i>
                            <div>
                                <strong>6h</strong>
                                <small>Semanales</small>
                            </div>
                        </div>
                        <div class="stat-item">
                            <i class="ri-calendar-check-line"></i>
                            <div>
                                <strong>88%</strong>
                                <small>Asistencia</small>
                            </div>
                        </div>
                    </div>

                    <div class="profesor-contact">
                        <div class="contact-item">
                            <i class="ri-mail-line"></i>
                            <span>carlos.mendez@colegio.edu</span>
                        </div>
                        <div class="contact-item">
                            <i class="ri-phone-line"></i>
                            <span>+57 300 123 4567</span>
                        </div>
                    </div>

                    <div class="profesor-horario">
                        <strong>Horario de Atención:</strong>
                        <p>Lunes a Viernes: 2:00 PM - 4:00 PM</p>
                    </div>

                    <div class="profesor-actions">
                        <button class="btn-profesor primary">
                            <i class="ri-message-3-line"></i> Enviar Mensaje
                        </button>
                        <button class="btn-profesor secondary">
                            <i class="ri-calendar-event-line"></i> Agendar Cita
                        </button>
                    </div>
                </div>

                <!-- PROFESOR 2 -->
                <div class="profesor-card" data-categoria="matematicas">
                    <div class="profesor-header">
                        <div class="profesor-avatar" style="background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);">
                            <span>AR</span>
                        </div>
                        <div class="profesor-status online">
                            <i class="ri-checkbox-circle-fill"></i>
                            <span>Disponible</span>
                        </div>
                    </div>

                    <div class="profesor-info">
                        <h3>Ana Rodríguez</h3>
                        <p class="profesor-titulo">Física Nuclear - PhD</p>
                        <div class="profesor-materia">
                            <i class="ri-flask-line"></i>
                            <span>Física</span>
                        </div>
                    </div>

                    <div class="profesor-stats">
                        <div class="stat-item">
                            <i class="ri-award-line"></i>
                            <div>
                                <strong>2.5</strong>
                                <small>Tu nota</small>
                            </div>
                        </div>
                        <div class="stat-item">
                            <i class="ri-time-line"></i>
                            <div>
                                <strong>5h</strong>
                                <small>Semanales</small>
                            </div>
                        </div>
                        <div class="stat-item">
                            <i class="ri-calendar-check-line"></i>
                            <div>
                                <strong>82%</strong>
                                <small>Asistencia</small>
                            </div>
                        </div>
                    </div>

                    <div class="profesor-contact">
                        <div class="contact-item">
                            <i class="ri-mail-line"></i>
                            <span>ana.rodriguez@colegio.edu</span>
                        </div>
                        <div class="contact-item">
                            <i class="ri-phone-line"></i>
                            <span>+57 300 234 5678</span>
                        </div>
                    </div>

                    <div class="profesor-horario">
                        <strong>Horario de Atención:</strong>
                        <p>Martes y Jueves: 3:00 PM - 5:00 PM</p>
                    </div>

                    <div class="profesor-actions">
                        <button class="btn-profesor primary">
                            <i class="ri-message-3-line"></i> Enviar Mensaje
                        </button>
                        <button class="btn-profesor secondary">
                            <i class="ri-calendar-event-line"></i> Agendar Cita
                        </button>
                    </div>
                </div>

                <!-- PROFESOR 3 -->
                <div class="profesor-card" data-categoria="matematicas">
                    <div class="profesor-header">
                        <div class="profesor-avatar" style="background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%);">
                            <span>LT</span>
                        </div>
                        <div class="profesor-status online">
                            <i class="ri-checkbox-circle-fill"></i>
                            <span>Disponible</span>
                        </div>
                    </div>

                    <div class="profesor-info">
                        <h3>Luis Torres</h3>
                        <p class="profesor-titulo">Químico Industrial</p>
                        <div class="profesor-materia">
                            <i class="ri-test-tube-line"></i>
                            <span>Química</span>
                        </div>
                    </div>

                    <div class="profesor-stats">
                        <div class="stat-item">
                            <i class="ri-award-line"></i>
                            <div>
                                <strong>3.0</strong>
                                <small>Tu nota</small>
                            </div>
                        </div>
                        <div class="stat-item">
                            <i class="ri-time-line"></i>
                            <div>
                                <strong>4h</strong>
                                <small>Semanales</small>
                            </div>
                        </div>
                        <div class="stat-item">
                            <i class="ri-calendar-check-line"></i>
                            <div>
                                <strong>90%</strong>
                                <small>Asistencia</small>
                            </div>
                        </div>
                    </div>

                    <div class="profesor-contact">
                        <div class="contact-item">
                            <i class="ri-mail-line"></i>
                            <span>luis.torres@colegio.edu</span>
                        </div>
                        <div class="contact-item">
                            <i class="ri-phone-line"></i>
                            <span>+57 300 345 6789</span>
                        </div>
                    </div>

                    <div class="profesor-horario">
                        <strong>Horario de Atención:</strong>
                        <p>Lunes y Miércoles: 1:00 PM - 3:00 PM</p>
                    </div>

                    <div class="profesor-actions">
                        <button class="btn-profesor primary">
                            <i class="ri-message-3-line"></i> Enviar Mensaje
                        </button>
                        <button class="btn-profesor secondary">
                            <i class="ri-calendar-event-line"></i> Agendar Cita
                        </button>
                    </div>
                </div>

                <!-- PROFESOR 4 -->
                <div class="profesor-card" data-categoria="humanidades">
                    <div class="profesor-header">
                        <div class="profesor-avatar" style="background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);">
                            <span>PG</span>
                        </div>
                        <div class="profesor-status online">
                            <i class="ri-checkbox-circle-fill"></i>
                            <span>Disponible</span>
                        </div>
                    </div>

                    <div class="profesor-info">
                        <h3>Patricia Gómez</h3>
                        <p class="profesor-titulo">Filología Inglesa</p>
                        <div class="profesor-materia">
                            <i class="ri-english-input"></i>
                            <span>Inglés</span>
                        </div>
                    </div>

                    <div class="profesor-stats">
                        <div class="stat-item">
                            <i class="ri-award-line"></i>
                            <div>
                                <strong>4.5</strong>
                                <small>Tu nota</small>
                            </div>
                        </div>
                        <div class="stat-item">
                            <i class="ri-time-line"></i>
                            <div>
                                <strong>5h</strong>
                                <small>Semanales</small>
                            </div>
                        </div>
                        <div class="stat-item">
                            <i class="ri-calendar-check-line"></i>
                            <div>
                                <strong>95%</strong>
                                <small>Asistencia</small>
                            </div>
                        </div>
                    </div>

                    <div class="profesor-contact">
                        <div class="contact-item">
                            <i class="ri-mail-line"></i>
                            <span>patricia.gomez@colegio.edu</span>
                        </div>
                        <div class="contact-item">
                            <i class="ri-phone-line"></i>
                            <span>+57 300 456 7890</span>
                        </div>
                    </div>

                    <div class="profesor-horario">
                        <strong>Horario de Atención:</strong>
                        <p>Lunes a Viernes: 10:00 AM - 12:00 PM</p>
                    </div>

                    <div class="profesor-actions">
                        <button class="btn-profesor primary">
                            <i class="ri-message-3-line"></i> Enviar Mensaje
                        </button>
                        <button class="btn-profesor secondary">
                            <i class="ri-calendar-event-line"></i> Agendar Cita
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

            <div class="panel-title">Horario de Clases</div>
            <p class="muted">Semana actual</p>

            <div class="horario-list">
                <div class="horario-item">
                    <div class="horario-dia">Lun</div>
                    <div class="horario-content">
                        <strong>8:00 AM - Matemáticas</strong>
                        <small>Prof. Carlos Méndez</small>
                    </div>
                </div>

                <div class="horario-item">
                    <div class="horario-dia">Mar</div>
                    <div class="horario-content">
                        <strong>9:00 AM - Física</strong>
                        <small>Prof. Ana Rodríguez</small>
                    </div>
                </div>

                <div class="horario-item">
                    <div class="horario-dia">Mié</div>
                    <div class="horario-content">
                        <strong>10:00 AM - Química</strong>
                        <small>Prof. Luis Torres</small>
                    </div>
                </div>

                <div class="horario-item">
                    <div class="horario-dia">Jue</div>
                    <div class="horario-content">
                        <strong>2:00 PM - Inglés</strong>
                        <small>Prof. Patricia Gómez</small>
                    </div>
                </div>

                <div class="horario-item">
                    <div class="horario-dia">Vie</div>
                    <div class="horario-content">
                        <strong>3:00 PM - Programación</strong>
                        <small>Prof. Diego Álvarez</small>
                    </div>
                </div>
            </div>

            <div class="panel-title" style="margin-top:24px">Próximas Citas</div>
            <p class="muted">Asesorías programadas</p>

            <div class="citas-list">
                <div class="cita-item">
                    <div class="cita-date">
                        <span class="day">23</span>
                        <span class="month">Nov</span>
                    </div>
                    <div class="cita-info">
                        <strong>Asesoría Matemáticas</strong>
                        <small>Prof. Carlos Méndez</small>
                        <div class="cita-time">
                            <i class="ri-time-line"></i> 2:00 PM - 3:00 PM
                        </div>
                    </div>
                </div>

                <div class="cita-item">
                    <div class="cita-date">
                        <span class="day">25</span>
                        <span class="month">Nov</span>
                    </div>
                    <div class="cita-info">
                        <strong>Revisión Proyecto</strong>
                        <small>Prof. Diego Álvarez</small>
                        <div class="cita-time">
                            <i class="ri-time-line"></i> 3:30 PM - 4:30 PM
                        </div>
                    </div>
                </div>
            </div>

            <button class="btn-primary">Ver Horario Completo</button>

            <div class="tips-card">
                <h4><i class="ri-lightbulb-line"></i> Consejo</h4>
                <p>Mantén una comunicación constante con tus profesores. Solicita asesorías cuando tengas dudas y aprovecha sus horarios de atención.</p>
            </div>
        </aside>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?= BASE_URL ?>/public/assets/dashboard/js/estudiante/profesores.js"></script>
</body>

</html>