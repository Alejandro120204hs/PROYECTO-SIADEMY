
<?php
// Iniciar sesión si no está iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verificar que el usuario esté autenticado
if (!isset($_SESSION['user']) || $_SESSION['user']['rol'] !== 'Docente') {
    header('Location: ' . BASE_URL . '/login');
    exit;
}

// Verificar que se recibió el id_curso
if (!isset($_GET['id_curso']) || empty($_GET['id_curso'])) {
    // Redirigir a cursos si no hay id_curso
    header('Location: ' . BASE_URL . '/docente-cursos');
    exit;
}

$id_curso = $_GET['id_curso'];
$id_institucion = $_SESSION['user']['id_institucion'];

// Obtener información del curso seleccionado
require_once BASE_PATH . '/config/database.php';
$db = new Conexion();
$conn = $db->getConexion();

try {
    $query = "SELECT c.*, 
                     ac.id as id_asignatura_curso,
                     a.nombre as nombre_asignatura,
                     a.id as id_asignatura
              FROM curso c
              INNER JOIN asignatura_curso ac ON ac.id_curso = c.id
              INNER JOIN asignatura a ON a.id = ac.id_asignatura
              WHERE c.id = :id_curso
              AND c.id_institucion = :id_institucion
              LIMIT 1";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':id_curso', $id_curso, PDO::PARAM_INT);
    $stmt->bindParam(':id_institucion', $id_institucion, PDO::PARAM_INT);
    $stmt->execute();
    $curso = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$curso) {
        header('Location: ' . BASE_URL . '/docente/cursos');
        exit;
    }
} catch(PDOException $e) {
    die("Error al consultar el curso: " . $e->getMessage());
}
?>
<!doctype html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SIADEMY • Nueva Actividad</title>
    <?php
        include_once __DIR__ . '/../../layouts/header_coordinador.php'
    ?>
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/dashboard/css/styles-tabla-formulario.css">

</head>
<body>
    <div class="app" id="appGrid">
   <!-- LEFT SIDEBAR -->
      <?php 
        include_once __DIR__ . '/../../layouts/sidebar_docente.php'
      ?>

        <!-- MAIN -->
        <main class="main">
            <div class="topbar">
                <div class="topbar-left">
                    <button class="toggle-btn" id="toggleLeft" title="Mostrar/Ocultar menú lateral">
                        <i class="ri-menu-2-line"></i>
                    </button>
                    <div class="title">Agregar Actividad</div>
                    
                </div>

                <div class="user">
                    <button class="btn" title="Notificaciones"><i class="ri-notification-3-line"></i></button>
                    <button class="btn" title="Configuración"><i class="ri-settings-3-line"></i></button>
                    <div class="avatar" title="Diego A.">DA</div>
                </div>
            </div>
            <div class="subtitulo"><p>Formulario de registro, Completa los siguientes pasos para registrar una nueva actividad en el sistema académico. <br> Al finalizar, revisa la información antes de confirmar el registro para evitar errores en la base de datos institucional.</p></div>

            <!-- Formulario Wizard -->
            <div class="container-fluid py-3">

                <div class="wizard-progress">
                    <div id="stepIndicator1" class="active-step">Paso 1</div>
                    <div id="stepIndicator3">Confirmar</div>
                </div>

                <form id="formWizard" action="<?= BASE_URL ?>/docente/guardar_actividad" method="POST" enctype="multipart/form-data">

                    <!-- Paso 1 -->
                    <div class="step active">
                        <div class="tabla-titulo mb-3">
                            <h5>Información de la actividad - <?= $curso['grado'] ?>° <?= $curso['curso'] ?> (<?= $curso['nombre_asignatura'] ?>)</h5>
                            <p style="color: #64748b; font-size: 14px; margin-top: 8px;">
                                <i class="ri-book-line"></i> Asignatura: <strong><?= $curso['nombre_asignatura'] ?></strong> | 
                                <i class="ri-calendar-line"></i> Año: <strong><?= $curso['anio'] ?></strong> | 
                                <i class="ri-time-line"></i> Jornada: <strong><?= $curso['jornada'] ?></strong>
                            </p>
                        </div>

                        <!-- Campo hidden para enviar el id_curso y id_asignatura -->
                        <input type="hidden" name="id_curso" value="<?= $curso['id'] ?>">
                        <input type="hidden" name="id_asignatura" value="<?= $curso['id_asignatura'] ?>">
                        <input type="hidden" name="id_asignatura_curso" value="<?= $curso['id_asignatura_curso'] ?>">

                        <div class="row g-3">
                           <div class="col-md-1">
                            </div>

                            <!-- Datos de la actividad -->
                            <div class="col-md-5">
                                <div class="mb-3">
                                    <label for="">Título de la actividad*</label>
                                    <input type="text" class="form-control" name="titulo_actividad" required tabindex="1">
                                </div>
                                 <div class="mb-3">
                                    <label for="">Tipo de actividad*</label>
                                    <select class="selector" name="tipo_actividad" required tabindex="2">
                                        <option selected>Seleccione el tipo de actividad</option>
                                        <option value="Taller">Taller</option>
                                        <option value="Quiz">Quiz</option>
                                        <option value="Examen">Examen</option>
                                        <option value="Proyecto">Proyecto</option>
                                        <option value="Exposición">Exposición</option>
                                        <option value="Laboratorio">Laboratorio</option>
                                        <option value="Tarea">Tarea</option>
                                    </select>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="">Descripción</label>
                                    <textarea class="form-control" name="descripcion" rows="4" tabindex="5"></textarea>
                                </div>
                            </div>

                            <!-- Ponderación y fecha -->
                            <div class="col-md-5">
                                <div class="mb-3">
                                    <label for="">Ponderación / Valor porcentual (%)*</label>
                                    <input type="number" class="form-control" name="ponderacion" min="0" max="100" step="0.01" required tabindex="3">
                                    <small class="form-text text-muted">Valor porcentual de la actividad al total del periodo (0-100%)</small>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="">Fecha de entrega*</label>
                                    <input type="date" class="form-control" name="fecha_entrega" required tabindex="4">
                                </div>
                            </div>
                        </div>

                        <div class="botones mt-3">
                            <button type="button" class="btn btn-primary" onclick="nextStep()">Siguiente</button>
                        </div>
                    </div>

                    <!-- Paso 3 -->
                    <div class="step">
                        <div class="tabla-titulo mb-3">
                            <h5>Confirmar Registro</h5>
                        </div>
                        <p>Revisa los datos ingresados antes de agregar el acudiente.</p>

                        <div class="botones mt-3">
                            <button type="button" class="btn btn-secondary" onclick="prevStep()">Anterior</button>
                            <button type="submit" class="btn btn-success">Agregar Actividad</button>
                        </div>
                    </div>

                </form>
            </div>
        </main>
    </div>

    <!-- FOOTER -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

    <script src="<?=BASE_URL ?>/public/assets/dashboard/js/main-formulario.js"></script>
</body>

</html>