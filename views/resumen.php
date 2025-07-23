<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resumen de Reservación - Sistema de Reservas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../public/css/styles.css">
    <style>
        /* body { background-color: #f4f4f4; } */
        .resumen-container { 
            max-width: 450px; margin: 50px auto; padding: 30px; 

            border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); 
            text-align: center;
        }
        .resumen-item { margin: 20px 0; padding: 15px; border: 1px solid #ddd; border-radius: 6px; }
        
        .resumen-actions { text-align: center; margin-top: 20px; }
        .resumen-actions a, .resumen-actions button { 
            display: inline-block; padding: 10px 20px; 
            text-decoration: none; border-radius: 4px; border: none; cursor: pointer; font-size: 16px; 
        }
    </style>
</head>
<body>
    <div class="resumen-container">
        <div class="d-flex justify-content-between align-items-center mb-4">
      <?php if (isset($_SESSION['loggedin'])): ?>
      <p class="mb-0">Bienvenido(a), <strong><?php echo htmlspecialchars($_SESSION['nombre'] ?? ''); ?></strong>!</p>
      <div>
        <!-- <a href="../public/index.php?action=mis_reservas" class="btn btn-sm btn-outline-primary me-2">Mis Reservas</a> -->
        <a href="../public/index.php?action=logout" class="btn btn-sm btn-danger">Cerrar Sesión</a>
      </div>
      <?php else: ?>
      <a href="../public/index.php?action=show_login_form">Iniciar Sesión</a>
      <?php endif; ?>
    </div>
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
            <?php foreach ($reservaciones as $reserva): ?>
                <div class="resumen-item">
                    <h2><?php echo htmlspecialchars($reserva['nombre_evento']); ?></h2>
                    <p><strong>Ubicación:</strong> <?php echo htmlspecialchars($reserva['ubicacion']); ?></p>
                    <p><strong>Hora:</strong> <?php echo htmlspecialchars($reserva['hora_inicio']); ?></p>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No se encontraron reservaciones. Por favor, intenta de nuevo.</p>
        <?php endif; ?>

        <div class="resumen-actions">
            <!-- <a href="../public/index.php?action=eventos" class="btn btn-secondary me-2">Modificar Selección</a> -->
            <a href="../public/index.php?action=finalizar_reserva" class="btn btn-primary">Confirmar y Finalizar</a>
        </div>
    </div>
</body>
</html>