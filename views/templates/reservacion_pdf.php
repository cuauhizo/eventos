<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Confirmación de Reservación</title>
    <style>
        body { font-family: sans-serif; margin: 20px; }
        .header { text-align: center; margin-bottom: 30px; }
        .header h1 { color: #007bff; }
        .user-info, .events-info { margin-bottom: 20px; }
        .events-list { width: 100%; border-collapse: collapse; }
        .events-list th, .events-list td { border: 1px solid #ccc; padding: 10px; text-align: left; }
        .footer { text-align: center; margin-top: 50px; color: #888; }
    </style>
</head>
<body>
    <div class="header">
        <img src="../../public/img/nike-logo.jpg" alt="" width="150">
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
                    <th>Fecha</th>
                    <th>Hora</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Bienvenida Doug Bowles</td>
                    <td>Gimnasio</td>
                    <td>2025-07-28</td>
                    <td>10:15:00 - 10:50:00</td>
                </tr>
                <tr>
                    <td>Team Building in Motion</td>
                    <td>Cancha Fut A</td>
                    <td>2025-07-28</td>
                    <td>11:00:00 - 11:45:00</td>
                </tr>
                <?php foreach ($reservaciones as $reserva): ?>
                <tr>
                    <td><?php echo htmlspecialchars($reserva['nombre_evento']); ?></td>
                    <td><?php echo htmlspecialchars($reserva['ubicacion']); ?></td>
                    <td><?php echo htmlspecialchars($reserva['fecha']); ?></td>
                    <td><?php echo htmlspecialchars($reserva['hora_inicio']) . ' - ' . htmlspecialchars($reserva['hora_fin']); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <div class="footer">
        <p>Por favor, guarda este documento como tu comprobante de reservación.</p>
    </div>
</body>
</html>