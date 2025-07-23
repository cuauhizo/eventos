<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Confirmación de Reservación</title>
    <style>
        body { font-family: sans-serif; margin: 20px; }
        .header { text-align: center; margin-bottom: 25px; }
        .header h1 { color: #fc3000; }
        .user-info, .events-info { margin-bottom: 20px; }
        .events-list { width: 100%; border-collapse: collapse; }
        .events-list th, .events-list td { border: 1px solid #ccc; padding: 10px; text-align: left; }
        .footer { text-align: center; margin-top: 30px; color: #888; }
        .qr-code { text-align: center; margin-top: 20px; }
    </style>
</head>
<body>
    <div class="header">
        <?php if (isset($logo_base64)): ?>
            <img src="data:image/jpeg;base64,<?php echo $logo_base64; ?>" alt="Logo de la Empresa" width="150">
        <?php else: ?>
            <h1>Logo de la Empresa</h1>
        <?php endif; ?>

        <h1>Confirmación de Reservación de Eventos</h1>
        <p>¡Gracias por tu registro, <?php echo htmlspecialchars($reservaciones[0]['nombre']); ?>!</p>
        <p>Aquí tienes el detalle de tus eventos seleccionados.</p>
    </div>
    <div class="events-info">
        <h3>Detalles de la Reserva</h3>
        <table class="events-list">
            <thead>
                <tr>
                    <th>Evento</th>
                    <th>Ubicación</th>
                    <th>Hora</th>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($reservaciones as $reserva): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($reserva['nombre_evento']); ?></td>
                        <td><?php echo htmlspecialchars($reserva['ubicacion']); ?></td>
                        <td><?php echo htmlspecialchars($reserva['hora_inicio']) . ' - ' . htmlspecialchars($reserva['hora_fin']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    
    <div class="qr-code">
        <h3>Tu Código de Acceso</h3>
        <?php if (isset($qr_base64)): ?>
            <p>Escanea este código al llegar al evento:</p>
            <img src="data:image/png;base64,<?php echo $qr_base64; ?>" alt="Código QR de Reserva" width="200" height="200">
        <?php else: ?>
            <p>No se pudo generar el código QR. Por favor, verifica tu correo de confirmación.</p>
        <?php endif; ?>
    </div>

    <div class="footer">
        <p>Por favor, guarda este documento como tu comprobante de reservación.</p>
        <p>Este boleto es personal e intransferible.</p>
    </div>
</body>
</html>