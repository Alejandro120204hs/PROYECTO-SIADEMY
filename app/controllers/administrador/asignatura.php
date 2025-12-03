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
            mostrarSweetAlert('success', 'Registro de acudiente exitoso', 'Se ha creado un nuevo acudiente. Redirigiendo...', '/siademy/administrador-panel-acudientes');
            exit();
        }else{
            mostrarSweetAlert('error', 'Error al registrar', 'No se pudo registrar el acudiente, intente nuevamente.  Redirigiendo...', '/siademy/administrador-panel-acudientes');
            exit();
        }
        exit();






}

function mostrarAsignaturas(){

}

function mostrarAsignaturaId($id){

}

function actualizarAsignatura(){

}

function eliminarAsignatura($id){

}

?>