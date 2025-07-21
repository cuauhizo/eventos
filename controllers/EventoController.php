<?php
// Incluye las clases del QR Code
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
use Dompdf\Dompdf; // Asumimos el uso de Dompdf

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
        if ($num_seleccionados < 1 || $num_seleccionados > 4) {
            return "Debes seleccionar entre 1 y 4 eventos.";
        }

        if (in_array(18, $eventos_seleccionados) && in_array(24, $eventos_seleccionados)) {
            return "No puedes reservar el 'Fútbol Torneo Fase 1' y 'Fútbol Torneo Fase 2' al mismo tiempo.";
        }

        // --- NUEVA LÓGICA DE VALIDACIÓN DE HORARIO EN EL SERVIDOR ---
            $eventos_data = [];
            foreach ($eventos_seleccionados as $id) {
                $evento = $this->eventoModel->obtenerPorId($id);
                if ($evento) {
                    $eventos_data[] = $evento;
                }
            }

            // Ordenar los eventos por fecha y hora de inicio para la validación
            usort($eventos_data, function($a, $b) {
                $datetime_a = strtotime($a['fecha'] . ' ' . $a['hora_inicio']);
                $datetime_b = strtotime($b['fecha'] . ' ' . $b['hora_inicio']);
                return $datetime_a - $datetime_b;
            });

            for ($i = 0; $i < count($eventos_data) - 1; $i++) {
                $current_event = $eventos_data[$i];
                $next_event = $eventos_data[$i + 1];

                $end_time_current = strtotime($current_event['fecha'] . ' ' . $current_event['hora_fin']);
                $start_time_next = strtotime($next_event['fecha'] . ' ' . $next_event['hora_inicio']);

                if ($start_time_next < $end_time_current) {
                    return "El evento '" . $next_event['nombre_evento'] . "' se empalma con el evento '" . $current_event['nombre_evento'] . "'.";
                }
            }
            
            // --- FIN DE LA NUEVA LÓGICA ---

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
            // === LÓGICA PARA GENERAR PDF Y ENVIAR CORREO ===
            
            // 1. Obtener los datos de las reservaciones y el usuario
            $sql_reservas = "SELECT r.*, e.nombre_evento, e.fecha, e.hora_inicio, e.hora_fin, e.ubicacion, u.nombre, u.correo
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
            
            // Construir el cuerpo del correo en formato HTML
            $cuerpo_html = '
                <h2>¡Hola ' . htmlspecialchars($reservaciones[0]['nombre']) . '!</h2>
                <p>Tu reservación ha sido confirmada. Adjunto encontrarás tus boletos de acceso en formato PDF.</p>
                <div style="border: 1px solid #ddd; padding: 10px; margin-bottom: 20px;">
                    <h3>Detalles de tu Reserva</h3>
            ';

            $cuerpo_html .='
                <div style="border-bottom: 1px dashed #ccc; padding-bottom: 10px; margin-bottom: 10px;">
                    <p><strong>Evento:</strong> Bienvenida Doug Bowles</p>
                    <p><strong>Ubicación:</strong> Gimnasio</p>
                    <p><strong>Fecha:</strong> 2025-07-28</p>
                    <p><strong>Hora:</strong> 10:15:00 - 10:50:00</p>
                </div>
                <div style="border-bottom: 1px dashed #ccc; padding-bottom: 10px; margin-bottom: 10px;">
                    <p><strong>Evento:</strong> Team Building in Motion</p>
                    <p><strong>Ubicación:</strong> Cancha Fut A</p>
                    <p><strong>Fecha:</strong> 2025-07-28</p>
                    <p><strong>Hora:</strong> 11:00:00 - 11:45:00</p>
                </div>
            ';

            foreach ($reservaciones as $reserva) {
                $cuerpo_html .= '
                    <div style="border-bottom: 1px dashed #ccc; padding-bottom: 10px; margin-bottom: 10px;">
                        <p><strong>Evento:</strong> ' . htmlspecialchars($reserva['nombre_evento']) . '</p>
                        <p><strong>Ubicación:</strong> ' . htmlspecialchars($reserva['ubicacion']) . '</p>
                        <p><strong>Fecha:</strong> ' . htmlspecialchars($reserva['fecha']) . '</p>
                        <p><strong>Hora:</strong> ' . htmlspecialchars($reserva['hora_inicio']) . ' - ' . htmlspecialchars($reserva['hora_fin']) . '</p>
                    </div>
                ';
            }
            $cuerpo_html .= '</div><p>¡Esperamos verte pronto!</p>';


            // 2. Generar el contenido del PDF (con la vista que creaste)
            ob_start();
            include ROOT_PATH . '/views/templates/reservacion_pdf.php'; // Usa la vista del PDF que ya creaste
            $html_pdf = ob_get_clean();

            // 3. Generar el PDF y guardarlo temporalmente
            $dompdf = new Dompdf();
            $dompdf->loadHtml($html_pdf);
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();
            $pdf_output = $dompdf->output();

            $pdf_path = ROOT_PATH . "/temp/reservacion_" . $id_grupo . ".pdf";
            file_put_contents($pdf_path, $pdf_output);

            // 4. Enviar el correo con el PDF adjunto (con el nombre de método corregido)
            $mailHelper = new MailHelper();
            $envio_exitoso = $mailHelper->enviarCorreoConAdjunto(
                $reservaciones[0]['correo'],
                $reservaciones[0]['nombre'],
                'Confirmación de Reservación de Eventos Nike', // Asunto
                $cuerpo_html,
                $pdf_path,
                'boleto_de_acceso.pdf' // Nombre del archivo adjunto
            );

            // 5. Eliminar el archivo PDF temporal
            unlink($pdf_path);

            if ($envio_exitoso) {
                return true;
            } else {
                return "Error al enviar el correo de confirmación.";
            }
            
        } catch (Exception $e) {
            error_log("Error en la confirmación final: " . $e->getMessage());
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

        // En tu controlador, antes de la línea require_once...
    public function mostrarEventos() {
        // Aquí se llama al modelo y se asigna el resultado a la variable
        $eventos = $this->eventoModel->getEventosDisponibles();

        // AGREGA ESTA LÍNEA TEMPORAL PARA VERIFICAR LA VARIABLE
        // echo '<pre>';
        // print_r($eventos);
        // echo '</pre>';
        // die(); // Esto detendrá la ejecución del script aquí

        require_once ROOT_PATH . '/views/eventos.php';
    }

}