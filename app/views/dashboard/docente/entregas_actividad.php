<!doctype html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SIADEMY • Entregas - <?= htmlspecialchars($info_actividad['titulo']) ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/remixicon@4.3.0/fonts/remixicon.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/dashboard/css/styles-docente.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/dashboard/css/docente/entregas.css">
</head>

<body>
    <div class="app hide-right" id="appGrid">
        
        <!-- LEFT SIDEBAR -->
        <?php include_once __DIR__ . '/../../layouts/sidebar_docente.php' ?>

        <!-- MAIN -->
        <main class="main">
            
            <!-- TOPBAR -->
            <div class="topbar">
                <div class="topbar-left">
                    <button class="toggle-btn" id="toggleLeft" title="Mostrar/Ocultar menú lateral">
                        <i class="ri-menu-2-line"></i>
                    </button>
                    <div class="breadcrumb">
                        <a href="<?= BASE_URL ?>/docente-panel-actividades" class="breadcrumb-link">
                            <i class="ri-arrow-left-line"></i> Actividades
                        </a>
                        <span class="breadcrumb-separator">/</span>
                        <span class="breadcrumb-current">Entregas</span>
                    </div>
                </div>
            </div>

            <!-- HEADER INFO ACTIVIDAD -->
            <div class="actividad-entregas-header">
                <div class="actividad-header-content">
                    <div class="actividad-tipo-badge-large <?= strtolower($info_actividad['tipo']) ?>">
                        <i class="ri-file-list-3-line"></i>
                        <?= strtoupper($info_actividad['tipo']) ?>
                    </div>
                    <div>
                        <h1><?= htmlspecialchars($info_actividad['titulo']) ?></h1>
                        <p><?= htmlspecialchars($info_actividad['descripcion']) ?></p>
                        <div class="actividad-meta-info">
                            <span><i class="ri-book-line"></i> <?= $info_actividad['nombre_asignatura'] ?></span>
                            <span><i class="ri-school-line"></i> <?= $info_actividad['grado'] ?>° <?= $info_actividad['nombre_curso'] ?></span>
                            <span><i class="ri-calendar-line"></i> Cierre: <?= date('d M, Y', strtotime($info_actividad['fecha_entrega'])) ?></span>
                            <span><i class="ri-percent-line"></i> Ponderación: <?= $info_actividad['ponderacion'] ?>%</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ESTADÍSTICAS -->
            <div class="entregas-stats-grid">
                <div class="stat-card-entrega">
                    <div class="stat-icon blue">
                        <i class="ri-group-line"></i>
                    </div>
                    <div class="stat-content">
                        <h3><?= $estadisticas['total_estudiantes'] ?></h3>
                        <p>Total Estudiantes</p>
                    </div>
                </div>

                <div class="stat-card-entrega">
                    <div class="stat-icon green">
                        <i class="ri-file-upload-line"></i>
                    </div>
                    <div class="stat-content">
                        <h3><?= $estadisticas['total_entregas'] ?></h3>
                        <p>Entregas Recibidas</p>
                        <small><?= $porcentaje_entregas ?>%</small>
                    </div>
                </div>

                <div class="stat-card-entrega">
                    <div class="stat-icon orange">
                        <i class="ri-time-line"></i>
                    </div>
                    <div class="stat-content">
                        <h3><?= $estadisticas['total_pendientes'] ?></h3>
                        <p>Pendientes</p>
                    </div>
                </div>

                <div class="stat-card-entrega">
                    <div class="stat-icon purple">
                        <i class="ri-check-double-line"></i>
                    </div>
                    <div class="stat-content">
                        <h3><?= $estadisticas['total_calificadas'] ?></h3>
                        <p>Calificadas</p>
                        <small><?= $porcentaje_calificadas ?>%</small>
                    </div>
                </div>

                <?php if ($estadisticas['promedio_notas']): ?>
                <div class="stat-card-entrega">
                    <div class="stat-icon yellow">
                        <i class="ri-medal-line"></i>
                    </div>
                    <div class="stat-content">
                        <h3><?= number_format($estadisticas['promedio_notas'], 1) ?></h3>
                        <p>Promedio Notas</p>
                    </div>
                </div>
                <?php endif; ?>
            </div>

            <!-- FILTROS -->
            <div class="entregas-filters">
                <div class="filter-buttons">
                    <button class="filter-btn-entrega active" data-filter="todos">
                        <i class="ri-list-check"></i> Todos
                    </button>
                    <button class="filter-btn-entrega" data-filter="entregado">
                        <i class="ri-file-check-line"></i> Entregados (<?= $estadisticas['total_entregas'] ?>)
                    </button>
                    <button class="filter-btn-entrega" data-filter="pendiente">
                        <i class="ri-time-line"></i> Pendientes (<?= $estadisticas['total_pendientes'] ?>)
                    </button>
                    <button class="filter-btn-entrega" data-filter="calificado">
                        <i class="ri-star-line"></i> Calificados (<?= $estadisticas['total_calificadas'] ?>)
                    </button>
                </div>
                <div class="search-box-entrega">
                    <i class="ri-search-line"></i>
                    <input type="text" id="searchEstudiante" placeholder="Buscar estudiante...">
                </div>
            </div>

            <!-- TABLA DE ENTREGAS -->
            <div class="entregas-table-container">
                <table class="entregas-table">
                    <thead>
                        <tr>
                            <th style="width: 50px;">#</th>
                            <th>Estudiante</th>
                            <th style="width: 150px;">Estado</th>
                            <th style="width: 150px;">Fecha Entrega</th>
                            <th style="width: 120px;">Puntualidad</th>
                            <th style="width: 100px;">Nota</th>
                            <th style="width: 200px;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($estudiantes)): ?>
                            <tr>
                                <td colspan="7" class="text-center" style="padding: 40px; color: #97a1b6;">
                                    <i class="ri-user-line" style="font-size: 48px; opacity: 0.5;"></i>
                                    <p style="margin-top: 16px;">No hay estudiantes matriculados en este curso</p>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($estudiantes as $index => $estudiante): ?>
                                <tr data-estado="<?= strtolower($estudiante['estado_general']) ?>" 
                                    data-nombre="<?= htmlspecialchars(strtolower($estudiante['nombres'] . ' ' . $estudiante['apellidos'])) ?>">
                                    <td><?= $index + 1 ?></td>
                                    <td>
                                        <div class="estudiante-info">
                                            <div class="estudiante-avatar">
                                                <?php if ($estudiante['foto'] && $estudiante['foto'] !== 'default.png'): ?>
                                                    <img src="<?= BASE_URL ?>/public/uploads/estudiantes/<?= $estudiante['foto'] ?>" alt="Foto">
                                                <?php else: ?>
                                                    <span><?= strtoupper(substr($estudiante['nombres'], 0, 1) . substr($estudiante['apellidos'], 0, 1)) ?></span>
                                                <?php endif; ?>
                                            </div>
                                            <div>
                                                <strong><?= htmlspecialchars($estudiante['nombres'] . ' ' . $estudiante['apellidos']) ?></strong>
                                                <small>Doc: <?= htmlspecialchars($estudiante['documento']) ?></small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <?php 
                                        $estado = strtolower($estudiante['estado_general']);
                                        $iconos_estado = [
                                            'pendiente' => 'ri-time-line',
                                            'entregado' => 'ri-file-check-line',
                                            'calificado' => 'ri-star-line',
                                            'atrasado' => 'ri-error-warning-line'
                                        ];
                                        $icono = $iconos_estado[$estado] ?? 'ri-question-line';
                                        ?>
                                        <span class="badge-estado <?= $estado ?>">
                                            <i class="<?= $icono ?>"></i>
                                            <?= ucfirst($estudiante['estado_general']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if ($estudiante['fecha_entrega_archivo']): ?>
                                            <span class="fecha-entrega">
                                                <?= date('d M, Y H:i', strtotime($estudiante['fecha_entrega_archivo'])) ?>
                                            </span>
                                        <?php else: ?>
                                            <span style="color: #97a1b6;">--</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($estudiante['id_entrega']): ?>
                                            <?php if ($estudiante['puntualidad'] === 'A tiempo'): ?>
                                                <span class="badge-puntualidad a-tiempo">
                                                    <i class="ri-check-line"></i> A tiempo
                                                </span>
                                            <?php else: ?>
                                                <span class="badge-puntualidad tarde">
                                                    <i class="ri-alert-line"></i> Tarde
                                                </span>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <span style="color: #97a1b6;">--</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($estudiante['nota']): ?>
                                            <span class="nota-display"><?= number_format($estudiante['nota'], 1) ?></span>
                                        <?php else: ?>
                                            <span style="color: #97a1b6;">--</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="acciones-btn-group">
                                            <?php if ($estudiante['id_entrega']): ?>
                                                <button class="btn-accion-entrega btn-descargar" 
                                                        data-id-entrega="<?= $estudiante['id_entrega'] ?>"
                                                        title="Descargar PDF">
                                                    <i class="ri-download-2-line"></i>
                                                </button>
                                                <button class="btn-accion-entrega btn-calificar" 
                                                        data-id-entrega="<?= $estudiante['id_entrega'] ?>"
                                                        data-nombre="<?= htmlspecialchars($estudiante['nombres'] . ' ' . $estudiante['apellidos']) ?>"
                                                        data-nota="<?= $estudiante['nota'] ?? '' ?>"
                                                        data-observacion="<?= htmlspecialchars($estudiante['observacion_docente'] ?? '') ?>"
                                                        title="Calificar">
                                                    <i class="ri-star-line"></i>
                                                </button>
                                                <?php if ($estudiante['observaciones_estudiante']): ?>
                                                <button class="btn-accion-entrega btn-ver-comentario" 
                                                        data-comentario="<?= htmlspecialchars($estudiante['observaciones_estudiante']) ?>"
                                                        title="Ver comentario estudiante">
                                                    <i class="ri-message-3-line"></i>
                                                </button>
                                                <?php endif; ?>
                                            <?php else: ?>
                                                <span style="color: #97a1b6; font-size: 12px;">Sin entrega</span>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

        </main>

    </div>

    <!-- MODAL CALIFICAR -->
    <div class="modal fade" id="modalCalificar" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="background: #1a1d29; border: 1px solid #2a2d3a; border-radius: 16px;">
                <div class="modal-header" style="border-bottom: 1px solid #2a2d3a;">
                    <h5 class="modal-title" id="modalCalificarLabel" style="color: #fff;">
                        <i class="ri-star-line" style="color: #f59e0b;"></i> Calificar Actividad
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" style="filter: invert(1);"></button>
                </div>
                <form id="formCalificar" method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="id_entrega" id="idEntregaCalificar">

                        <div class="mb-3">
                            <label for="notaEntrega" class="form-label" style="color: #97a1b6;">
                                Nota (0.0 - 5.0) *
                            </label>
                            <input type="number" name="nota" id="notaEntrega" class="form-control" 
                                   min="0" max="5" step="0.1" required
                                   style="background: #252836; border: 1px solid #2a2d3a; color: #fff; border-radius: 8px;">
                        </div>

                        <div class="mb-3">
                            <label for="observacionNota" class="form-label" style="color: #97a1b6;">
                                Retroalimentación (opcional)
                            </label>
                            <textarea name="observacion" id="observacionNota" rows="4" class="form-control"
                                      placeholder="Escribe comentarios sobre el trabajo del estudiante..."
                                      style="background: #252836; border: 1px solid #2a2d3a; color: #fff; border-radius: 8px; resize: none;"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer" style="border-top: 1px solid #2a2d3a;">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"
                                style="background: #2a2d3a; border: none; border-radius: 8px;">
                            Cancelar
                        </button>
                        <button type="submit" id="btnGuardarCalificacion" class="btn btn-primary"
                                style="background: linear-gradient(135deg, #f59e0b, #d97706); border: none; border-radius: 8px;">
                            <i class="ri-check-line"></i> Guardar Calificación
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="<?= BASE_URL ?>/public/assets/dashboard/js/docente/entregas.js"></script>
</body>

</html>
