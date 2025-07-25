<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reservas Pendientes</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        :root {
            --primary-color: #4e73df;
            --success-color: #1cc88a;
            --warning-color: #f6c23e;
            --danger-color: #e74a3b;
            --info-color: #36b9cc;
        }
        
        body {
            background-color: #f8f9fc;
            font-family: 'Nunito', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
        }
        
        .header-section {
            background-color: #fff;
            border-radius: 15px;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.1);
            padding: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .reserva-group-card {
            border: none;
            border-radius: 0.5rem;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.1);
            transition: transform 0.3s;
            margin-bottom: 1.5rem;
            overflow: hidden;
        }
        
        .reserva-group-card:hover {
            transform: translateY(-5px);
        }
        
        .reserva-group-header {
            background-color: var(--primary-color);
            color: white;
            padding: 1rem;
            position: relative;
        }
        
        .reserva-group-header h2 {
            font-size: 1.25rem;
            margin-bottom: 0.5rem;
        }
        
        .user-info {
            font-size: 0.9rem;
            opacity: 0.9;
        }
        
        .estado-reserva {
            position: absolute;
            top: 1rem;
            right: 1rem;
            font-weight: bold;
            padding: 0.25rem 0.75rem;
            border-radius: 0.25rem;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .estado-pendiente {
            background-color: var(--warning-color);
            color: #000;
        }
        
        .estado-confirmada {
            background-color: var(--success-color);
            color: #fff;
        }
        
        .estado-cancelada {
            background-color: var(--danger-color);
            color: #fff;
        }
        
        .reserva-item {
            border-left: 3px solid var(--primary-color);
            padding: 1rem;
            margin-bottom: 0.5rem;
            background-color: #fff;
            transition: all 0.3s;
        }
        
        .reserva-item:hover {
            background-color: #f8f9fa;
        }
        
        .reserva-item:last-child {
            margin-bottom: 0;
        }
        
        .event-title {
            font-weight: 600;
            margin-bottom: 0.25rem;
        }
        
        .event-details {
            font-size: 0.85rem;
            color: #6c757d;
        }
        
        .event-details i {
            width: 1.25rem;
            text-align: center;
            margin-right: 0.25rem;
        }
        
        .action-buttons {
            padding: 1rem;
            background-color: #f8f9fa;
            border-top: 1px solid #e3e6f0;
        }
        
        .empty-state {
            text-align: center;
            padding: 3rem;
            background-color: #fff;
            border-radius: 0.5rem;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.1);
        }
        
        .empty-state i {
            font-size: 3rem;
            color: #d1d3e2;
            margin-bottom: 1rem;
        }
        
        @media (max-width: 768px) {
            .reserva-group-card {
                margin-bottom: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="container py-4">
        <div class="header-section">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0 text-gray-800">
                        <i class="bi bi-hourglass-top me-2"></i>Reservaciones Pendientes
                    </h1>
                    <p class="mb-0 text-muted">Gestiona las reservas que requieren aprobación</p>
                </div>
                <a href="index.php?action=admin_dashboard" class="btn btn-outline-primary">
                    <i class="bi bi-arrow-left me-1"></i>Volver al Panel
                </a>
            </div>
        </div>

        <?php 
        // Mensajes de sesión del AdminController
        if (isset($_SESSION['admin_mensaje'])):
            $alert_class = ($_SESSION['admin_exito']) ? 'alert-success' : 'alert-danger';
        ?>
            <div class="alert <?php echo $alert_class; ?> alert-dismissible fade show" role="alert">
                <i class="bi <?php echo ($_SESSION['admin_exito']) ? 'bi-check-circle-fill' : 'bi-exclamation-triangle-fill'; ?> me-2"></i>
                <?php echo $_SESSION['admin_mensaje']; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php 
            unset($_SESSION['admin_mensaje']);
            unset($_SESSION['admin_exito']);
        endif;
        ?>

        <?php 
        // Agrupar las reservas por ID de grupo
        $reservas_agrupadas = [];
        if (!empty($reservas_pendientes)) { // $reservas_pendientes viene del AdminController
            foreach ($reservas_pendientes as $reserva) {
                $reservas_agrupadas[$reserva['id_grupo']][] = $reserva;
            }
        }
        ?>

        <div class="row">
            <?php if (!empty($reservas_agrupadas)): ?>
                <?php foreach ($reservas_agrupadas as $id_grupo => $grupo_reservas): 
                    // Tomar el primer elemento del grupo para obtener información general
                    $reserva_ejemplo_grupo = $grupo_reservas[0]; 
                ?>
                    <div class="col-lg-6">
                        <div class="reserva-group-card">
                            <div class="reserva-group-header">
                                <h2>Grupo #<?php echo htmlspecialchars($id_grupo); ?></h2>
                                <p class="user-info mb-0">
                                    <i class="bi bi-person-fill"></i>
                                    <?php echo htmlspecialchars($reserva_ejemplo_grupo['nombre_usuario'] . ' ' . $reserva_ejemplo_grupo['apellidos_usuario']); ?>
                                </p>
                                <?php 
                                    $estado_general = $reserva_ejemplo_grupo['estado']; // Por defecto, es 'pendiente' aquí
                                    $estado_clase_general = 'estado-pendiente'; 
                                ?>
                                <span class="estado-reserva <?php echo $estado_clase_general; ?>">
                                    <?php echo htmlspecialchars(ucfirst($estado_general)); ?>
                                </span>
                            </div>

                            <div class="reserva-group-body p-3">
                                <?php foreach ($grupo_reservas as $reserva): ?>
                                    <div class="reserva-item">
                                        <h5 class="event-title"><?php echo htmlspecialchars($reserva['nombre_evento']); ?></h5>
                                        <div class="event-details">
                                            <div>
                                                <i class="bi bi-geo-alt"></i>
                                                <?php echo htmlspecialchars($reserva['ubicacion']); ?>
                                            </div>
                                            <div>
                                                <i class="bi bi-calendar"></i>
                                                <?php echo htmlspecialchars($reserva['fecha_evento']); ?>
                                            </div>
                                            <div>
                                                <i class="bi bi-clock"></i>
                                                <?php echo htmlspecialchars($reserva['hora_inicio']) . ' - ' . htmlspecialchars($reserva['hora_fin']); ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>

                            <div class="action-buttons text-center">
                                <a href="index.php?action=admin_confirm_reservation&id_grupo=<?php echo htmlspecialchars($id_grupo); ?>" 
                                   class="btn btn-success"
                                   onclick="return confirm('¿Estás seguro de que quieres confirmar TODAS las reservas para este grupo #<?php echo htmlspecialchars($id_grupo); ?>? Se enviará un correo de confirmación.');">
                                    <i class="bi bi-check-circle-fill me-1"></i>Confirmar Grupo
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12">
                    <div class="empty-state">
                        <i class="bi bi-hourglass"></i>
                        <h3 class="h4 text-gray-800 mb-3">No hay reservaciones pendientes</h3>
                        <p class="text-muted">Todas las reservas están gestionadas en este momento.</p>
                        <a href="index.php?action=admin_dashboard" class="btn btn-primary mt-3">
                            <i class="bi bi-arrow-left me-1"></i>Volver al Panel
                        </a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Confirmación para acciones importantes
        document.addEventListener('DOMContentLoaded', function() {
            // Activar tooltips si los hubiera
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        });
    </script>
</body>
</html>