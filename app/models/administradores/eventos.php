<?php

    // IMPORTAMOS LAS DEPENDENCIAS NECESARIAS
    require_once __DIR__ . '/../../../config/database.php';

    class Evento{
        // LLAMAMOS LA BASE DE DATOS
        private $conexion;

        public function __construct(){
            $db = new Conexion();
            $this -> conexion = $db -> getConexion();
        }

        public function contar($id_institucion){
            try{
                $consultar = "SELECT COUNT(*) as total FROM eventos WHERE id_institucion = :id_institucion";
                $resultado = $this->conexion->prepare($consultar);
                $resultado->bindParam(':id_institucion', $id_institucion);
                $resultado->execute();
                $fila = $resultado->fetch();
                return $fila['total'] ?? 0;
            }catch(PDOException $e){
                error_log("Error en Evento::contar->" . $e->getMessage());
                return 0;
            }
        }

        public function listar($id_institucion){
            try{
                $consultar = "SELECT * FROM eventos WHERE id_institucion = :id_institucion ORDER BY fecha_evento DESC, hora_inicio ASC";
                $resultado = $this->conexion->prepare($consultar);
                $resultado->bindParam(':id_institucion', $id_institucion);
                $resultado->execute();
                return $resultado->fetchAll(PDO::FETCH_ASSOC);
            }catch(PDOException $e){
                error_log("Error en Evento::listar->" . $e->getMessage());
                return [];
            }
        }

        public function listarEventoId($id){
            try{
                $consultar = "SELECT * FROM eventos WHERE id = :id LIMIT 1";
                $resultado = $this->conexion->prepare($consultar);
                $resultado->bindParam(':id', $id);
                $resultado->execute();
                return $resultado->fetch(PDO::FETCH_ASSOC);
            }catch(PDOException $e){
                error_log("Error en Evento::listarEventoId->" . $e->getMessage());
                return null;
            }
        }

        public function registrar($data){
            try{
                $insertar = "INSERT INTO eventos(id_institucion, tipo_evento, nombre_evento, descripcion, fecha_evento, hora_inicio, hora_fin, ubicacion, grado, participantes_esperados, responsable, correo_contacto, requiere_confirmacion, materiales, notas_adicionales, enviar_notificacion) VALUES(:id_institucion, :tipo_evento, :nombre_evento, :descripcion, :fecha_evento, :hora_inicio, :hora_fin, :ubicacion, :grado, :participantes_esperados, :responsable, :correo_contacto, :requiere_confirmacion, :materiales, :notas_adicionales, :enviar_notificacion)";
                
                $resultado = $this->conexion->prepare($insertar);
                $resultado->bindParam(':id_institucion', $data['id_institucion']);
                $resultado->bindParam(':tipo_evento', $data['tipo_evento']);
                $resultado->bindParam(':nombre_evento', $data['nombre_evento']);
                $resultado->bindParam(':descripcion', $data['descripcion']);
                $resultado->bindParam(':fecha_evento', $data['fecha_evento']);
                $resultado->bindParam(':hora_inicio', $data['hora_inicio']);
                $resultado->bindParam(':hora_fin', $data['hora_fin']);
                $resultado->bindParam(':ubicacion', $data['ubicacion']);
                $resultado->bindParam(':grado', $data['grado']);
                $resultado->bindParam(':participantes_esperados', $data['participantes_esperados']);
                $resultado->bindParam(':responsable', $data['responsable']);
                $resultado->bindParam(':correo_contacto', $data['correo_contacto']);
                $resultado->bindParam(':requiere_confirmacion', $data['requiere_confirmacion']);
                $resultado->bindParam(':materiales', $data['materiales']);
                $resultado->bindParam(':notas_adicionales', $data['notas_adicionales']);
                $resultado->bindParam(':enviar_notificacion', $data['enviar_notificacion']);
                
                return $resultado->execute();
            }catch(PDOException $e){
                error_log("Error en Evento::registrar->" . $e->getMessage());
                return false;
            }
        }

        public function actualizar($data){
            try{
                $actualizar = "UPDATE eventos SET tipo_evento = :tipo_evento, nombre_evento = :nombre_evento, descripcion = :descripcion, fecha_evento = :fecha_evento, hora_inicio = :hora_inicio, hora_fin = :hora_fin, ubicacion = :ubicacion, grado = :grado, participantes_esperados = :participantes_esperados, responsable = :responsable, correo_contacto = :correo_contacto, requiere_confirmacion = :requiere_confirmacion, materiales = :materiales, notas_adicionales = :notas_adicionales, enviar_notificacion = :enviar_notificacion WHERE id = :id AND id_institucion = :id_institucion";
                
                $resultado = $this->conexion->prepare($actualizar);
                $resultado->bindParam(':id', $data['id']);
                $resultado->bindParam(':id_institucion', $data['id_institucion']);
                $resultado->bindParam(':tipo_evento', $data['tipo_evento']);
                $resultado->bindParam(':nombre_evento', $data['nombre_evento']);
                $resultado->bindParam(':descripcion', $data['descripcion']);
                $resultado->bindParam(':fecha_evento', $data['fecha_evento']);
                $resultado->bindParam(':hora_inicio', $data['hora_inicio']);
                $resultado->bindParam(':hora_fin', $data['hora_fin']);
                $resultado->bindParam(':ubicacion', $data['ubicacion']);
                $resultado->bindParam(':grado', $data['grado']);
                $resultado->bindParam(':participantes_esperados', $data['participantes_esperados']);
                $resultado->bindParam(':responsable', $data['responsable']);
                $resultado->bindParam(':correo_contacto', $data['correo_contacto']);
                $resultado->bindParam(':requiere_confirmacion', $data['requiere_confirmacion']);
                $resultado->bindParam(':materiales', $data['materiales']);
                $resultado->bindParam(':notas_adicionales', $data['notas_adicionales']);
                $resultado->bindParam(':enviar_notificacion', $data['enviar_notificacion']);
                
                return $resultado->execute();
            }catch(PDOException $e){
                error_log("Error en Evento::actualizar->" . $e->getMessage());
                return false;
            }
        }

        public function eliminar($id, $id_institucion){
            try{
                $eliminar = "DELETE FROM eventos WHERE id = :id AND id_institucion = :id_institucion";
                $resultado = $this->conexion->prepare($eliminar);
                $resultado->bindParam(':id', $id);
                $resultado->bindParam(':id_institucion', $id_institucion);
                return $resultado->execute();
            }catch(PDOException $e){
                error_log("Error en Evento::eliminar->" . $e->getMessage());
                return false;
            }
        }
    }

?>
