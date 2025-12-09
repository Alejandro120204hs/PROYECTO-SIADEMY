<?php

    //IMPORTAMOS LAS DEPENDECIAS NECESARIAS
    require __DIR__ . '/../../helpers/alert_helper.php';
    require __DIR__ . '/../../models/administradores/docente.php';

    //CAPTURAMOS EN UNA VARIABLE  LA SOLICITUD O METODO HECHA AL SERVIDOR 

    $method = $_SERVER['REQUEST_METHOD'];

    switch($method){
        case 'POST':
            // se valida si el formulario viene un input con name accion y value actualizar, sisi el formulario es de actualizar, si no es de registrar
            $accion = $_POST['accion'] ?? '';
            if($accion==='actualizar'){
                actualizarDocente();
            }else{
                registrarDocente();
            }
            break;

            case 'GET':
            // SE VALIDA LOS BOTONES LO QUE VIENE POR METODO GET PARA EDITAR O ELIMINAR
            $accion = $_GET['accion'] ?? '';
            // ELIMINAR ACUDIENTE
            if($accion === 'eliminar'){
               eliminarDocente($_GET['id']); 
            }

            // EDITAR ACUDIENTE
            if(isset($_GET['id'])){
                // LLENA EL FORMULARIO DE EDITAR
                mostrarDocenteId($_GET['id']);
            }else{
                // LLENA LA TABLA DE ACUDIENTES
                mostrarDocentes();
            }
            
            break;            


            
            default;
            http_response_code(405);
            echo"Metodo no permitido";
            break;
    }

        //FUNCIONES DEL CRUD
        function registrarDocente(){
            //capturamos en variables los datos enviados a traves del metodo post y los name de los campos

            $nombres = $_POST['nombres'] ?? '';
            $apellidos = $_POST['apellidos'] ?? '';
            $tipo_documento = $_POST['tipo_documento'] ?? '';
            $documento = $_POST['documento'] ?? '';
            $fecha_nacimiento = $_POST['fecha_nacimiento']?? '';
            $genero = $_POST['genero'] ?? '';
            $correo = $_POST['correo'] ?? '';
            $telefono = $_POST['telefono'] ?? '';
            $ciudad = $_POST['ciudad'] ?? '';
            $direccion = $_POST['direccion'] ?? '';
            $profesion = $_POST['profesion'] ?? '';
            $fecha_ingreso = $_POST['fecha_ingreso'] ?? '';
            $tipo_contrato = $_POST['tipo_contrato'] ?? '';
            $fecha_fin_contrato = $_POST['fecha_fin_contrato'] ?? '';

            //  VALIDAMOS LOS CAMPOS QUE SON OBLIGATORIOS
                    if(empty($nombres) || empty($apellidos) || empty($fecha_nacimiento) || empty($tipo_documento) || empty($documento) || empty($profesion) || empty($genero) || empty($correo) || empty($telefono) || empty($ciudad) || empty($direccion) || empty($fecha_ingreso) || empty($tipo_contrato) || empty($fecha_fin_contrato)){
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


            $ruta_img = null;
            //validamos si se envio o no la foto desde el formulario
            //si el usuario no registro una foto dejar una imagen por defecto

            if(!empty($_FILES['foto']['name'])){

                $file = $_FILES['foto'];

                //obtenemos la extension del archivo
                $extension = strtolower(pathinfo($file['name'],PATHINFO_EXTENSION));

                //definimos las extensiones permitidas
                $permitidas = ['png', 'jpg', 'jpeg'];

                //validamos si la extension de la imagen cargada este dentro del arreglo
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
                $destino = BASE_PATH . '/public/uploads/docentes/' . $ruta_img;
                // MOVEMOS EL ARCHIVO AL DESTINO
                move_uploaded_file($file['tmp_name'], $destino);   

            }else{
                // AGREGAR LA LOGICA DE LA IMAGEN POR DEFECTO
                $ruta_img = 'default.png';
            }

                $objetoDocente = new Docente();
                $data = [
                    'nombres' => $nombres,
                    'apellidos' => $apellidos,
                    'fecha_nacimiento' => $fecha_nacimiento,
                    'tipo_documento' => $tipo_documento,
                    'documento' => $documento,
                    'genero' => $genero,
                    'correo' => $correo,
                    'telefono' => $telefono,
                    'foto' => $ruta_img,
                    'id_institucion' => $id_institucion,
                    'ciudad' => $ciudad,
                    'direccion' => $direccion,
                    'profesion' => $profesion,
                    'fecha_ingreso' => $fecha_ingreso,
                    'tipo_contrato' => $tipo_contrato,
                    'fecha_fin_contrato' => $fecha_fin_contrato

                ];
            // ENVIAMOS LA DATA AL METODO "REGISTRAR" DE LA CLASE INSTANCEADA ANTERIORMENTE "ESTUDIANTE" Y ESPERAMOS UNA RESPUESTA BOOLEANA DEL MODELO EN RESULTADO
                    $resultado = $objetoDocente -> registrar($data);

                    // SI LA RESPUESTA DEL MODELO ES VERDADERA CONFIRMAMOS EL REGISTRO Y REDIRECCIONAMOS, SI ES FALSA NOTIFICAMOS Y REDIRECCIONAMOS
                    if($resultado === true){
                        mostrarSweetAlert('success', 'Registro de docente exitoso', 'Se ha creado un nuevo docente. Redirigiendo...', '/siademy/administrador-panel-profesores');
                        exit();
                    }else{
                        mostrarSweetAlert('error', 'Error al registrar', 'No se pudo registrar el docente, intente nuevamente.  Redirigiendo...', '/siademy/administrador-panel-docentes');
                        exit();
                    }
                    exit();
            



        }

            function mostrarDocentes(){   
                // VERIFICAMOS SI LA SESIÓN YA ESTÁ INICIADA
                if (session_status() === PHP_SESSION_NONE) {
                    session_start();
                }

                // CAPTURAMOS EL ID DE LA INSTITUCIÓN DEL ADMIN LOGUEADO
                $id_institucion = $_SESSION['user']['id_institucion'];

                // INSTANCEAMOS LA CLASE ACUDIENTE
                $resultado = new Docente();

                // LISTAMOS SOLO LOS ACUDIENTES DE ESA INSTITUCIÓN
                $docentes = $resultado->listar($id_institucion);

                return $docentes;
        }

            function mostrarDocenteId($id){
            // INSTANCEAMOS LA CLASE
                $objetoDocente = new Docente();
                $resultado = $objetoDocente -> listarId($id);

                return $resultado;
            }

           function actualizarDocente(){

            // CAPTURAMOS EN VARIABLES LOS DATOS ENVIADOS A TARAVEZ DEL METODO POST Y LOS NAME DE LOS CAMPOS
            $id = $_POST['id'] ?? '';
            $id_usuario = $_POST['id_usuario'] ?? '';
            $nombres = $_POST['nombres'] ?? '';
            $apellidos = $_POST['apellidos'] ?? '';
            $tipo_documento = $_POST['tipo_documento'] ?? '';
            $fecha_nacimiento = $_POST['fecha_nacimiento']?? '';
            $genero = $_POST['genero'] ?? '';
            $correo = $_POST['correo'] ?? '';
            $telefono = $_POST['telefono'] ?? '';
            $ciudad = $_POST['ciudad'] ?? '';
            $direccion = $_POST['direccion'] ?? '';
            $profesion = $_POST['profesion'] ?? '';
            $estado = $_POST['estado'] ?? '';
            $fecha_ingreso = $_POST['fecha_ingreso'] ?? '';
            $tipo_contrato = $_POST['tipo_contrato'] ?? '';
            $fecha_fin_contrato = $_POST['fecha_fin_contrato'] ?? '';


             //  VALIDAMOS LOS CAMPOS QUE SON OBLIGATORIOS
            if(empty($nombres) || empty($apellidos) || empty($fecha_nacimiento) || empty($tipo_documento) || empty($estado) || empty($profesion) || empty($genero) || empty($correo) || empty($telefono) || empty($ciudad) || empty($direccion) || empty($fecha_ingreso) || empty($tipo_contrato) || empty($fecha_fin_contrato)){
            mostrarSweetAlert('error', 'Campos vacios', 'Por favor complete todos los campos.');
            exit();
            }

            // PROGRAMACION ORIENTADA A OBJETOS
            // INSTANCEAMOS LA CLASE
            $objetoDocente = new Docente();
            $data = [
                'id' => $id,
                'id_usuario' => $id_usuario,
                'nombres' => $nombres,
                'apellidos' => $apellidos,
                'fecha_nacimiento' => $fecha_nacimiento,
                'tipo_documento' => $tipo_documento,
                'genero' => $genero,
                'correo' => $correo,
                'telefono' => $telefono,
                'ciudad' => $ciudad,
                'direccion' => $direccion,
                'profesion' => $profesion,
                'estado' => $estado,
                'fecha_ingreso' => $fecha_ingreso,
                'tipo_contrato' => $tipo_contrato,
                'fecha_fin_contrato' => $fecha_fin_contrato

            ];

            // ENVIAMOS LA DATA AL METODO DE ACTUALIZAR DE LA CLASE INSTANCEADA
            $resultado = $objetoDocente -> actualizar($data);

            // MENSAJES DE RESPUESTA
            if($resultado === true){
                mostrarSweetAlert('success', 'Modificacion de acudiente exitoso', 'Se ha modificado el  acudiente. Redirigiendo...', '/siademy/administrador-panel-profesores');
                exit();
            }else{
                mostrarSweetAlert('error', 'Error al modificar', 'No se pudo modificar el acudiente, intente nuevamente.  Redirigiendo...', '/siademy/administrador-panel-profesores');
                exit();
            }
            exit();


            
    }

    function eliminarDocente($id){
            // INSTANCEAMOS LA CLASE
            $objetoDocente = new Docente();
            $resultado = $objetoDocente -> eliminar($id);

            // MENSAJESDE RESPUESTA
            if($resultado === true){
                mostrarSweetAlert('success', 'Eliminación de docente exitosa', 'Se ha eliminado un docente. Redirigiendo...', '/siademy/administrador-panel-profesores');
                exit();
            }else{
                mostrarSweetAlert('error', 'Error al eliminar', 'No se pudo eliminar el docente, intente nuevamente.  Redirigiendo...', '/siademy/administrador-panel-profesores');
                exit();
            }
            }       
?>