<?php

class Reservacion {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function iniciarGrupoReservas() {
        $sql = "INSERT INTO grupos_reservas (qr_code) VALUES (NULL)";
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
        $sql = "INSERT INTO reservaciones (id_usuario, id_evento, id_grupo, estado) VALUES (?, ?, ?, 'pendiente')";
        try {
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([$id_usuario, $id_evento, $id_grupo]);
        } catch (PDOException $e) {
            error_log("Error al crear reserva: " . $e->getMessage());
            return false;
        }
    }

    public function finalizarReservacion($id_grupo, $qr_path) {
        $sql = "UPDATE grupos_reservas SET qr_code = ? WHERE id_grupo = ?";
        try {
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([$qr_path, $id_grupo]);
        } catch (PDOException $e) {
            error_log("Error al finalizar reservación: " . $e->getMessage());
            return false;
        }
    }

    public function getReservacionesPorGrupo($id_grupo) {
        $sql = "SELECT r.*, e.nombre_evento, e.fecha, e.hora_inicio, e.ubicacion, e.descripcion 
                FROM reservaciones r
                JOIN eventos e ON r.id_evento = e.id_evento
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
                ORDER BY e.fecha DESC, e.hora_inicio DESC";
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
            $sql1 = "UPDATE eventos SET cupo_disponible = cupo_disponible + 1 WHERE id_evento = ?";
            $stmt1 = $this->pdo->prepare($sql1);
            $stmt1->execute([$id_evento]);
            
            $sql2 = "DELETE FROM reservaciones WHERE id_reservacion = ?";
            $stmt2 = $this->pdo->prepare($sql2);
            $stmt2->execute([$id_reservacion]);
            
            $this->pdo->commit();
            return true;
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            error_log("Error al cancelar reserva: " . $e->getMessage());
            return false;
        }
    }

    public function getReservacionPorId($id_reservacion) {
        $sql = "SELECT id_reservacion, id_evento FROM reservaciones WHERE id_reservacion = ?";
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
            $sql_select = "SELECT id_evento FROM reservaciones WHERE id_usuario = ? AND estado = 'pendiente'";
            $stmt_select = $this->pdo->prepare($sql_select);
            $stmt_select->execute([$id_usuario]);
            $eventos_pendientes = $stmt_select->fetchAll(PDO::FETCH_COLUMN);

            if (!empty($eventos_pendientes)) {
                $placeholders = implode(',', array_fill(0, count($eventos_pendientes), '?'));
                $sql_update = "UPDATE eventos SET cupo_disponible = cupo_disponible + 1 WHERE id_evento IN ($placeholders)";
                $stmt_update = $this->pdo->prepare($sql_update);
                $stmt_update->execute($eventos_pendientes);
            }

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
}