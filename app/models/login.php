<?php
// IMPORTAMOS LA CONEXION A LA BASE DE DATOS
require_once __DIR__ . '/../../config/database.php';

    class Login{
        // LLAMAMOS LA BASE DE DATOS
        private $conexion;

        public function __construct(){
            $db = new Conexion();
            $this->conexion = $db -> getConexion();
        }
        
        public function autenticar($correo,$clave){
            try{
                // DEFINIMOS EN UNA VARIABLE LA CONSULTA DE SQL SEGUN SEA EL CASO
                $consultar = "SELECT * FROM usuario WHERE correo = :correo AND estado = 'Activo' LIMIT 1";
                // PREPARAMOS LA ACCION A EJECUTAR Y LA EJECUTAMOS
                $resultado = $this->conexion->prepare($consultar);
                $resultado->bindParam(':correo', $correo);
                $resultado->execute();

                // OBTENEMOS EL USUARIO COMO ARREGLO ASOCIATIVO
                $user = $resultado->fetch(PDO::FETCH_ASSOC);

                // VALIDAMOS SI EL USUARIO NO EXISTE O ESTA INACTIVO
                if(!$user){
                    return['error' => 'Usuario no encontrado o inactivo'];
                }

                // USUARIO EXISTE, Y AHORA VERIFICAMOS LA CONTRASEÑA ENCRIPTADA
                if(!password_verify($clave, $user['clave'])){
                    return ['error' => 'Contraseña incorrecta'];
                }

                // RETORNAMOS LOS DATOS DEL USUARIO AUTENTICADO
                return[
                    'id' => $user['id'],
                    'rol' => $user['rol'],
                    'correo' => $user['correo']
                    
                ];
            }catch(PDOException $e){
                // CAPTURAMOS ERRORES Y LOS REGISTRAMOS EN EL LOG DEL SERVIDOR
                error_log("Error en el modelo Login: " . $e->getMessage());
                return ['error' => 'Error interno del servidor'];
            }
        }
    }

?>