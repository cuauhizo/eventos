<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Reservas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link as="font" crossorigin="crossorigin" href="https://www.nike.com/static/ncss/5.0/dotcom/fonts/Nike-Futura.woff2" rel="preload" type="font/woff2">
    <link rel="stylesheet" href="../public/css/styles.css">
    <style>
        /* Tus estilos CSS personalizados */
        .reserva-container { max-width: 450px; margin: 50px auto; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1); }


        .reserva-group-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }

        
        .reserva-item { border: 1px solid #ddd; padding: 15px; margin-bottom: 15px; border-radius: 6px; display: flex; align-items: flex-start; }
        .reserva-info { flex-grow: 1; }
        .reserva-info p { margin: 5px 0; }
        .qr-image { text-align: center; margin-bottom: 20px; }
        .qr-image img { max-width: 150px; height: auto; }
        .reserva-actions { margin-top: 10px; }
        .reserva-actions a { display: inline-block; padding: 8px 15px; text-decoration: none; border-radius: 4px; }
        .btn-cancelar { background-color: #dc3545; color: white; }
        .btn-cancelar:hover { background-color: #c82333; }
        
        /* Estilos para el estado de la reserva */
        .estado-reserva { font-weight: bold; padding: 5px 10px; border-radius: 4px; color: white; display: inline-block; }
        .estado-pendiente { background-color: #ffc107; /* Amarillo */ }
        .estado-confirmada { background-color: #28a745; /* Verde Bootstrap success */ }
        .estado-cancelada { background-color: #6c757d; /* Gris */ }
        
        .success-message, .error-message { text-align: center; margin-bottom: 15px; padding: 10px; border-radius: 4px; }
        .success-message { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .error-message { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
    </style>
</head>
<body>
    <div class="reserva-container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <?php if (isset($_SESSION['loggedin'])): ?>
            <p class="mb-0">Bienvenido(a), <strong><?php echo htmlspecialchars($_SESSION['nombre'] ?? ''); ?></strong>!</p>
            <div>
                <a href="../public/index.php?action=logout" class="btn btn-sm btn-danger">Cerrar Sesión</a>
            </div>
            <?php else: ?>
            <a href="../public/index.php?action=show_login_form">Iniciar Sesión</a>
            <?php endif; ?>
        </div>

        <h1>Mis Reservaciones</h1>

        <?php
        // Manejo de mensajes de sesión (éxito/error)
        if (isset($_SESSION['mensaje'])) {
            $clase_mensaje = (isset($_SESSION['mensaje_exito']) && $_SESSION['mensaje_exito']) ? 'success-message' : 'error-message';
            echo '<div class="' . $clase_mensaje . '">' . $_SESSION['mensaje'] . '</div>';
            unset($_SESSION['mensaje']);
            unset($_SESSION['mensaje_exito']);
        }

        // === PASO 1: Agrupar las reservas por ID de grupo ===
        $reservas_agrupadas = [];
        if (!empty($reservaciones)) {
            foreach ($reservaciones as $reserva) {
                $reservas_agrupadas[$reserva['id_grupo']][] = $reserva;
            }
        }
        ?>

        <?php if (!empty($reservas_agrupadas)): ?>
            <?php foreach ($reservas_agrupadas as $id_grupo => $grupo_reservas): 
                // Tomar el primer elemento del grupo para obtener información general del grupo (como el QR)
                $reserva_ejemplo_grupo = $grupo_reservas[0]; 
            ?>
                <div class="">
                    <?php if (!empty($reserva_ejemplo_grupo['qr_code']) && $reserva_ejemplo_grupo['estado'] !== 'cancelada'): ?>
                        <div class="qr-image">
                            <p><strong>Tu Código de Acceso del Grupo:</strong></p>
                            <img src="<?php echo htmlspecialchars($reserva_ejemplo_grupo['qr_code']); ?>" alt="Código QR de tu reserva de grupo">
                        </div>
                    <?php endif; ?>

                    <?php foreach ($grupo_reservas as $reserva): ?>
                        <div class="reserva-item">
                            <div class="reserva-info">
                                <h2><?php echo htmlspecialchars($reserva['nombre_evento']); ?></h2>
                                <p><strong>Hora:</strong> <?php echo htmlspecialchars($reserva['hora_inicio']) . ' - ' . htmlspecialchars($reserva['hora_fin']); ?></p>
                                <p><strong>Ubicación:</strong> <?php echo htmlspecialchars($reserva['ubicacion']); ?></p>
                                <!-- <p>
                                    <strong>Estado:</strong> 
                                    <?php 
                                        $estado_clase = '';
                                        switch ($reserva['estado']) {
                                            case 'pendiente': $estado_clase = 'estado-pendiente'; break;
                                            case 'confirmada': $estado_clase = 'estado-confirmada'; break;
                                            case 'cancelada': $estado_clase = 'estado-cancelada'; break;
                                            default: $estado_clase = 'estado-reserva'; break;
                                        }
                                    ?>
                                    <span class="estado-reserva <?php echo $estado_clase; ?>">
                                        <?php echo htmlspecialchars(ucfirst($reserva['estado'])); ?>
                                    </span>
                                </p> 

                                <?php if ($reserva['estado'] !== 'cancelada'): ?>
                                    <div class="reserva-actions">
                                        <a href="../public/index.php?action=cancelar_reserva&id=<?php echo htmlspecialchars($reserva['id_reservacion']); ?>" class="btn-cancelar" onclick="return confirm('¿Estás seguro de que quieres cancelar esta reservación? Esta acción es irreversible y liberará tu cupo.');">Cancelar Reservación</a>
                                    </div>
                                <?php endif; ?> -->
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div> <?php endforeach; ?>
        <?php else: ?>
            <p style="text-align: center;">Aún no tienes reservaciones. ¡<a href="../public/index.php?action=eventos">Reserva un evento</a>!</p>
        <?php endif; ?>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>