<?php

    // IMPORTAMOS LAS DEPENDENCIAS NECESARIAS
    require_once __DIR__ . '/../../../config/database.php';

    class Institucion{
        // LLAMAMOS LA BASE DE DATOS
        private $conexion;

        public function __construct()
        {
            $db = new Conexion();
            $this -> conexion = $db -> getConexion();
        }

        // CREAMOS LAS FUNCIONES PUBLICAS
        public function registrar($data){
            try{
                // DEFINIMOS EN UNA VARIABLE LA CONSULTA DE SQL DE REGISTRAR INSTITUCION
                $insertar = "INSERT INTO institucion(nombre,ciudad,direccion,telefono,correo,estado,tipo,logo) VALUES(:nombre,:ciudad,:direccion,:telefono,:correo,'Activo',:tipo,:logo)";

                // PREPARAMOS LA ACCION A EJECUTAR Y LA EJECUTAMOS
                $resultado = $this -> conexion -> prepare($insertar);
                $resultado -> bindParam(':nombre', $data['nombre']);
                $resultado -> bindParam(':ciudad', $data['ciudad']);
                $resultado -> bindParam(':direccion',$data['direccion']);
                $resultado -> bindParam(':telefono',$data['telefono']);
                $resultado -> bindParam(':correo',$data['correo']);
                $resultado -> bindParam(':tipo',$data['tipo']);
                $resultado -> bindParam(':logo',$data['logo']);
                
                return $resultado -> execute();

            }catch(PDOException $e){
                die("Error en Institucion::registrar->" . $e->getMessage());
                
            }
        }

        public function listar(){
            try{
                // DEFINIMOS EN UNA VARIABLE LA CONSULTA DE SQL PARA MOSTRAR LAS INSTITUCIONES
            $consultar = "SELECT * FROM institucion";

            // PREPARAMOS LA ACCION A EJECUTAR Y LA EJECUTAMOS
            $resultado = $this -> conexion -> prepare($consultar);
            $resultado -> execute();
            return $resultado -> fetchAll();
            }catch(PDOException $e){
                die("Error en Institucion::consultar->" . $e->getMessage());
                return [];
            }
        }

        public function eliminar($id){
            try{
                // DEFINIMOS EN UNA VARIABLE LA CONSULTA DE SQL PARA ACTUALIZAR EL ESTADO DE LA INSTITUCION
                $actualizar = "UPDATE institucion SET estado='Inactivo' WHERE id=:id";

                // PREPARAMOS LA ACCION A EJECUTAR Y LA EJECUTAMOS
                $resultado = $this -> conexion -> prepare($actualizar);
                $resultado -> bindParam(':id',$id);
                return $resultado -> execute();
            }catch(PDOException $e){
                die("Error en Institucion::consultar->" . $e->getMessage());

            }
        }

        public function listarInstitucionId($id){
            try{
                // DEFINIMOS EN UNA VARIABLE LA CONSULTA DE SQL PARA CONSULTAR LA INSTITUCION
                $consultar = "SELECT * FROM institucion WHERE id=:id";
                // PREPARAMOS LA ACCION A EJECUTAR Y LA EJECUTAMOS
                $resultado = $this -> conexion -> prepare($consultar);
                $resultado -> bindParam(':id',$id);
                $resultado -> execute();    
                return $resultado -> fetch();
            }catch(PDOException $e){
                die("Error en Institucion::consultar->" . $e->getMessage());
                return [];
            }
        }

        public function actualizar($data){
            try{
                // DEFINIMOS EN UNA VARIABLE LA CONSULTA DE SQL PARA ACTUALIAR LA INSTITUCION
                $actualizar = "UPDATE institucion SET nombre=:nombre, ciudad=:ciudad, direccion=:direccion, telefono=:telefono, correo=:correo,estado=:estado, tipo=:tipo WHERE id=:id";

                // PREPARAMOS LA ACCION A EJECUTAR Y LA EJECUTAMOS
                $resultado = $this -> conexion -> prepare($actualizar);
                $resultado -> bindParam(':id',$data['id']);
                $resultado -> bindParam(':nombre',$data['nombre']);
                $resultado -> bindParam(':ciudad',$data['ciudad']);
                $resultado -> bindParam(':direccion',$data['direccion']);
                $resultado -> bindParam('telefono',$data['telefono']);
                $resultado ->bindParam(':correo',$data['correo']);
                $resultado -> bindParam(':estado',$data['estado']);
                $resultado -> bindParam(':tipo',$data['tipo']);
                return $resultado -> execute();
            }catch(PDOException $e){
                die("Error en Institucion::editar->" . $e->getMessage());
                return false;
            }
        }

        private function obtenerColumnaFechaRegistro(){
            try {
                $resultado = $this->conexion->query("SHOW COLUMNS FROM institucion");
                $columnas = $resultado->fetchAll(PDO::FETCH_COLUMN, 0);

                $candidatas = ['fecha_registro', 'created_at', 'fecha_creacion', 'fecha'];
                foreach ($candidatas as $columna) {
                    if (in_array($columna, $columnas, true)) {
                        return $columna;
                    }
                }

                return null;
            } catch (PDOException $e) {
                error_log("Error en Institucion::obtenerColumnaFechaRegistro -> " . $e->getMessage());
                return null;
            }
        }

        private function obtenerConteoTotalInstituciones(){
            try {
                $resultado = $this->conexion->query("SELECT COUNT(*) FROM institucion");
                return (int) $resultado->fetchColumn();
            } catch (PDOException $e) {
                error_log("Error en Institucion::obtenerConteoTotalInstituciones -> " . $e->getMessage());
                return 0;
            }
        }

        private function obtenerSerieAcumuladaAnual($anio, $columnaFecha = null){
            $serieMensual = array_fill(1, 12, 0);

            if (!empty($columnaFecha)) {
                try {
                    $consulta = "SELECT MONTH($columnaFecha) AS mes, COUNT(*) AS total
                                 FROM institucion
                                 WHERE YEAR($columnaFecha) = :anio
                                 GROUP BY MONTH($columnaFecha)";
                    $resultado = $this->conexion->prepare($consulta);
                    $resultado->bindParam(':anio', $anio, PDO::PARAM_INT);
                    $resultado->execute();
                    $filas = $resultado->fetchAll(PDO::FETCH_ASSOC);

                    foreach ($filas as $fila) {
                        $mes = (int) $fila['mes'];
                        if ($mes >= 1 && $mes <= 12) {
                            $serieMensual[$mes] = (int) $fila['total'];
                        }
                    }
                } catch (PDOException $e) {
                    error_log("Error en Institucion::obtenerSerieAcumuladaAnual -> " . $e->getMessage());
                }
            } else {
                // Fallback: sin columna de fecha, solo se puede reflejar el total actual en el mes vigente.
                $mesActual = (int) date('n');
                $anioActual = (int) date('Y');
                if ((int) $anio === $anioActual) {
                    $serieMensual[$mesActual] = $this->obtenerConteoTotalInstituciones();
                }
            }

            $acumulado = [];
            $suma = 0;
            for ($mes = 1; $mes <= 12; $mes++) {
                $suma += $serieMensual[$mes];
                $acumulado[] = $suma;
            }

            return $acumulado;
        }

        public function obtenerMetricasDashboard(){
            try {
                $totales = [
                    'total' => 0,
                    'activas' => 0,
                    'inactivas' => 0
                ];

                $consulta = "SELECT
                                COUNT(*) AS total,
                                SUM(CASE WHEN estado = 'Activo' THEN 1 ELSE 0 END) AS activas,
                                SUM(CASE WHEN estado <> 'Activo' THEN 1 ELSE 0 END) AS inactivas
                             FROM institucion";
                $resultado = $this->conexion->query($consulta);
                $fila = $resultado->fetch(PDO::FETCH_ASSOC);

                if ($fila) {
                    $totales['total'] = (int) ($fila['total'] ?? 0);
                    $totales['activas'] = (int) ($fila['activas'] ?? 0);
                    $totales['inactivas'] = (int) ($fila['inactivas'] ?? 0);
                }

                $anioActual = (int) date('Y');
                $anioAnterior = $anioActual - 1;
                $columnaFecha = $this->obtenerColumnaFechaRegistro();

                $serieActual = $this->obtenerSerieAcumuladaAnual($anioActual, $columnaFecha);
                $serieAnterior = $this->obtenerSerieAcumuladaAnual($anioAnterior, $columnaFecha);

                $totalActual = (int) end($serieActual);
                $totalAnterior = (int) end($serieAnterior);
                $nuevasEsteAnio = max(0, $totalActual - $totalAnterior);
                $crecimiento = $totalAnterior > 0
                    ? round((($totalActual - $totalAnterior) / $totalAnterior) * 100, 1)
                    : ($totalActual > 0 ? 100.0 : 0.0);

                return [
                    'totales' => $totales,
                    'chart' => [
                        'labels' => ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'],
                        'anioActual' => $anioActual,
                        'anioAnterior' => $anioAnterior,
                        'serieActual' => $serieActual,
                        'serieAnterior' => $serieAnterior,
                        'totalActual' => $totalActual,
                        'totalAnterior' => $totalAnterior,
                        'nuevasEsteAnio' => $nuevasEsteAnio,
                        'crecimiento' => $crecimiento,
                        'usaColumnaFecha' => !empty($columnaFecha)
                    ]
                ];
            } catch (PDOException $e) {
                error_log("Error en Institucion::obtenerMetricasDashboard -> " . $e->getMessage());
                return [
                    'totales' => ['total' => 0, 'activas' => 0, 'inactivas' => 0],
                    'chart' => [
                        'labels' => ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'],
                        'anioActual' => (int) date('Y'),
                        'anioAnterior' => (int) date('Y') - 1,
                        'serieActual' => array_fill(0, 12, 0),
                        'serieAnterior' => array_fill(0, 12, 0),
                        'totalActual' => 0,
                        'totalAnterior' => 0,
                        'nuevasEsteAnio' => 0,
                        'crecimiento' => 0,
                        'usaColumnaFecha' => false
                    ]
                ];
            }
        }
    }

?>