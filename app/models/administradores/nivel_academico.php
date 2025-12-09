<?php

    // IMPORTAMOS LAS DEPENDENCIAS NECESARIAS
    require_once __DIR__ . '/../../../config/database.php';

    class Nivel{

        // LLAMAMOS LA BASE DE DATOS
        private $conexion;

        public function __construct(){
            $db = new Conexion();
            $this -> conexion = $db -> getConexion();
        }

        public function listar(){
           
            try{
                 // DEFINIMOS EN UNA VARIABLE LA CONSULTA DE SQL PARA VER LOS NIVELES ACADEMICOS
            $consultar = "SELECT * FROM nivel_academico";

            // PREPARAMOS LA ACCION A EJECUTAR Y LA EJECUTAMOS
            $resultado = $this -> conexion -> prepare($consultar);
            $resultado -> execute();
            return $resultado -> fetchAll();
            
            }catch(PDOException $e){
                die("Error en Acudiente::actualizar->" . $e->getMessage());
                return [];
            }

        }
    }

?>