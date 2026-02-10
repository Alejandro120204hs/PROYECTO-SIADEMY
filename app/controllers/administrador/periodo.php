<?php

// importamos las dependencias necesarias, el helper y el modelo siempre para poder con el modelo mvc

require_once __DIR__ . '/../../helpers/alert_helper.php';
require_once __DIR__ . '/../../models/administradores/periodo.php';


$method = $_SERVER['REQUEST_METHOD'];

switch($method){
    case 'POST':
            //  SE VALIDA SI DEL FORMULARIO VIENE UN INPUT CON NAME ACCION Y VALUE ACTUALIZAR, SI SI EL FORMULARIO ES DE ACTUALIZAR, SI NO ES DE REGISTRAR
            $accion = $_POST['accion'] ?? '';
            if($accion === 'actualizar'){
                actualizarPeriodo();
            }else{
                registrarPeriodo();
            }
            break;

    case 'GET':
            // SE VALIDA LOS BOTONES LO QUE VIENE POR METODO GET PARA EDITAR O ELIMINAR
            $accion = $_GET['accion'] ?? '';
            
            // ELIMINAR PERIODO
            if($accion === 'eliminar'){
                eliminarPeriodo($_GET['id']);
            }
            
            // ACTIVAR PERIODO
            if($accion === 'activar'){
                activarPeriodo($_GET['id']);
            }

            // EDITAR PERIODO (RETORNA JSON)
            if($accion === 'editar' && isset($_GET['id'])){
                editarPeriodo($_GET['id']);
            }else if(!isset($_GET['accion'])){
                // Muestra la lista de periodos
                mostrarPeriodos();
            }
            break;
      
        default;
            http_response_code(405);
            echo"Metodo no permitido";
            break;            
}


// FUNCIONES DEL CRUD
function registrarPeriodo(){

        // CAPTURAMOS EN VARIABLES LOS DATOS ENVIADOS A TRAVÉS DEL METODO POST Y LOS NAME DE LOS CAMPOS
        $nombre = $_POST['nombre'] ?? '';
        $tipo_periodo = $_POST['tipo_periodo'] ?? '';
        $numero_periodo = $_POST['numero_periodo'] ?? '';
        $ano_lectivo = $_POST['ano_lectivo'] ?? '';
        $fecha_inicio = $_POST['fecha_inicio'] ?? '';
        $fecha_fin = $_POST['fecha_fin'] ?? '';
        $activo = isset($_POST['activo']) && $_POST['activo'] == 'on' ? 1 : 0;

        // VALIDAMOS LOS CAMPOS QUE SON OBLIGATORIOS
        if(empty($nombre) || empty($tipo_periodo) || empty($numero_periodo) || empty($ano_lectivo) || empty($fecha_inicio) || empty($fecha_fin)){
            mostrarSweetAlert('error', 'Campos vacíos', 'Por favor complete todos los campos requeridos.');
            exit();
        }

        // VALIDAR FECHAS
        if(strtotime($fecha_inicio) >= strtotime($fecha_fin)){
            mostrarSweetAlert('error', 'Fechas inválidas', 'La fecha de inicio debe ser menor a la fecha de fin.');
            exit();
        }

         // CAPTURAMOS EL ID DE LA INSTITUCIÓN DEL ADMIN LOGUEADO
        session_start();
        if(!isset($_SESSION['user']['id_institucion'])){
            mostrarSweetAlert('error', 'Error de sesión', 'No se encontró la institución del administrador.');
            exit();
        }
        $id_institucion = $_SESSION['user']['id_institucion'];
      
        // creamos un objeto con los datos traidos por el metodo post
        $objetoPeriodo = new Periodo();
        $data = [
            'nombre' => $nombre,
            'tipo_periodo' => $tipo_periodo,
            'numero_periodo' => $numero_periodo,
            'ano_lectivo' => $ano_lectivo,
            'fecha_inicio' => $fecha_inicio,
            'fecha_fin' => $fecha_fin,
            'activo' => $activo,
            'estado' => $activo == 1 ? 'en_curso' : 'planificado',
            'institucion_id' => $id_institucion
        ];

        // ENVIAMOS LA DATA AL METODO "REGISTRAR" DE LA CLASE INSTANSEADA ANTERIORMENTE "Periodo" Y ESPERAMOS UNA RESPUESTA BOOLEANA DEL MODELO EN EL RESULTADO

        $resultado = $objetoPeriodo -> registrar($data);

        // SI LA RESPUESTA DEL MODELO ES VERDADERA CONFIRMAMOS EL REGISTRO Y REDIRECCIONAMOS, SI ES FALSA NOTIFICAMOS Y REDIRECCIONAMOS
        if($resultado === true){
            mostrarSweetAlert('success', 'Registro exitoso', 'Se ha creado un nuevo periodo académico. Redirigiendo...', '/siademy/administrador-periodo');
            exit();
        }else{
            mostrarSweetAlert('error', 'Error al registrar', 'No se pudo registrar el período, intente nuevamente. Redirigiendo...', '/siademy/administrador-periodo');
            exit();
        }
}

function mostrarPeriodos(){
    // VERIFICAMOS SI LA SESIÓN YA ESTÁ INICIADA
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // VERIFICAMOS SI EL USUARIO TIENE LA VARIABLE DE SESIÓN SETEADA
    if(!isset($_SESSION['user'])){
        http_response_code(401);
        die('No autorizado');
    }

    $id_institucion = $_SESSION['user']['id_institucion'];
    $objetoPeriodo = new Periodo();
    $periodos = $objetoPeriodo->listar($id_institucion);

    // RETORNAMOS EN JSON
    header('Content-Type: application/json');
    echo json_encode($periodos);
}

