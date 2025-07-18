<?php
// Este controlador se encargará de mostrar los eventos, procesar las reservas, etc.
// Incluye las clases del QR Code
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;

class EventoController {

    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Método para obtener todos los eventos disponibles
    public function getEventosDisponibles() {
        $sql = "SELECT * FROM eventos WHERE cupo_disponible > 0 ORDER BY fecha, hora_inicio ASC";
        try {
            $stmt = $this->pdo->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            // Manejo de error: podrías registrarlo o mostrar un mensaje al usuario
            return []; // Retorna un array vacío en caso de error
        }
    }

    // Método para procesar la reservación de eventos
    public function procesarReservacion($id_usuario, $eventos_seleccionados) {
        $num_seleccionados = count($eventos_seleccionados);
        if ($num_seleccionados < 1 || $num_seleccionados > 3) {
            return "Debes seleccionar entre 1 y 3 eventos.";
        }

        $this->pdo->beginTransaction();

        try {
            // Crear un nuevo grupo de reservación
            $sql_grupo = "INSERT INTO grupos_reservas (qr_code) VALUES (NULL)";
            $this->pdo->query($sql_grupo);
            $id_grupo = $this->pdo->lastInsertId();

            foreach ($eventos_seleccionados as $id_evento) {
                $sql_check_user = "SELECT COUNT(*) FROM reservaciones WHERE id_usuario = ? AND id_evento = ?";
                $stmt_check_user = $this->pdo->prepare($sql_check_user);
                $stmt_check_user->execute([$id_usuario, $id_evento]);
                
                if ($stmt_check_user->fetchColumn() > 0) {
                    $this->pdo->rollBack();
                    return "Ya has reservado el evento con ID " . $id_evento . ".";
                }

                $sql_check_cupo = "SELECT cupo_disponible FROM eventos WHERE id_evento = ? AND cupo_disponible > 0 FOR UPDATE";
                $stmt_check_cupo = $this->pdo->prepare($sql_check_cupo);
                $stmt_check_cupo->execute([$id_evento]);
                $evento = $stmt_check_cupo->fetch();

                if (!$evento) {
                    $this->pdo->rollBack();
                    return "El evento con ID " . $id_evento . " ya no tiene cupo disponible.";
                }

                // Insertar la reservación individual y asociarla al grupo
                $sql_insert = "INSERT INTO reservaciones (id_usuario, id_evento, id_grupo) VALUES (?, ?, ?)";
                $stmt_insert = $this->pdo->prepare($sql_insert);
                $stmt_insert->execute([$id_usuario, $id_evento, $id_grupo]);
                
                // Actualizar el cupo disponible del evento
                $sql_update_cupo = "UPDATE eventos SET cupo_disponible = cupo_disponible - 1 WHERE id_evento = ?";
                $stmt_update_cupo = $this->pdo->prepare($sql_update_cupo);
                $stmt_update_cupo->execute([$id_evento]);
            }

            $this->pdo->commit();
            return $id_grupo; // Retorna el ID del grupo de reservaciones
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            return "Error al procesar la reservación: " . $e->getMessage();
        }
    }

    // **AÑADE ESTE NUEVO MÉTODO**
    public function finalizarReservacion($id_grupo) {
        try {
            // 1. Generar el código QR para el grupo de reservaciones
            $qr_content = "GRUPO_RESERVACION_ID:" . $id_grupo;
            $qr_file_name = "grupo_reserva_" . $id_grupo . ".png";
            $qr_path = ROOT_PATH . "/public/qrcodes/" . $qr_file_name;
            
            $options = new QROptions([
                'outputType' => QRCode::OUTPUT_IMAGE_PNG,
                'eccLevel' => QRCode::ECC_L,
                'imageTransparent' => false,
                'scale' => 8
            ]);
            (new QRCode($options))->render($qr_content, $qr_path);

            // 2. Actualizar la tabla de grupos con la ruta del QR
            $qr_db_path = "/eventoNike.com/public/qrcodes/" . $qr_file_name;
            $sql_update_qr = "UPDATE grupos_reservas SET qr_code = ? WHERE id_grupo = ?";
            $stmt_update_qr = $this->pdo->prepare($sql_update_qr);
            $stmt_update_qr->execute([$qr_db_path, $id_grupo]);
            
            // 3. Obtener los datos del usuario y de las reservaciones
            $sql_reservas = "SELECT r.*, e.nombre_evento, e.fecha, e.hora_inicio, u.nombre, u.correo
                            FROM reservaciones r
                            JOIN eventos e ON r.id_evento = e.id_evento
                            JOIN usuarios u ON r.id_usuario = u.id_usuario
                            WHERE r.id_grupo = ?";
            $stmt_reservas = $this->pdo->prepare($sql_reservas);
            $stmt_reservas->execute([$id_grupo]);
            $reservaciones = $stmt_reservas->fetchAll(PDO::FETCH_ASSOC);

            if (empty($reservaciones)) {
                return "Error: No se encontraron reservas para este grupo.";
            }
            
            // 4. Enviar el correo de confirmación
            $mailHelper = new MailHelper();
            $mailHelper->enviarConfirmacionReserva(
                $reservaciones[0]['correo'],
                $reservaciones[0]['nombre'],
                $reservaciones,
                $qr_db_path // Pasar la ruta del QR para el correo
            );

            return true;

        } catch (Exception $e) {
            return "Error en la confirmación final: " . $e->getMessage();
        }
    }

    // **AÑADE ESTE MÉTODO PARA OBTENER EL RESUMEN DEL GRUPO**
    public function getReservacionesPorGrupo($id_grupo) {
        $sql = "SELECT r.*, e.nombre_evento, e.fecha, e.hora_inicio, e.descripcion
                FROM reservaciones r
                JOIN eventos e ON r.id_evento = e.id_evento
                WHERE r.id_grupo = :id_grupo";
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':id_grupo', $id_grupo, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return "Error al obtener las reservas del grupo.";
        }
    }


    // ===========================================================

    // Nuevo método para obtener los detalles de las reservaciones
    public function getReservacionesPorIds($ids) {
        if (empty($ids)) {
            return [];
        }
        $placeholders = str_repeat('?,', count($ids) - 1) . '?';
        $sql = "SELECT r.*, e.nombre_evento, e.fecha, e.hora_inicio FROM reservaciones r JOIN eventos e ON r.id_evento = e.id_evento WHERE r.id_reservacion IN ($placeholders)";
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($ids);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return "Error al obtener las reservaciones: " . $e->getMessage();
        }
    }

    public function getReservacionesDeUsuario($id_usuario) {
        $sql = "SELECT r.id_reservacion, r.fecha_reservacion, r.estado, r.id_grupo, e.nombre_evento, e.fecha, e.hora_inicio, e.hora_fin, e.cupo_maximo, gr.qr_code
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
            return [];
        }
    }
}