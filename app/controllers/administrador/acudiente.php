<?php 

    // IMPORTAMOS LAS DEPENDENCIAS NECESARIAS
    require_once __DIR__ . '/../../helpers/alert_helper.php';
    require_once __DIR__ . '/../../models/administradores/acudiente.php';

    // CAPTURAMOS EN UNA VARIABLE EL METODO O SOLICITUD HECHA AL SERVIDOR
    $method = $_SERVER['REQUEST_METHOD'];

    switch($method){
        case 'POST':
            //  SE VALIDA SI DEL FORMULARIO VIENE UN INPUT CON NAME ACCION Y VALUE ACTUALIZAR, SI SI EL FORMULARIO ES DE ACTUALIZAR, SI NO ES DE REGISTRAR
            $accion = $_POST['accion'] ?? '';
            if($accion === 'actualizar'){
                actualizarAcudiente();
            }else{
                registrarAcudiente();
            }
            
            break;
        
        case 'GET':
            // SE VALIDA LOS BOTONES LO QUE VIENE POR METODO GET PARA EDITAR O ELIMINAR
            $accion = $_GET['accion'] ?? '';
            // ELIMINAR ACUDIENTE
            if($accion === 'eliminar'){
               eliminarAcudiente($_GET['id']); 
            }

            // EDITAR ACUDIENTE
            if(isset($_GET['id'])){
                // LLENA EL FORMULARIO DE EDITAR
                mostrarAcudienteId($_GET['id']);
            }else{
                // LLENA LA TABLA DE ACUDIENTES
                mostrarAcudientes();
            }
            
            break;

        // case 'PUT':
        //     actualizarAcudiente();
        //     break;

        // case 'DELETE':
        //     eliminarAcudiente();
        //     break;
        default;
            http_response_code(405);
            echo"Metodo no permitido";
            break;
    }

    // FUNCIONES DEL CRUD
    function registrarAcudiente(){
        // CAPTURAMOS EN VARIABLES LOS DATOS ENVIADOSA  ATRAVEZ DEL METODO POST Y LOS NAME DE LOS CAMPOS
        // $foto = $_POST['foto'] ?? '';
        $nombres= $_POST['nombres'] ?? '';
        $apellidos = $_POST['apellidos'] ?? '';
        $edad = $_POST['edad'] ?? '';
        $documento = $_POST['documento'] ?? '';
        $parentesco = $_POST['parentesco'] ?? '';
        $correo = $_POST['correo'] ?? '';
        $telefono = $_POST['telefono'] ?? '';
        
        


        // VALIDAMOS LOS CAMPOS QUE SON OBLIGATORIOS
        if(empty($nombres) || empty($apellidos) || empty($edad) || empty($documento) || empty($parentesco) || empty($correo) || empty($telefono)){
            mostrarSweetAlert('error', 'Campos vacios', 'Por favor complete todos los campos.');
            exit();
        }

         // CAPTURAMOS EL ID DE LA INSTITUCIÓN DEL ADMIN LOGUEADO
        session_start();
        if(!isset($_SESSION['user']['id_institucion'])){
            mostrarSweetAlert('error', 'Error de sesión', 'No se encontró la institución del administrador.');
            exit();
        }
        $id_institucion = $_SESSION['user']['id_institucion'];

        // CAPTURAMOS EL ID DEL USUARIO QUE INICIA SESION PARA GUARDARLO SOLO SI ES NECESARIO
        // session_start();
        // $id_coordinador = $_SESSION['user']['id'];

        // PROGRAMACION ORIENTADA A OBJETOS
        // INSTANCEAMOS LA CLASE

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
            $destino = BASE_PATH . '/public/uploads/acudientes/' . $ruta_img;
            // MOVEMOS EL ARCHIVO AL DESTINO
            move_uploaded_file($file['tmp_name'], $destino);   

        }else{
            // AGREGAR LA LOGICA DE LA IMAGEN POR DEFECTO
            $ruta_img = 'default.png';
        }

        $objetoAcudiente = new Acudiente();
        $data = [
            'nombres' => $nombres,
            'apellidos' => $apellidos,
            'edad' => $edad,
            'documento' => $documento,
            'parentesco' => $parentesco,
            'correo' => $correo,
            'telefono' => $telefono,
            'foto' => $ruta_img,
            'id_institucion' => $id_institucion
            // 'id_coordinador' => $id_coordinador
            
        ];

        // ENVIAMOS LA DATA AL METODO "REGISTRAR" DE LA CLASE INSTANCEADA ANTERIORMENTE "ESTUDIANTE" Y ESPERAMOS UNA RESPUESTA BOOLEANA DEL MODELO EN RESULTADO
        $resultado = $objetoAcudiente -> registrar($data);

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

    function mostrarAcudientes(){   
        // VERIFICAMOS SI LA SESIÓN YA ESTÁ INICIADA
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // CAPTURAMOS EL ID DE LA INSTITUCIÓN DEL ADMIN LOGUEADO
        $id_institucion = $_SESSION['user']['id_institucion'];

        // INSTANCEAMOS LA CLASE ACUDIENTE
        $resultado = new Acudiente();

        // LISTAMOS SOLO LOS ACUDIENTES DE ESA INSTITUCIÓN
        $acudientes = $resultado->listar($id_institucion);

        return $acudientes;
    }
      
    function mostrarAcudienteId($id){
        // INSTANCEAMOS LA CLASE
        $objetoAcudiente = new Acudiente();
        $acudiente = $objetoAcudiente -> listarAcudienteId($id);

        return $acudiente;
    }
    
    function actualizarAcudiente(){
        // CAPTURAMOS EN VARIABLES LOS DATOS ENVIADOS A TARAVEZ DEL METODO POST Y LOS NAME DE LOS CAMPOS
        $id = $_POST['id'] ?? '';
        $id_usuario = $_POST['id_usuario'] ?? '';
        $nombres = $_POST['nombres'] ?? '';
        $apellidos = $_POST['apellidos'] ?? '';
        $edad = $_POST['edad'] ?? '';
        $parentesco = $_POST['parentesco'] ?? '';
        $correo = $_POST['correo'] ?? '';
        $telefono = $_POST['telefono'] ?? '';
        $estado = $_POST['estado'] ?? '';

        // VALLIDAMOS LOS CAMPOS OBLIGATORIOS
        if(empty($nombres) || empty($apellidos) || empty($edad) || empty($parentesco) || empty($correo) || empty($telefono) || empty($estado)){
             mostrarSweetAlert('error', 'Campos vacios', 'Por favor complete todos los campos.');
            exit();
        }

        // PROGRAMACION ORIENTADA A OBJETOS
        // INSTANCEAMOS LA CLASE
        $objetoAcudiente = new Acudiente();
        $data = [
            'id' => $id,
            'id_usuario' => $id_usuario,
            'nombres' => $nombres,
            'apellidos' => $apellidos,
            'edad' => $edad,
            'parentesco' => $parentesco,
            'correo' => $correo,
            'telefono' => $telefono,
            'estado' => $estado
        ];

        // ENVIAMOS LA DATA AL METODO DE ACTUALIZAR DE LA CLASE INSTANCEADA
        $resultado = $objetoAcudiente -> acutalizar($data);

        // MENSAJES DE RESPUESTA
        if($resultado === true){
            mostrarSweetAlert('success', 'Modificacion de acudiente exitoso', 'Se ha modificado el  acudiente. Redirigiendo...', '/siademy/administrador-panel-acudientes');
            exit();
        }else{
            mostrarSweetAlert('error', 'Error al modificar', 'No se pudo modificar el acudiente, intente nuevamente.  Redirigiendo...', '/siademy/administrador-panel-acudientes');
            exit();
        }
        exit();
    }

    function eliminarAcudiente($id){
        // INSTANCEAMOS LA CLASE
        $objetoAcudiente = new Acudiente();
        $resultado = $objetoAcudiente -> eliminar($id);

         // MENSAJESDE RESPUESTA
        if($resultado === true){
            mostrarSweetAlert('success', 'Eliminación de acudiente exitosa', 'Se ha eliminado un acudiente. Redirigiendo...', '/siademy/administrador-panel-acudientes');
            exit();
        }else{
            mostrarSweetAlert('error', 'Error al eliminar', 'No se pudo eliminar el acudiente, intente nuevamente.  Redirigiendo...', '/siademy/administrador-panel-acudientes');
            exit();
        }
    }

?>