<?php

    // IMPORTAMOS LAS DEPENDENCIAS NECESARIAS
    require_once __DIR__ . '/../../helpers/alert_helper.php';
    require_once __DIR__ . '/../../models/superAdmin/institucion.php';

    // CAPTURAMOS EN UNA VARIABLE EL METODO O SOLICITUD HECHA POR EL SERVIDOR
    $method = $_SERVER['REQUEST_METHOD'];

    switch($method){
        case 'POST':
            // SE VALIDA EL NAME Y EL INPUT DEL FORMULARIO PARA HACER UNA FUNCION EN ESPECIFICO
            $accion = $_POST['accion'] ?? '';
            if($accion == 'actualizar'){
                actulizarInstitucion();
            }else{
                registrarInstitucion();
            }

            break;

        case 'GET':
            // SE VALIDA LOS BOTONES QUE VIENEN POR METODO GET (EDITAR O ELIMINAR)
            $accion = $_GET['accion'] ?? '';
            if($accion == 'eliminar'){
                eliminarInstitucion($_GET['id']);
            }

            // EDITAR INSTITUCION
            if(isset($_GET['id'])){
                // LLENA EL FORMULARIO DE EDITAR
                mostrarInstitucionId($_GET['id']);
            }else{
                // LLENA LA TABLA DE INSTITUCIONES
                mostrarInstituciones();
            }
        
    }

        function registrarInstitucion(){
            // CAPTURAMOS EN VARIABLES LOS DATOS ENVIAODS A TRAVEZ DEL METODO POST Y LOS NAME DE LOS CAMPOS
            $nombre = $_POST['nombre'] ?? '';
            $tipo = $_POST['tipo'] ?? '';
            $ciudad = $_POST['ciudad'] ?? '';
            $direccion = $_POST['direccion'] ?? '';
            $telefono = $_POST['telefono'] ?? '';
            $correo = $_POST['correo'] ?? '';

            // VALIDAMOS LOS CAMPOS QUE SON OBLIGATORIOS
            if(empty($nombre) || empty($tipo) || empty($ciudad) || empty($direccion) || empty($telefono) || empty($correo)){
                mostrarSweetAlert('error', 'Campos vacios', 'Por favor complete los campos');
                exit();
            }

            // LOGICA PARA CARGAR IMAGENES
            $ruta_img = null;
            // VALIDAMOS SI SE ENVIO O NO LA FOTO DESDE EL FORMULARIO
            // **************** SI EL USUARIO NO REGISTRO UNA FOTO DEJAR UNA IMAGEN POR DEFECTO

            if(!empty($_FILES['logo']['name'])){

                $file = $_FILES['logo'];
                
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
                $destino = BASE_PATH . '/public/uploads/instituciones/' . $ruta_img;
                // MOVEMOS EL ARCHIVO AL DESTINO
                move_uploaded_file($file['tmp_name'], $destino);   

                }else{
                    // AGREGAR LA LOGICA DE LA IMAGEN POR DEFECTO
                    $ruta_img = 'default.png';
                }

            // PROGRAMACION ORIENTADA A OBJETOS
            $objetoInstitucion = new Institucion();
            $data = [
                'nombre' => $nombre,
                'tipo' => $tipo,
                'ciudad' => $ciudad,
                'direccion' => $direccion,
                'telefono' => $telefono,
                'correo' => $correo,
                'logo' => $ruta_img
            ];

            // ENVIAMOS LA DATA AL METODO
            $resultado = $objetoInstitucion -> registrar($data);

            // MOSTRAMOS LOS SWEETALERT
            if($resultado === true){
                mostrarSweetAlert('success', 'Registro de institución exitoso', 'Se ha creado una nueva institución. Redirigiendo...', '/siademy/superAdmin-panel-instituciones');
            }else{
                mostrarSweetAlert('error', 'Error al registrar', 'No se pudo registrar la insitución, intente nuevamente. Redirigiendo...', '/siademy/superAdmin-panel-instituciones');
            }
            exit();

            
        }

        function mostrarInstituciones(){
            // INSTANCEAMOS LA CLASE
            $resultado = new Institucion();
            $institucion = $resultado -> listar();

            return $institucion;
        }

        function eliminarInstitucion($id){
            // INSTANCEAMOS LA CLASE
            $objetoInstitucion = new Institucion();
            $resultado = $objetoInstitucion -> eliminar($id);

            // MOSTRAMOS LOS SWEETALERT
            if($resultado === true){
                mostrarSweetAlert('success', 'Actualización de estado exitoso', 'Se ha cambiado el estado de la institucion a Inactivo. Redirigiendo...', '/siademy/superAdmin-panel-instituciones');
            }else{
                mostrarSweetAlert('error', 'Error al actualizar', 'No se pudo actualizar el estado, intente nuevamente. Redirigiendo...', '/siademy/superAdmin-panel-instituciones');
            }
            exit();
        }

        function mostrarInstitucionId($id){
            // INSTANCEAMOS LA CLASE
            $objetoInstitucion = new Institucion();
            $institucion = $objetoInstitucion -> listarInstitucionId($id);

            return $institucion;
        }

        function actulizarInstitucion(){
            // CAPTURAMOS EN VARIABLES LOS DATOS ENVIADOS A TRAVESZ DEL METODO POST Y LOS NAME DE LOS CAMPOS
            $id = $_POST['id'] ?? '';
            $nombre = $_POST['nombre'] ?? '';
            $tipo = $_POST['tipo'] ?? '';
            $jornada = $_POST['jornada'] ?? '';
            $estado = $_POST['estado'] ?? '';
            $direccion = $_POST['direccion'] ?? '';
            $telefono = $_POST['telefono'] ?? '';
            $correo = $_POST['correo'] ?? '';

            // VALIDAMOS LOS CAMPOS OBLIGATOTIOS
            if(empty($nombre) || empty($tipo) || empty($jornada) || empty($jornada) || empty($estado) || empty($direccion) || empty($telefono) || empty($correo)){
                mostrarSweetAlert('error', 'Campos vacios', 'Por favor complete todos los campos.');
                exit();
            }

            // PROGRAMACION ORIENTADA A OBJETOS
            // INSTANCEAMOS LA CLASE
            $objetoInstitucion = new Institucion();
            $data = [
                'id' => $id,
                'nombre' => $nombre,
                'tipo' => $tipo,
                'jornada' => $jornada,
                'estado' => $estado,
                'direccion' => $direccion,
                'telefono' => $telefono,
                'correo' => $correo
            ];

            $resultado = $objetoInstitucion -> actualizar($data);

            // MOSTRAMOS LOS SWEET ALERT
            if($resultado === true){
            mostrarSweetAlert('success', 'Modificacion de acudiente exitoso', 'Se ha modificado el  acudiente. Redirigiendo...', '/siademy/superAdmin-panel-instituciones');
            exit();
            }else{
            mostrarSweetAlert('error', 'Error al modificar', 'No se pudo modificar el acudiente, intente nuevamente.  Redirigiendo...', '/siademy/superAdmin-panel-instituciones');
            exit();
            }
        exit();
        }

?>