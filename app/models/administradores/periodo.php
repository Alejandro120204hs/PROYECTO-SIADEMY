<?php

    // IMPORTAMOS LAS DEPENDENCIAS NECESARIAS
    require_once __DIR__ . '/../../../config/database.php';

    class Periodo{
        // llamamos la base datos
        private $conexion;
        public function __construct(){
            $db = new Conexion;
            $this -> conexion = $db -> getConexion();
        }

        public function registrar($data){
            try{
                // INSERTAMOS DATOS EN LA TABLA periodos_academicos

                $insertar = "INSERT INTO periodos_academicos(institucion_id, nombre, tipo_periodo, numero_periodo, ano_lectivo, fecha_inicio, fecha_fin, activo, estado) 
                            VALUES (:institucion_id, :nombre, :tipo_periodo, :numero_periodo, :ano_lectivo, :fecha_inicio, :fecha_fin, :activo, :estado)";

                // PREPARAMOS LA ACCION A EJECUTAR Y LA EJECUTAMOS
                $resultado = $this->conexion->prepare($insertar);
                $resultado->bindParam(':institucion_id', $data['institucion_id']);
                $resultado->bindParam(':nombre', $data['nombre']);
                $resultado->bindParam(':tipo_periodo', $data['tipo_periodo']);
                $resultado->bindParam(':numero_periodo', $data['numero_periodo']);
                $resultado->bindParam(':ano_lectivo', $data['ano_lectivo']);
                $resultado->bindParam(':fecha_inicio', $data['fecha_inicio']);
                $resultado->bindParam(':fecha_fin', $data['fecha_fin']);
                $resultado->bindParam(':activo', $data['activo']);
                $resultado->bindParam(':estado', $data['estado']);

                return $resultado -> execute();

            }catch(PDOException $e){
                error_log("Error en Periodo::registrar->" . $e->getMessage());
                return false;
            }
        }


        public function listar($id_institucion, $ano_lectivo = null){
            try{
                // DEFINIMOS EN UNA VARIABLE LA CONSULTA DE SQL SEGUN SEA EL CASO
                if($ano_lectivo){
                    $consultar = "SELECT * FROM periodos_academicos 
                                WHERE institucion_id = :institucion_id AND ano_lectivo = :ano_lectivo 
                                ORDER BY numero_periodo ASC";
                } else {
                    $consultar = "SELECT * FROM periodos_academicos 
                                WHERE institucion_id = :institucion_id 
                                ORDER BY ano_lectivo DESC, numero_periodo ASC";
                }

                // PREPARAMOS LA ACCION A EJECUTAR Y LA EJECUTAMOS
                $resultado = $this->conexion->prepare($consultar);
                $resultado -> bindParam(':institucion_id', $id_institucion);
                if($ano_lectivo){
                    $resultado -> bindParam(':ano_lectivo', $ano_lectivo);
                }
                $resultado -> execute();
                return $resultado -> fetchAll();

            }catch(PDOException $e){
                error_log("Error en Periodo::listar->" . $e->getMessage());
                return[];
            }
        }

        public function listarPeriodoId($id){

            try{
                // DEFINIMOS EN UNA VARIABLE LA CONSULTA DE SQL SEGUN SEA EL CASO
                $consultar = "SELECT * FROM periodos_academicos WHERE id = :id LIMIT 1";

                // PREPARAR Y EJECUTAR
                $resultado = $this->conexion->prepare($consultar);
                $resultado->bindParam(':id', $id, PDO::PARAM_INT);
                $resultado->execute();

                return $resultado->fetch();

            }catch(PDOException $e){
                error_log("Error en Periodo::listarPeriodoId->" . $e->getMessage());
                return null;
            }
        }

        public function obtenerPeriodoActivo($id_institucion){
            try{
                $consultar = "SELECT * FROM periodos_academicos 
                            WHERE institucion_id = :institucion_id AND activo = 1 LIMIT 1";

                $resultado = $this->conexion->prepare($consultar);
                $resultado->bindParam(':institucion_id', $id_institucion);
                $resultado->execute();

                return $resultado->fetch();

            }catch(PDOException $e){
                error_log("Error en Periodo::obtenerPeriodoActivo->" . $e->getMessage());
                return null;
            }
        }

        public function actualizar($data){
            try{
                // DEFINIMOS EN UNA VARIABLE LA CONSULTA DE SQL SEGUN SEA EL CASO

                $actualizar = "UPDATE periodos_academicos SET nombre=:nombre, tipo_periodo=:tipo_periodo, numero_periodo=:numero_periodo, 
                              ano_lectivo=:ano_lectivo, fecha_inicio=:fecha_inicio, fecha_fin=:fecha_fin, estado=:estado 
                              WHERE id = :id";

                // PREPARAMOS LA ACCION A EJECUTAR Y LA EJECUTAMOS
                $resultado = $this->conexion->prepare($actualizar);
                $resultado->bindParam(':id',$data['id']);
                $resultado->bindParam(':nombre',$data['nombre']);
                $resultado->bindParam(':tipo_periodo',$data['tipo_periodo']);
                $resultado->bindParam(':numero_periodo',$data['numero_periodo']);
                $resultado->bindParam(':ano_lectivo',$data['ano_lectivo']);
                $resultado->bindParam(':fecha_inicio',$data['fecha_inicio']);
                $resultado->bindParam(':fecha_fin',$data['fecha_fin']);
                $resultado->bindParam(':estado',$data['estado']);

                $resultado -> execute();

                 if($resultado){
                    return true;
                }else{
                    return false;
                }

            }catch(PDOException $e){
                error_log("Error en Periodo::actualizar->" . $e->getMessage());
                return false;
            }
        }

        public function activar($id, $id_institucion){
            try{
                // PRIMERO DESACTIVA EL PERIODO ACTIVO ACTUAL
                $desactivar = "UPDATE periodos_academicos SET activo = 0, estado = 'finalizado' 
                             WHERE institucion_id = :institucion_id AND activo = 1";
                
                $resultado_desactivar = $this->conexion->prepare($desactivar);
                $resultado_desactivar->bindParam(':institucion_id', $id_institucion);
                $resultado_desactivar->execute();

                // LUEGO ACTIVA EL NUEVO PERIODO
                $activar = "UPDATE periodos_academicos SET activo = 1, estado = 'en_curso' WHERE id = :id";
                
                $resultado_activar = $this->conexion->prepare($activar);
                $resultado_activar->bindParam(':id', $id);
                
                return $resultado_activar->execute();

            }catch(PDOException $e){
                error_log("Error en Periodo::activar->" . $e->getMessage());
                return false;
            }
        }

        public function eliminar($id){
            try{
                // DEFINIMOS EN UNA VARIABLE LA CONSULTA DE SQL SEGUN SEA EL CASO
                $eliminar = "DELETE FROM periodos_academicos WHERE id = :id";

                // PREPARAMOS LA ACCION A EJECUTAR Y LA EJECUTAMOS
                $resultado = $this->conexion->prepare($eliminar);
                $resultado->bindParam(':id',$id);

                return $resultado -> execute();

            }catch(PDOException $e){
                error_log("Error en Periodo::eliminar->" . $e->getMessage());
                return false;
            }
        }

        public function obtenerKPIs($id_institucion){
            try{
                $kpis = [];

                // Total de periodos
                $total = "SELECT COUNT(*) as total FROM periodos_academicos WHERE institucion_id = :institucion_id";
                $res_total = $this->conexion->prepare($total);
                $res_total->bindParam(':institucion_id', $id_institucion);
                $res_total->execute();
                $kpis['total'] = $res_total->fetch()['total'];

                // Períodos activos
                $activos = "SELECT COUNT(*) as activos FROM periodos_academicos WHERE institucion_id = :institucion_id AND activo = 1";
                $res_activos = $this->conexion->prepare($activos);
                $res_activos->bindParam(':institucion_id', $id_institucion);
                $res_activos->execute();
                $kpis['activos'] = $res_activos->fetch()['activos'];

                // Períodos próximos (planificado)
                $proximos = "SELECT COUNT(*) as proximos FROM periodos_academicos WHERE institucion_id = :institucion_id AND estado = 'planificado'";
                $res_proximos = $this->conexion->prepare($proximos);
                $res_proximos->bindParam(':institucion_id', $id_institucion);
                $res_proximos->execute();
                $kpis['proximos'] = $res_proximos->fetch()['proximos'];

                // Períodos finalizados
                $finalizados = "SELECT COUNT(*) as finalizados FROM periodos_academicos WHERE institucion_id = :institucion_id AND estado = 'finalizado'";
                $res_finalizados = $this->conexion->prepare($finalizados);
                $res_finalizados->bindParam(':institucion_id', $id_institucion);
                $res_finalizados->execute();
                $kpis['finalizados'] = $res_finalizados->fetch()['finalizados'];

                return $kpis;

            }catch(PDOException $e){
                error_log("Error en Periodo::obtenerKPIs->" . $e->getMessage());
                return ['total' => 0, 'activos' => 0, 'proximos' => 0, 'finalizados' => 0];
            }
        }
    }
?>
