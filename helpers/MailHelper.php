<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Dompdf\Dompdf;

// La conexión SMTP de Gmail es una de las más sencillas de configurar
// Para usarla, necesitas crear una 'Contraseña de Aplicación' en tu cuenta de Google
// Ve a tu cuenta de Google > Seguridad > Verificación en dos pasos > Contraseñas de Aplicaciones
// Crea una nueva, por ejemplo para "Otra" y copia la contraseña generada.


class MailHelper {
  
  private $mail;
  
  public function __construct() {
    $this->mail = new PHPMailer(true);

    $TRASNSPORTER_USER='cuauhizo@gmail.com';
    $TRASNSPORTER_PASS='hcdtlbuzuwvqoyzo';

        try {
            // Configuración del servidor
            $this->mail->isSMTP();
            $this->mail->Host = 'smtp.gmail.com';
            $this->mail->SMTPAuth = true;
            $this->mail->Username = $TRASNSPORTER_USER; // **CAMBIA ESTO**
            $this->mail->Password = $TRASNSPORTER_PASS; // **CAMBIA ESTO**
            $this->mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $this->mail->Port = 465;

            // Configuración de los remitentes y el formato
            $this->mail->setFrom($TRASNSPORTER_USER, 'Reservas Nike'); // **CAMBIA ESTO**
            $this->mail->isHTML(true);
            $this->mail->CharSet = 'UTF-8';

        } catch (Exception $e) {
            // En un entorno de producción, loguear este error
        }
    }

    public function enviarConfirmacionReserva($destinatario, $nombre_destinatario, $reservaciones, $qr_url) {
        try {
            // Destinatarios
            $this->mail->addAddress($destinatario, $nombre_destinatario);

            // Contenido del correo (ahora más simple, sin el QR)
            $this->mail->Subject = 'Confirmación de tu Reservación de Eventos Nike';

            $cuerpo = '
            <h2>¡Hola ' . htmlspecialchars($nombre_destinatario) . '!</h2>
            <p>Tu reservación ha sido confirmada. Adjunto encontrarás tus boletos de acceso en formato PDF.</p>
            ';

            foreach ($reservaciones as $reserva) {
                $cuerpo .= '
                <div style="border: 1px solid #ddd; padding: 10px; margin-bottom: 20px;">
                    <h3>' . htmlspecialchars($reserva['nombre_evento']) . '</h3>
                    <p><strong>Fecha:</strong> ' . htmlspecialchars($reserva['fecha']) . '</p>
                    <p><strong>Hora:</strong> ' . htmlspecialchars($reserva['hora_inicio']) . '</p>
                </div>
                ';
            }
            $cuerpo .= '<p>¡Esperamos verte pronto!</p>';
            $this->mail->Body = $cuerpo;

            // **NUEVO: Generar el PDF y adjuntarlo**
            
            // Renderiza la vista del ticket
            $dompdf = new Dompdf();
            ob_start();
            $nombre_usuario = $nombre_destinatario;
            $nombre_evento = $reservaciones[0]['nombre_evento']; // Asumimos que es para un solo evento o el principal
            $fecha_evento = $reservaciones[0]['fecha'];
            $hora_inicio = $reservaciones[0]['hora_inicio'];
            $qr_content = "GRUPO_RESERVACION_ID:" . $reservaciones[0]['id_grupo'];
            
            // Obtener el QR en formato base64 para incrustarlo en el PDF
            // $qr_path = ROOT_PATH . "/public" . $qr_url;
            // $qr_data_base64 = base64_encode(file_get_contents($qr_path));
            $qr_path = ROOT_PATH . str_replace("/eventoNike.com", "", $qr_url);
            $qr_data_base64 = base64_encode(file_get_contents($qr_path));
            
            require ROOT_PATH . '/views/templates/pdf_ticket.php';
            $html = ob_get_clean();
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A5', 'landscape');
            $dompdf->render();
            
            $pdf_output = $dompdf->output();
            
            // Adjuntar el PDF al correo
            $this->mail->addStringAttachment($pdf_output, 'boleto_de_acceso.pdf', 'base64', 'application/pdf');

            $this->mail->send();
            return true;
        } catch (Exception $e) {
            return "Error al enviar el correo: " . $this->mail->ErrorInfo;
        }
    }
}
?>