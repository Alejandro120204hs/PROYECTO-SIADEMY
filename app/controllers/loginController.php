<?php

    // IMPORTAMOS LAS DEPENDENCIAS NECESARIAS
    require_once __DIR__ . '/../models/login.php';

    // $clave = 'Alejo1202';
    // echo password_hash($clave, PASSWORD_DEFAULT);

    // EJECUTAR SEGUN LA SOILICTUD AL SERVIDOR "POST"  
    if($_SERVER['REQUEST_METHOD'] === 'POST'){

        // CAPTURAMOS EN VARIABLES LOS DATOS ENVIADOS A TRAVAEZ DEL METODO POST Y LOS NAME DE LOS CAMPOS
        $correo = $_POST['correo'] ?? '';
        $clave = $_POST['clave'] ?? '';

        // VALIDAMOS QUE LOS CAMPOS NO ESTEN VACIOS
        if(empty($correo) || empty($clave)){
            mostrarSweetAlert('error', 'Campos vacios', 'Por favor complete el formulario.');
            exit();
        }

        // PROGRAMACION ORIENTADA A OBJETOS
        // INSTANCEAMOS LA CLASE PARA ACCEDER A UNA FUNCION EN ESPECIFICO
        $login = new Login();
        $resultado = $login->autenticar($correo,$clave);

        // VERIFICAR SI EL MODELO DEVOLVIO UN ERROR
        if(isset($resultado['error'])){
            mostrarSweetAlert('error', 'Error de autenticacion', $resultado['error']);
            exit();
        }

        // SI PASA ESTA LINEA, EL USUARIO ES VALIDO
        session_start();
        $_SESSION['user']=[
            'id' => $resultado['id'],
            'rol' => $resultado['rol'],
            'nombres' => $resultado['nombres'],
            'apellidos' => $resultado['apellidos']
        ];

        mostrarSweetAlert('success', 'Bienvenido', 'Inicio de sesion exitoso. Redirigiendo...', '/siademy/superAdmin');
        exit();

    }else{
        http_response_code(405);
        echo"Método no permitido";
        exit();
    }

    /**
     * Función para imprimir SweetAlert dinámico con estilo SENA
     */
function mostrarSweetAlert($tipo, $titulo, $mensaje, $redirect = null) {
    echo "
    <html>
        <head>
            <meta charset='UTF-8'>
            <style>
                @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap');

                body {
                    margin: 0;
                    height: 100vh;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    background: linear-gradient(180deg, #0f1e4a 0%, #0b1736 100%);
                    font-family: 'Poppins', sans-serif;
                    color: #fff;
                }

                .swal2-popup {
                    font-family: 'Poppins', sans-serif !important;
                    background: #11193a !important;
                    border: 1px solid rgba(255, 255, 255, .08) !important;
                    border-radius: 18px !important;
                    box-shadow: 0 12px 32px rgba(0, 0, 0, .25) !important;
                }

                .swal2-title {
                    color: #e6e9f4 !important;
                    font-weight: 600 !important;
                    font-size: 24px !important;
                }

                .swal2-html-container {
                    color: #c8cede !important;
                    font-size: 15px !important;
                }

                .swal2-icon {
                    border-color: #4f46e5 !important;
                }

                .swal2-icon.swal2-success [class^='swal2-success-line'] {
                    background-color: #10b981 !important;
                }

                .swal2-icon.swal2-success .swal2-success-ring {
                    border-color: rgba(16, 185, 129, .3) !important;
                }

                .swal2-icon.swal2-error [class^='swal2-x-mark-line'] {
                    background-color: #ef4444 !important;
                }

                .swal2-icon.swal2-warning {
                    border-color: #f59e0b !important;
                    color: #f59e0b !important;
                }

                .swal2-icon.swal2-info {
                    border-color: #6366f1 !important;
                    color: #6366f1 !important;
                }

                .swal2-styled.swal2-confirm {
                    background: linear-gradient(135deg, #4f46e5, #6366f1) !important;
                    border: none !important;
                    border-radius: 12px !important;
                    padding: 12px 28px !important;
                    font-weight: 600 !important;
                    font-size: 14px !important;
                    box-shadow: 0 4px 12px rgba(79, 70, 229, .4) !important;
                    transition: all 0.2s ease !important;
                }

                .swal2-styled.swal2-confirm:hover {
                    background: linear-gradient(135deg, #6366f1, #7c3aed) !important;
                    transform: translateY(-2px) !important;
                    box-shadow: 0 6px 16px rgba(79, 70, 229, .5) !important;
                }

                .swal2-styled.swal2-cancel {
                    background: #0e142e !important;
                    border: 1px solid rgba(255, 255, 255, .08) !important;
                    border-radius: 12px !important;
                    padding: 12px 28px !important;
                    font-weight: 600 !important;
                    font-size: 14px !important;
                    color: #e6e9f4 !important;
                    transition: all 0.2s ease !important;
                }

                .swal2-styled.swal2-cancel:hover {
                    background: #1d2340 !important;
                    border-color: #4f46e5 !important;
                    transform: translateY(-2px) !important;
                }

                .swal2-actions {
                    gap: 12px !important;
                }
            </style>
            <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
        </head>
        <body>
            <script>
                Swal.fire({
                    icon: '$tipo',
                    title: '$titulo',
                    text: '$mensaje',
                    confirmButtonText: 'Aceptar',
                    background: '#11193a',
                    color: '#e6e9f4',
                    showClass: {
                        popup: 'swal2-show',
                        backdrop: 'swal2-backdrop-show',
                        icon: 'swal2-icon-show'
                    },
                    hideClass: {
                        popup: 'swal2-hide',
                        backdrop: 'swal2-backdrop-hide',
                        icon: 'swal2-icon-hide'
                    }
                }).then((result) => {
                    " . ($redirect ? "window.location.href = '$redirect';" : "window.history.back();") . "
                });
            </script>
        </body>
    </html>";
}

?>