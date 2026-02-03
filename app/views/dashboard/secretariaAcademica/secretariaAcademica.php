<!doctype html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SIADEMY • Panel Secretaría Académica</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/remixicon@4.3.0/fonts/remixicon.css">
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/dashboard/css/styles-secretaría.css">
</head>

<body>
    <div class="app" id="appGrid">
        <!-- LEFT SIDEBAR -->
        <?php
        include_once __DIR__ . '/../../layouts/sidebar_secretaría.php'
        ?>

        <!-- MAIN -->
        <main class="main">
            <div class="topbar">
                <div class="topbar-left">
                    <button class="toggle-btn" id="toggleLeft" title="Mostrar/Ocultar menú lateral">
                        <i class="ri-menu-2-line"></i>
                    </button>
                    <div class="title">Panel Secretaría</div>
                </div>
                <div class="search">
                    <i class="ri-search-2-line"></i>
                    <input type="text" placeholder="Buscar estudiante, grupo...">
                </div>
                <button class="toggle-btn" id="toggleRight" title="Mostrar/Ocultar panel derecho">
                    <i class="ri-layout-right-2-line"></i>
                </button>
            </div>

            <!-- STATS CARDS -->
            <div class="kpis">
                <div class="kpi-card">
                    <div class="kpi-icon blue">
                        <i class="ri-user-add-line"></i>
                    </div>
                    <div class="kpi-content">
                        <div class="kpi-value">12</div>
                        <div class="kpi-label">Matrículas Pendientes</div>
                        <div class="kpi-trend up">
                            <i class="ri-arrow-up-line"></i>
                            <span>+3 esta semana</span>
                        </div>
                    </div>
                </div>

                <div class="kpi-card">
                    <div class="kpi-icon green">
                        <i class="ri-team-line"></i>
                    </div>
                    <div class="kpi-content">
                        <div class="kpi-value">450</div>
                        <div class="kpi-label">Estudiantes Activos</div>
                        <div class="kpi-trend up">
                            <i class="ri-arrow-up-line"></i>
                            <span>+8 este mes</span>
                        </div>
                    </div>
                </div>

                <div class="kpi-card">
                    <div class="kpi-icon orange">
                        <i class="ri-file-list-3-line"></i>
                    </div>
                    <div class="kpi-content">
                        <div class="kpi-value">8</div>
                        <div class="kpi-label">Certificados Solicitados</div>
                        <div class="kpi-trend">
                            <i class="ri-time-line"></i>
                            <span>Pendientes de firma</span>
                        </div>
                    </div>
                </div>

                <div class="kpi-card">
                    <div class="kpi-icon purple">
                        <i class="ri-group-line"></i>
                    </div>
                    <div class="kpi-content">
                        <div class="kpi-value">18</div>
                        <div class="kpi-label">Grados Activos</div>
                        <div class="kpi-trend">
                            <i class="ri-information-line"></i>
                            <span>6 grados</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ACCIONES RÁPIDAS -->
            <section class="quick-actions-section">
                <h3>Acciones Rápidas</h3>
                <div class="quick-actions-grid">
                    <button class="action-btn blue" onclick="openMatriculaModal()">
                        <i class="ri-user-add-line"></i>
                        <span>Matrícular Estudiante</span>
                    </button>
                    <button class="action-btn green">
                        <i class="ri-file-list-3-line"></i>
                        <span>Generar Documento</span>
                    </button>
                    <button class="action-btn orange">
                        <i class="ri-user-search-line"></i>
                        <span>Gestión Académica</span>
                    </button>
                    <button class="action-btn purple">
                        <i class="ri-bar-chart-line"></i>
                        <span>Ver Reportes</span>
                    </button>
                    <button class="action-btn red">
                        <i class="ri-task-line"></i>
                        <span>Solicitudes Academicas</span>
                    </button>
                </div>
            </section>

            <!-- DATATABLE: SOLICITUDES PENDIENTES -->
            <section class="datatable-card">
                <div class="card-header-custom">
                    <h3>Solicitudes de Matrícula Pendientes</h3>
                    <button class="btn-export">
                        <i class="ri-download-line"></i>
                        Exportar
                    </button>
                </div>

                <div class="table-responsive">
                    <table id="matriculasTable" class="table table-dark table-hover align-middle" style="width:100%">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Estudiante</th>
                                <th>Grado</th>
                                <th>Fecha Solicitud</th>
                                <th>Acudiente</th>
                                <th>Teléfono</th>
                                <th class="text-center">Estado</th>
                                <th class="text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><strong>#2024-001</strong></td>
                                <td>
                                    <div class="student-info">
                                        <div class="avatar-sm">JM</div>
                                        <div>
                                            <strong>Juan Martínez Pérez</strong>
                                            <small class="d-block text-muted">CC: 1234567890</small>
                                        </div>
                                    </div>
                                </td>
                                <td>7° A</td>
                                <td>15 Nov 2024</td>
                                <td>María Pérez</td>
                                <td>+57 300 123 4567</td>
                                <td class="text-center">
                                    <span class="badge-status pending">Pendiente</span>
                                </td>
                                <td class="text-center">
                                    <div class="action-buttons">
                                        <button class="btn-action approve" title="Aprobar">
                                            <i class="ri-check-line"></i>
                                        </button>
                                        <button class="btn-actions view" title="Ver perfil">
                                            <a href="">Ver</a>
                                        </button>
                                        <button class="btn-action reject" title="Rechazar">
                                            <i class="ri-close-line"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>

                            <tr>
                                <td><strong>#2024-002</strong></td>
                                <td>
                                    <div class="student-info">
                                        <div class="avatar-sm" style="background: #10b981">AG</div>
                                        <div>
                                            <strong>Ana García López</strong>
                                            <small class="d-block text-muted">CC: 9876543210</small>
                                        </div>
                                    </div>
                                </td>
                                <td>8° B</td>
                                <td>16 Nov 2024</td>
                                <td>Carlos López</td>
                                <td>+57 300 234 5678</td>
                                <td class="text-center">
                                    <span class="badge-status pending">Pendiente</span>
                                </td>
                                <td class="text-center">
                                    <div class="action-buttons">
                                        <button class="btn-action approve" title="Aprobar">
                                            <i class="ri-check-line"></i>
                                        </button>
                                        <button class="btn-actions view" title="Ver perfil">
                                            <a href="">Ver</a>
                                        </button>
                                        <button class="btn-action reject" title="Rechazar">
                                            <i class="ri-close-line"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>

                            <tr>
                                <td><strong>#2024-003</strong></td>
                                <td>
                                    <div class="student-info">
                                        <div class="avatar-sm" style="background: #f59e0b">LR</div>
                                        <div>
                                            <strong>Luis Rodríguez Gómez</strong>
                                            <small class="d-block text-muted">CC: 5678901234</small>
                                        </div>
                                    </div>
                                </td>
                                <td>9° A</td>
                                <td>17 Nov 2024</td>
                                <td>Sandra Gómez</td>
                                <td>+57 300 345 6789</td>
                                <td class="text-center">
                                    <span class="badge-status review">Revisión</span>
                                </td>
                                <td class="text-center">
                                    <div class="action-buttons">
                                        <button class="btn-action approve" title="Aprobar">
                                            <i class="ri-check-line"></i>
                                        </button>
                                        <button class="btn-actions view" title="Ver perfil">
                                            <a href="">Ver</a>
                                        </button>
                                        <button class="btn-action reject" title="Rechazar">
                                            <i class="ri-close-line"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>

                            <tr>
                                <td><strong>#2024-004</strong></td>
                                <td>
                                    <div class="student-info">
                                        <div class="avatar-sm" style="background: #8b5cf6">MT</div>
                                        <div>
                                            <strong>María Torres Silva</strong>
                                            <small class="d-block text-muted">CC: 3456789012</small>
                                        </div>
                                    </div>
                                </td>
                                <td>10° B</td>
                                <td>18 Nov 2024</td>
                                <td>Pedro Silva</td>
                                <td>+57 300 456 7890</td>
                                <td class="text-center">
                                    <span class="badge-status pending">Pendiente</span>
                                </td>
                                <td class="text-center">
                                    <div class="action-buttons">
                                        <button class="btn-action approve" title="Aprobar">
                                            <i class="ri-check-line"></i>
                                        </button>
                                        <button class="btn-actions view" title="Ver perfil">
                                            <a href="">Ver</a>
                                        </button>
                                        <button class="btn-action reject" title="Rechazar">
                                            <i class="ri-close-line"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>

                            <tr>
                                <td><strong>#2024-005</strong></td>
                                <td>
                                    <div class="student-info">
                                        <div class="avatar-sm" style="background: #ef4444">CR</div>
                                        <div>
                                            <strong>Carlos Ramírez Díaz</strong>
                                            <small class="d-block text-muted">CC: 7890123456</small>
                                        </div>
                                    </div>
                                </td>
                                <td>11° A</td>
                                <td>19 Nov 2024</td>
                                <td>Laura Díaz</td>
                                <td>+57 300 567 8901</td>
                                <td class="text-center">
                                    <span class="badge-status pending">Pendiente</span>
                                </td>
                                <td class="text-center">
                                    <div class="action-buttons">
                                        <button class="btn-action approve" title="Aprobar">
                                            <i class="ri-check-line"></i>
                                        </button>
                                        <button class="btn-actions view" title="Ver perfil">
                                            <a href="">Ver</a>
                                        </button>
                                        <button class="btn-action reject" title="Rechazar">
                                            <i class="ri-close-line"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>

            <!-- GRÁFICO DE ESTADÍSTICAS -->
            <section class="chart-section">
                <h3>Estadísticas de Matrículas 2024</h3>
                <div class="chart-container">
                    <canvas id="matriculasChart"></canvas>
                </div>
            </section>

        </main>

        <!-- RIGHT SIDEBAR -->
        <aside class="rightbar" id="rightSidebar">
            <div class="user">
                <button class="btn" title="Notificaciones"><i class="ri-notification-3-line"></i></button>
                <button class="btn" title="Configuración"><i class="ri-settings-3-line"></i></button>
                <div class="avatar" title="Secretaría">SA</div>
            </div>

            <div class="panel-title">Tareas del Día</div>
            <p class="muted">Actividades programadas</p>

            <div class="task-list">
                <div class="task-item urgent">
                    <div class="task-checkbox">
                        <input type="checkbox" id="task1">
                        <label for="task1"></label>
                    </div>
                    <div class="task-content">
                        <strong>Revisar matrículas pendientes</strong>
                        <small>12 solicitudes sin revisar</small>
                    </div>
                </div>

                <div class="task-item">
                    <div class="task-checkbox">
                        <input type="checkbox" id="task2">
                        <label for="task2"></label>
                    </div>
                    <div class="task-content">
                        <strong>Generar certificados</strong>
                        <small>8 certificados solicitados</small>
                    </div>
                </div>

                <div class="task-item">
                    <div class="task-checkbox">
                        <input type="checkbox" id="task3">
                        <label for="task3"></label>
                    </div>
                    <div class="task-content">
                        <strong>Actualizar horarios</strong>
                        <small>Grado 7° A y 7° B</small>
                    </div>
                </div>

                <div class="task-item">
                    <div class="task-checkbox">
                        <input type="checkbox" id="task4">
                        <label for="task4"></label>
                    </div>
                    <div class="task-content">
                        <strong>Enviar reportes mensuales</strong>
                        <small>Vence hoy a las 5:00 PM</small>
                    </div>
                </div>
            </div>

            <div class="panel-title" style="margin-top:24px">Alertas Importantes</div>
            <p class="muted">Requieren atención</p>

            <div class="alert-list">
                <div class="alert-item urgent">
                    <div class="alert-icon">
                        <i class="ri-alarm-warning-line"></i>
                    </div>
                    <div class="alert-content">
                        <strong>Documentos incompletos</strong>
                        <small>5 estudiantes con documentación pendiente</small>
                    </div>
                </div>

                <div class="alert-item warning">
                    <div class="alert-icon">
                        <i class="ri-time-line"></i>
                    </div>
                    <div class="alert-content">
                        <strong>Cupos limitados</strong>
                        <small>Grado 10° solo 3 cupos disponibles</small>
                    </div>
                </div>

                <div class="alert-item info">
                    <div class="alert-icon">
                        <i class="ri-information-line"></i>
                    </div>
                    <div class="alert-content">
                        <strong>Reunión programada</strong>
                        <small>Mañana 10:00 AM - Coordinación</small>
                    </div>
                </div>
            </div>

            <div class="panel-title" style="margin-top:24px">Acceso Rápido</div>
            <p class="muted">Enlaces frecuentes</p>

            <div class="quick-links">
                <a href="#" class="quick-link">
                    <i class="ri-file-list-3-line"></i>
                    <span>Lista de Estudiantes</span>
                </a>
                <a href="#" class="quick-link">
                    <i class="ri-calendar-line"></i>
                    <span>Calendario Académico</span>
                </a>
                <a href="#" class="quick-link">
                    <i class="ri-printer-line"></i>
                    <span>Imprimir Planillas</span>
                </a>
                <a href="#" class="quick-link">
                    <i class="ri-folder-line"></i>
                    <span>Archivo Digital</span>
                </a>
            </div>

            <button class="btn-primary">Ver Todas las Tareas</button>
        </aside>
    </div>

    <!-- MODAL NUEVA MATRÍCULA -->
    <div class="modal-overlay" id="matriculaModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3><i class="ri-user-add-line"></i> Nueva Matrícula</h3>
                <button class="modal-close" onclick="closeMatriculaModal()">
                    <i class="ri-close-line"></i>
                </button>
            </div>
            <div class="modal-body">
                <form id="matriculaForm">
                    <!-- Datos del Estudiante -->
                    <div class="form-section">
                        <h4><i class="ri-user-line"></i> Datos del Estudiante</h4>
                        <div class="form-row">
                            <div class="form-group">
                                <label>Nombres <span class="required">*</span></label>
                                <input type="text" class="form-control" name="estudiante_nombres" required>
                            </div>
                            <div class="form-group">
                                <label>Apellidos <span class="required">*</span></label>
                                <input type="text" class="form-control" name="estudiante_apellidos" required>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label>Tipo de Documento <span class="required">*</span></label>
                                <select class="form-control" name="tipo_documento" required>
                                    <option value="">Seleccionar...</option>
                                    <option value="TI">Tarjeta de Identidad</option>
                                    <option value="CC">Cédula de Ciudadanía</option>
                                    <option value="CE">Cédula de Extranjería</option>
                                    <option value="RC">Registro Civil</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Número de Documento <span class="required">*</span></label>
                                <input type="text" class="form-control" name="numero_documento" required>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label>Fecha de Nacimiento <span class="required">*</span></label>
                                <input type="date" class="form-control" name="fecha_nacimiento" required>
                            </div>
                            <div class="form-group">
                                <label>Género <span class="required">*</span></label>
                                <select class="form-control" name="genero" required>
                                    <option value="">Seleccionar...</option>
                                    <option value="M">Masculino</option>
                                    <option value="F">Femenino</option>
                                    <option value="O">Otro</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label>Dirección de Residencia <span class="required">*</span></label>
                                <input type="text" class="form-control" name="direccion" required>
                            </div>
                            <div class="form-group">
                                <label>Barrio</label>
                                <input type="text" class="form-control" name="barrio">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label>EPS <span class="required">*</span></label>
                                <input type="text" class="form-control" name="eps" required>
                            </div>
                            <div class="form-group">
                                <label>Grupo Sanguíneo</label>
                                <select class="form-control" name="grupo_sanguineo">
                                    <option value="">Seleccionar...</option>
                                    <option>A+</option>
                                    <option>A-</option>
                                    <option>B+</option>
                                    <option>B-</option>
                                    <option>AB+</option>
                                    <option>AB-</option>
                                    <option>O+</option>
                                    <option>O-</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Datos del Acudiente -->
                    <div class="form-section">
                        <h4><i class="ri-parent-line"></i> Datos del Acudiente</h4>
                        <div class="form-row">
                            <div class="form-group">
                                <label>Nombres <span class="required">*</span></label>
                                <input type="text" class="form-control" name="acudiente_nombres" required>
                            </div>
                            <div class="form-group">
                                <label>Apellidos <span class="required">*</span></label>
                                <input type="text" class="form-control" name="acudiente_apellidos" required>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label>Tipo de Documento <span class="required">*</span></label>
                                <select class="form-control" name="acudiente_tipo_documento" required>
                                    <option value="">Seleccionar...</option>
                                    <option value="CC">Cédula de Ciudadanía</option>
                                    <option value="CE">Cédula de Extranjería</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Número de Documento <span class="required">*</span></label>
                                <input type="text" class="form-control" name="acudiente_documento" required>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label>Parentesco <span class="required">*</span></label>
                                <select class="form-control" name="parentesco" required>
                                    <option value="">Seleccionar...</option>
                                    <option>Madre</option>
                                    <option>Padre</option>
                                    <option>Abuelo/a</option>
                                    <option>Tío/a</option>
                                    <option>Hermano/a</option>
                                    <option>Otro</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Ocupación</label>
                                <input type="text" class="form-control" name="acudiente_ocupacion">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label>Teléfono Principal <span class="required">*</span></label>
                                <input type="tel" class="form-control" name="acudiente_telefono" required>
                            </div>
                            <div class="form-group">
                                <label>Teléfono Alternativo</label>
                                <input type="tel" class="form-control" name="acudiente_telefono2">
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Email <span class="required">*</span></label>
                            <input type="email" class="form-control" name="acudiente_email" required>
                        </div>
                    </div>

                    <!-- Información Académica -->
                    <div class="form-section">
                        <h4><i class="ri-book-line"></i> Información Académica</h4>
                        <div class="form-row">
                            <div class="form-group">
                                <label>Grado <span class="required">*</span></label>
                                <select class="form-control" name="grado" required>
                                    <option value="">Seleccionar...</option>
                                    <option>6° A</option>
                                    <option>6° B</option>
                                    <option>7° A</option>
                                    <option>7° B</option>
                                    <option>8° A</option>
                                    <option>8° B</option>
                                    <option>9° A</option>
                                    <option>9° B</option>
                                    <option>10° A</option>
                                    <option>10° B</option>
                                    <option>11° A</option>
                                    <option>11° B</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Jornada <span class="required">*</span></label>
                                <select class="form-control" name="jornada" required>
                                    <option value="">Seleccionar...</option>
                                    <option>Mañana</option>
                                    <option>Tarde</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label>Institución de Procedencia</label>
                                <input type="text" class="form-control" name="institucion_procedencia">
                            </div>
                            <div class="form-group">
                                <label>Año de Ingreso <span class="required">*</span></label>
                                <input type="number" class="form-control" name="año_ingreso" min="2020" max="2030" required>
                            </div>
                        </div>
                    </div>

                    <!-- Documentación Requerida -->
                    <div class="form-section">
                        <h4><i class="ri-file-text-line"></i> Documentación Requerida</h4>

                        <div class="form-group">
                            <label>Documento de Identidad del Estudiante <span class="required">*</span></label>
                            <div class="file-upload-area" onclick="document.getElementById('doc_identidad').click()">
                                <i class="ri-upload-cloud-line"></i>
                                <p><strong>Clic para seleccionar archivo</strong> o arrastra aquí</p>
                                <p class="file-types">PDF (máx. 5MB)</p>
                            </div>
                            <input type="file" id="doc_identidad" class="file-input" accept=".pdf" data-target="uploaded_doc_identidad">
                            <div class="uploaded-files" id="uploaded_doc_identidad"></div>
                        </div>

                        <div class="form-group">
                            <label>Certificado de Estudios <span class="required">*</span></label>
                            <div class="file-upload-area" onclick="document.getElementById('certificado_estudios').click()">
                                <i class="ri-upload-cloud-line"></i>
                                <p><strong>Clic para seleccionar archivo</strong> o arrastra aquí</p>
                                <p class="file-types">PDF (máx. 5MB)</p>
                            </div>
                            <input type="file" id="certificado_estudios" class="file-input" accept=".pdf" data-target="uploaded_certificado">
                            <div class="uploaded-files" id="uploaded_certificado"></div>
                        </div>

                        <div class="form-group">
                            <label>Certificado EPS <span class="required">*</span></label>
                            <div class="file-upload-area" onclick="document.getElementById('certificado_eps').click()">
                                <i class="ri-upload-cloud-line"></i>
                                <p><strong>Clic para seleccionar archivo</strong> o arrastra aquí</p>
                                <p class="file-types">PDF (máx. 5MB)</p>
                            </div>
                            <input type="file" id="certificado_eps" class="file-input" accept=".pdf" data-target="uploaded_eps">
                            <div class="uploaded-files" id="uploaded_eps"></div>
                        </div>

                        <div class="form-group">
                            <label>Documento de Identidad del Acudiente <span class="required">*</span></label>
                            <div class="file-upload-area" onclick="document.getElementById('doc_acudiente').click()">
                                <i class="ri-upload-cloud-line"></i>
                                <p><strong>Clic para seleccionar archivo</strong> o arrastra aquí</p>
                                <p class="file-types">PDF (máx. 5MB)</p>
                            </div>
                            <input type="file" id="doc_acudiente" class="file-input" accept=".pdf" data-target="uploaded_doc_acudiente">
                            <div class="uploaded-files" id="uploaded_doc_acudiente"></div>
                        </div>

                        <div class="form-group">
                            <label>Recibo de Servicio Público</label>
                            <div class="file-upload-area" onclick="document.getElementById('recibo_publico').click()">
                                <i class="ri-upload-cloud-line"></i>
                                <p><strong>Clic para seleccionar archivo</strong> o arrastra aquí</p>
                                <p class="file-types">PDF (máx. 5MB)</p>
                            </div>
                            <input type="file" id="recibo_publico" class="file-input" accept=".pdf" data-target="uploaded_recibo">
                            <div class="uploaded-files" id="uploaded_recibo"></div>
                        </div>

                        <div class="form-group">
                            <label>Otros Documentos (Opcional)</label>
                            <div class="file-upload-area" onclick="document.getElementById('otros_docs').click()">
                                <i class="ri-upload-cloud-line"></i>
                                <p><strong>Clic para seleccionar archivos</strong> o arrastra aquí</p>
                                <p class="file-types">PDF (máx. 5MB por archivo)</p>
                            </div>
                            <input type="file" id="otros_docs" class="file-input" accept=".pdf" multiple data-target="uploaded_otros">
                            <div class="uploaded-files" id="uploaded_otros"></div>
                        </div>
                    </div>

                    <div class="modal-actions">
                        <button type="button" class="btn-secondary" onclick="closeMatriculaModal()">
                            Cancelar
                        </button>
                        <button type="submit" class="btn-primary">
                            <i class="ri-save-line"></i> Registrar Matrícula
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
    <script src="<?= BASE_URL ?>/public/assets/dashboard/js/main-secretaría.js"></script>
</body>

</html>