<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resumen de Reservación - Sistema de Reservas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f4f4f4; }
        .resumen-container { 
            max-width: 600px; margin: 50px auto; padding: 30px; 
            background-color: #fff; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); 
            text-align: center;
        }
        .resumen-item { margin: 20px 0; padding: 15px; border: 1px solid #ddd; border-radius: 6px; }
        .resumen-item h3 { color: #007bff; margin: 0 0 10px 0; }
        .resumen-actions { text-align: center; margin-top: 20px; }
        .resumen-actions a, .resumen-actions button { 
            display: inline-block; padding: 10px 20px; 
            text-decoration: none; border-radius: 4px; border: none; cursor: pointer; font-size: 16px; 
        }
    </style>
</head>
<body>
    <div class="resumen-container">
        <h1 class="mb-4">Resumen de tu Reservación</h1>
        <p class="text-muted">Por favor, revisa tus eventos seleccionados y haz clic en "Confirmar" para finalizar.</p>

        <?php
        // session_start();
        if (isset($_SESSION['reserva_mensaje'])):
            $alert_class = ($_SESSION['reserva_exito']) ? 'alert-success' : 'alert-danger';
            echo '<div class="alert ' . $alert_class . ' text-center">' . $_SESSION['reserva_mensaje'] . '</div>';
            unset($_SESSION['reserva_mensaje']);
            unset($_SESSION['reserva_exito']);
        endif;
        ?>

        <?php if (!empty($reservaciones)): ?>
                <div class="resumen-item">
                    <h3>Bienvenida Doug Bowles</h3>
                    <p><strong>Fecha:</strong> 2025-07-28</p>
                    <p><strong>Hora:</strong> 10:15:00</p>
                </div>
                <div class="resumen-item">
                    <h3>Team Building in Motion</h3>
                    <p><strong>Fecha:</strong> 2025-07-28</p>
                    <p><strong>Hora:</strong> 11:00:00</p>
                </div>
            <?php foreach ($reservaciones as $reserva): ?>
                <div class="resumen-item">
                    <h3><?php echo htmlspecialchars($reserva['nombre_evento']); ?></h3>
                    <p><strong>Fecha:</strong> <?php echo htmlspecialchars($reserva['fecha']); ?></p>
                    <p><strong>Hora:</strong> <?php echo htmlspecialchars($reserva['hora_inicio']); ?></p>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No se encontraron reservaciones. Por favor, intenta de nuevo.</p>
        <?php endif; ?>

        <div class="resumen-actions">
            <a href="../public/index.php?action=eventos" class="btn btn-secondary me-2">Modificar Selección</a>
            <a href="../public/index.php?action=finalizar_reserva" class="btn btn-success">Confirmar y Finalizar</a>
        </div>
    </div>
</body>
</html>