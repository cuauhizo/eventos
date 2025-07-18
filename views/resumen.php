<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resumen de Reservación - Sistema de Reservas</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f4; display: flex; justify-content: center; align-items: center; min-height: 100vh; margin: 0; }
        .resumen-container { background-color: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1); width: 600px; text-align: center; }
        .resumen-container h1 { color: #333; }
        .resumen-container p { color: #555; }
        .qr-item { margin: 20px 0; padding: 15px; border: 1px solid #ddd; border-radius: 6px; }
        .qr-item h3 { color: #007bff; margin: 0 0 10px 0; }
        .qr-item img { max-width: 150px; height: auto; }
        .resumen-actions a { display: inline-block; padding: 10px 20px; background-color: #007bff; color: white; text-decoration: none; border-radius: 4px; margin-top: 20px; }
    </style>
</head>
<body>
    <div class="resumen-container">
        <div style="text-align: right; margin-bottom: 20px;">
            <?php if (isset($_SESSION['loggedin'])): ?>
                <p style="margin: 0;">Bienvenido, <strong><?php echo htmlspecialchars($_SESSION['nombre'] ?? ''); ?></strong>!</p>
                <a href="../public/index.php?action=eventos">Volver a Eventos</a> |
                <a href="../public/index.php?action=logout">Cerrar Sesión</a>
            <?php else: ?>
                <a href="../public/index.php?action=show_login_form">Iniciar Sesión</a>
            <?php endif; ?>
        </div>

        <h1>Resumen de tu Reservación</h1>
        <p>Por favor, revisa tus eventos seleccionados y haz clic en "Confirmar" para finalizar.</p>

        <?php
        // session_start();
        if (isset($_SESSION['reserva_mensaje'])):
            $clase_mensaje = ($_SESSION['reserva_exito']) ? 'success-message' : 'error-message';
            echo '<div class="' . $clase_mensaje . '">' . $_SESSION['reserva_mensaje'] . '</div>';
        endif;
        ?>

        <?php if (!empty($reservaciones)): ?>
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
            <a href="../public/index.php?action=finalizar_reserva" class="btn-confirmar">Confirmar y Finalizar</a>
        </div>
    </div>
</body>
</html>