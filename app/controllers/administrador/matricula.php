<?php

    // IMPORTAMOS LAS DEPENDENCIAS NECESARIAS
    require_once __DIR__ . '/../../helpers/alert_helper.php';
    require_once __DIR__ . '/../../models/administradores/matricula.php';

    // CAPTURAMOS EN UNA VARIABLE EL METODO O SOLICITUD HECHA AL SERVIDOR
    $method = $_SERVER['REQUEST_METHOD'];

    switch($method){
        case 'POST':
            // SE VALIDA SI DEL FORMULARIO VIENE UN INPUT CON NAME ACCION Y VALUE ACTUALIZAR
            $accion = $_POST['accion'] ?? '';
            if($accion === 'actualizar'){
                actualizarMatricula();
            }else{
                registrarMatricula();
            }
            break;
        
        case 'GET':
            // SE VALIDA LOS BOTONES LO QUE VIENE POR METODO GET PARA EDITAR O ELIMINAR
            $accion = $_GET['accion'] ?? '';
            
            // ELIMINAR MATRÍCULA
            if($accion === 'eliminar'){
                eliminarMatricula($_GET['id']); 
            }

            // EDITAR MATRÍCULA
            if(isset($_GET['id'])){
                // LLENA EL FORMULARIO DE EDITAR
                mostrarMatriculaId($_GET['id']);
            }else{
                // LLENA LA TABLA DE MATRÍCULAS
                mostrarMatriculas();
            }
            break;

        default:
            http_response_code(405);
            echo "Método no permitido";
            break;
    }

    // FUNCIONES DEL CRUD
    function registrarMatricula(){
        // CAPTURAMOS EN VARIABLES LOS DATOS ENVIADOS A TRAVÉS DEL MÉTODO POST
        $id_estudiante = $_POST['id_estudiante'] ?? '';
        $id_curso = $_POST['id_curso'] ?? '';
        $anio = $_POST['anio'] ?? date('Y');
        $fecha = $_POST['fecha'] ?? date('Y-m-d');

        // VALIDAMOS LOS CAMPOS OBLIGATORIOS
        if(empty($id_estudiante) || empty($id_curso)){
            mostrarSweetAlert('error', 'Campos vacíos', 'Por favor seleccione estudiante y curso.');
            exit();
        }

        // CAPTURAMOS EL ID DE LA INSTITUCIÓN DEL ADMIN LOGUEADO
        session_start();
        if(!isset($_SESSION['user']['id_institucion'])){
            mostrarSweetAlert('error', 'Error de sesión', 'No se encontró la institución del administrador.');
            exit();
        }
        $id_institucion = $_SESSION['user']['id_institucion'];

        // PROGRAMACIÓN ORIENTADA A OBJETOS
        $objetoMatricula = new Matricula();
        $data = [
            'id_institucion' => $id_institucion,
            'id_estudiante' => $id_estudiante,
            'id_curso' => $id_curso,
            'anio' => $anio,
            'fecha' => $fecha
        ];

        // ENVIAMOS LA DATA AL MÉTODO "REGISTRAR"
        $resultado = $objetoMatricula->registrar($data);

        // MENSAJES DE RESPUESTA
        if($resultado['success'] === true){
            mostrarSweetAlert('success', 'Matrícula exitosa', 'El estudiante ha sido matriculado correctamente. Redirigiendo...', '/siademy/administrador-panel-matriculas');
            exit();
        }else{
            mostrarSweetAlert('error', 'Error al matricular', $resultado['message'], '/siademy/administrador-registrar-matricula');
            exit();
        }
    }

    function mostrarMatriculas(){
        // VERIFICAMOS SI LA SESIÓN YA ESTÁ INICIADA
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // CAPTURAMOS EL ID DE LA INSTITUCIÓN DEL ADMIN LOGUEADO
        $id_institucion = $_SESSION['user']['id_institucion'];

        // INSTANCIAMOS LA CLASE
        $objetoMatricula = new Matricula();

        // LISTAMOS LAS MATRÍCULAS DE LA INSTITUCIÓN
        $matriculas = $objetoMatricula->listar($id_institucion);

        return $matriculas;
    }

    function mostrarMatriculaId($id){
        // INSTANCIAMOS LA CLASE
        $objetoMatricula = new Matricula();
        $resultado = $objetoMatricula->listarMatriculaId($id);

        return $resultado;    
    }

    function actualizarMatricula(){
        // CAPTURAMOS EN VARIABLES LOS DATOS ENVIADOS
        $id = $_POST['id'] ?? '';
        $id_estudiante = $_POST['id_estudiante'] ?? '';
        $id_curso = $_POST['id_curso'] ?? '';
        $anio = $_POST['anio'] ?? '';
        $fecha = $_POST['fecha'] ?? '';

        // VALIDAMOS LOS CAMPOS OBLIGATORIOS
        if(empty($id) || empty($id_estudiante) || empty($id_curso) || empty($anio)){
            mostrarSweetAlert('error', 'Campos vacíos', 'Por favor complete todos los campos.');
            exit();
        }

        // INSTANCIAMOS LA CLASE
        $objetoMatricula = new Matricula();
        $data = [
            'id' => $id,
            'id_estudiante' => $id_estudiante,
            'id_curso' => $id_curso,
            'anio' => $anio,
            'fecha' => $fecha
        ];

        $resultado = $objetoMatricula->actualizar($data);
        
        // MENSAJES DE RESPUESTA
        if($resultado['success'] === true){
            mostrarSweetAlert('success', 'Actualización exitosa', 'La matrícula ha sido actualizada. Redirigiendo...', '/siademy/administrador-panel-matriculas');
            exit();
        }else{
            mostrarSweetAlert('error', 'Error al actualizar', $resultado['message'], '/siademy/administrador-panel-matriculas');
            exit();
        }
    }

    function eliminarMatricula($id){
        // INSTANCIAMOS LA CLASE
        $objetoMatricula = new Matricula();
        $resultado = $objetoMatricula->eliminar($id);

        // MENSAJES DE RESPUESTA
        if($resultado === true){
            mostrarSweetAlert('success', 'Eliminación exitosa', 'La matrícula ha sido eliminada. Redirigiendo...', '/siademy/administrador-panel-matriculas');
            exit();
        }else{
            mostrarSweetAlert('error', 'Error al eliminar', 'No se pudo eliminar la matrícula, intente nuevamente. Redirigiendo...', '/siademy/administrador-panel-matriculas');
            exit();
        }
    }

?>
