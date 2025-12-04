<?php

    // IMPORTAMOS LAS DEPENDENCIAS NECESARIAS
    require_once BASE_PATH . '/app/helpers/pdf_helper.php';
    require_once BASE_PATH . '/app/controllers/superAdmin/instituciones.php';
    require_once BASE_PATH . '/app/controllers/superAdmin/administradores.php';
    require_once BASE_PATH . '/app/controllers/administrador/acudiente.php';
    require_once BASE_PATH . '/app/controllers/administrador/estudiante_controller.php';



   

   function mostrarReportes(){
    $tipo = $_GET['reporte'];
    
    switch($tipo){
        case 'instituciones':
            reportesInstitucionesPdf();
            break;
        
        case 'administradores':
            resportesAdministradoresPdf();
            break;

        case 'acudientes':
            resportesAcudientesPdf();
            break;

        case 'estudiantes':
            reportesEstudiantesPdf();
            break;
        
   }
   }

    function reportesInstitucionesPdf(){
        // CARGAR LA VISTA Y OBTENERLA COMO HTML
        ob_start();
        // ASIGNAMOS LOS DATOS DE LA FUNCION EN EL CONTROLADOR ENLAZADO A UNA VARIABLE QUE PODAMOS MANIPULAR EN LA VISTA DEL PDF
        $instituciones = mostrarInstituciones();

        // ARCHIVO QUE TIENE LA INTERFAZ DISEÑADA EN HTML
        require BASE_PATH . '/app/views/pdf/instituciones_pdf.php';
        $html = ob_get_clean();

        generarPDF($html, 'reporte_instituciones.pdf', false);
        
    }

    function resportesAdministradoresPdf(){
        // CARGAR LA VISTA Y IBTENERLA COMO HTML
        ob_start();
        // ASIGNAMOS LOS DATOS DE LA FUNCION EN EL CONTROLADOR ENLAZADO A UNA VARIABLE QUE PODAMOS MANIPULAR EN LA VISTA DEL PDF
        
        $administradores = mostrarAdministradores();

        // ARCHIVO QUE TIENE LA INTERFAZ DISEÑADA EN HTML
        require BASE_PATH . '/app/views/pdf/administradores_pdf.php';
        $html = ob_get_clean();

        generarPDF($html, 'reporte_administradores.pdf', false);
    }

    function resportesAcudientesPdf(){
        // CARGAR LA VISTA Y IBTENERLA COMO HTML
        ob_start();
        // ASIGNAMOS LOS DATOS DE LA FUNCION EN EL CONTROLADOR ENLAZADO A UNA VARIABLE QUE PODAMOS MANIPULAR EN LA VISTA DEL PDF
        
        $acudientes = mostrarAcudientes();

        // ARCHIVO QUE TIENE LA INTERFAZ DISEÑADA EN HTML
        require BASE_PATH . '/app/views/pdf/acudientes_pdf.php';
        $html = ob_get_clean();

        generarPDF($html, 'reporte_acudientes.pdf', false);
    }

    function reportesEstudiantesPdf(){
        // CARGAR LA VISTA Y IBTENERLA COMO HTML
        ob_start();
        // ASIGNAMOS LOS DATOS DE LA FUNCION EN EL CONTROLADOR ENLAZADO A UNA VARIABLE QUE PODAMOS MANIPULAR EN LA VISTA DEL PDF
        
        $estudiantes = mostrarEstudiantes();

        // ARCHIVO QUE TIENE LA INTERFAZ DISEÑADA EN HTML
        require BASE_PATH . '/app/views/pdf/estudiantes_pdf.php';
        $html = ob_get_clean();

        generarPDF($html, 'reporte_acudientes.pdf', false);
    }





?>