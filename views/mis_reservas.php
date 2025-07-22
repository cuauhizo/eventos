<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Reservas</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f4; padding: 20px; }
        .reserva-container { max-width: 800px; margin: auto; background-color: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1); }
        h1, h2 { text-align: center; color: #333; }
        .reserva-item { border: 1px solid #ddd; padding: 15px; margin-bottom: 15px; border-radius: 6px; display: flex; align-items: flex-start; }
        .reserva-info { flex-grow: 1; }
        .reserva-info h3 { margin: 0 0 10px 0; color: #007bff; }
        .reserva-info p { margin: 5px 0; }
        .qr-image { text-align: center; margin-left: 20px; }
        .qr-image img { max-width: 150px; height: auto; border: 1px solid #eee; padding: 5px; }
        .reserva-actions { margin-top: 10px; }
        .reserva-actions a { display: inline-block; padding: 8px 15px; text-decoration: none; border-radius: 4px; }
        .btn-cancelar { background-color: #dc3545; color: white; }
        .btn-cancelar:hover { background-color: #c82333; }
        .estado-reserva { font-weight: bold; padding: 5px 10px; border-radius: 4px; color: white; display: inline-block; }
        .estado-pendiente { background-color: #ffc107; }
        .estado-cancelada { background-color: #6c757d; }
        .success-message, .error-message { text-align: center; margin-bottom: 15px; padding: 10px; border-radius: 4px; }
        .success-message { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .error-message { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
    </style>
</head>
<body>
  <!-- <pre><?php var_dump($_SESSION); ?></pre> -->
<!-- <p>ID de usuario actual: <?php echo $_SESSION['id_usuario'] ?? 'No encontrado'; ?></p> -->
    <div class="reserva-container">
        <div style="text-align: right; margin-bottom: 20px;">
            <?php if (isset($_SESSION['loggedin'])): ?>
                <p style="margin: 0;">Bienvenido, <strong><?php echo htmlspecialchars($_SESSION['nombre'] ?? ''); ?></strong>!</p>
                <a href="../public/index.php?action=eventos">Volver a Eventos</a> |
                <a href="../public/index.php?action=logout">Cerrar Sesión</a>
            <?php else: ?>
                <a href="../public/index.php?action=show_login_form">Iniciar Sesión</a>
            <?php endif; ?>
        </div>

        <h1>Mis Reservaciones</h1>

        <?php
        // session_start();
        // var_dump($_SESSION);
        // exit(); // Detiene el script para que puedas ver el valor
        if (isset($_SESSION['mensaje'])) {
            $clase_mensaje = (isset($_SESSION['mensaje_exito']) && $_SESSION['mensaje_exito']) ? 'success-message' : 'error-message';
            echo '<div class="' . $clase_mensaje . '">' . $_SESSION['mensaje'] . '</div>';
            unset($_SESSION['mensaje']);
            unset($_SESSION['mensaje_exito']);
        }
        ?>
        <?php if (!empty($reservaciones)): ?>
            <div class="reserva-item">
                <div class="reserva-info">
                    <h3>Bienvenida Doug Bowles</h3>
                    <p><strong>Fecha del Evento:</strong> 2025-07-28</p>
                    <p><strong>Hora:</strong> 10:15:00</p>
                    <p><strong>Ubicación:</strong> Gimnasio</p>
                </div>
                </div>
                <div class="reserva-item">
                    <div class="reserva-info">
                    <h3>Team Building in Motion</h3>
                    <p><strong>Fecha del Evento:</strong> 2025-07-28</p>
                    <p><strong>Hora:</strong> 11:00:00</p>
                    <p><strong>Ubicación:</strong> Cancha Fut A</p>
                </div>
                </div>
            <?php
            $grupos_mostrados = [];
            foreach ($reservaciones as $reserva):
            ?>
                <div class="reserva-item">
                    <div class="reserva-info">
                        <h3><?php echo htmlspecialchars($reserva['nombre_evento']); ?></h3>
                        <p><strong>Fecha del Evento:</strong> <?php echo htmlspecialchars($reserva['fecha']); ?></p>
                        <p><strong>Hora:</strong> <?php echo htmlspecialchars($reserva['hora_inicio']) . ' - ' . htmlspecialchars($reserva['hora_fin']); ?></p>
                        <p><strong>Ubicación:</strong> <?php echo htmlspecialchars($reserva['ubicacion']); ?></p>

                        <!-- <?php if ($reserva['estado'] === 'pendiente'): ?>
                            <div class="reserva-actions">
                                <a href="../public/index.php?action=cancelar_reserva&id=<?php echo htmlspecialchars($reserva['id_reservacion']); ?>" class="btn-cancelar" onclick="return confirm('¿Estás seguro de que quieres cancelar esta reservación?');">Cancelar Reservación</a>
                            </div>
                        <?php endif; ?> -->
                    </div>
                    <!-- <pre><?php var_dump($reserva); ?></pre> -->
                    <?php
                    // Muestra el QR solo una vez por cada grupo de reservación
                    if (!in_array($reserva['id_grupo'], $grupos_mostrados) && $reserva['estado'] !== 'cancelada'):
                        $grupos_mostrados[] = $reserva['id_grupo'];
                    ?>
                        <!-- <div class="qr-image">
                            <p><strong>Tu QR:</strong></p>
                            <img src="<?php echo htmlspecialchars($reserva['qr_code']); ?>" alt="Código QR de tu reserva">
                        </div> -->
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p style="text-align: center;">Aún no tienes reservaciones. ¡<a href="../public/index.php?action=eventos">Reserva un evento</a>!</p>
        <?php endif; ?>
    </div>
</body>
</html>