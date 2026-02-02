<?php
require_once BASE_PATH . '/config/database.php';
require_once BASE_PATH . '/app/models/estudiante/entrega.php';
require_once BASE_PATH . '/app/helpers/upload_helper.php';
require_once BASE_PATH . '/app/helpers/alert_helper.php';

// Verificar sesión de estudiante
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['user']) || $_SESSION['user']['rol'] !== 'Estudiante') {
    header('Location: ' . BASE_URL . '/login');
    exit();
}

// Verificar que sea POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . BASE_URL . '/estudiante-panel-materias');
    exit();
}

// Obtener datos del formulario
$id_actividad = isset($_POST['id_actividad']) ? intval($_POST['id_actividad']) : 0;
$id_asignatura_curso = isset($_POST['id_asignatura_curso']) ? intval($_POST['id_asignatura_curso']) : 0;
$observaciones = isset($_POST['observaciones']) ? trim($_POST['observaciones']) : '';

// Validar datos obligatorios
if ($id_actividad <= 0 || $id_asignatura_curso <= 0) {
    mostrarSweetAlert('error', 'Error', 'Datos inválidos', BASE_URL . '/estudiante-panel-materias');
    exit();
}

// Validar que se haya subido un archivo
if (!isset($_FILES['archivo']) || $_FILES['archivo']['error'] === UPLOAD_ERR_NO_FILE) {
    mostrarSweetAlert('error', 'Error', 'Debes seleccionar un archivo PDF', BASE_URL . '/estudiante-materia-detalle?id=' . $id_asignatura_curso);
    exit();
}

// Obtener ID del estudiante desde la sesión
$id_usuario = $_SESSION['user']['id'];
$database = new Conexion();
$conn = $database->getConexion();

// Buscar ID del estudiante
$sql_estudiante = "SELECT id FROM estudiante WHERE id_usuario = :id_usuario";
$stmt = $conn->prepare($sql_estudiante);
$stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
$stmt->execute();
$estudiante = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$estudiante) {
    mostrarSweetAlert('error', 'Error', 'Estudiante no encontrado', BASE_URL . '/estudiante-panel-materias');
    exit();
}

$id_estudiante = $estudiante['id'];

// Instanciar modelo
$entregaModel = new EntregaEstudiante();

// Obtener información de la actividad (curso, asignatura)
$info_actividad = $entregaModel->obtenerInfoActividad($id_actividad);

if (!$info_actividad) {
    mostrarSweetAlert('error', 'Error', 'Actividad no encontrada', BASE_URL . '/estudiante-materia-detalle?id=' . $id_asignatura_curso);
    exit();
}

// Obtener información del estudiante
$info_estudiante = $entregaModel->obtenerInfoEstudiante($id_estudiante);

if (!$info_estudiante) {
    mostrarSweetAlert('error', 'Error', 'Información del estudiante no encontrada', BASE_URL . '/estudiante-materia-detalle?id=' . $id_asignatura_curso);
    exit();
}

// Validar archivo PDF
$errores = UploadHelper::validarArchivoPDF($_FILES['archivo']);

if (!empty($errores)) {
    $mensaje_error = implode('. ', $errores);
    mostrarSweetAlert('error', 'Error en el archivo', $mensaje_error, BASE_URL . '/estudiante-materia-detalle?id=' . $id_asignatura_curso);
    exit();
}

// Preparar información para carpetas
$institucion_info = [
    'id' => $info_actividad['id_institucion'],
    'nombre' => $info_actividad['nombre_institucion']
];

$curso_info = [
    'id' => $info_actividad['curso_id'],
    'grado' => $info_actividad['grado'],
    'nombre' => $info_actividad['nombre_curso']
];

$asignatura_info = [
    'id' => $info_actividad['asignatura_id'],
    'nombre' => $info_actividad['nombre_asignatura']
];

$estudiante_info = [
    'id' => $info_estudiante['id'],
    'nombres' => $info_estudiante['nombres'],
    'apellidos' => $info_estudiante['apellidos']
];

$actividad_info = [
    'id' => $info_actividad['id'],
    'titulo' => $info_actividad['titulo']
];

// Crear estructura de carpetas: entregas/[institucion]/[curso]/[asignatura]/[estudiante]/[actividad]/
$resultado_carpetas = UploadHelper::crearEstructuraCarpetas(
    $institucion_info,
    $curso_info,
    $asignatura_info,
    $estudiante_info,
    $actividad_info
);

if (!$resultado_carpetas) {
    mostrarSweetAlert('error', 'Error', 'No se pudo crear la carpeta de destino', BASE_URL . '/estudiante-materia-detalle?id=' . $id_asignatura_curso);
    exit();
}

// Generar nombre único para el archivo
$nombre_archivo = UploadHelper::generarNombreUnico($id_actividad);

// Guardar archivo en el servidor
if (!UploadHelper::guardarArchivo($_FILES['archivo'], $resultado_carpetas['ruta_completa'], $nombre_archivo)) {
    mostrarSweetAlert('error', 'Error', 'No se pudo guardar el archivo', BASE_URL . '/estudiante-materia-detalle?id=' . $id_asignatura_curso);
    exit();
}

// Generar ruta relativa para la base de datos
$ruta_relativa = UploadHelper::generarRutaRelativa(
    $resultado_carpetas['carpeta_institucion'],
    $resultado_carpetas['carpeta_curso'],
    $resultado_carpetas['carpeta_asignatura'],
    $resultado_carpetas['carpeta_estudiante'],
    $resultado_carpetas['carpeta_actividad'],
    $nombre_archivo
);

// Verificar si ya existe una entrega previa
$entrega_existente = $entregaModel->verificarEntregaExistente($id_actividad, $id_estudiante);

$datos_entrega = [
    'id_actividad' => $id_actividad,
    'id_estudiante' => $id_estudiante,
    'archivo_ruta' => $ruta_relativa,
    'observaciones_estudiante' => $observaciones
];

$exito = false;

if ($entrega_existente) {
    // Si ya existe una entrega, eliminar el archivo anterior
    if (UploadHelper::archivoExiste($entrega_existente['archivo_ruta'])) {
        UploadHelper::eliminarArchivo($entrega_existente['archivo_ruta']);
    }
    
    // Actualizar entrega existente
    $exito = $entregaModel->actualizar($id_actividad, $id_estudiante, $datos_entrega);
    $mensaje = 'Tarea re-entregada exitosamente';
} else {
    // Crear nueva entrega
    $exito = $entregaModel->crear($datos_entrega);
    $mensaje = 'Tarea entregada exitosamente';
}

if ($exito) {
    mostrarSweetAlert('success', '¡Éxito!', $mensaje, BASE_URL . '/estudiante-materia-detalle?id=' . $id_asignatura_curso);
} else {
    // Si falla la BD, eliminar el archivo subido
    UploadHelper::eliminarArchivo($ruta_relativa);
    mostrarSweetAlert('error', 'Error', 'No se pudo registrar la entrega en la base de datos', BASE_URL . '/estudiante-materia-detalle?id=' . $id_asignatura_curso);
}

exit();
