<?php
// Incluye las clases del QR Code
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;

// Incluye los modelos que va a usar el controlador
require_once ROOT_PATH . '/models/Evento.php';
require_once ROOT_PATH . '/models/Reservacion.php';
require_once ROOT_PATH . '/helpers/MailHelper.php';

class EventoController {

    private $pdo;
    private $eventoModel;
    private $reservacionModel;

    public function __construct($pdo) {
        $this->pdo = $pdo;
        $this->eventoModel = new Evento($pdo);
        $this->reservacionModel = new Reservacion($pdo);
    }

    public function getEventosDisponibles() {
        return $this->eventoModel->getEventosDisponibles();
    }
    
    public function getEventosDisponiblesPorCategoria() {
        return $this->eventoModel->getEventosDisponiblesPorCategoria();
    }

    public function procesarReservacion($id_usuario, $eventos_seleccionados) {
        $num_seleccionados = count($eventos_seleccionados);
        if ($num_seleccionados < 1 || $num_seleccionados > 3) {
            return "Debes seleccionar entre 1 y 3 eventos.";
        }

        $this->pdo->beginTransaction();
        try {
            $id_grupo = $this->reservacionModel->iniciarGrupoReservas();

            foreach ($eventos_seleccionados as $id_evento) {
                if ($this->reservacionModel->existeReserva($id_usuario, $id_evento)) {
                    $this->pdo->rollBack();
                    return "Ya has reservado el evento con ID " . $id_evento . ".";
                }
                
                $evento_data = $this->eventoModel->obtenerPorId($id_evento);
                if (!$evento_data || $evento_data['cupo_disponible'] <= 0) {
                     $this->pdo->rollBack();
                     return "El evento con ID " . $id_evento . " ya no tiene cupo disponible.";
                }

                $this->reservacionModel->crearReserva($id_usuario, $id_evento, $id_grupo);
                $this->eventoModel->actualizarCupo($id_evento);
            }

            $this->pdo->commit();
            return (int) $id_grupo;
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            return "Error al procesar la reservación: " . $e->getMessage();
        }
    }

    public function finalizarReservacion($id_grupo) {
        try {
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

            $qr_db_path = "/eventoNike.com/public/qrcodes/" . $qr_file_name;
            $this->reservacionModel->finalizarReservacion($id_grupo, $qr_db_path);
            
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
            
            $mailHelper = new MailHelper();
            $mailHelper->enviarConfirmacionReserva(
                $reservaciones[0]['correo'],
                $reservaciones[0]['nombre'],
                $reservaciones,
                $qr_db_path
            );

            return true;
        } catch (Exception $e) {
            return "Error en la confirmación final: " . $e->getMessage();
        }
    }
    
    public function getReservacionesPorGrupo($id_grupo) {
        return $this->reservacionModel->getReservacionesPorGrupo($id_grupo);
    }
    
    public function getReservacionesDeUsuario($id_usuario) {
        return $this->reservacionModel->getReservacionesDeUsuario($id_usuario);
    }
    
    public function cancelarReservacion($id_reservacion) {
        $reserva_data = $this->reservacionModel->getReservacionPorId($id_reservacion);
        if (!$reserva_data) {
            $_SESSION['mensaje'] = "Error: Reservación no encontrada.";
            return false;
        }
        return $this->reservacionModel->cancelarReserva($reserva_data['id_reservacion'], $reserva_data['id_evento']);
    }

    public function eliminarReservasPendientes($id_usuario) {
        return $this->reservacionModel->eliminarReservasPendientes($id_usuario);
    }
}