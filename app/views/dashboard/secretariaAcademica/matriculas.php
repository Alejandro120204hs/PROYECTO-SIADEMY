<!doctype html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SIADEMY • Gestión de Matrículas</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/remixicon@4.3.0/fonts/remixicon.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/dashboard/css/styles-matriculas.css">

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
                    <div class="title">Gestión de Matrículas</div>
                </div>
                <div class="search">
                    <i class="ri-search-2-line"></i>
                    <input type="text" id="globalSearch" placeholder="Buscar estudiante, documento...">
                </div>
                <button class="toggle-btn" id="toggleRight" title="Mostrar/Ocultar panel derecho">
                    <i class="ri-layout-right-2-line"></i>
                </button>
            </div>

            <!-- STATS CARDS -->
            <div class="stats-grid">
                <div class="stat-card blue">
                    <div class="stat-icon">
                        <i class="ri-user-add-line"></i>
                    </div>
                    <div class="stat-content">
                        <h3>12</h3>
                        <p>Pendientes</p>
                        <small>Por revisar</small>
                    </div>
                </div>

                <div class="stat-card green">
                    <div class="stat-icon">
                        <i class="ri-checkbox-circle-line"></i>
                    </div>
                    <div class="stat-content">
                        <h3>38</h3>
                        <p>Aprobadas</p>
                        <small>Este mes</small>
                    </div>
                </div>

                <div class="stat-card orange">
                    <div class="stat-icon">
                        <i class="ri-time-line"></i>
                    </div>
                    <div class="stat-content">
                        <h3>5</h3>
                        <p>En Revisión</p>
                        <small>Documentos</small>
                    </div>
                </div>

                <div class="stat-card red">
                    <div class="stat-icon">
                        <i class="ri-close-circle-line"></i>
                    </div>
                    <div class="stat-content">
                        <h3>2</h3>
                        <p>Rechazadas</p>
                        <small>Último mes</small>
                    </div>
                </div>
            </div>

            <!-- FILTROS Y ACCIONES -->
            <div class="toolbar">
                <div class="filter-tabs">
                    <button class="tab-btn active" data-filter="todas">Todas (57)</button>
                    <button class="tab-btn" data-filter="pendientes">Pendientes (12)</button>
                    <button class="tab-btn" data-filter="revision">En Revisión (5)</button>
                    <button class="tab-btn" data-filter="aprobadas">Aprobadas (38)</button>
                    <button class="tab-btn" data-filter="rechazadas">Rechazadas (2)</button>
                </div>
                <div class="toolbar-actions">
                    <button class="btn-action" onclick="openMatriculaModal()">
                        <i class="ri-add-line"></i>
                        Nueva Matrícula
                    </button>
                    <button class="btn-action secondary">
                        <i class="ri-download-line"></i>
                        Exportar
                    </button>
                </div>
            </div>

            <!-- DATATABLE -->
            <section class="table-card">
                <div class="table-responsive">
                    <table id="matriculasTable" class="table table-dark table-hover align-middle" style="width:100%">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Estudiante</th>
                                <th>Documento</th>
                                <th>Grado</th>
                                <th>Acudiente</th>
                                <th>Teléfono</th>
                                <th>Fecha Solicitud</th>
                                <th class="text-center">Estado</th>
                                <th class="text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- PENDIENTES -->
                            <tr data-status="pendiente">
                                <td><strong>#2024-045</strong></td>
                                <td>
                                    <div class="student-cell">
                                        <div class="avatar">JM</div>
                                        <div>
                                            <strong>Juan Martínez Pérez</strong>
                                            <small>Nuevo ingreso</small>
                                        </div>
                                    </div>
                                </td>
                                <td>1234567890</td>
                                <td><span class="badge-grade">7° A</span></td>
                                <td>María Pérez</td>
                                <td>+57 300 123 4567</td>
                                <td>15 Nov 2024</td>
                                <td class="text-center">
                                    <span class="status-badge pending">
                                        <i class="ri-time-line"></i> Pendiente
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="action-btns">
                                        <button class="btn-icon approve" title="Aprobar">
                                            <i class="ri-check-line"></i>
                                        </button>
                                        <button class="btn-icon view" title="Ver" onclick="openDetalleModal(1)">
                                            <i class="ri-eye-line"></i>
                                        </button>
                                        <button class="btn-icon edit" title="Editar">
                                            <i class="ri-edit-line"></i>
                                        </button>
                                        <button class="btn-icon reject" title="Rechazar">
                                            <i class="ri-close-line"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>

                            <tr data-status="pendiente">
                                <td><strong>#2024-046</strong></td>
                                <td>
                                    <div class="student-cell">
                                        <div class="avatar" style="background: #10b981">AG</div>
                                        <div>
                                            <strong>Ana García López</strong>
                                            <small>Nuevo ingreso</small>
                                        </div>
                                    </div>
                                </td>
                                <td>9876543210</td>
                                <td><span class="badge-grade">8° B</span></td>
                                <td>Carlos López</td>
                                <td>+57 300 234 5678</td>
                                <td>16 Nov 2024</td>
                                <td class="text-center">
                                    <span class="status-badge pending">
                                        <i class="ri-time-line"></i> Pendiente
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="action-btns">
                                        <button class="btn-icon approve" title="Aprobar">
                                            <i class="ri-check-line"></i>
                                        </button>
                                        <button class="btn-icon view" title="Ver" onclick="openDetalleModal(2)">
                                            <i class="ri-eye-line"></i>
                                        </button>
                                        <button class="btn-icon edit" title="Editar">
                                            <i class="ri-edit-line"></i>
                                        </button>
                                        <button class="btn-icon reject" title="Rechazar">
                                            <i class="ri-close-line"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>

                            <!-- EN REVISIÓN -->
                            <tr data-status="revision">
                                <td><strong>#2024-043</strong></td>
                                <td>
                                    <div class="student-cell">
                                        <div class="avatar" style="background: #f59e0b">LR</div>
                                        <div>
                                            <strong>Luis Rodríguez Gómez</strong>
                                            <small>Transferencia</small>
                                        </div>
                                    </div>
                                </td>
                                <td>5678901234</td>
                                <td><span class="badge-grade">9° A</span></td>
                                <td>Sandra Gómez</td>
                                <td>+57 300 345 6789</td>
                                <td>13 Nov 2024</td>
                                <td class="text-center">
                                    <span class="status-badge review">
                                        <i class="ri-file-search-line"></i> En Revisión
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="action-btns">
                                        <button class="btn-icon approve" title="Aprobar">
                                            <i class="ri-check-line"></i>
                                        </button>
                                        <button class="btn-icon view" title="Ver" onclick="openDetalleModal(3)">
                                            <i class="ri-eye-line"></i>
                                        </button>
                                        <button class="btn-icon edit" title="Editar">
                                            <i class="ri-edit-line"></i>
                                        </button>
                                        <button class="btn-icon reject" title="Rechazar">
                                            <i class="ri-close-line"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>

                            <!-- APROBADAS -->
                            <tr data-status="aprobada">
                                <td><strong>#2024-038</strong></td>
                                <td>
                                    <div class="student-cell">
                                        <div class="avatar" style="background: #8b5cf6">MT</div>
                                        <div>
                                            <strong>María Torres Silva</strong>
                                            <small>Renovación</small>
                                        </div>
                                    </div>
                                </td>
                                <td>3456789012</td>
                                <td><span class="badge-grade">10° B</span></td>
                                <td>Pedro Silva</td>
                                <td>+57 300 456 7890</td>
                                <td>10 Nov 2024</td>
                                <td class="text-center">
                                    <span class="status-badge approved">
                                        <i class="ri-checkbox-circle-line"></i> Aprobada
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="action-btns">
                                        <button class="btn-icon view" title="Ver" onclick="openDetalleModal(4)">
                                            <i class="ri-eye-line"></i>
                                        </button>
                                        <button class="btn-icon print" title="Imprimir">
                                            <i class="ri-printer-line"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>

                            <tr data-status="aprobada">
                                <td><strong>#2024-039</strong></td>
                                <td>
                                    <div class="student-cell">
                                        <div class="avatar" style="background: #ef4444">CR</div>
                                        <div>
                                            <strong>Carlos Ramírez Díaz</strong>
                                            <small>Renovación</small>
                                        </div>
                                    </div>
                                </td>
                                <td>7890123456</td>
                                <td><span class="badge-grade">11° A</span></td>
                                <td>Laura Díaz</td>
                                <td>+57 300 567 8901</td>
                                <td>11 Nov 2024</td>
                                <td class="text-center">
                                    <span class="status-badge approved">
                                        <i class="ri-checkbox-circle-line"></i> Aprobada
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="action-btns">
                                        <button class="btn-icon view" title="Ver" onclick="openDetalleModal(5)">
                                            <i class="ri-eye-line"></i>
                                        </button>
                                        <button class="btn-icon print" title="Imprimir">
                                            <i class="ri-printer-line"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>

                            <!-- RECHAZADA -->
                            <tr data-status="rechazada">
                                <td><strong>#2024-030</strong></td>
                                <td>
                                    <div class="student-cell">
                                        <div class="avatar" style="background: #6b7280">PS</div>
                                        <div>
                                            <strong>Pedro Sánchez Ruiz</strong>
                                            <small>Nuevo ingreso</small>
                                        </div>
                                    </div>
                                </td>
                                <td>2345678901</td>
                                <td><span class="badge-grade">6° A</span></td>
                                <td>Carmen Ruiz</td>
                                <td>+57 300 678 9012</td>
                                <td>05 Nov 2024</td>
                                <td class="text-center">
                                    <span class="status-badge rejected">
                                        <i class="ri-close-circle-line"></i> Rechazada
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="action-btns">
                                        <button class="btn-icon view" title="Ver" onclick="openDetalleModal(6)">
                                            <i class="ri-eye-line"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
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

            <div class="panel-title">Grados Disponibles</div>
            <p class="muted">Cupos disponibles por grado</p>

            <div class="cupos-list">
                <div class="cupo-item">
                    <div class="cupo-grade">6° A</div>
                    <div class="cupo-info">
                        <strong>35/40</strong>
                        <small>5 cupos</small>
                    </div>
                    <div class="cupo-bar">
                        <div class="cupo-progress" style="width: 87.5%"></div>
                    </div>
                </div>

                <div class="cupo-item">
                    <div class="cupo-grade">7° A</div>
                    <div class="cupo-info">
                        <strong>38/40</strong>
                        <small>2 cupos</small>
                    </div>
                    <div class="cupo-bar">
                        <div class="cupo-progress warning" style="width: 95%"></div>
                    </div>
                </div>

                <div class="cupo-item">
                    <div class="cupo-grade">8° B</div>
                    <div class="cupo-info">
                        <strong>32/40</strong>
                        <small>8 cupos</small>
                    </div>
                    <div class="cupo-bar">
                        <div class="cupo-progress" style="width: 80%"></div>
                    </div>
                </div>

                <div class="cupo-item">
                    <div class="cupo-grade">10° A</div>
                    <div class="cupo-info">
                        <strong>40/40</strong>
                        <small>Completo</small>
                    </div>
                    <div class="cupo-bar">
                        <div class="cupo-progress full" style="width: 100%"></div>
                    </div>
                </div>
            </div>

            <div class="panel-title" style="margin-top:24px">Documentos Requeridos</div>
            <p class="muted">Checklist de matrícula</p>

            <div class="checklist">
                <div class="checklist-item">
                    <i class="ri-checkbox-circle-line"></i>
                    <span>Registro civil</span>
                </div>
                <div class="checklist-item">
                    <i class="ri-checkbox-circle-line"></i>
                    <span>Documento de identidad</span>
                </div>
                <div class="checklist-item">
                    <i class="ri-checkbox-circle-line"></i>
                    <span>Certificado de estudios</span>
                </div>
                <div class="checklist-item">
                    <i class="ri-checkbox-circle-line"></i>
                    <span>Carnet de vacunas</span>
                </div>
                <div class="checklist-item">
                    <i class="ri-checkbox-circle-line"></i>
                    <span>Foto 3x4</span>
                </div>
                <div class="checklist-item">
                    <i class="ri-checkbox-circle-line"></i>
                    <span>Paz y salvo anterior</span>
                </div>
            </div>

            <button class="btn-primary">Descargar Checklist</button>

            <div class="info-card">
                <h4><i class="ri-information-line"></i> Información</h4>
                <p>Las matrículas están abiertas hasta el 30 de diciembre. Recuerda validar todos los documentos antes de aprobar.</p>
            </div>
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

    <!-- MODAL DETALLE -->
    <div class="modal-overlay" id="detalleModal">
        <div class="modal-dialog large">
            <div class="modal-header">
                <h3><i class="ri-file-info-line"></i> Detalle de Matrícula</h3>
                <button class="modal-close" onclick="closeDetalleModal()">
                    <i class="ri-close-line"></i>
                </button>
            </div>
            <div class="modal-body detalle" id="detalleContent">
                <!-- Contenido dinámico -->
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="<?= BASE_URL ?>/public/assets/dashboard/js/secretaría/matriculas.js"></script>

</body>

</html>