<?php 

    // IMPORTAMOS LAS DEPENDENCIAS NECESARIAS
    require_once __DIR__ . '/../helpers/alert_helper.php';
    require_once __DIR__ . '/../models/estudiante.php';

    // CAPTURAMOS EN UNA VARIABLE EL METODO O SOLICITUD HECHA AL SERVIDOR
    $method = $_SERVER['REQUEST_METHOD'];

    switch($method){
        case 'POST':
            registrarEstudiante();
            break;
        
        case 'GET':
            mostrarEstudiantes();
            break;

        case 'PUT':
            actualizarEstudiante();
            break;

        case 'DELETE':
            eliminarEstudiante();
            break;
        default;
            http_response_code(405);
            echo"Metodo no permitido";
            break;
    }

    // FUNCIONES DEL CRUD
    function registrarEstudiante(){
        // CAPTURAMOS EN VARIABLES LOS DATOS ENVIADOSA  ATRAVEZ DEL METODO POST Y LOS NAME DE LOS CAMPOS
        // $foto = $_POST['foto'] ?? '';
        $tipo_documento = $_POST['tipo_documento'] ?? '';
        $nombres = $_POST['nombres'] ?? '';
        $fecha_nacimiento = $_POST['fecha_nacimiento'] ?? '';
        $telefono = $_POST['telefono'] ?? '';
        $documento = $_POST['documento'] ?? '';
        $apellidos = $_POST['apellidos'] ?? '';
        $correo = $_POST['correo'] ?? '';
        $acudiente = $_POST['acudiente'] ?? '';
        


        // VALIDAMOS LOS CAMPOS QUE SON OBLIGATORIOS
        if(empty($tipo_documento) || empty($nombres) || empty($fecha_nacimiento) || empty($telefono) || empty($documento) || empty($apellidos) || empty($correo) || empty($acudiente)){
            mostrarSweetAlert('error', 'Campos vacios', 'Por favor complete todos los campos.');
            exit();
        }

        // CAPTURAMOS EL ID DEL USUARIO QUE INICIA SESION PARA GUARDARLO SOLO SI ES NECESARIO
        // session_start();
        // $id_coordinador = $_SESSION['user']['id'];

        // PROGRAMACION ORIENTADA A OBJETOS
        // INSTANCEAMOS LA CLASE
        $objetoEstudiante = new Estudiante();
        $data = [
            'tipo_documento' => $tipo_documento,
            'nombres' => $nombres,
            'fecha_nacimiento' => $fecha_nacimiento,
            'telefono' => $telefono,
            'documento' => $documento,
            'apellidos' => $apellidos,
            'correo' => $correo,
            'acudiente' => $acudiente
            // 'id_coordinador' => $id_coordinador
            
        ];

        // ENVIAMOS LA DATA AL METODO "REGISTRAR" DE LA CLASE INSTANCEADA ANTERIORMENTE "ESTUDIANTE" Y ESPERAMOS UNA RESPUESTA BOOLEANA DEL MODELO EN RESULTADO
        $resultado = $objetoEstudiante -> registrar($data);

        // SI LA RESPUESTA DEL MODELO ES VERDADERA CONFIRMAMOS EL REGISTRO Y REDIRECCIONAMOS, SI ES FALSA NOTIFICAMOS Y REDIRECCIONAMOS
        if($resultado === true){
            mostrarSweetAlert('success', 'Registro de estudiante exitoso', 'Se ha creado un nuevo estudiante. Redirigiendo...', '/siademy/coordinador/registrar-estudiante');
            exit();
        }else{
            mostrarSweetAlert('error', 'Error al registrar', 'No se pudo registrar el estudiante, intente nuevamente.  Redirigiendo...', '/siademy/coordinador/registrar-estudiante');
            exit();
        }
        exit();


    }

    function mostrarEstudiantes(){

    }   
    
    function actualizarEstudiante(){

    }

    function eliminarEstudiante(){

    }

?>