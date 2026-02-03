<?php

// IMPORTAMOS LAS DEPENDENCIAS NECESARIAS
require_once BASE_PATH . '/app/models/estudiante/materia.php';
require_once BASE_PATH . '/config/database.php';

// CAPTURAMOS EN UNA VARIABLE EL MÉTODO O SOLICITUD HECHA AL SERVIDOR
$method = $_SERVER['REQUEST_METHOD'];

switch($method){
    case 'GET':
        mostrarMaterias();
        break;

    default:
        http_response_code(405);
        echo "Método no permitido";
        break;
}

function mostrarMaterias(){
    // VERIFICAMOS SI LA SESIÓN YA ESTÁ INICIADA
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // VERIFICAR QUE ESTÉ LOGUEADO COMO ESTUDIANTE
    if (!isset($_SESSION['user']) || $_SESSION['user']['rol'] !== 'Estudiante') {
        header('Location: ' . BASE_URL . '/login');
        exit;
    }

    // OBTENER ID DEL ESTUDIANTE DESDE LA TABLA estudiante
    $id_usuario_sesion = $_SESSION['user']['id'];
    $id_institucion = $_SESSION['user']['id_institucion'];

    $db = new Conexion();
    $pdo = $db->getConexion();

    $stmt = $pdo->prepare("SELECT id FROM estudiante WHERE id_usuario = ?");
    $stmt->execute([$id_usuario_sesion]);
    $estudiante_info = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$estudiante_info) {
        header('Location: ' . BASE_URL . '/login');
        exit;
    }

    $id_estudiante = $estudiante_info['id'];
    $anio_actual = date('Y');

    // INSTANCIAR MODELO Y OBTENER DATOS
    $materiaModel = new MateriaEstudiante();
    $materias = $materiaModel->obtenerMateriasConEstadisticas($id_estudiante, $id_institucion, $anio_actual);
    $estadisticas = $materiaModel->obtenerEstadisticasGenerales($id_estudiante, $id_institucion, $anio_actual);
    $actividades_proximas = $materiaModel->obtenerActividadesProximas($id_estudiante, $id_institucion, $anio_actual, 3);

    // INCLUIR LA VISTA
    require BASE_PATH . '/app/views/dashboard/estudiante/materias.php';
}

// FUNCIÓN AUXILIAR PARA FORMATEAR FECHAS
function formatearFechaProxima($fecha) {
    $dias = floor((strtotime($fecha) - time()) / (60 * 60 * 24));
    
    if ($dias == 0) {
        return 'Vence hoy';
    } elseif ($dias == 1) {
        return 'Vence mañana';
    } else {
        return 'En ' . $dias . ' días';
    }
}

function obtenerMesAbreviado($fecha) {
    $meses = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];
    $mes = date('n', strtotime($fecha)) - 1;
    return $meses[$mes];
}

?>
