<?php

    // IMPORTAMOS LAS DEPENDENCIAS NECESARIAS
    require_once __DIR__ . '/../../helpers/alert_helper.php';
    require_once __DIR__ . '/../../models/superAdmin/administradores.php';

    // CAPTURAMOS EN UNA VARIABLE EL METODO O SOLICITUD HECHA POR EL SERVIDOR
    $method = $_SERVER['REQUEST_METHOD'];

    switch($method){
        case 'POST':
            // SE VALIDA EL NAME Y EL INPIT DEL FORMUALRIO PARA HACER UNA FUNCION EN ESPECIFICO
            $accion = $_POST['accion'] ?? '';
            if($accion == 'actualizar'){
                actualizarAdministrador();
            }else{
                registrarAdministrador();
            }

            break;

        case 'GET':
            // SE VALIDA LOS BOTONES QUE VIENEN POR METODO GET (EDITAR O ELIMINAR)

            $accion = $_GET['accion'] ?? '';
            if($accion == 'eliminar'){
                eliminarAdministrador($_GET['id']);
            }

            // EDITAR INSTITUCION
            if(isset($_GET['id'])){
                mostrarAdministradoresId($_GET['id']);
            }else{
                mostrarAdministradores();
            }

            break;

    }

    // FUNCIONES
    function registrarAdministrador(){
        // CAPTURAMOS EN VARIBALES LOS DATOS ENVIADOS A TARAVEZ DEL METODO POST Y LOS NAME DE LOS CAMPOS
        $nombres = $_POST['nombres'] ?? '';
        $apellidos = $_POST['apellidos'] ?? '';
        $documento = $_POST['documento'] ?? '';
        $edad = $_POST['edad'] ?? '';
        $correo = $_POST['correo'] ?? '';
        $telefono = $_POST['telefono'] ?? '';
        $institucion = $_POST['institucion'] ?? '';

        // VALIDAMOS LOS CAMPOS OBLIGATORIOS
        if(empty($nombres) || empty($apellidos) || empty($documento) || empty($edad) || empty($edad) || empty($correo) || empty($telefono) || empty($institucion)){
            mostrarSweetAlert('error', 'Campos vacios', 'Por favor complete los campos');
            exit();
        }

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
            $destino = BASE_PATH . '/public/uploads/administradores/' . $ruta_img;
            // MOVEMOS EL ARCHIVO AL DESTINO
            move_uploaded_file($file['tmp_name'], $destino);   

        }else{
            // AGREGAR LA LOGICA DE LA IMAGEN POR DEFECTO
            $ruta_img = 'default.png';
        }

        // PROGRAMACION ORIENTADA A OBJETROS
        $objetoAdministrador = new Administrador();
        $data = [
            'nombres' => $nombres,
            'apellidos' => $apellidos,
            'documento' => $documento,
            'edad' => $edad,
            'correo' => $correo,
            'telefono' => $telefono,
            'institucion' => $institucion,
            'foto' => $ruta_img
        ];

        $resultado = $objetoAdministrador -> registrar($data);

        // MOSTRAMOS LOS SWEET ALERT
          if($resultado === true){
                mostrarSweetAlert('success', 'Registro de institución exitoso', 'Se ha creado una nueva institución. Redirigiendo...', '/siademy/superAdmin-panel-administradores');
            }else{
                mostrarSweetAlert('error', 'Error al registrar', 'No se pudo registrar la insitución, intente nuevamente. Redirigiendo...', '/siademy/superAdmin-agregar-administrador');
            }
            exit();
    }

    function mostrarAdministradores(){
        // INSTANCEAMOS LA CONEXION 
        $resultado = new Administrador();
        $administradores = $resultado -> listar();

        return $administradores;
    }

    function eliminarAdministrador($id){
        // INSTANCEAMOS LA CLASE
        $objetoAdministrador = new Administrador();
        $resultado = $objetoAdministrador -> eliminar($id);

         // MOSTRAMOS LOS SWEETALERT
            if($resultado === true){
                mostrarSweetAlert('success', 'Actualización de estado exitoso', 'Se ha cambiado el estado de la institucion a Inactivo. Redirigiendo...', '/siademy/superAdmin-panel-administradores');
            }else{
                mostrarSweetAlert('error', 'Error al actualizar', 'No se pudo actualizar el estado, intente nuevamente. Redirigiendo...', '/siademy/superAdmin-panel-administradores');
            }
            exit();
    }

    function mostrarAdministradoresID($id){
        // INSTANCEAMOS LA CONEXION
        $objetoAdministrador = new Administrador();
        $resultado = $objetoAdministrador -> listarAdministradorID($id);

        return $resultado;
    }

    function actualizarAdministrador(){
        // CAPTURAMOS EN VARIABLES LOS DATOS ENVIADOS A TARAVEZ DEL METODO POSY Y LOS NAME DE LOS CAMPOS
        $id = $_POST['id'] ?? '';
        $id_usuario = $_POST['id_usuario'] ?? '';
        $nombres = $_POST['nombres'] ?? '';
        $apellidos = $_POST['apellidos'] ?? '';
        $edad = $_POST['edad'] ?? '';
        $estado = $_POST['estado'] ?? '';
        $correo = $_POST['correo'] ?? '';
        $telefono = $_POST['telefono'] ?? '';

        // VALIDAMOS LOS CAMPOS OBLIGATORIOS
        if(empty($nombres) || empty($apellidos) || empty($edad) || empty($estado) || empty($correo) || empty($telefono)){
             mostrarSweetAlert('error', 'Campos vacios', 'Por favor complete todos los campos.');
            exit();
        }

        // PROGRAMACION ORIENTADA A OBJETOS
        // INSTANCEAMOS LA CLASE
        $objetoAdministrador = new Administrador();
        $data = [
            'id' => $id,
            'id_usuario' => $id_usuario,
            'nombres' => $nombres,
            'apellidos' => $apellidos,
            'edad' => $edad,
            'estado' => $estado,
            'correo' => $correo,
            'telefono' => $telefono
        ];

        $resultado = $objetoAdministrador -> actualizar($data);

        // MOSTRAMOS LOS SWEET ALERT
            if($resultado === true){
                mostrarSweetAlert('success', 'Modificacion de acudiente exitoso', 'Se ha modificado el  acudiente. Redirigiendo...', '/siademy/superAdmin-panel-administradores');
                exit();
            }else{
                mostrarSweetAlert('error', 'Error al modificar', 'No se pudo modificar el acudiente, intente nuevamente.  Redirigiendo...', '/siademy/superAdmin-panel-administradores');
                exit();
            }
            exit();
        
    }

?>