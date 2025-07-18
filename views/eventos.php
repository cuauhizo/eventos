<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Selección de Eventos - Sistema de Reservas</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f4; padding: 20px; }
        .eventos-container { max-width: 800px; margin: auto; background-color: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1); }
        .top-nav { text-align: right; margin-bottom: 20px; }
        .top-nav a { margin-left: 10px; text-decoration: none; color: #007bff; font-weight: bold; }
        h1, h2 { text-align: center; color: #333; }

        /* Estilo base de las tarjetas de evento */
        .evento-item { 
            border: 1px solid #ddd; padding: 15px; margin-bottom: 15px; border-radius: 6px; 
            cursor: pointer; transition: all 0.3s ease; 
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        .evento-item:hover { 
            border-color: #007bff; 
            box-shadow: 0 2px 6px rgba(0,0,0,0.15);
        }
        /* Estilo cuando el evento está seleccionado */
        .evento-item.selected { 
            border-color: #28a745; 
            background-color: #e9f5e9; 
            box-shadow: 0 0 10px #28a74550;
        }

        .evento-info { flex-grow: 1; }
        .evento-info h3 { margin: 0; color: #007bff; }
        .evento-info p { margin: 5px 0; }
        
        /* Ocultar el checkbox original */
        .evento-item input[type="checkbox"] { display: none; }

        .eventos-actions { text-align: center; margin-top: 20px; }
        .eventos-actions button { padding: 12px 24px; background-color: #28a745; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 16px; transition: background-color 0.3s ease; }
        .eventos-actions button:hover { background-color: #218838; }
        .error-message, .success-message { text-align: center; margin-bottom: 15px; padding: 10px; border-radius: 4px; }
        .error-message { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .success-message { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
    </style>
</head>
<body>
    <div class="eventos-container">
        <div class="top-nav">
            <?php if (isset($_SESSION['loggedin'])): ?>
                <p style="margin: 0;">Bienvenido, <strong><?php echo htmlspecialchars($_SESSION['nombre'] ?? ''); ?></strong>!</p>
                <a href="../public/index.php?action=mis_reservas">Mis Reservas</a> |
                <a href="../public/index.php?action=logout">Cerrar Sesión</a>
            <?php else: ?>
                <a href="../public/index.php?action=show_login_form">Iniciar Sesión</a>
            <?php endif; ?>
        </div>
        
        <h1>Eventos Deportivos</h1>
        <p style="text-align: center;">Selecciona los eventos a los que te gustaría asistir (máximo 3).</p>

        <?php
        // session_start();
        if (isset($_SESSION['reserva_mensaje'])):
            $clase_mensaje = ($_SESSION['reserva_exito']) ? 'success-message' : 'error-message';
            echo '<div class="' . $clase_mensaje . '">' . $_SESSION['reserva_mensaje'] . '</div>';
            unset($_SESSION['reserva_mensaje']);
            unset($_SESSION['reserva_exito']);
        endif;
        ?>

        <form action="../public/index.php?action=reservar" method="POST">
            <?php if (!empty($eventos)): ?>
                <?php foreach ($eventos as $evento): ?>
                    <div class="evento-item" onclick="toggleSelection(this, <?php echo htmlspecialchars($evento['id_evento']); ?>)">
                        <input type="checkbox" name="eventos_seleccionados[]" value="<?php echo htmlspecialchars($evento['id_evento']); ?>">
                        <div class="evento-info">
                            <h3><?php echo htmlspecialchars($evento['nombre_evento']); ?></h3>
                            <p><strong>Fecha:</strong> <?php echo htmlspecialchars($evento['fecha']); ?></p>
                            <p><strong>Hora:</strong> <?php echo htmlspecialchars($evento['hora_inicio']) . ' - ' . htmlspecialchars($evento['hora_fin']); ?></p>
                            <p><strong>Cupo disponible:</strong> <?php echo htmlspecialchars($evento['cupo_disponible']); ?></p>
                            <p><?php echo htmlspecialchars($evento['descripcion']); ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p style="text-align: center;">No hay eventos disponibles en este momento.</p>
            <?php endif; ?>

            <?php if (!empty($eventos)): ?>
            <div class="eventos-actions">
                <button type="submit">Reservar Eventos</button>
            </div>
            <?php endif; ?>
        </form>
    </div>

    <script>
        function toggleSelection(element, eventId) {
            const checkbox = element.querySelector('input[type="checkbox"]');
            
            // Alternar el estado del checkbox
            checkbox.checked = !checkbox.checked;

            // Alternar la clase 'selected' en el div
            if (checkbox.checked) {
                element.classList.add('selected');
            } else {
                element.classList.remove('selected');
            }
        }
    </script>
</body>
</html>