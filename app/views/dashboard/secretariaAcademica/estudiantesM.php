<!doctype html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SIADEMY • Gestión de Estudiantes</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/remixicon@4.3.0/fonts/remixicon.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/dashboard/css/styles-estudiantesM.css">
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
                    <div class="title">Gestión de Estudiantes</div>
                </div>
                <div class="search">
                    <i class="ri-search-2-line"></i>
                    <input type="text" id="globalSearch" placeholder="Buscar por nombre, documento, grado...">
                </div>
                <button class="toggle-btn" id="toggleRight" title="Mostrar/Ocultar panel derecho">
                    <i class="ri-layout-right-2-line"></i>
                </button>
            </div>

            <!-- STATS CARDS -->
            <div class="stats-grid">
                <div class="stat-card blue">
                    <div class="stat-icon">
                        <i class="ri-user-line"></i>
                    </div>
                    <div class="stat-content">
                        <h3>245</h3>
                        <p>Total Estudiantes</p>
                        <small>Activos</small>
                    </div>
                </div>

                <div class="stat-card green">
                    <div class="stat-icon">
                        <i class="ri-user-add-line"></i>
                    </div>
                    <div class="stat-content">
                        <h3>18</h3>
                        <p>Nuevos Ingresos</p>
                        <small>Este mes</small>
                    </div>
                </div>

                <div class="stat-card orange">
                    <div class="stat-icon">
                        <i class="ri-user-follow-line"></i>
                    </div>
                    <div class="stat-content">
                        <h3>227</h3>
                        <p>Regulares</p>
                        <small>Con matrícula activa</small>
                    </div>
                </div>

                <div class="stat-card purple">
                    <div class="stat-icon">
                        <i class="ri-graduation-cap-line"></i>
                    </div>
                    <div class="stat-content">
                        <h3>42</h3>
                        <p>Grado 11°</p>
                        <small>Próximos graduados</small>
                    </div>
                </div>
            </div>

            <!-- FILTROS Y ACCIONES -->
            <div class="toolbar">
                <div class="filter-tabs">
                    <button class="tab-btn active" data-filter="todos">Todos (245)</button>
                    <button class="tab-btn" data-filter="6">6° (40)</button>
                    <button class="tab-btn" data-filter="7">7° (42)</button>
                    <button class="tab-btn" data-filter="8">8° (38)</button>
                    <button class="tab-btn" data-filter="9">9° (41)</button>
                    <button class="tab-btn" data-filter="10">10° (40)</button>
                    <button class="tab-btn" data-filter="11">11° (44)</button>
                </div>
                <div class="toolbar-actions">
                    <button class="btn-action" onclick="openEstudianteModal()">
                        <i class="ri-user-add-line"></i>
                        Nuevo Estudiante
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
                    <table id="estudiantesTable" class="table table-dark table-hover align-middle" style="width:100%">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Estudiante</th>
                                <th>Documento</th>
                                <th>Grado</th>
                                <th>Acudiente</th>
                                <th>Teléfono</th>
                                <th>Email</th>
                                <th class="text-center">Estado</th>
                                <th class="text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Estudiantes Grado 6 -->
                            <tr data-grado="6">
                                <td><strong>#EST-001</strong></td>
                                <td>
                                    <div class="student-cell">
                                        <div class="avatar">SM</div>
                                        <div>
                                            <strong>Sofía Martínez López</strong>
                                            <small>Femenino • 11 años</small>
                                        </div>
                                    </div>
                                </td>
                                <td>1098765432</td>
                                <td><span class="badge-grade">6° A</span></td>
                                <td>Ana López</td>
                                <td>+57 300 123 4567</td>
                                <td>ana.lopez@email.com</td>
                                <td class="text-center">
                                    <span class="status-badge active">
                                        <i class="ri-checkbox-circle-line"></i> Activo
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="action-btns">
                                        <button class="btn-actions view" title="Ver perfil"><a href="">Ver</a></button>
                                        <button class="btn-actions  edit" title="Editar"><a href="">Editar</a></button>
                                        <button class="btn-actions delete" title="Eliminar"><a href=""><i class="bi bi-trash3-fill"></i></a></button>
                                    </div>
                                </td>
                            </tr>

                            <!-- Estudiantes Grado 7 -->
                            <tr data-grado="7">
                                <td><strong>#EST-015</strong></td>
                                <td>
                                    <div class="student-cell">
                                        <div class="avatar" style="background: #10b981">JC</div>
                                        <div>
                                            <strong>Juan Carlos Ramírez</strong>
                                            <small>Masculino • 12 años</small>
                                        </div>
                                    </div>
                                </td>
                                <td>1087654321</td>
                                <td><span class="badge-grade">7° B</span></td>
                                <td>María Ramírez</td>
                                <td>+57 301 234 5678</td>
                                <td>maria.ramirez@email.com</td>
                                <td class="text-center">
                                    <span class="status-badge active">
                                        <i class="ri-checkbox-circle-line"></i> Activo
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="action-btns">
                                        <button class="btn-actions view" title="Ver perfil"><a href="">Ver</a></button>
                                        <button class="btn-actions edit" title="Editar"><a href="">Editar</a></button>
                                        <button class="btn-actions delete" title="Eliminar"><a href=""><i class="bi bi-trash3-fill"></i></a></button>
                                    </div>
                                </td>
                            </tr>

                            <!-- Estudiantes Grado 8 -->
                            <tr data-grado="8">
                                <td><strong>#EST-028</strong></td>
                                <td>
                                    <div class="student-cell">
                                        <div class="avatar" style="background: #f59e0b">VG</div>
                                        <div>
                                            <strong>Valentina García Torres</strong>
                                            <small>Femenino • 13 años</small>
                                        </div>
                                    </div>
                                </td>
                                <td>1076543210</td>
                                <td><span class="badge-grade">8° A</span></td>
                                <td>Pedro Torres</td>
                                <td>+57 302 345 6789</td>
                                <td>pedro.torres@email.com</td>
                                <td class="text-center">
                                    <span class="status-badge active">
                                        <i class="ri-checkbox-circle-line"></i> Activo
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="action-btns">
                                        <button class="btn-actions view" title="Ver perfil"><a href="">Ver</a></button>
                                        <button class="btn-actions  edit" title="Editar"><a href="">Editar</a></button>
                                        <button class="btn-actions delete" title="Eliminar"><a href=""><i class="bi bi-trash3-fill"></i></a></button>
                                    </div>
                                </td>
                            </tr>

                            <!-- Estudiantes Grado 9 -->
                            <tr data-grado="9">
                                <td><strong>#EST-042</strong></td>
                                <td>
                                    <div class="student-cell">
                                        <div class="avatar" style="background: #8b5cf6">DR</div>
                                        <div>
                                            <strong>Daniel Rodríguez Pérez</strong>
                                            <small>Masculino • 14 años</small>
                                        </div>
                                    </div>
                                </td>
                                <td>1065432109</td>
                                <td><span class="badge-grade">9° B</span></td>
                                <td>Laura Pérez</td>
                                <td>+57 303 456 7890</td>
                                <td>laura.perez@email.com</td>
                                <td class="text-center">
                                    <span class="status-badge active">
                                        <i class="ri-checkbox-circle-line"></i> Activo
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="action-btns">
                                        <button class="btn-actions view" title="Ver perfil"><a href="">Ver</a></button>
                                        <button class="btn-actions  edit" title="Editar"><a href="">Editar</a></button>
                                        <button class="btn-actions delete" title="Eliminar"><a href=""><i class="bi bi-trash3-fill"></i></a></button>
                                    </div>
                                </td>
                            </tr>

                            <!-- Estudiantes Grado 10 -->
                            <tr data-grado="10">
                                <td><strong>#EST-055</strong></td>
                                <td>
                                    <div class="student-cell">
                                        <div class="avatar" style="background: #ef4444">CM</div>
                                        <div>
                                            <strong>Camila Morales Silva</strong>
                                            <small>Femenino • 15 años</small>
                                        </div>
                                    </div>
                                </td>
                                <td>1054321098</td>
                                <td><span class="badge-grade">10° A</span></td>
                                <td>Roberto Silva</td>
                                <td>+57 304 567 8901</td>
                                <td>roberto.silva@email.com</td>
                                <td class="text-center">
                                    <span class="status-badge active">
                                        <i class="ri-checkbox-circle-line"></i> Activo
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="action-btns">
                                        <button class="btn-actions view" title="Ver perfil"><a href="">Ver</a></button>
                                        <button class="btn-actions  edit" title="Editar"><a href="">Editar</a></button>
                                        <button class="btn-actions delete" title="Eliminar"><a href=""><i class="bi bi-trash3-fill"></i></a></button>
                                    </div>
                                </td>
                            </tr>

                            <!-- Estudiantes Grado 11 -->
                            <tr data-grado="11">
                                <td><strong>#EST-070</strong></td>
                                <td>
                                    <div class="student-cell">
                                        <div class="avatar" style="background: #06b6d4">AS</div>
                                        <div>
                                            <strong>Andrés Sánchez Gómez</strong>
                                            <small>Masculino • 16 años</small>
                                        </div>
                                    </div>
                                </td>
                                <td>1043210987</td>
                                <td><span class="badge-grade">11° A</span></td>
                                <td>Carmen Gómez</td>
                                <td>+57 305 678 9012</td>
                                <td>carmen.gomez@email.com</td>
                                <td class="text-center">
                                    <span class="status-badge active">
                                        <i class="ri-checkbox-circle-line"></i> Activo
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="action-btns">
                                        <button class="btn-actions view" title="Ver perfil"><a href="">Ver</a></button>
                                        <button class="btn-actions  edit" title="Editar"><a href="">Editar</a></button>
                                        <button class="btn-actions delete" title="Eliminar"><a href=""><i class="bi bi-trash3-fill"></i></a></button>
                                    </div>
                                </td>
                            </tr>

                            <!-- Estudiante Inactivo -->
                            <tr data-grado="9">
                                <td><strong>#EST-038</strong></td>
                                <td>
                                    <div class="student-cell">
                                        <div class="avatar" style="background: #6b7280">MP</div>
                                        <div>
                                            <strong>Miguel Pérez Díaz</strong>
                                            <small>Masculino • 14 años</small>
                                        </div>
                                    </div>
                                </td>
                                <td>1067890123</td>
                                <td><span class="badge-grade">9° A</span></td>
                                <td>Sandra Díaz</td>
                                <td>+57 306 789 0123</td>
                                <td>sandra.diaz@email.com</td>
                                <td class="text-center">
                                    <span class="status-badge inactive">
                                        <i class="ri-close-circle-line"></i> Inactivo
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="action-btns">
                                        <button class="btn-actions view" title="Ver perfil"><a href="">Ver</a></button>
                                        <button class="btn-actions delete" title="Eliminar"><a href=""><i class="bi bi-trash3-fill"></i></a></button>
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

            <div class="panel-title">Distribución por Grado</div>
            <p class="muted">Estudiantes activos</p>

            <div class="grade-distribution">
                <div class="grade-item">
                    <div class="grade-info">
                        <span class="grade-label">6°</span>
                        <span class="grade-count">40</span>
                    </div>
                    <div class="grade-bar">
                        <div class="grade-progress" style="width: 80%"></div>
                    </div>
                </div>

                <div class="grade-item">
                    <div class="grade-info">
                        <span class="grade-label">7°</span>
                        <span class="grade-count">42</span>
                    </div>
                    <div class="grade-bar">
                        <div class="grade-progress" style="width: 84%"></div>
                    </div>
                </div>

                <div class="grade-item">
                    <div class="grade-info">
                        <span class="grade-label">8°</span>
                        <span class="grade-count">38</span>
                    </div>
                    <div class="grade-bar">
                        <div class="grade-progress" style="width: 76%"></div>
                    </div>
                </div>

                <div class="grade-item">
                    <div class="grade-info">
                        <span class="grade-label">9°</span>
                        <span class="grade-count">41</span>
                    </div>
                    <div class="grade-bar">
                        <div class="grade-progress" style="width: 82%"></div>
                    </div>
                </div>

                <div class="grade-item">
                    <div class="grade-info">
                        <span class="grade-label">10°</span>
                        <span class="grade-count">40</span>
                    </div>
                    <div class="grade-bar">
                        <div class="grade-progress" style="width: 80%"></div>
                    </div>
                </div>

                <div class="grade-item">
                    <div class="grade-info">
                        <span class="grade-label">11°</span>
                        <span class="grade-count">44</span>
                    </div>
                    <div class="grade-bar">
                        <div class="grade-progress highlight" style="width: 88%"></div>
                    </div>
                </div>
            </div>

            <div class="panel-title" style="margin-top:24px">Acciones Rápidas</div>
            <p class="muted">Operaciones frecuentes</p>

            <div class="quick-actions">
                <button class="quick-btn">
                    <i class="ri-file-list-line"></i>
                    <span>Generar Listado</span>
                </button>
                <button class="quick-btn">
                    <i class="ri-mail-send-line"></i>
                    <span>Enviar Circular</span>
                </button>
                <button class="quick-btn">
                    <i class="ri-printer-line"></i>
                    <span>Imprimir Reporte</span>
                </button>
                <button class="quick-btn">
                    <i class="ri-bar-chart-line"></i>
                    <span>Ver Estadísticas</span>
                </button>
            </div>

            <div class="info-card">
                <h4><i class="ri-information-line"></i> Recordatorio</h4>
                <p>Recuerda actualizar la información de contacto de los acudientes regularmente para mantener una comunicación efectiva.</p>
            </div>
        </aside>
    </div>

    <!-- MODAL NUEVO ESTUDIANTE -->
    <div class="modal-overlay" id="estudianteModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3><i class="ri-user-add-line"></i> Nuevo Estudiante</h3>
                <button class="modal-close" onclick="closeEstudianteModal()">
                    <i class="ri-close-line"></i>
                </button>
            </div>
            <div class="modal-body">
                <form id="estudianteForm">
                    <!-- Información Personal -->
                    <div class="form-section">
                        <h4><i class="ri-user-line"></i> Información Personal</h4>
                        <div class="form-row">
                            <div class="form-group">
                                <label>Nombres <span class="required">*</span></label>
                                <input type="text" class="form-control" name="nombres" required>
                            </div>
                            <div class="form-group">
                                <label>Apellidos <span class="required">*</span></label>
                                <input type="text" class="form-control" name="apellidos" required>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label>Tipo de Documento <span class="required">*</span></label>
                                <select class="form-control" name="tipo_documento" required>
                                    <option value="">Seleccionar...</option>
                                    <option value="TI">Tarjeta de Identidad</option>
                                    <option value="RC">Registro Civil</option>
                                    <option value="CC">Cédula de Ciudadanía</option>
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
                                </select>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label>Lugar de Nacimiento <span class="required">*</span></label>
                                <input type="text" class="form-control" name="lugar_nacimiento" required>
                            </div>
                            <div class="form-group">
                                <label>Nacionalidad <span class="required">*</span></label>
                                <input type="text" class="form-control" name="nacionalidad" value="Colombiana" required>
                            </div>
                        </div>
                    </div>

                    <!-- Información de Contacto -->
                    <div class="form-section">
                        <h4><i class="ri-map-pin-line"></i> Información de Contacto</h4>
                        <div class="form-group">
                            <label>Dirección de Residencia <span class="required">*</span></label>
                            <input type="text" class="form-control" name="direccion" required>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label>Barrio <span class="required">*</span></label>
                                <input type="text" class="form-control" name="barrio" required>
                            </div>
                            <div class="form-group">
                                <label>Ciudad <span class="required">*</span></label>
                                <input type="text" class="form-control" name="ciudad" required>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label>Teléfono Fijo</label>
                                <input type="tel" class="form-control" name="telefono_fijo">
                            </div>
                            <div class="form-group">
                                <label>Email del Estudiante</label>
                                <input type="email" class="form-control" name="email_estudiante">
                            </div>
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
                                <label>Fecha de Ingreso <span class="required">*</span></label>
                                <input type="date" class="form-control" name="fecha_ingreso" required>
                            </div>
                            <div class="form-group">
                                <label>Estado <span class="required">*</span></label>
                                <select class="form-control" name="estado" required>
                                    <option value="">Seleccionar...</option>
                                    <option value="activo" selected>Activo</option>
                                    <option value="inactivo">Inactivo</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Información de Salud -->
                    <div class="form-section">
                        <h4><i class="ri-heart-pulse-line"></i> Información de Salud</h4>
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
                        <div class="form-group">
                            <label>Condiciones Médicas o Alergias</label>
                            <textarea class="form-control" name="condiciones_medicas" rows="3" placeholder="Describir condiciones médicas, alergias o restricciones..."></textarea>
                        </div>
                    </div>

                    <div class="modal-actions">
                        <button type="button" class="btn-secondary" onclick="closeEstudianteModal()">
                            Cancelar
                        </button>
                        <button type="submit" class="btn-primary">
                            <i class="ri-save-line"></i> Guardar Estudiante
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
    <script src="<?= BASE_URL ?>/public/assets/dashboard/js/secretaría/estudiantes.js"></script>
    

</body>

</html>