<?php 

    // IMPORTAMOS LAS DEPENDENCIAS NECESARIAS
    require_once __DIR__ . '/../../helpers/alert_helper.php';
    require_once __DIR__ . '/../../models/administradores/eventos.php';

    function manejarSolicitudEventos(){
        $method = $_SERVER['REQUEST_METHOD'];

        switch($method){
            case 'POST':
                $accion = $_POST['accion'] ?? '';
                if($accion === 'actualizar'){
                    actualizarEvento();
                }else{
                    registrarEvento();
                }
                break;

            case 'GET':
                $accion = $_GET['accion'] ?? '';
                if($accion === 'eliminar' && isset($_GET['id'])){
                    eliminarEvento($_GET['id']);
                    return;
                }
                break;

            default:
                http_response_code(405);
                echo "Metodo no permitido";
                break;
        }
    }

    // FUNCIONES DEL CRUD
    function registrarEvento(){
        // CAPTURAMOS EN VARIABLES LOS DATOS ENVIADOS A TRAVÉS DEL MÉTODO POST
        $nombre_evento = $_POST['nombre_evento'] ?? '';
        $tipo_evento = $_POST['tipo_evento'] ?? '';
        $descripcion = $_POST['descripcion'] ?? '';
        $fecha_evento = $_POST['fecha_evento'] ?? '';
        $hora_inicio = $_POST['hora_inicio'] ?? '';
        $hora_fin = $_POST['hora_fin'] ?? '';
        $ubicacion = $_POST['ubicacion'] ?? '';
        $grado = $_POST['grado'] ?? '';
        $participantes_esperados = $_POST['participantes_esperados'] ?? '0';
        $responsable = $_POST['responsable'] ?? '';
        $correo_contacto = $_POST['correo_contacto'] ?? '';
        $requiere_confirmacion = isset($_POST['requiere_confirmacion']) ? '1' : '0';
        $materiales = $_POST['materiales'] ?? '';
        $notas_adicionales = $_POST['notas_adicionales'] ?? '';
        $enviar_notificacion = isset($_POST['enviar_notificacion']) ? '1' : '0';

        // VALIDAMOS LOS CAMPOS QUE SON OBLIGATORIOS
        if(empty($nombre_evento) || empty($tipo_evento) || empty($fecha_evento) || empty($hora_inicio) || empty($hora_fin) || empty($ubicacion) || empty($responsable) || empty($correo_contacto)){
            mostrarSweetAlert('error', 'Campos vacios', 'Por favor complete todos los campos obligatorios.');
            exit();
        }

         // CAPTURAMOS EL ID DE LA INSTITUCIÓN DEL ADMIN LOGUEADO
        if (session_status() === PHP_SESSION_NONE) { session_start(); }
        if(!isset($_SESSION['user']['id_institucion'])){
            mostrarSweetAlert('error', 'Error de sesión', 'No se encontró la institución del administrador.');
            exit();
        }
        $id_institucion = $_SESSION['user']['id_institucion'];

        $objetoEvento = new Evento();
        $data = [
            'nombre_evento' => $nombre_evento,
            'tipo_evento' => $tipo_evento,
            'descripcion' => $descripcion,
            'fecha_evento' => $fecha_evento,
            'hora_inicio' => $hora_inicio,
            'hora_fin' => $hora_fin,
            'ubicacion' => $ubicacion,
            'grado' => $grado,
            'participantes_esperados' => $participantes_esperados,
            'responsable' => $responsable,
            'correo_contacto' => $correo_contacto,
            'requiere_confirmacion' => $requiere_confirmacion,
            'materiales' => $materiales,
            'notas_adicionales' => $notas_adicionales,
            'enviar_notificacion' => $enviar_notificacion,
            'id_institucion' => $id_institucion
        ];

        // ENVIAMOS LA DATA AL METODO "REGISTRAR" DE LA CLASE INSTANCEADA
        $resultado = $objetoEvento->registrar($data);

        // SI LA RESPUESTA DEL MODELO ES VERDADERA CONFIRMAMOS EL REGISTRO Y REDIRECCIONAMOS
        if($resultado === true){
            mostrarSweetAlert('success', 'Evento registrado', 'El evento ha sido creado exitosamente. Redirigiendo...', BASE_URL . '/administrador-eventos');
            exit();
        }else{
            mostrarSweetAlert('error', 'Error al registrar', 'No se pudo registrar el evento, intente nuevamente. Redirigiendo...', BASE_URL . '/administrador-eventos');
            exit();
        }
    }

    function mostrarEventos(){   
        // VERIFICAMOS SI LA SESIÓN YA ESTÁ INICIADA
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // CAPTURAMOS EL ID DE LA INSTITUCIÓN DEL ADMIN LOGUEADO
        $id_institucion = $_SESSION['user']['id_institucion'];

        // INSTANCEAMOS LA CLASE EVENTO
        $resultado = new Evento();

        // LISTAMOS SOLO LOS EVENTOS DE ESA INSTITUCIÓN
        $eventos = $resultado->listar($id_institucion);

        return $eventos;
    }
      
    function mostrarEventoId($id){
        // INSTANCEAMOS LA CLASE
        $objetoEvento = new Evento();
        $evento = $objetoEvento->listarEventoId($id);

        return $evento;
    }
    
    function actualizarEvento(){
        // CAPTURAMOS EN VARIABLES LOS DATOS ENVIADOS 
        $id = $_POST['id'] ?? '';
        $nombre_evento = $_POST['nombre_evento'] ?? '';
        $tipo_evento = $_POST['tipo_evento'] ?? '';
        $descripcion = $_POST['descripcion'] ?? '';
        $fecha_evento = $_POST['fecha_evento'] ?? '';
        $hora_inicio = $_POST['hora_inicio'] ?? '';
        $hora_fin = $_POST['hora_fin'] ?? '';
        $ubicacion = $_POST['ubicacion'] ?? '';
        $grado = $_POST['grado'] ?? '';
        $participantes_esperados = $_POST['participantes_esperados'] ?? '0';
        $responsable = $_POST['responsable'] ?? '';
        $correo_contacto = $_POST['correo_contacto'] ?? '';
        $requiere_confirmacion = isset($_POST['requiere_confirmacion']) ? '1' : '0';
        $materiales = $_POST['materiales'] ?? '';
        $notas_adicionales = $_POST['notas_adicionales'] ?? '';
        $enviar_notificacion = isset($_POST['enviar_notificacion']) ? '1' : '0';

        // VALIDAMOS LOS CAMPOS OBLIGATORIOS
        if(empty($id) || empty($nombre_evento) || empty($tipo_evento) || empty($fecha_evento) || empty($hora_inicio) || empty($hora_fin) || empty($ubicacion) || empty($responsable) || empty($correo_contacto)){
            mostrarSweetAlert('error', 'Campos incompletos', 'Por favor complete todos los campos obligatorios.');
            exit();
        }

        // CAPTURAMOS LA INSTITUCIÓN DE LA SESIÓN
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $id_institucion = $_SESSION['user']['id_institucion'];

        // VERIFICAR QUE EL EVENTO PERTENECE A LA INSTITUCIÓN DEL ADMIN
        $objetoEvento = new Evento();
        $evento = $objetoEvento->listarEventoId($id);
        
        if(!$evento || $evento['id_institucion'] != $id_institucion){
            mostrarSweetAlert('error', 'No autorizado', 'No tienes permiso para editar este evento.');
            exit();
        }

        $data = [
            'id' => $id,
            'nombre_evento' => $nombre_evento,
            'tipo_evento' => $tipo_evento,
            'descripcion' => $descripcion,
            'fecha_evento' => $fecha_evento,
            'hora_inicio' => $hora_inicio,
            'hora_fin' => $hora_fin,
            'ubicacion' => $ubicacion,
            'grado' => $grado,
            'participantes_esperados' => $participantes_esperados,
            'responsable' => $responsable,
            'correo_contacto' => $correo_contacto,
            'requiere_confirmacion' => $requiere_confirmacion,
            'materiales' => $materiales,
            'notas_adicionales' => $notas_adicionales,
            'enviar_notificacion' => $enviar_notificacion,
            'id_institucion' => $id_institucion
        ];

        $resultado = $objetoEvento->actualizar($data);

        if($resultado === true){
            mostrarSweetAlert('success', 'Evento actualizado', 'El evento ha sido actualizado exitosamente. Redirigiendo...', BASE_URL . '/administrador-eventos');
            exit();
        }else{
            mostrarSweetAlert('error', 'Error al actualizar', 'No se pudo actualizar el evento, intente nuevamente. Redirigiendo...', BASE_URL . '/administrador-eventos');
            exit();
        }
    }

    function eliminarEvento($id){
        // VERIFICAMOS SESIÓN
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $id_institucion = $_SESSION['user']['id_institucion'];

        // VERIFICAR QUE EL EVENTO EXISTE Y PERTENECE A LA INSTITUCIÓN
        $objetoEvento = new Evento();
        $evento = $objetoEvento->listarEventoId($id);
        
        if(!$evento || $evento['id_institucion'] != $id_institucion){
            mostrarSweetAlert('error', 'No autorizado', 'No tienes permiso para eliminar este evento.');
            exit();
        }

        $resultado = $objetoEvento->eliminar($id, $id_institucion);

        if($resultado === true){
            mostrarSweetAlert('success', 'Evento eliminado', 'El evento ha sido eliminado exitosamente. Redirigiendo...', BASE_URL . '/administrador-eventos');
            exit();
        }else{
            mostrarSweetAlert('error', 'Error al eliminar', 'No se pudo eliminar el evento, intente nuevamente. Redirigiendo...', BASE_URL . '/administrador-eventos');
            exit();
        }
    }

?>
