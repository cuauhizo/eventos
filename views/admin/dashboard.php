<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f4; padding: 20px; }
        .admin-container { max-width: 900px; margin: auto; background-color: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1); }
        .admin-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        .admin-menu a { margin-left: 15px; text-decoration: none; color: #007bff; font-weight: bold; }
        .admin-menu a:hover { text-decoration: underline; }
        h1 { color: #333; margin: 0; }
        .resumen-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-top: 20px; }
        .resumen-card { background-color: #f8f9fa; padding: 20px; border-radius: 8px; border: 1px solid #e9ecef; }
        .resumen-card h3 { margin-top: 0; color: #6c757d; }
        .resumen-card p { font-size: 2.5rem; font-weight: bold; color: #343a40; margin: 0; }
        .eventos-recientes ul { list-style: none; padding: 0; }
        .eventos-recientes li { border-bottom: 1px dashed #e9ecef; padding: 10px 0; }
        .eventos-recientes li:last-child { border-bottom: none; }
    </style>
</head>
<body>
    <div class="admin-container">
        <div class="admin-header">
            <h1>Panel de Administración</h1>
            <div class="admin-menu">
              <a href="../public/index.php?action=admin_qr_validator">Validar QR</a>
              <a href="../public/index.php?action=admin_eventos_list">Gestionar Eventos</a>
              <a href="../public/index.php?action=admin_usuarios_list">Gestionar Usuarios</a>
              <a href="../public/index.php?action=logout">Cerrar Sesión</a>
          </div>
          </div>

          <p>Bienvenido al panel de administración. Aquí podrás gestionar los eventos y reservaciones.</p>
          <div class="resumen-grid">
            <div class="resumen-card">
                <h3>Total de Eventos</h3>
                <p><?php echo htmlspecialchars($resumen['total_eventos']); ?></p>
            </div>
            <div class="resumen-card">
                <h3>Total de Reservas</h3>
                <p><?php echo htmlspecialchars($resumen['total_reservaciones']); ?></p>
            </div>
            <div class="resumen-card">
                <h3>Evento Más Reservado</h3>
                <?php if ($resumen['evento_mas_reservado']): ?>
                    <p style="font-size: 1.2rem;"><?php echo htmlspecialchars($resumen['evento_mas_reservado']['nombre_evento']); ?></p>
                    <small>con <?php echo htmlspecialchars($resumen['evento_mas_reservado']['total_reservas']); ?> reservas</small>
                <?php else: ?>
                    <p style="font-size: 1rem;">No hay reservas todavía</p>
                <?php endif; ?>
            </div>
        </div>

        <div class="resumen-card eventos-recientes" style="margin-top: 20px;">
            <h3>Eventos Recientes</h3>
            <?php if (!empty($resumen['eventos_recientes'])): ?>
            <ul>
                <?php foreach ($resumen['eventos_recientes'] as $evento): ?>
                    <li>
                        <strong><?php echo htmlspecialchars($evento['nombre_evento']); ?></strong>
                        <br>
                        <small>Fecha: <?php echo htmlspecialchars($evento['fecha']); ?> Hora: <?php echo htmlspecialchars($evento['hora_inicio']); ?></small>
                    </li>
                <?php endforeach; ?>
            </ul>
            <?php else: ?>
                <p style="text-align: center;">No hay eventos recientes.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>