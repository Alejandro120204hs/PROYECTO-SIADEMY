<?php 
    require_once BASE_PATH . '/app/helpers/session_administrador.php';
    require_once BASE_PATH . '/app/controllers/administrador/estudiante_controller.php';
    require_once BASE_PATH . '/app/controllers/administrador/curso.php';
    
    $estudiantes = mostrarEstudiantes();
    $cursos = mostrarCursos();
?>

<!doctype html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SIADEMY • Matricular Estudiante</title>
    <?php
        include_once __DIR__ . '/../../layouts/header_coordinador.php'
    ?>
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/dashboard/css/styles-admin.css">
    
    <!-- Select2 para mejorar los selects -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    
    <style>
        /* Layout de 2 columnas sin sidebar derecho */
        .app {
            grid-template-columns: 260px 1fr !important;
        }

        .app.hide-left {
            grid-template-columns: 0 1fr !important;
        }

        /* Estilos específicos para esta página */
        .form-card {
            background: #151d3e;
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 28px;
            margin-bottom: 24px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .form-card h3 {
            font-size: 20px;
            font-weight: 600;
            margin-bottom: 8px;
            color: #fff;
        }

        .form-card p {
            color: var(--muted);
            font-size: 14px;
            margin-bottom: 24px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            color: #d7d9df;
            font-size: 14px;
            font-weight: 500;
            margin-bottom: 8px;
        }

        .form-group label i {
            color: var(--brand);
            margin-right: 6px;
        }

        .form-control, .form-select {
            width: 100%;
            background: #0f1736;
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: 12px 16px;
            color: #fff;
            font-size: 14px;
            transition: all 0.2s ease;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--brand);
            outline: none;
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
        }

        .btn-submit {
            background: var(--brand);
            color: white;
            border: none;
            border-radius: 10px;
            padding: 12px 28px;
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-submit:hover {
            background: #4338ca;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3);
        }

        .btn-cancel {
            background: rgba(148, 163, 184, 0.15);
            color: #94a3b8;
            border: none;
            border-radius: 10px;
            padding: 12px 28px;
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
        }

        .btn-cancel:hover {
            background: rgba(148, 163, 184, 0.25);
            transform: translateY(-2px);
        }

        .alert {
            padding: 16px 20px;
            border-radius: 12px;
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            gap: 12px;
            border: 1px solid;
        }

        .alert-success {
            background: rgba(16, 185, 129, 0.1);
            border-color: #10b981;
            color: #10b981;
        }

        .alert-danger {
            background: rgba(239, 68, 68, 0.1);
            border-color: #ef4444;
            color: #ef4444;
        }

        .alert-warning {
            background: rgba(255, 176, 32, 0.1);
            border-color: var(--accent);
            color: var(--accent);
        }

        .info-box {
            background: rgba(79, 70, 229, 0.1);
            border-left: 4px solid var(--brand);
            padding: 16px;
            border-radius: 12px;
            margin-bottom: 24px;
            color: #d7d9df;
        }

        .info-box i {
            color: var(--brand);
            margin-right: 8px;
        }

        .info-box ul {
            margin: 8px 0 0 0;
            padding-left: 20px;
        }

        .info-box ul li {
            margin-bottom: 6px;
            color: var(--muted);
        }

        .curso-info {
            background: rgba(16, 185, 129, 0.1);
            border: 1px solid rgba(16, 185, 129, 0.3);
            border-radius: 12px;
            padding: 20px;
            margin-top: 24px;
            display: none;
        }

        .curso-info.active {
            display: block;
        }

        .curso-info h6 {
            color: #10b981;
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .curso-info p {
            color: #d7d9df;
            margin-bottom: 8px;
            font-size: 14px;
        }

        .curso-info strong {
            color: #fff;
        }

        .curso-info .row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 16px;
        }

        /* Select2 customization */
        .select2-container--default .select2-selection--single {
            background: #0f1736;
            border: 1px solid var(--border);
            border-radius: 10px;
            height: 48px;
            padding: 8px;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            color: #fff;
            line-height: 32px;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 46px;
        }

        .select2-dropdown {
            background: #151d3e;
            border: 1px solid var(--border);
            border-radius: 10px;
        }

        .select2-container--default .select2-results__option {
            color: #d7d9df;
            padding: 12px;
        }

        .select2-container--default .select2-results__option--highlighted {
            background: var(--brand);
            color: #fff;
        }

        .select2-container--default .select2-search--dropdown .select2-search__field {
            background: #0f1736;
            border: 1px solid var(--border);
            color: #fff;
        }

        .form-help {
            display: block;
            font-size: 12px;
            color: var(--muted);
            margin-top: 6px;
        }

        .btn-actions {
            display: flex;
            gap: 12px;
            justify-content: flex-end;
            margin-top: 30px;
            padding-top: 24px;
            border-top: 1px solid var(--border);
        }

        @media (max-width: 768px) {
            .form-card {
                padding: 16px;
            }
            
            .btn-actions {
                flex-direction: column-reverse;
            }

            .btn-submit, .btn-cancel {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
</head>

<body>
    <div class="app" id="appGrid">
        <!-- LEFT SIDEBAR -->
        <?php include_once __DIR__ . '/../../layouts/sidebar_coordinador.php'; ?>

        <!-- MAIN -->
        <main class="main">
            <div class="topbar">
                <div class="topbar-left">
                    <button class="toggle-btn" id="toggleLeft" title="Mostrar/Ocultar menú lateral">
                        <i class="ri-menu-2-line"></i>
                    </button>
                    <div class="title">Matricular Estudiante</div>
                </div>
            </div>

            <?php 
            // Mostrar alerta si existe
            if(isset($_SESSION['alerta'])){
                $alerta = $_SESSION['alerta'];
                echo '<div class="alert alert-'.$alerta['tipo'].'">';
                echo '<i class="ri-information-line"></i>';
                echo '<span>'.$alerta['mensaje'].'</span>';
                echo '<button class="btn-close" onclick="this.parentElement.remove()" style="margin-left: auto; background: transparent; border: none; color: inherit; cursor: pointer; opacity: 0.7;"><i class="ri-close-line"></i></button>';
                echo '</div>';
                unset($_SESSION['alerta']);
            }
            ?>

            <!-- FORMULARIO DE MATRÍCULA -->
            <div class="form-card">
                <h3><i class="ri-graduation-cap-line"></i> Nueva Matrícula</h3>
                <p>Completa los siguientes campos para matricular un estudiante en un curso específico</p>

                <div class="info-box">
                    <i class="ri-information-line"></i>
                    <strong>Información importante:</strong>
                    <ul>
                        <li>Verifica que el estudiante no esté ya matriculado en el curso seleccionado</li>
                        <li>El sistema validará automáticamente el cupo disponible del curso</li>
                        <li>Solo se pueden matricular estudiantes en cursos activos</li>
                    </ul>
                </div>

                <?php if(isset($_GET['curso']) && !empty($_GET['curso'])): ?>
                <div class="alert alert-success">
                    <i class="ri-checkbox-circle-line"></i>
                    <span><strong>¡Curso pre-seleccionado!</strong> El curso actual ya está seleccionado en el formulario.</span>
                </div>
                <?php endif; ?>

                <form id="formMatricula" action="<?= BASE_URL ?>/administrador/guardar-matricula" method="POST">
                    
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 20px;">
                        
                        <!-- SELECCIONAR ESTUDIANTE -->
                        <div class="form-group">
                            <label>
                                <i class="ri-user-line"></i> Estudiante <span style="color: #ef4444;">*</span>
                            </label>
                            <select id="selectEstudiante" class="form-select select2" name="id_estudiante" required>
                                <option value="">Seleccione un estudiante...</option>
                                <?php if (!empty($estudiantes)): ?>
                                    <?php foreach ($estudiantes as $estudiante): ?>
                                        <option value="<?= $estudiante['id'] ?>" 
                                                data-documento="<?= $estudiante['documento'] ?>">
                                            <?= htmlspecialchars($estudiante['nombres'] . ' ' . $estudiante['apellidos']) ?> 
                                            - Doc: <?= $estudiante['documento'] ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <option disabled>No hay estudiantes registrados</option>
                                <?php endif; ?>
                            </select>
                            <small class="form-help">Busca por nombre o documento del estudiante</small>
                        </div>

                        <!-- SELECCIONAR CURSO -->
                        <div class="form-group">
                            <label>
                                <i class="ri-book-line"></i> Curso <span style="color: #ef4444;">*</span>
                            </label>
                            <select id="selectCurso" class="form-select select2" name="id_curso" required>
                                <option value="">Seleccione un curso...</option>
                                <?php 
                                // Obtener el ID del curso desde el parámetro GET
                                $curso_preseleccionado = $_GET['curso'] ?? null;
                                ?>
                                <?php if (!empty($cursos)): ?>
                                    <?php foreach ($cursos as $curso): ?>
                                        <?php if($curso['estado'] == 'Activo'): ?>
                                            <option value="<?= $curso['id'] ?>" 
                                                    <?= ($curso_preseleccionado && $curso['id'] == $curso_preseleccionado) ? 'selected' : '' ?>
                                                    data-grado="<?= $curso['grado'] ?>"
                                                    data-nombre="<?= $curso['curso'] ?>"
                                                    data-nivel="<?= $curso['nivel_academico'] ?>"
                                                    data-jornada="<?= $curso['jornada'] ?>"
                                                    data-cupo="<?= $curso['cupo_maximo'] ?>"
                                                    data-docente="<?= $curso['nombres_docente'] . ' ' . $curso['apellidos_docente'] ?>">
                                                <?= $curso['grado'] ?>° - <?= $curso['curso'] ?> 
                                                (<?= $curso['nivel_academico'] ?> - <?= $curso['jornada'] ?>)
                                            </option>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <option disabled>No hay cursos activos</option>
                                <?php endif; ?>
                            </select>
                            <small class="form-help">Solo se muestran cursos activos</small>
                        </div>

                        <!-- AÑO LECTIVO -->
                        <div class="form-group">
                            <label>
                                <i class="ri-calendar-line"></i> Año Lectivo <span style="color: #ef4444;">*</span>
                            </label>
                            <select class="form-control" name="anio" id="anio" required>
                                <?php 
                                    $anioActual = date('Y');
                                    for($i = $anioActual - 1; $i <= $anioActual + 1; $i++): 
                                ?>
                                    <option value="<?= $i ?>" <?= ($i == $anioActual) ? 'selected' : '' ?>>
                                        <?= $i ?>
                                    </option>
                                <?php endfor; ?>
                            </select>
                        </div>

                        <!-- FECHA DE MATRÍCULA -->
                        <div class="form-group">
                            <label>
                                <i class="ri-calendar-check-line"></i> Fecha de Matrícula <span style="color: #ef4444;">*</span>
                            </label>
                            <input type="date" class="form-control" name="fecha" id="fecha" 
                                   value="<?= date('Y-m-d') ?>" required>
                        </div>

                    </div>

                    <!-- INFORMACIÓN DEL CURSO SELECCIONADO -->
                    <div id="cursoInfo" class="curso-info">
                        <h6><i class="ri-information-line"></i> Información del Curso Seleccionado</h6>
                        <div class="row">
                            <div>
                                <p><strong>Grado:</strong> <span id="infoCursoGrado">-</span></p>
                                <p><strong>Nivel:</strong> <span id="infoCursoNivel">-</span></p>
                                <p><strong>Jornada:</strong> <span id="infoCursoJornada">-</span></p>
                            </div>
                            <div>
                                <p><strong>Director:</strong> <span id="infoCursoDocente">-</span></p>
                                <p><strong>Cupo Máximo:</strong> <span id="infoCursoCupo">-</span></p>
                            </div>
                        </div>
                    </div>

                    <!-- BOTONES -->
                    <div class="btn-actions">
                        <a href="<?= BASE_URL ?>/administrador/panel-matriculas" class="btn-cancel">
                            <i class="ri-arrow-left-line"></i> Cancelar
                        </a>
                        <button type="submit" class="btn-submit">
                            <i class="ri-save-line"></i> Matricular Estudiante
                        </button>
                    </div>

                </form>
            </div>

        </main>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="<?= BASE_URL ?>/public/assets/dashboard/js/dashboard.js"></script>
    
    <script>
        $(document).ready(function() {
            // Inicializar Select2
            $('.select2').select2({
                theme: 'default',
                width: '100%',
                placeholder: function() {
                    return $(this).data('placeholder') || 'Seleccione una opción...';
                }
            });

            // Mostrar información del curso al seleccionarlo
            $('#selectCurso').on('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                
                if(selectedOption.value) {
                    document.getElementById('infoCursoGrado').textContent = selectedOption.dataset.grado + '°';
                    document.getElementById('infoCursoNivel').textContent = selectedOption.dataset.nivel;
                    document.getElementById('infoCursoJornada').textContent = selectedOption.dataset.jornada;
                    document.getElementById('infoCursoDocente').textContent = selectedOption.dataset.docente;
                    document.getElementById('infoCursoCupo').textContent = selectedOption.dataset.cupo + ' estudiantes';
                    
                    $('#cursoInfo').addClass('active');
                } else {
                    $('#cursoInfo').removeClass('active');
                }
            });

            // Si hay un curso pre-seleccionado, disparar el evento change y resaltar
            <?php if(isset($_GET['curso']) && !empty($_GET['curso'])): ?>
                // Disparar el evento change para mostrar la información del curso
                $('#selectCurso').trigger('change');
                
                // Resaltar visualmente el select de curso
                $('#selectCurso').parent().find('.select2-container').css({
                    'border': '2px solid #10b981',
                    'border-radius': '10px',
                    'box-shadow': '0 0 0 3px rgba(16, 185, 129, 0.1)'
                });

                // Remover el resaltado después de 3 segundos
                setTimeout(function() {
                    $('#selectCurso').parent().find('.select2-container').css({
                        'border': '',
                        'box-shadow': ''
                    });
                }, 3000);
            <?php endif; ?>

            // Validación del formulario antes de enviar
            $('#formMatricula').on('submit', function(e) {
                const estudiante = $('#selectEstudiante').val();
                const curso = $('#selectCurso').val();
                
                if(!estudiante || !curso) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'error',
                        title: 'Campos incompletos',
                        text: 'Por favor seleccione un estudiante y un curso',
                        confirmButtonColor: '#4f46e5'
                    });
                }
            });

            // Toggle sidebar izquierdo
            $('#toggleLeft').on('click', function() {
                $('#appGrid').toggleClass('hide-left');
            });
        });
    </script>

</body>
</html>
