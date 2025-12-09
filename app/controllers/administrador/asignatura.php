<?php

// importamos las dependencias necesarias, el helper y el modelo siempre para poder con el modelo mvc

require_once __DIR__ . '/../../helpers/alert_helper.php';
require_once __DIR__ . '/../../models/administradores/asignatura.php';


$method = $_SERVER['REQUEST_METHOD'];

switch($method){
    case 'POST':
            //  SE VALIDA SI DEL FORMULARIO VIENE UN INPUT CON NAME ACCION Y VALUE ACTUALIZAR, SI SI EL FORMULARIO ES DE ACTUALIZAR, SI NO ES DE REGISTRAR
            $accion = $_POST['accion'] ?? '';
            if($accion === 'actualizar'){
                actualizarAsignatura();
            }else{
                registrarAsignatura();
            }
            break;

    case 'GET':
            // SE VALIDA LOS BOTONES LO QUE VIENE POR METODO GET PARA EDITAR O ELIMINAR
            $accion = $_GET['accion'] ?? '';
            // ELIMINAR ACUDIENTE
            if($accion === 'eliminar'){
                eliminarAsignatura($_GET['id']);
            }

            // EDITAR ACUDINTE
            if(isset($_GET['id'])){
                // llena el formulario de editar
                mostrarAsignaturaId($_GET['id']);
            }else{
                // llena la tabla de acudientes
                mostrarAsignaturas();
            }
            break;
      
        default;
            http_response_code(405);
            echo"Metodo no permitido";
            break;            
}


// FUNCIONES DEL CRUD
function registrarAsignatura(){

        // CAPTURAMOS EN VARIABLES LOS DATOS ENVIADOSA  ATRAVEZ DEL METODO POST Y LOS NAME DE LOS CAMPOS
        $nombre = $_POST['nombre'] ?? '';
        $descripcion = $_POST['descripcion'] ?? '';

        // VALIDAMOS LOS CAMPOS QUE SON OBLIGATORIOS

        if(empty($nombre)){
            mostrarSweetAlert('error', 'Campo vacio', 'Por favor agregue un nombre.');
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
        $objetoAsignatura = new Asignatura();
        $data = [
            'nombre' => $nombre,
            'descripcion' => $descripcion,
            'id_institucion' => $id_institucion
        ];

        // ENVIAMOS LA DATA AL METODO "REGISTRAR" DE LA CLASE INSTANSEADA ANTERIORMENTE "Asignatura" Y ESPERAMOS UNA RESPUESTA BOOLEANA DEL MODELO EN EL RESULTADO

        $resultado = $objetoAsignatura -> registrar($data);

        // SI LA RESPUESTA DEL MODELO ES VERDADERA CONFIRMAMOS EL REGISTRO Y REDIRECCIONAMOS, SI ES FALSA NOTIFICAMOS Y REDIRECCIONAMOS
        if($resultado === true){
            mostrarSweetAlert('success', 'Registro de asignatura exitoso', 'Se ha creado una nueva asignatura. Redirigiendo...', '/siademy/administrador-panel-asignaturas');
            exit();
        }else{
            mostrarSweetAlert('error', 'Error al registrar', 'No se pudo registrar la asignatura, intente nuevamente.  Redirigiendo...', '/siademy/administrador/registrar-asignatura');
            exit();
        }
        exit();






}

function mostrarAsignaturas(){
    // VERIFICAMOS SI LA SESIÓN YA ESTÁ INICIADA
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // CAPTURAMOS EL ID DE LA INSTITUCIÓN DEL ADMIN LOGUEADO
    $id_institucion = $_SESSION['user']['id_institucion'];

    // INSTANCEAMOS LA CLASE ACUDIENTE
    $resultado = new Asignatura();

    // LISTAMOS SOLO LOS ACUDIENTES DE ESA INSTITUCIÓN
    $asignaturas = $resultado->listar($id_institucion);

    return $asignaturas;
}

function mostrarAsignaturaId($id){

    // INSTANCEAMOS LA CLASE
    $objetoAsignatura = new Asignatura();
    $asignatura = $objetoAsignatura -> listarAsignaturaId($id);

    return $asignatura;    

}

function actualizarAsignatura(){

    // CAPTURAMOS EN VARIABLES LOS DATOS ENVIADOS A TARAVEZ DEL METODO POST Y LOS NAME DE LOS CAMPOS

    $id = $_POST['id'] ?? '';
    $nombre = $_POST['nombre'] ?? '';
    $descripcion = $_POST['descripcion'] ?? '';
    $estado = $_POST['estado'] ?? '';

    echo $nombre;
    echo $descripcion;
    echo $nombre;


   
    // VALLIDAMOS LOS CAMPOS OBLIGATORIOS
    if(empty($nombre) || empty($estado)){
        mostrarSweetAlert('error', 'Campos vacios', 'Por favor complete los campos.');
        exit();
    }

    // PROGRAMACION ORIENTADA A OBJETOS
    // INSTANCEAMOS LA CLASE
    $objetoAsignatura = new Asignatura();
    $data = [
        'id' => $id,
        'nombre' => $nombre,
        'descripcion' => $descripcion,
        'estado' => $estado
    ];

        // ENVIAMOS LA DATA AL METODO DE ACTUALIZAR DE LA CLASE INSTANCEADA
        $resultado = $objetoAsignatura -> actualizar($data);

        // MENSAJES DE RESPUESTA
        if($resultado === true){
            mostrarSweetAlert('success', 'Modificacion de asignatura exitosa', 'Se ha modificado la asignatura. Redirigiendo...', '/siademy/administrador-panel-asignaturas');
            exit();
        }else{
            mostrarSweetAlert('error', 'Error al modificar', 'No se pudo modificar la asignatura, intente nuevamente.  Redirigiendo...', '/siademy/administrador-panel-asignaturas');
            exit();
        }
}

function eliminarAsignatura($id){

}

?>