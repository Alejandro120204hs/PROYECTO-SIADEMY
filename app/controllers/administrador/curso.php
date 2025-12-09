<?php

    // IMPORTAMOS LAS DEPENDENCIAS NECESARIAS
       require_once __DIR__ . '/../../helpers/alert_helper.php';
       require_once __DIR__ . '/../../models/administradores/cursos.php';

       // CAPTURAMOS EN UNA VARIABLE EL METODO O SOLICITUD HECHA AL SERVIDOR
        $method = $_SERVER['REQUEST_METHOD'];

        switch($method){
              case 'POST':
            //  SE VALIDA SI DEL FORMULARIO VIENE UN INPUT CON NAME ACCION Y VALUE ACTUALIZAR, SI SI EL FORMULARIO ES DE ACTUALIZAR, SI NO ES DE REGISTRAR
            $accion = $_POST['accion'] ?? '';
            if($accion === 'actualizar'){
                actualizarCurso();
            }else{
                registrarCurso();
            }
            
            break;
        
        case 'GET':
            // SE VALIDA LOS BOTONES LO QUE VIENE POR METODO GET PARA EDITAR O ELIMINAR
            $accion = $_GET['accion'] ?? '';
            // ELIMINAR ACUDIENTE
            if($accion === 'eliminar'){
               eliminarCurso($_GET['id']); 
            }

            // EDITAR ACUDIENTE
            if(isset($_GET['id'])){
                // LLENA EL FORMULARIO DE EDITAR
                mostrarCursoId($_GET['id']);
            }else{
                // LLENA LA TABLA DE ACUDIENTES
                mostrarCursos();
            }
            
            break;

        default;
            http_response_code(405);
            echo"Metodo no permitido";
            break;
        }

        // FUNCIONES DEL CRUD
        function registrarCurso(){
            // CAPTURAMOS EN VARIBALES LOS DATOS ENVIADOS A TRAVEZ DEL METODO POST Y LOS NAME DE LOS CAMPOS
            $grado = $_POST['grado'] ?? '';
            $docente = $_POST['docente'] ?? '';
            $cupo = $_POST['cupo'] ?? '';
            $curso = $_POST['curso'] ?? '';
            $nivel = $_POST['nivel'] ?? '';
            $jornada = $_POST['jornada'] ?? '';

            // VALIDAMOS LOS CAMPOS OBLIGATORIOS
            if(empty($grado) || empty($cupo) || empty($curso) || empty($nivel) || empty($jornada)){
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

            // PROGRAMACION ORIENTADA A OBAJEROS
            $objetoCurso = new Curso();
            $data =[
                'grado' => $grado,
                'docente' => $docente,
                'cupo' => $cupo,
                'curso' => $curso,
                'nivel' => $nivel,
                'jornada' => $jornada,
                'id_institucion' => $id_institucion
            ];

            // ENVIAMOS LA DATA AL METODO "REGISTRAR" DE LA CLASE INSTANCEADA ANTERIORMENTE "ESTUDIANTE" Y ESPERAMOS UNA RESPUESTA BOOLEANA DEL MODELO EN RESULTADO
            $resultado = $objetoCurso -> registrar($data);

            // SI LA RESPUESTA DEL MODELO ES VERDADERA CONFIRMAMOS EL REGISTRO Y REDIRECCIONAMOS, SI ES FALSA NOTIFICAMOS Y REDIRECCIONAMOS
            if($resultado === true){
                mostrarSweetAlert('success', 'Registro de curso exitoso', 'Se ha creado un nuevo curso. Redirigiendo...', '/siademy/administrador-panel-cursos');
                exit();
            }else{
                mostrarSweetAlert('error', 'Error al registrar', 'No se pudo registrar el curso, intente nuevamente.  Redirigiendo...', '/siademy/administrador-panel-cursos');
                exit();
            }
            exit();

        }

        function mostrarCursos(){
            // VERIFICAMOS SI LA SESIÓN YA ESTÁ INICIADA
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }

            // CAPTURAMOS EL ID DE LA INSTITUCIÓN DEL ADMIN LOGUEADO
            $id_institucion = $_SESSION['user']['id_institucion'];

            // INSTANCEAMOS LA CLASE
            $objetoCurso = new Curso();

            // LISTAMOS LOS CURSOS DE LA INSTITUCION
            $cursos = $objetoCurso -> listar($id_institucion);

            return $cursos;


        }

        function mostrarCursoId($id){

        }

        function actualizarCurso(){

        }

        function eliminarCurso($id){

        }

        

?>