function editarPeriodo($id){
    // VERIFICAMOS SI LA SESIÓN YA ESTÁ INICIADA
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    $objetoPeriodo = new Periodo();
    $periodo = $objetoPeriodo->listarPeriodoId($id);

    // RETORNAMOS EN JSON
    header('Content-Type: application/json');
    echo json_encode($periodo);
}

function actualizarPeriodo(){

        // CAPTURAMOS EN VARIABLES LOS DATOS ENVIADOS A TRAVÉS DEL METODO POST
        $id = $_POST['id'] ?? '';
        $nombre = $_POST['nombre'] ?? '';
        $tipo_periodo = $_POST['tipo_periodo'] ?? '';
        $numero_periodo = $_POST['numero_periodo'] ?? '';
        $ano_lectivo = $_POST['ano_lectivo'] ?? '';
        $fecha_inicio = $_POST['fecha_inicio'] ?? '';
        $fecha_fin = $_POST['fecha_fin'] ?? '';

        // VALIDAMOS LOS CAMPOS QUE SON OBLIGATORIOS
        if(empty($id) || empty($nombre) || empty($tipo_periodo) || empty($numero_periodo) || empty($ano_lectivo) || empty($fecha_inicio) || empty($fecha_fin)){
            mostrarSweetAlert('error', 'Campos vacíos', 'Por favor complete todos los campos requeridos.');
            exit();
        }

        // VALIDAR FECHAS
        if(strtotime($fecha_inicio) >= strtotime($fecha_fin)){
            mostrarSweetAlert('error', 'Fechas inválidas', 'La fecha de inicio debe ser menor a la fecha de fin.');
            exit();
        }

        // CAPTURAMOS EL ID DE LA INSTITUCIÓN DEL ADMIN LOGUEADO
        session_start();
        if(!isset($_SESSION['user']['id_institucion'])){
            mostrarSweetAlert('error', 'Error de sesión', 'No se encontró la institución del administrador.');
            exit();
        }
        $id_institucion = $_SESSION['user']['id_institucion'];
      
        // creamos un objeto con los datos traidos por el metodo post
        $objetoPeriodo = new Periodo();
        $data = [
            'id' => $id,
            'nombre' => $nombre,
            'tipo_periodo' => $tipo_periodo,
            'numero_periodo' => $numero_periodo,
            'ano_lectivo' => $ano_lectivo,
            'fecha_inicio' => $fecha_inicio,
            'fecha_fin' => $fecha_fin,
            'estado' => 'planificado'
        ];

        // ENVIAMOS LA DATA AL METODO "ACTUALIZAR" DE LA CLASE INSTANSEADA ANTERIORMENTE "Periodo"

        $resultado = $objetoPeriodo -> actualizar($data);

        // SI LA RESPUESTA DEL MODELO ES VERDADERA CONFIRMAMOS LA ACTUALIZACIÓN Y REDIRECCIONAMOS
        if($resultado === true){
            mostrarSweetAlert('success', 'Actualización exitosa', 'Se ha actualizado el período académico. Redirigiendo...', '/siademy/administrador-periodo');
            exit();
        }else{
            mostrarSweetAlert('error', 'Error al actualizar', 'No se pudo actualizar el período, intente nuevamente. Redirigiendo...', '/siademy/administrador-periodo');
            exit();
        }
}

function eliminarPeriodo($id){
    // VERIFICAMOS SI LA SESIÓN YA ESTÁ INICIADA
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // VERIFICAMOS SI EL USUARIO TIENE LA VARIABLE DE SESIÓN SETEADA
    if(!isset($_SESSION['user'])){
        http_response_code(401);
        mostrarSweetAlert('error', 'No autorizado', 'No tienes permiso para realizar esta acción.');
        exit();
    }

    $objetoPeriodo = new Periodo();
    $resultado = $objetoPeriodo->eliminar($id);

    // SI LA RESPUESTA DEL MODELO ES VERDADERA CONFIRMAMOS LA ELIMINACIÓN Y REDIRECCIONAMOS
    if($resultado === true){
        mostrarSweetAlert('success', 'Eliminación exitosa', 'Se ha eliminado el período académico. Redirigiendo...', '/siademy/administrador-periodo');
        exit();
    }else{
        mostrarSweetAlert('error', 'Error al eliminar', 'No se pudo eliminar el período, intente nuevamente. Redirigiendo...', '/siademy/administrador-periodo');
        exit();
    }
}

function activarPeriodo($id){
    // VERIFICAMOS SI LA SESIÓN YA ESTÁ INICIADA
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // VERIFICAMOS SI EL USUARIO TIENE LA VARIABLE DE SESIÓN SETEADA
    if(!isset($_SESSION['user'])){
        http_response_code(401);
        mostrarSweetAlert('error', 'No autorizado', 'No tienes permiso para realizar esta acción.');
        exit();
    }

    $id_institucion = $_SESSION['user']['id_institucion'];

    $objetoPeriodo = new Periodo();
    $resultado = $objetoPeriodo->activar($id, $id_institucion);

    // SI LA RESPUESTA DEL MODELO ES VERDADERA CONFIRMAMOS LA ACTIVACIÓN Y REDIRECCIONAMOS
    if($resultado === true){
        mostrarSweetAlert('success', 'Activación exitosa', 'Se ha activado el período académico. Redirigiendo...', '/siademy/administrador-periodo');
        exit();
    }else{
        mostrarSweetAlert('error', 'Error al activar', 'No se pudo activar el período, intente nuevamente. Redirigiendo...', '/siademy/administrador-periodo');
        exit();
    }
}

?>
