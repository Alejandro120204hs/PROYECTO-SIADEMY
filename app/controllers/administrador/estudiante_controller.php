<?php 

    // IMPORTAMOS LAS DEPENDENCIAS NECESARIAS
    require_once __DIR__ . '/../../helpers/alert_helper.php';
    require_once __DIR__ . '/../../models/administradores/estudiante.php';

    // CAPTURAMOS EN UNA VARIABLE EL METODO O SOLICITUD HECHA AL SERVIDOR
    $method = $_SERVER['REQUEST_METHOD'];

    switch($method){
        case 'POST':
            $accion = $_POST['accion'] ?? '';
            if($accion == 'actualizar'){
                actualizarEstudiante();
            }else{
                registrarEstudiante();
            }
            
            break;
        
        case 'GET':
            $accion = $_GET['accion'] ?? '';
            if($accion == 'eliminar'){
                 eliminarEstudiante($_GET['id']);
            }

            if(isset($_GET['id'])){
                // SE LLENA EL FORMULARIO DE EDITAR
                mostrarEstudianteId($_GET['id']);
            }else{
                 mostrarEstudiantes();
            }
           
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
        $genero = $_POST['genero'] ?? '';
        $ciudad = $_POST['ciudad'] ?? '';
        $direccion = $_POST['direccion'] ?? '';


        


        // VALIDAMOS LOS CAMPOS QUE SON OBLIGATORIOS
        if(empty($tipo_documento) || empty($nombres) || empty($fecha_nacimiento) || empty($telefono) || empty($documento) || empty($apellidos) || empty($correo) || empty($acudiente) || empty($genero) || empty($ciudad) || empty($direccion)){
            mostrarSweetAlert('error', 'Campos vacios', 'Por favor complete todos los campos.');
            exit();
        }

        // CAPTURAMOS EL ID DE LA INSTITUCION DEL ADMIN QUE INICIO SESION
        session_start();
        if(!isset($_SESSION['user']['id_institucion'])){
            mostrarSweetAlert('error', 'Error de sesión', 'No se encontró la institución del administrador.');
            exit();
        }
        $id_institucion = $_SESSION['user']['id_institucion'];
        // CAPTURAMOS EL ID DEL USUARIO QUE INICIA SESION PARA GUARDARLO SOLO SI ES NECESARIO
        // session_start();
        // $id_coordinador = $_SESSION['user']['id'];

        // LOGICA PARA CARGAR IMAGENES
        $ruta_img = null;
        // VALIDAMOS SI SE ENVIO O NO LA FOTO DESDE EL FORMULARIO
        // **************** SI EL USUARIO NO REGISTRO UNA FOTO DEJAR UNA IMAGEN POR DEFECTO

        if(!empty($_FILES['foto']['name'])){

            $file = $_FILES['foto'];
            
            // OBTENEMOS LA EXTENSION DEL ARCHIVO
            $extension = strtolower(pathinfo($file['name'],PATHINFO_EXTENSION));

            // DEFINIMOS LAS EXTENSIONES PERMITIDAS
            $permitidas = ['png', 'jpg', 'jpeg'];

            // VALIDAMOS SI LA EXTENSION DE LA IMGAEN CARGADA ESTE DENTRO DEL ARREGLO
            if(!in_array($extension, $permitidas)){
                mostrarSweetAlert('error', 'Extension no permitidad', 'Cargue una extension permitida (jpg, png, jpeg).');
                exit();
            }
            
            // VALIDAMOS EL TAMAÑO O PESO MAX 2MB
            if($file['size'] > 2 * 1024 * 1024){
                mostrarSweetAlert('error', 'Error al cargar la foto', 'El peso de la foto es superior a 2MB.');
                exit();
            }
            // DEFINIMOS EL NOMBRE DEL ARCHIVO Y LE CONCATENAMOS LA EXTENSION
            $ruta_img = uniqid('user_') . '.' . $extension;
            // DEFINIMOS EL DESTINO DONDE MOVEREMOS EL ARCHIVO
            $destino = BASE_PATH . '/public/uploads/estudiantes/' . $ruta_img;
            // MOVEMOS EL ARCHIVO AL DESTINO
            move_uploaded_file($file['tmp_name'], $destino);   

        }else{
            // AGREGAR LA LOGICA DE LA IMAGEN POR DEFECTO
            $ruta_img = 'default.png';
        }

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
            'acudiente' => $acudiente,
            'foto' => $ruta_img,
            'id_institucion' => $id_institucion,
            'genero' => $genero,
            'ciudad' => $ciudad,
            'direccion' => $direccion


            // 'id_coordinador' => $id_coordinador
            
        ];

        // ENVIAMOS LA DATA AL METODO "REGISTRAR" DE LA CLASE INSTANCEADA ANTERIORMENTE "ESTUDIANTE" Y ESPERAMOS UNA RESPUESTA BOOLEANA DEL MODELO EN RESULTADO
        $resultado = $objetoEstudiante -> registrar($data);

        // SI LA RESPUESTA DEL MODELO ES VERDADERA CONFIRMAMOS EL REGISTRO Y REDIRECCIONAMOS, SI ES FALSA NOTIFICAMOS Y REDIRECCIONAMOS
        if($resultado === true){
            mostrarSweetAlert('success', 'Registro de estudiante exitoso', 'Se ha creado un nuevo estudiante. Redirigiendo...', '/siademy/administrador/registrar-estudiante');
            exit();
        }else{
            mostrarSweetAlert('error', 'Error al registrar', 'No se pudo registrar el estudiante, intente nuevamente.  Redirigiendo...', '/siademy/administrador/registrar-estudiante');
            exit();
        }
        exit();


    }

    function mostrarEstudiantes(){
        // VERIFICAMOS SI LA SESIÓN YA ESTÁ INICIADA
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // CAPTURAMOS EL ID DE LA INSTITUCION DEL ADMIN LOGUEADO
        $id_institucion = $_SESSION['user']['id_institucion'];

        // INSTANCEAMOS LA CLASE
        $objetoEstudiante = new Estudiante();
        $resultado = $objetoEstudiante->listar($id_institucion);
        return $resultado;
    }
    
    function mostrarEstudianteId($id){
        // INSTANCEAMOS LA CLASE
        $objetoEstudiante = new Estudiante();
        $resultado = $objetoEstudiante -> listarId($id);

        return $resultado;
    }
    
    function actualizarEstudiante(){
        // CAPTURAMOS EN VARIABLES LOS DATOS ENVIADOS A TRAVEZ DEL METODO POST Y LOS NAME DE LOS CAMPOS
        $id = $_POST['id'] ?? '';
        $id_usuario = $_POST['id_usuario'] ?? '';
        $nombres = $_POST['nombres'] ?? '';
        $tipo_documento = $_POST['tipo_documento'] ?? '';
        $fecha_nacimiento = $_POST['fecha_nacimiento'] ?? '';
        $estado = $_POST['estado'] ?? '';
        $apellidos = $_POST['apellidos'] ?? '';
        $documento = $_POST['documento'] ?? '';
        $genero = $_POST['genero'] ?? '';
        $correo = $_POST['correo'] ?? '';
        $ciudad = $_POST['ciudad'] ?? '';
        $acudiente = $_POST['acudiente'] ?? '';
        $telefono = $_POST['telefono'] ?? '';
        $direccion = $_POST['direccion'] ?? '';

        // VALIDAMOS LOS CAMPOS OBLIGATORIOS
        if(empty($nombres) || empty($tipo_documento) || empty($fecha_nacimiento) || empty($estado) || empty($apellidos) || empty($documento) || empty($genero) || empty($correo) || empty($ciudad) || empty($acudiente) || empty($telefono) || empty($direccion)){
            mostrarSweetAlert('error', 'Campos vacios', 'Por favor complete todos los campos.');
            exit();
        }

        // PROGRAMACION ORIENTADA A OBJETOS
        // ACCEDEMOS A LA CLASE
        $objetoEstudiante = new Estudiante();
        $data = [
            'id' => $id,
            'id_usuario' => $id_usuario,
            'nombres' => $nombres,
            'tipo_documento' => $tipo_documento,
            'fecha_nacimiento' => $fecha_nacimiento,
            'estado' => $estado,
            'apellidos' => $apellidos,
            'documento' => $documento,
            'genero' => $genero,
            'correo' => $correo,
            'ciudad' => $ciudad,
            'acudiente' => $acudiente,
            'telefono' => $telefono,
            'direccion' => $direccion
        ];

        // ENVIAMOS LA DATA AL OBJETO DE ACTUALIZAR
        $resultado = $objetoEstudiante -> actulizar($data);

        // MENSAJES DE RESPUESTA
        if($resultado === true){
            mostrarSweetAlert('success', 'Modificacion de estudiante exitoso', 'Se ha modificado el  estudiante. Redirigiendo...', '/siademy/administrador-panel-estudiantes');
            exit();
        }else{
            mostrarSweetAlert('error', 'Error al modificar', 'No se pudo modificar el estudiante, intente nuevamente.  Redirigiendo...', '/siademy/administrador-panel-estudiantes');
            exit();
        }
        exit();
    }

    function eliminarEstudiante($id){
        // INSTANCEAMOS LA CLASE
        $objetoEstudiante = new Estudiante();
        $resultado = $objetoEstudiante -> eliminar($id);

          // MENSAJESDE RESPUESTA
        if($resultado === true){
            mostrarSweetAlert('success', 'Eliminación de estudiante exitosa', 'Se ha eliminado un estudianre. Redirigiendo...', '/siademy/administrador-panel-estudiantes');
            exit();
        }else{
            mostrarSweetAlert('error', 'Error al eliminar', 'No se pudo eliminar el estudiante, intente nuevamente.  Redirigiendo...', '/siademy/administrador-panel-estudiantes');
            exit();
        }
    }

?>