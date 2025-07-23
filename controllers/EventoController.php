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

    // MODIFICADO: Renombrado el parámetro a $eventos_seleccionados_from_post para mayor claridad
    public function procesarReservacion($id_usuario, $eventos_seleccionados_from_post) { 
        // IDs de los eventos indispensables (estos siempre se reservan para el usuario)
        // ¡IMPORTANTE! Asegúrate de que estos IDs (1 y 2) sean los ID reales de "Bienvenida Doug Bowles" y "Team Building in Motion" en tu DB
        $indispensable_event_ids = [1, 2]; 
        
        // --- LIMPIAR CUALQUIER RESERVA PENDIENTE ANTERIOR DEL USUARIO ---
        $this->reservacionModel->eliminarReservasPendientes($id_usuario);

        // === PASO 1: Filtrar los eventos indispensables del array que viene del POST ===
        // Esto evita que los IDs de los indispensables se dupliquen si la vista los envía.
        // Después de este paso, $eventos_opcionales_recibidos solo contendrá IDs de eventos *opcionales*.
        $eventos_opcionales_recibidos = array_diff($eventos_seleccionados_from_post, $indispensable_event_ids);
        
        $num_opcionales_recibidos = count($eventos_opcionales_recibidos);

        // === PASO 2: Validar el número de eventos opcionales seleccionados por el usuario ===
        // La validación ahora solo se aplica al conteo de eventos opcionales (0 a 4).
        if ($num_opcionales_recibidos > 4) {
             return "Solo puedes seleccionar un máximo de 4 eventos opcionales.";
        }
        // Si necesitaras forzar que al menos un opcional sea seleccionado:
        // if ($num_opcionales_recibidos < 1) { return "Debes seleccionar al menos 1 evento opcional."; }


        // === PASO 3: Combinar la lista FINAL de eventos a reservar (indispensables + opcionales limpios) ===
        // Esta será la lista COMPLETA de IDs para todas las validaciones y el proceso de booking.
        // array_merge añade los indispensables y luego los opcionales limpios.
        $all_events_to_book_ids = array_merge($indispensable_event_ids, $eventos_opcionales_recibidos);

        // Por si acaso, si no hay ningún evento para reservar (solo pasaría si los indispensables no existen o se elimina algo clave)
        if (empty($all_events_to_book_ids)) {
             return "No hay eventos para reservar."; 
        }

        // === PASO 4: Validaciones de negocio (fútbol, empalmes, cupo disponible) aplicadas a la lista COMBINADA ===
        // La validación de conflicto de Fútbol Torneo se aplica a $all_events_to_book_ids
        if (in_array(18, $all_events_to_book_ids) && in_array(24, $all_events_to_book_ids)) {
            return "No puedes reservar el 'Fútbol Torneo Fase 1' y 'Fútbol Torneo Fase 2' al mismo tiempo.";
        }

        $eventos_data = [];
        foreach ($all_events_to_book_ids as $id) {
            $evento = $this->eventoModel->obtenerPorId($id);
            if ($evento) {
                $eventos_data[] = $evento;
            } else {
                error_log("ID de evento no encontrado o inválido: " . $id);
                return "Error: Uno de los eventos seleccionados no es válido.";
            }
        }

        // --- LÓGICA DE VALIDACIÓN DE HORARIO DE EMPALMES Y CUPO DISPONIBLE FINAL ---
        usort($eventos_data, function($a, $b) {
            $datetime_a = strtotime($a['fecha'] . ' ' . $a['hora_inicio']);
            $datetime_b = strtotime($b['fecha'] . ' ' . $b['hora_inicio']);
            return $datetime_a - $datetime_b;
        });

        for ($i = 0; $i < count($eventos_data); $i++) {
            $current_event = $eventos_data[$i];

            // Vuelve a verificar el cupo JIT (Just In Time) antes de intentar reservar
            $evento_actualizado = $this->eventoModel->obtenerPorId($current_event['id_evento']);
            if (!$evento_actualizado || $evento_actualizado['cupo_disponible'] <= 0) {
               return "El evento '" . htmlspecialchars($current_event['nombre_evento']) . "' ya no tiene cupo disponible.";
            }

            // Verifica empalmes (si no es el último evento)
            if ($i < count($eventos_data) - 1) {
                $next_event = $eventos_data[$i + 1];
                $end_time_current = strtotime($current_event['fecha'] . ' ' . $current_event['hora_fin']);
                $start_time_next = strtotime($next_event['fecha'] . ' ' . $next_event['hora_inicio']);
                if ($start_time_next < $end_time_current) {
                    return "El evento '" . $next_event['nombre_evento'] . "' se empalma con el evento '" . $current_event['nombre_evento'] . "'.";
                }
            }
        }
        // --- FIN DE LA LÓGICA DE VALIDACIÓN ---


        // === PASO 5: Iniciar transacción, crear reservas y DECREMENTAR CUPO INMEDIATAMENTE ===
        $this->pdo->beginTransaction();
        try {
            $id_grupo = $this->reservacionModel->iniciarGrupoReservas();

            foreach ($all_events_to_book_ids as $id_evento) {
                // Verificar si ya tiene una reserva CONFIRMADA o PENDIENTE para este evento
                if ($this->reservacionModel->existeReserva($id_usuario, $id_evento)) {
                    $this->pdo->rollBack();
                    return "Ya tienes una reserva confirmada o pendiente para el evento con ID " . $id_evento . ".";
                }
                
                // Crear la reserva y DECREMENTAR CUPO aquí
                $this->reservacionModel->crearReserva($id_usuario, $id_evento, $id_grupo);
                $this->eventoModel->actualizarCupo($id_evento); // Cupo se decrementa aquí en este punto
            }

            $this->pdo->commit(); 
            return (int) $id_grupo; 
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            error_log("Error al procesar la reservación: " . $e->getMessage());
            return "Error al procesar la reservación: " . $e->getMessage();
        }
    }

    public function finalizarReservacion($id_grupo) {
        // CAMBIO CLAVE: Iniciar la transacción para asegurar que todas las operaciones se realicen o ninguna.
        $this->pdo->beginTransaction(); 
        try {
            // === 1. Obtener los datos de las reservaciones y el usuario para el correo y PDF ===
            $reservaciones_grupo = $this->reservacionModel->getReservacionesPorGrupo($id_grupo);

            if (empty($reservaciones_grupo)) {
                return "Error: No se encontraron reservas pendientes para este grupo.";
            }

            // 2. Confirmar el estado de las reservas en la base de datos (de 'pendiente' a 'confirmada')
            $this->reservacionModel->confirmarGrupoReservas($id_grupo); 

            // === 3. Generación y almacenamiento del Código QR (AHORA ACTIVO) ===
            // Construir el contenido del QR con los nombres de los eventos
            $event_names_for_qr = [];
            foreach ($reservaciones_grupo as $reserva) {
                $event_names_for_qr[] = $reserva['nombre_evento'];
            }
            // Limitar la longitud si hay muchos eventos para que el QR sea escaneable
            $qr_content_events = implode(", ", $event_names_for_qr);
            if (strlen($qr_content_events) > 200) { // Limitar a unos 200 caracteres para asegurar escaneabilidad
                $qr_content_events = substr($qr_content_events, 0, 197) . "...";
            }
            $qr_content = "Reserva ID Grupo: " . $id_grupo . " - Eventos: " . $qr_content_events; // Contenido completo del QR


            $qr_file_name = "grupo_reserva_" . $id_grupo . ".png";
            $qr_path = ROOT_PATH . "/public/qrcodes/" . $qr_file_name;
            $qr_db_path = "qrcodes/" . $qr_file_name; // Ruta para la base de datos

            $options = new QROptions([
                'outputType' => QRCode::OUTPUT_IMAGE_PNG,
                'eccLevel' => QRCode::ECC_L,
                'imageTransparent' => false,
                'scale' => 8 
            ]);
            (new QRCode($options))->render($qr_content, $qr_path); // Genera y guarda el archivo QR

            // Guardar la ruta del QR en la base de datos
            $this->reservacionModel->guardarQrPath($id_grupo, $qr_db_path); 

            // === 4. Preparar el logo y el QR para incrustar en el PDF (Base64) ===
            $logo_path = ROOT_PATH . "/public/img/nike-logo.jpg"; // <-- Verifica esta ruta
            $logo_base64 = '';
            if (file_exists($logo_path) && is_readable($logo_path)) {
                $logo_base64 = base64_encode(file_get_contents($logo_path));
            } else {
                error_log("Error: No se encontró el logo en la ruta: " . $logo_path);
            }

            $qr_base64 = '';
            if (file_exists($qr_path) && is_readable($qr_path)) {
                $qr_base64 = base64_encode(file_get_contents($qr_path));
            } else {
                error_log("Error: No se encontró el archivo QR en la ruta: " . $qr_path);
            }


            // 5. Generar el contenido HTML del cuerpo del correo
            $cuerpo_html = '
                <h2>¡Hola ' . htmlspecialchars($reservaciones_grupo[0]['nombre']) . '!</h2>
                <p>Tu reservación ha sido confirmada. Adjunto encontrarás tus boletos de acceso en formato PDF.</p>
                <h3>Detalles de tu Reserva</h3>
                <div style="border: 1px solid #ddd; padding: 10px; margin-bottom: 20px;">
            ';

            foreach ($reservaciones_grupo as $reserva) {
                $cuerpo_html .= '
                    <div style="border-bottom: 1px dashed #ccc; padding-bottom: 10px; margin-bottom: 10px;">
                        <p><strong>Evento:</strong> ' . htmlspecialchars($reserva['nombre_evento']) . '</p>
                        <p><strong>Ubicación:</strong> ' . htmlspecialchars($reserva['ubicacion']) . '</p>
                        <p><strong>Hora:</strong> ' . htmlspecialchars($reserva['hora_inicio']) . ' - ' . htmlspecialchars($reserva['hora_fin']) . '</p>
                    </div>
                ';
            }
            $cuerpo_html .= '</div><p>¡Esperamos verte pronto!</p>';


            // 6. Generar el PDF y guardarlo temporalmente (pasando variables para logo y QR a la vista PDF)
            ob_start();
            $reservaciones = $reservaciones_grupo; 
            include ROOT_PATH . '/views/templates/reservacion_pdf.php'; // Ruta corregida
            $html_pdf = ob_get_clean();

            $dompdf = new Dompdf();
            $dompdf->loadHtml($html_pdf);
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();
            $pdf_output = $dompdf->output();

            $pdf_path = ROOT_PATH . "/temp/reservacion_" . $id_grupo . ".pdf";
            file_put_contents($pdf_path, $pdf_output);

            // 7. Enviar el correo con el PDF adjunto
            $mailHelper = new MailHelper();
            $envio_exitoso = $mailHelper->enviarCorreoConAdjunto(
                $reservaciones_grupo[0]['correo'],
                $reservaciones_grupo[0]['nombre'],
                'Confirmación de Reservación de Eventos Nike', // Asunto
                $cuerpo_html,
                $pdf_path,
                'boleto_de_acceso.pdf'
            );

            // 8. Eliminar los archivos temporales (solo el PDF, el QR se mantiene para visualización)
            unlink($pdf_path);
            // La línea unlink($qr_path); ha sido ELIMINADA para que el QR se conserve.
            
            $this->pdo->commit(); 

            if (!$envio_exitoso) {
                error_log("Fallo al enviar correo para grupo " . $id_grupo);
            }

            return true;
        } catch (Exception $e) {
            $this->pdo->rollBack(); 
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
            return "Error: Reservación no encontrada."; 
        }
        return $this->reservacionModel->cancelarReserva($reserva_data['id_reservacion'], $reserva_data['id_evento']);
    }

    public function eliminarReservasPendientes($id_usuario) {
        return $this->reservacionModel->eliminarReservasPendientes($id_usuario);
    }

    public function mostrarEventos() {
        $eventos = $this->eventoModel->getEventosDisponibles();
        require_once ROOT_PATH . '/views/eventos.php';
    }

}