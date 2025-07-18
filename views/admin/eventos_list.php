<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Eventos</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f4; padding: 20px; }
        .admin-container { max-width: 900px; margin: auto; background-color: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1); }
        .admin-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        .admin-header h1 { margin: 0; }
        .admin-menu a { margin-left: 15px; text-decoration: none; color: #007bff; font-weight: bold; }
        .admin-menu a:hover { text-decoration: underline; }
        .success-message, .error-message { padding: 10px; margin-bottom: 15px; border-radius: 4px; text-align: center; }
        .success-message { background-color: #d4edda; color: #155724; }
        .error-message { background-color: #f8d7da; color: #721c24; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 10px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background-color: #f2f2f2; }
        .action-links a { margin-right: 10px; text-decoration: none; }
        .btn-add { display: inline-block; padding: 8px 15px; background-color: #28a745; color: white; text-decoration: none; border-radius: 4px; }
    </style>
</head>
<body>
    <div class="admin-container">
        <div class="admin-header">
            <h1>Gestión de Eventos</h1>
            <div class="admin-menu">
              <a href="../public/index.php?action=admin_dashboard">Dashboard</a>
              <a href="../public/index.php?action=admin_usuarios_list">Gestionar Usuarios</a>
              <a href="../public/index.php?action=logout">Cerrar Sesión</a>
            </div>
        </div>
        
        <p><a href="../public/index.php?action=admin_evento_form" class="btn-add">Crear Nuevo Evento</a></p>

        <?php
        // ... (El código para mostrar mensajes de la sesión es el mismo) ...
        // session_start();
        if (isset($_SESSION['admin_mensaje'])) {
            $clase = ($_SESSION['admin_exito']) ? 'success-message' : 'error-message';
            echo '<div class="' . $clase . '">' . $_SESSION['admin_mensaje'] . '</div>';
            unset($_SESSION['admin_mensaje']);
            unset($_SESSION['admin_exito']);
        }
        ?>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Fecha</th>
                    <th>Hora</th>
                    <th>Cupo</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($eventos)): ?>
                    <?php foreach ($eventos as $evento): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($evento['id_evento']); ?></td>
                            <td><?php echo htmlspecialchars($evento['nombre_evento']); ?></td>
                            <td><?php echo htmlspecialchars($evento['fecha']); ?></td>
                            <td><?php echo htmlspecialchars($evento['hora_inicio']) . ' - ' . htmlspecialchars($evento['hora_fin']); ?></td>
                            <td><?php echo htmlspecialchars($evento['cupo_disponible']) . ' / ' . htmlspecialchars($evento['cupo_maximo']); ?></td>
                            <td class="action-links">
                                <a href="../public/index.php?action=admin_evento_form&id=<?php echo htmlspecialchars($evento['id_evento']); ?>">Editar</a>
                                <a href="../public/index.php?action=admin_eliminar_evento&id=<?php echo htmlspecialchars($evento['id_evento']); ?>" onclick="return confirm('¿Estás seguro de que quieres eliminar este evento?');">Eliminar</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="6" style="text-align: center;">No hay eventos para mostrar.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>