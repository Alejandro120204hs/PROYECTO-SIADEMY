<?php

    // USAMOS UNA CLASE CON PROPIEDADES PRIVADAS PARA GUARDAR LAS CERDENCIALES DE
    // LA BASE DE DATOS (HOST, USUARIO, CONTRASEÑA Y NOMBRE DE LA BASE DE DATOS.
    
    // LO HACEMOS ASI PARA QUE NADIE FUERA DE LA CLASE PUEDA ACCEDER O MODIFICAR ESOS DATOS.

    class Conexion{
        private $host = "localhost";
        private $db = "siademy";
        private $user = "root";
        private $pass = "";
        private $conexion;
    // EL CONSTRUCTOR (__construct) SE EJECUTA AUTOMATICAMENTE CUANDO CREAMOS UN OBJETO DE LA CLASE Y SE ENCARGA DE ABRIR
    // LA CONEXION CON LA BASE DE DATOS USANDO PDO.
        public function __construct(){
    // LA PALABRA $this SIGNIFICA ÑITERALMENTE 'ESTA CLASE', LA USAMOS PARA ACCEDER A LAS VARIABLES INTERAS DE LA MISMA CLASE.
    // POR EJEMPLO, $this->conexion HACE REFERENCIA A LA CONEXION QUE PEERTENECE A ESTA INSTANCIA DE LA CLASE.
            try{
                $this->conexion = new PDO("mysql:host={$this->host};dbname={$this->db};charset=utf8", $this->user,$this->pass);
                $this->conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e){
                die("Error de conexion: " . $e->getMessage());
            }
    
        }
    // FINALMENTE, EL METODO getConexion() SIRVE PARA OBTENER ÑA CONEXION Y EN VEZ DE ABRIR UNA NUEVA
    // CONEXION CADA VEZ, SIMPLEMENTE PEDIMOS LA QUE YA EXISTE DENTRO DEL OBJETO.

        public function getConexion(){
            return $this->conexion;
        }
    }

    // EN RESUMEN:
    // LA CLASE GUARDA LAS CREDENCIALES DE FORMA SEGURA.
    // EL CONSTRUCTOR ABRE LA CONEXION AUTOMATICAMENTE.
    // $this PERMITE ACCEDER A LAS VARIABLES INTERNAS DE LA CLASE.
    // getConexion() NOS DEVUELVE LA CONEXION PARA EJECUTAR CONSULTAS.
    // DE ESTA FROMA EL CODIGO QUEDA MAS LIMPIO, REUTILIZABLE Y FACIL DE MANTENER.

?>