<!doctype html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SIADEMY • <?= htmlspecialchars($materia_info['materia']) ?> - Actividades</title>
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
        <?php include_once __DIR__ . '/../../layouts/sidebar_estudiante.php' ?>

        <!-- MAIN CONTENT -->
        <main class="main">
            <div class="topbar">
                <div class="topbar-left">
                    <button class="toggle-btn" id="toggleLeft" title="Mostrar/Ocultar menú lateral">
                        <i class="ri-menu-2-line"></i>
                    </button>
                    <div class="breadcrumb">
                        <a href="<?= BASE_URL ?>/estudiante-panel-materias" class="breadcrumb-link">
                            <i class="ri-arrow-left-line"></i> Materias
                        </a>
                        <span class="breadcrumb-separator">/</span>
                        <span class="breadcrumb-current"><?= htmlspecialchars($materia_info['materia']) ?></span>
                    </div>
                </div>

                <div class="search">
                    <i class="ri-search-2-line"></i>
                    <input type="text" id="searchInput" placeholder="Buscar actividades...">
                </div>

                <button class="toggle-btn" id="toggleRight" title="Mostrar/Ocultar panel derecho">
                    <i class="ri-layout-right-2-line"></i>
                </button>
            </div>

            <!-- HEADER INFO MATERIA -->
            <div class="materia-header-info">
                <div class="materia-info-content">
                    <h1 class="materia-titulo"><?= htmlspecialchars($materia_info['materia']) ?></h1>
                    <p class="materia-descripcion"><?= htmlspecialchars($materia_info['descripcion'] ?: 'Sin descripción') ?></p>
                    <div class="materia-meta">
                        <span class="meta-badge">
                            <i class="ri-user-line"></i>
                            Prof. <?= htmlspecialchars($materia_info['docente_nombres'] . ' ' . $materia_info['docente_apellidos']) ?>
                        </span>
                        <span class="meta-badge">
                            <i class="ri-school-line"></i>
                            <?= htmlspecialchars($materia_info['grado'] . ' ' . $materia_info['curso']) ?>
                        </span>
                        <?php if ($materia_info['promedio']): ?>
                        <span class="meta-badge success">
                            <i class="ri-medal-line"></i>
                            Promedio: <?= number_format($materia_info['promedio'], 1) ?>
                        </span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- STATS CARDS -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon blue">
                        <i class="ri-file-list-3-line"></i>
                    </div>
                    <div class="stat-content">
                        <h3><?= $total_actividades ?></h3>
                        <p>Total Actividades</p>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon orange">
                        <i class="ri-time-line"></i>
                    </div>
                    <div class="stat-content">
                        <h3><?= $pendientes ?></h3>
                        <p>Pendientes</p>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon green">
                        <i class="ri-checkbox-circle-line"></i>
                    </div>
                    <div class="stat-content">
                        <h3><?= $completadas ?></h3>
                        <p>Completadas</p>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon red">
                        <i class="ri-error-warning-line"></i>
                    </div>
                    <div class="stat-content">
                        <h3><?= $atrasadas ?></h3>
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
                        <i class="ri-time-line"></i> Pendientes (<?= $pendientes ?>)
                    </button>
                    <button class="filter-btn" data-filter="completadas">
                        <i class="ri-checkbox-circle-line"></i> Completadas (<?= $completadas ?>)
                    </button>
                    <button class="filter-btn" data-filter="atrasadas">
                        <i class="ri-error-warning-line"></i> Atrasadas (<?= $atrasadas ?>)
                    </button>
                </div>
                <div class="sort-group">
                    <select class="sort-select" id="sortSelect">
                        <option value="fecha">Ordenar por Fecha</option>
                        <option value="tipo">Ordenar por Tipo</option>
                        <option value="ponderacion">Ordenar por Ponderación</option>
                    </select>
                </div>
            </div>

            <!-- ACTIVIDADES LIST -->
            <div class="actividades-container" id="actividadesContainer">

                <?php if (empty($actividades)): ?>
                    <div style="grid-column: 1/-1; text-align: center; padding: 60px 20px; color: #97a1b6;">
                        <i class="ri-file-list-3-line" style="font-size: 64px; opacity: 0.5;"></i>
                        <h3 style="margin-top: 24px; color: #fff;">No hay actividades registradas</h3>
                        <p style="margin-top: 12px; font-size: 16px;">El profesor aún no ha creado actividades para esta materia</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($actividades as $actividad): 
                        $estado_clase = strtolower($actividad['estado_entrega']);
                        $es_urgente = $actividad['dias_restantes'] <= 1 && $estado_clase === 'pendiente';
                        
                        // Iconos por tipo de actividad
                        $iconos_tipo = [
                            'Tarea' => 'ri-file-text-line',
                            'Examen' => 'ri-file-list-3-line',
                            'Proyecto' => 'ri-code-box-line',
                            'Quiz' => 'ri-questionnaire-line',
                            'Taller' => 'ri-tools-line',
                            'Exposición' => 'ri-presentation-line',
                            'Laboratorio' => 'ri-test-tube-line'
                        ];
                        $icono = $iconos_tipo[$actividad['tipo']] ?? 'ri-file-line';
                        
                        // Colores por tipo
                        $colores_tipo = [
                            'Tarea' => '#3b82f6',
                            'Examen' => '#ef4444',
                            'Proyecto' => '#8b5cf6',
                            'Quiz' => '#f59e0b',
                            'Taller' => '#10b981',
                            'Exposición' => '#ec4899',
                            'Laboratorio' => '#06b6d4'
                        ];
                        $color = $colores_tipo[$actividad['tipo']] ?? '#6b7280';
                    ?>
                    
                    <div class="actividad-card <?= $estado_clase ?>" 
                         data-status="<?= $estado_clase ?>" 
                         data-tipo="<?= strtolower($actividad['tipo']) ?>" 
                         data-fecha="<?= $actividad['fecha_entrega'] ?>">
                        
                        <div class="actividad-priority <?= $es_urgente ? 'urgent' : ($estado_clase === 'atrasada' ? 'urgent' : ($estado_clase === 'pendiente' ? 'high' : 'low')) ?>"></div>
                        
                        <div class="actividad-header">
                            <div class="actividad-icon" style="background: linear-gradient(135deg, <?= $color ?> 0%, <?= $color ?>dd 100%);">
                                <i class="<?= $icono ?>"></i>
                            </div>
                            <div class="actividad-info">
                                <?php if ($estado_clase === 'atrasada'): ?>
                                    <div class="actividad-badge atrasada">
                                        <i class="ri-error-warning-line"></i> Atrasada
                                    </div>
                                <?php elseif ($estado_clase === 'calificada'): ?>
                                    <div class="actividad-badge completada">
                                        <i class="ri-checkbox-circle-line"></i> Calificada
                                    </div>
                                <?php elseif ($es_urgente): ?>
                                    <div class="actividad-badge urgente">
                                        <i class="ri-alarm-warning-line"></i> Vence pronto
                                    </div>
                                <?php else: ?>
                                    <div class="actividad-badge pendiente">
                                        <i class="ri-time-line"></i> Pendiente
                                    </div>
                                <?php endif; ?>
                                
                                <h3 class="actividad-title"><?= htmlspecialchars($actividad['titulo']) ?></h3>
                                <p class="actividad-materia"><?= htmlspecialchars($actividad['tipo']) ?> • Prof. <?= htmlspecialchars($actividad['nombre_docente']) ?></p>
                            </div>
                            <div class="actividad-status">
                                <?php if ($actividad['nota']): ?>
                                    <div class="progress-circle complete">
                                        <span><?= number_format($actividad['nota'], 1) ?></span>
                                    </div>
                                <?php elseif ($estado_clase === 'atrasada'): ?>
                                    <div class="progress-circle urgent">
                                        <span>0%</span>
                                    </div>
                                <?php else: ?>
                                    <div class="progress-circle pending">
                                        <span>--</span>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <p class="actividad-description">
                            <?= htmlspecialchars($actividad['descripcion']) ?>
                        </p>

                        <div class="actividad-meta">
                            <div class="meta-item">
                                <i class="ri-calendar-line"></i>
                                <span>Vencimiento: <strong><?= date('d M, Y', strtotime($actividad['fecha_entrega'])) ?></strong></span>
                            </div>
                            
                            <?php if ($estado_clase === 'calificada'): ?>
                                <div class="meta-item success">
                                    <i class="ri-thumb-up-line"></i>
                                    <span>Calificación: <strong><?= number_format($actividad['nota'], 1) ?>/5.0</strong></span>
                                </div>
                            <?php elseif ($estado_clase === 'atrasada'): ?>
                                <div class="meta-item urgent">
                                    <i class="ri-time-line"></i>
                                    <span>Venció hace <?= abs($actividad['dias_restantes']) ?> día(s)</span>
                                </div>
                            <?php elseif ($estado_clase === 'pendiente'): ?>
                                <div class="meta-item <?= $es_urgente ? 'warning' : '' ?>">
                                    <i class="ri-time-line"></i>
                                    <span><?= $actividad['dias_restantes'] == 0 ? 'Vence hoy' : ($actividad['dias_restantes'] == 1 ? 'Vence mañana' : 'Vence en ' . $actividad['dias_restantes'] . ' días') ?></span>
                                </div>
                            <?php endif; ?>
                            
                            <div class="meta-item">
                                <i class="ri-percent-line"></i>
                                <span>Ponderación: <?= $actividad['ponderacion'] ?>%</span>
                            </div>
                        </div>

                        <div class="actividad-actions">
                            <?php if ($estado_clase === 'calificada'): ?>
                                <button class="btn-actividad secondary">
                                    <i class="ri-eye-line"></i> Ver Retroalimentación
                                </button>
                                <?php if ($actividad['observacion']): ?>
                                <button class="btn-actividad secondary" onclick="alert('<?= htmlspecialchars($actividad['observacion']) ?>')">
                                    <i class="ri-message-3-line"></i> Ver Comentario
                                </button>
                                <?php endif; ?>
                            <?php else: ?>
                                <button class="btn-actividad primary" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#modalEntregarTarea"
                                        data-id-actividad="<?= $actividad['id_actividad'] ?>"
                                        data-titulo="<?= htmlspecialchars($actividad['titulo']) ?>"
                                        data-tipo="<?= htmlspecialchars($actividad['tipo']) ?>">
                                    <i class="ri-upload-line"></i> Entregar Tarea
                                </button>
                                <button class="btn-actividad secondary">
                                    <i class="ri-eye-line"></i> Ver Detalles
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <?php endforeach; ?>
                <?php endif; ?>

            </div>
        </main>

        <!-- RIGHT SIDEBAR -->
        <aside class="rightbar" id="rightSidebar">
            <div class="user">
                <button class="btn" title="Notificaciones"><i class="ri-notification-3-line"></i></button>
                <button class="btn" title="Configuración"><i class="ri-settings-3-line"></i></button>
                <div class="avatar" title="Estudiante">E</div>
            </div>

            <div class="panel-title">Información del Docente</div>
            <p class="muted">Contacto</p>

            <div class="profesor-info-card">
                <div class="profesor-avatar-large">
                    <?= strtoupper(substr($materia_info['docente_nombres'], 0, 1) . substr($materia_info['docente_apellidos'], 0, 1)) ?>
                </div>
                <h4 style="margin-top: 12px; color: #fff;">
                    Prof. <?= htmlspecialchars($materia_info['docente_nombres'] . ' ' . $materia_info['docente_apellidos']) ?>
                </h4>
                <p style="color: #97a1b6; font-size: 14px;"><?= htmlspecialchars($materia_info['docente_correo']) ?></p>
            </div>

            <div class="panel-title" style="margin-top:24px">Progreso en la Materia</div>
            <p class="muted">Tu rendimiento</p>

            <?php if ($total_actividades > 0): ?>
            <div class="progress-card">
                <div class="progress-header">
                    <span>Actividades completadas</span>
                    <strong><?= $completadas ?>/<?= $total_actividades ?></strong>
                </div>
                <div class="progress-bar-container">
                    <div class="progress-bar" style="width: <?= round(($completadas / $total_actividades) * 100) ?>%"></div>
                </div>
                <small class="progress-label"><?= round(($completadas / $total_actividades) * 100) ?>% completado</small>
            </div>
            <?php endif; ?>

            <?php if ($materia_info['promedio']): ?>
            <div class="stat-card-right success">
                <div class="stat-icon-right">
                    <i class="ri-medal-line"></i>
                </div>
                <div class="stat-content-right">
                    <h3><?= number_format($materia_info['promedio'], 1) ?></h3>
                    <p>Promedio Actual</p>
                </div>
            </div>
            <?php endif; ?>

            <div class="panel-title" style="margin-top:24px">Recordatorios</div>
            <p class="muted">Próximas entregas</p>

            <div class="reminder-list">
                <?php 
                $proximas = array_filter($actividades, function($act) {
                    return $act['estado_entrega'] === 'Pendiente' && $act['dias_restantes'] <= 7;
                });
                $proximas = array_slice($proximas, 0, 3);
                ?>
                
                <?php if (empty($proximas)): ?>
                    <div style="text-align: center; padding: 20px; color: #97a1b6;">
                        <i class="ri-checkbox-circle-line" style="font-size: 32px; opacity: 0.5;"></i>
                        <p style="margin-top: 12px; font-size: 14px;">¡Estás al día!</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($proximas as $proxima): ?>
                    <div class="reminder-item <?= $proxima['dias_restantes'] <= 1 ? 'urgent' : ($proxima['dias_restantes'] <= 3 ? 'warning' : 'info') ?>">
                        <div class="reminder-icon <?= $proxima['dias_restantes'] <= 1 ? 'urgent' : ($proxima['dias_restantes'] <= 3 ? 'warning' : 'info') ?>">
                            <i class="<?= $proxima['dias_restantes'] <= 1 ? 'ri-alarm-warning-line' : 'ri-time-line' ?>"></i>
                        </div>
                        <div class="reminder-content">
                            <strong><?= htmlspecialchars($proxima['titulo']) ?></strong>
                            <small><?= $proxima['dias_restantes'] == 0 ? 'Vence hoy' : ($proxima['dias_restantes'] == 1 ? 'Vence mañana' : 'En ' . $proxima['dias_restantes'] . ' días') ?></small>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <button class="btn-primary" onclick="window.location.href='<?= BASE_URL ?>/estudiante-panel-materias'">
                <i class="ri-arrow-left-line"></i> Volver a Materias
            </button>

            <div class="tips-card">
                <h4><i class="ri-lightbulb-line"></i> Consejo</h4>
                <p>Organiza tu tiempo y prioriza las actividades con fechas más próximas. No dejes todo para el último momento.</p>
            </div>
        </aside>
    </div>

    <!-- MODAL ENTREGAR TAREA -->
    <div class="modal fade" id="modalEntregarTarea" tabindex="-1" aria-labelledby="modalEntregarTareaLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="background: #1a1d29; border: 1px solid #2a2d3a; border-radius: 16px;">
                <div class="modal-header" style="border-bottom: 1px solid #2a2d3a; padding: 24px;">
                    <h5 class="modal-title" id="modalEntregarTareaLabel" style="color: #fff; font-weight: 600;">
                        <i class="ri-upload-cloud-line" style="color: #3b82f6;"></i> Entregar Tarea
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="filter: invert(1);"></button>
                </div>
                <form id="formEntregarTarea" method="POST" action="<?= BASE_URL ?>/estudiante-entregar-actividad" enctype="multipart/form-data">
                    <div class="modal-body" style="padding: 24px;">
                        <input type="hidden" name="id_actividad" id="modalIdActividad">
                        <input type="hidden" name="id_asignatura_curso" value="<?= $id_asignatura_curso ?>">
                        
                        <div class="mb-3">
                            <label style="color: #97a1b6; font-size: 14px; margin-bottom: 8px; display: block;">
                                Actividad:
                            </label>
                            <div style="background: #252836; padding: 12px; border-radius: 8px;">
                                <strong id="modalTituloActividad" style="color: #fff;"></strong>
                                <br>
                                <small id="modalTipoActividad" style="color: #97a1b6;"></small>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="archivo" class="form-label" style="color: #97a1b6; font-size: 14px;">
                                <i class="ri-file-pdf-line" style="color: #ef4444;"></i> Archivo PDF (máx. 10MB) *
                            </label>
                            <div class="upload-area" id="uploadArea" style="border: 2px dashed #3b82f6; border-radius: 12px; padding: 32px; text-align: center; background: #252836; cursor: pointer; transition: all 0.3s;">
                                <i class="ri-upload-cloud-2-line" style="font-size: 48px; color: #3b82f6; display: block; margin-bottom: 12px;"></i>
                                <p style="color: #fff; margin-bottom: 4px; font-weight: 500;">Haz clic para seleccionar o arrastra tu archivo aquí</p>
                                <small style="color: #97a1b6;">Solo archivos PDF, máximo 10MB</small>
                                <input type="file" name="archivo" id="archivo" accept=".pdf" required style="display: none;">
                            </div>
                            <div id="fileInfo" style="display: none; margin-top: 12px; padding: 12px; background: #252836; border-radius: 8px; border-left: 3px solid #10b981;">
                                <div style="display: flex; align-items: center; gap: 12px;">
                                    <i class="ri-file-pdf-line" style="font-size: 32px; color: #ef4444;"></i>
                                    <div style="flex: 1;">
                                        <strong id="fileName" style="color: #fff; display: block;"></strong>
                                        <small id="fileSize" style="color: #97a1b6;"></small>
                                    </div>
                                    <button type="button" id="removeFile" style="background: none; border: none; color: #ef4444; cursor: pointer; font-size: 20px;">
                                        <i class="ri-close-circle-line"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="observaciones" class="form-label" style="color: #97a1b6; font-size: 14px;">
                                <i class="ri-message-3-line"></i> Comentarios (opcional)
                            </label>
                            <textarea name="observaciones" id="observaciones" rows="3" class="form-control" 
                                      placeholder="Agrega comentarios sobre tu entrega..." 
                                      style="background: #252836; border: 1px solid #2a2d3a; color: #fff; border-radius: 8px; resize: none;"></textarea>
                        </div>

                        <div style="background: #252836; padding: 12px; border-radius: 8px; border-left: 3px solid #f59e0b;">
                            <small style="color: #97a1b6;">
                                <i class="ri-information-line" style="color: #f59e0b;"></i>
                                <strong style="color: #fff;">Importante:</strong> Una vez entregada, podrás reenviar la tarea si lo necesitas antes de que el profesor la califique.
                            </small>
                        </div>
                    </div>
                    <div class="modal-footer" style="border-top: 1px solid #2a2d3a; padding: 16px 24px;">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" 
                                style="background: #2a2d3a; border: none; padding: 10px 20px; border-radius: 8px; color: #97a1b6;">
                            Cancelar
                        </button>
                        <button type="submit" class="btn btn-primary" id="btnEntregar"
                                style="background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); border: none; padding: 10px 24px; border-radius: 8px; font-weight: 500;">
                            <i class="ri-upload-line"></i> Entregar Tarea
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?= BASE_URL ?>/public/assets/dashboard/js/estudiante/actividades.js"></script>
    <script>
        // Manejo del modal de entrega
        const modalEntregarTarea = document.getElementById('modalEntregarTarea');
        modalEntregarTarea.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const idActividad = button.getAttribute('data-id-actividad');
            const titulo = button.getAttribute('data-titulo');
            const tipo = button.getAttribute('data-tipo');
            
            document.getElementById('modalIdActividad').value = idActividad;
            document.getElementById('modalTituloActividad').textContent = titulo;
            document.getElementById('modalTipoActividad').textContent = tipo;
        });

        // Manejo de archivo
        const uploadArea = document.getElementById('uploadArea');
        const fileInput = document.getElementById('archivo');
        const fileInfo = document.getElementById('fileInfo');
        const fileName = document.getElementById('fileName');
        const fileSize = document.getElementById('fileSize');
        const removeFile = document.getElementById('removeFile');
        const btnEntregar = document.getElementById('btnEntregar');

        uploadArea.addEventListener('click', () => fileInput.click());

        uploadArea.addEventListener('dragover', (e) => {
            e.preventDefault();
            uploadArea.style.borderColor = '#10b981';
            uploadArea.style.background = '#1f2937';
        });

        uploadArea.addEventListener('dragleave', () => {
            uploadArea.style.borderColor = '#3b82f6';
            uploadArea.style.background = '#252836';
        });

        uploadArea.addEventListener('drop', (e) => {
            e.preventDefault();
            uploadArea.style.borderColor = '#3b82f6';
            uploadArea.style.background = '#252836';
            
            const files = e.dataTransfer.files;
            if (files.length > 0) {
                fileInput.files = files;
                mostrarInfoArchivo(files[0]);
            }
        });

        fileInput.addEventListener('change', function() {
            if (this.files.length > 0) {
                mostrarInfoArchivo(this.files[0]);
            }
        });

        removeFile.addEventListener('click', function() {
            fileInput.value = '';
            uploadArea.style.display = 'block';
            fileInfo.style.display = 'none';
            btnEntregar.disabled = true;
        });

        function mostrarInfoArchivo(file) {
            // Validar tipo
            if (file.type !== 'application/pdf') {
                alert('Solo se permiten archivos PDF');
                fileInput.value = '';
                return;
            }

            // Validar tamaño (10MB)
            if (file.size > 10485760) {
                alert('El archivo no debe superar los 10MB');
                fileInput.value = '';
                return;
            }

            // Mostrar información
            fileName.textContent = file.name;
            fileSize.textContent = formatBytes(file.size);
            uploadArea.style.display = 'none';
            fileInfo.style.display = 'block';
            btnEntregar.disabled = false;
        }

        function formatBytes(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
        }

        // Deshabilitar botón si no hay archivo
        btnEntregar.disabled = true;

        // Resetear modal al cerrar
        modalEntregarTarea.addEventListener('hidden.bs.modal', function () {
            fileInput.value = '';
            uploadArea.style.display = 'block';
            fileInfo.style.display = 'none';
            btnEntregar.disabled = true;
            document.getElementById('observaciones').value = '';
        });
    </script>
</body>

</html>
