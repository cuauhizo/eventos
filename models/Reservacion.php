<?php

class Reservacion {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function iniciarGrupoReservas() {
        $sql = "INSERT INTO grupos_reservas (qr_code) VALUES (NULL)"; // qr_code puede seguir siendo NULL inicialmente
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            return $this->pdo->lastInsertId();
        } catch (PDOException $e) {
            error_log("Error al iniciar grupo de reservas: " . $e->getMessage());
            return false;
        }
    }
    
    public function existeReserva($id_usuario, $id_evento) {
        // Verifica si ya hay una reserva (pendiente o confirmada) para el usuario y evento
        $sql = "SELECT COUNT(*) FROM reservaciones WHERE id_usuario = ? AND id_evento = ? AND estado IN ('pendiente', 'confirmada')";
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$id_usuario, $id_evento]);
            return $stmt->fetchColumn() > 0;
        } catch (PDOException $e) {
            error_log("Error al verificar reserva existente: " . $e->getMessage());
            return false;
        }
    }

    public function crearReserva($id_usuario, $id_evento, $id_grupo) {
        // Las reservas se crean inicialmente como 'pendiente'
        $sql = "INSERT INTO reservaciones (id_usuario, id_evento, id_grupo, estado) VALUES (?, ?, ?, 'pendiente')";
        try {
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([$id_usuario, $id_evento, $id_grupo]);
        } catch (PDOException $e) {
            error_log("Error al crear reserva: " . $e->getMessage());
            return false;
        }
    }

    // Método para guardar la ruta del QR una vez generado (llamado desde EventoController)
    public function guardarQrPath($id_grupo, $qr_path) {
        $sql = "UPDATE grupos_reservas SET qr_code = ? WHERE id_grupo = ?";
        try {
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([$qr_path, $id_grupo]);
        } catch (PDOException $e) {
            error_log("Error al guardar QR path en grupo de reservas: " . $e->getMessage());
            return false;
        }
    }

    // MÉTODO AGREGADO/CORREGIDO: Para confirmar un grupo de reservas (cambia estado a 'confirmada')
    public function confirmarGrupoReservas($id_grupo) {
        $sql = "UPDATE reservaciones SET estado = 'confirmada' WHERE id_grupo = ?";
        try {
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([$id_grupo]);
        } catch (PDOException $e) {
            error_log("Error al confirmar grupo de reservas: " . $e->getMessage());
            return false;
        }
    }

    public function getReservacionesPorGrupo($id_grupo) {
        $sql = "SELECT r.*, e.nombre_evento, e.fecha, e.hora_inicio, e.hora_fin, e.ubicacion, e.descripcion, u.nombre, u.correo 
                FROM reservaciones r
                JOIN eventos e ON r.id_evento = e.id_evento
                JOIN usuarios u ON r.id_usuario = u.id_usuario
                WHERE r.id_grupo = ?";
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$id_grupo]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al obtener reservaciones por grupo: " . $e->getMessage());
            return [];
        }
    }

    public function getReservacionesDeUsuario($id_usuario) {
        $sql = "SELECT r.id_reservacion, r.fecha_reservacion, r.estado, r.id_grupo, e.nombre_evento, e.fecha, e.hora_inicio, e.hora_fin, e.cupo_maximo, e.ubicacion, gr.qr_code
                FROM reservaciones r
                JOIN eventos e ON r.id_evento = e.id_evento
                LEFT JOIN grupos_reservas gr ON r.id_grupo = gr.id_grupo
                WHERE r.id_usuario = :id_usuario
                ORDER BY e.fecha ASC, e.hora_inicio ASC";
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':id_usuario' => $id_usuario]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al obtener reservaciones de usuario: " . $e->getMessage());
            return [];
        }
    }
    
    public function cancelarReserva($id_reservacion, $id_evento) {
        $this->pdo->beginTransaction();
        try {
            // Obtener el estado de la reserva para decidir si incrementar el cupo
            $reserva_info = $this->getReservacionPorId($id_reservacion); 
            
            $sql_delete = "DELETE FROM reservaciones WHERE id_reservacion = ?";
            $stmt_delete = $this->pdo->prepare($sql_delete);
            $stmt_delete->execute([$id_reservacion]);
            
            // Solo incrementar cupo si la reserva cancelada estaba 'confirmada'
            if ($reserva_info && $reserva_info['estado'] === 'confirmada') {
                $sql_update_cupo = "UPDATE eventos SET cupo_disponible = cupo_disponible + 1 WHERE id_evento = ?";
                $stmt_update_cupo = $this->pdo->prepare($sql_update_cupo);
                $stmt_update_cupo->execute([$id_evento]);
            }
            
            $this->pdo->commit();
            return true;
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            error_log("Error al cancelar reserva: " . $e->getMessage());
            return false;
        }
    }

    // MODIFICADO: Ahora también devuelve el estado de la reserva
    public function getReservacionPorId($id_reservacion) {
        $sql = "SELECT id_reservacion, id_evento, estado FROM reservaciones WHERE id_reservacion = ?";
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$id_reservacion]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al obtener reservación por ID: " . $e->getMessage());
            return false;
        }
    }

    public function eliminarReservasPendientes($id_usuario) {
        $this->pdo->beginTransaction();
        try {
            // Elimina reservas con estado 'pendiente'.
            // Los cupos se decrementaron en procesarReservacion y se liberan en cancelarReserva (si es confirmada)
            // o si se inicia una nueva reserva con eliminarReservasPendientes.
            $sql_delete = "DELETE FROM reservaciones WHERE id_usuario = ? AND estado = 'pendiente'";
            $stmt_delete = $this->pdo->prepare($sql_delete);
            $stmt_delete->execute([$id_usuario]);
            
            $this->pdo->commit();
            return true;
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            error_log("Error al eliminar reservas pendientes: " . $e->getMessage());
            return false;
        }
    }
    
    public function validarReservacionesPorGrupo($id_grupo) {
        $sql = "SELECT estado FROM reservaciones WHERE id_grupo = ?";
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$id_grupo]);
            $reservas = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            if (empty($reservas)) {
                return "No se encontraron reservaciones para este grupo.";
            }
            
            foreach ($reservas as $reserva) {
                if ($reserva['estado'] === 'confirmada') {
                    return true;
                }
            }
            
            return "Las reservaciones de este grupo aún no han sido confirmadas.";
        } catch (PDOException $e) {
            error_log("Error al validar reservaciones por grupo: " . $e->getMessage());
            return "Error en la validación.";
        }
    }

    public function getReservacionesConEstado($estado) {
        $sql = "SELECT 
                    r.id_grupo, 
                    u.nombre AS nombre_usuario, 
                    u.apellidos AS apellidos_usuario, 
                    e.nombre_evento, 
                    e.fecha AS fecha_evento, 
                    e.hora_inicio, 
                    e.hora_fin, 
                    e.ubicacion,
                    r.id_reservacion,
                    r.estado,
                    r.fecha_reservacion,
                    gr.qr_code
                FROM reservaciones r
                JOIN eventos e ON r.id_evento = e.id_evento
                JOIN usuarios u ON r.id_usuario = u.id_usuario
                LEFT JOIN grupos_reservas gr ON r.id_grupo = gr.id_grupo
                WHERE r.estado = ?
                ORDER BY e.hora_inicio ASC";
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$estado]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al obtener reservaciones por estado: " . $e->getMessage());
            return [];
        }
    }
}