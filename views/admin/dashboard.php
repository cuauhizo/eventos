<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        :root {
            --primary-color: #4e73df;
            --secondary-color: #1cc88a;
            --danger-color: #e74a3b;
            --warning-color: #f6c23e;
            --info-color: #36b9cc;
        }
        
        body {
            background-color: #f8f9fc;
            font-family: 'Nunito', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
        }
        
        .admin-container {
            background-color: #fff;
            border-radius: 15px;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
            padding: 2rem;
            margin-top: 2rem;
        }
        
        .admin-header {
            border-bottom: 1px solid #e3e6f0;
            padding-bottom: 1rem;
            margin-bottom: 2rem;
        }
        
        .card {
            border: none;
            border-radius: 0.35rem;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.1);
            transition: transform 0.3s;
        }
        
        .card:hover {
            transform: translateY(-5px);
        }
        
        .card-primary {
            border-left: 0.25rem solid var(--primary-color);
        }
        
        .card-success {
            border-left: 0.25rem solid var(--secondary-color);
        }
        
        .card-warning {
            border-left: 0.25rem solid var(--warning-color);
        }
        
        .card-icon {
            font-size: 2rem;
            opacity: 0.3;
            position: absolute;
            right: 1rem;
            top: 1rem;
        }
        
        .stat-number {
            font-size: 2rem;
            font-weight: 700;
        }
        
        .nav-link {
            color: #d1d3e2;
            font-weight: 600;
            padding: 0.75rem 1rem;
        }
        
        .nav-link:hover, .nav-link.active {
            color: #b7b9cc;
        }
        
        .nav-link i {
            margin-right: 0.5rem;
        }
        
        .recent-event-item {
            border-left: 0.25rem solid var(--primary-color);
            padding-left: 1rem;
            margin-bottom: 1rem;
        }
        
        .recent-event-item:last-child {
            margin-bottom: 0;
        }
        
        @media (max-width: 768px) {
            .admin-menu {
                display: flex;
                flex-wrap: wrap;
                gap: 0.5rem;
                margin-top: 1rem;
            }
            
            .admin-menu a {
                margin-left: 0 !important;
            }
        }
    </style>
</head>
<body>
    <div class="container py-4">
        <div class="admin-container p-4 mb-4">
            <div class="admin-header d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center ">
                <div>
                    <h1 class="h2 mb-3 mb-md-0 text-gray-800">
                        <i class="bi bi-speedometer2 me-2"></i>Panel de Administración
                    </h1>
                    <p class="text-muted mb-4">Bienvenido al panel de administración. Aquí podrás gestionar los eventos y reservaciones.</p>
                </div>
                <div class="admin-menu d-flex flex-wrap">
                    <!-- <a href="../public/index.php?action=admin_import_form" class="btn btn-outline-primary btn-sm me-2 mb-2">
                        <i class="bi bi-upload me-1"></i>Importar
                    </a> -->
                    <a href="../public/index.php?action=admin_pending_reservations" class="btn btn-outline-warning btn-sm me-2 mb-2">
                        <i class="bi bi-hourglass-split me-1"></i>Pendientes
                    </a>
                    <a href="../public/index.php?action=admin_eventos_list" class="btn btn-outline-primary btn-sm me-2 mb-2">
                        <i class="bi bi-calendar-event me-1"></i>Eventos
                    </a>
                    <a href="../public/index.php?action=admin_usuarios_list" class="btn btn-outline-primary btn-sm me-2 mb-2">
                        <i class="bi bi-people me-1"></i>Usuarios
                    </a>
                    <a href="../public/index.php?action=logout" class="btn btn-danger btn-sm mb-2">
                        <i class="bi bi-box-arrow-right me-1"></i>Salir
                    </a>
                </div>
            </div>

            
            
            <div class="row g-4 mb-4">
                <div class="col-md-4">
                    <div class="card card-primary h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-uppercase text-primary mb-2">Total de Eventos</h6>
                                    <h2 class="stat-number mb-0"><?php echo htmlspecialchars($resumen['total_eventos']); ?></h2>
                                </div>
                                <i class="bi bi-calendar-check card-icon text-primary"></i>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="card card-success h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-uppercase text-success mb-2">Total de Reservas</h6>
                                    <h2 class="stat-number mb-0"><?php echo htmlspecialchars($resumen['total_reservaciones']); ?></h2>
                                </div>
                                <i class="bi bi-ticket-perforated card-icon text-success"></i>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="card card-warning h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-uppercase text-warning mb-2">Evento Más Reservado</h6>
                                    <?php if ($resumen['evento_mas_reservado']): ?>
                                        <h4 class="mb-1"><?php echo htmlspecialchars($resumen['evento_mas_reservado']['nombre_evento']); ?></h4>
                                        <p class="mb-0 text-gray-600"><small><?php echo htmlspecialchars($resumen['evento_mas_reservado']['total_reservas']); ?> reservas</small></p>
                                    <?php else: ?>
                                        <p class="mb-0 text-gray-600">No hay reservas todavía</p>
                                    <?php endif; ?>
                                </div>
                                <i class="bi bi-star-fill card-icon text-warning"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="row g-4">
                <div class="col-lg-8">
                    <div class="card h-100">
                        <div class="card-header bg-white py-3">
                            <h6 class="m-0 font-weight-bold text-primary">
                                <i class="bi bi-calendar-week me-2"></i>Eventos Recientes
                            </h6>
                        </div>
                        <div class="card-body">
                            <?php if (!empty($resumen['eventos_recientes'])): ?>
                                <div class="list-group list-group-flush">
                                    <?php foreach ($resumen['eventos_recientes'] as $evento): ?>
                                        <div class="recent-event-item">
                                            <h5 class="mb-1"><?php echo htmlspecialchars($evento['nombre_evento']); ?></h5>
                                            <p class="mb-1 text-muted">
                                                <i class="bi bi-calendar-date me-1"></i><?php echo htmlspecialchars($evento['fecha']); ?>
                                                <i class="bi bi-clock ms-2 me-1"></i><?php echo htmlspecialchars($evento['hora_inicio']); ?>
                                            </p>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <div class="text-center py-4">
                                    <i class="bi bi-calendar-x text-muted" style="font-size: 2rem;"></i>
                                    <p class="mt-2 text-muted">No hay eventos recientes</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-4">
                    <div class="card h-100">
                        <div class="card-header bg-white py-3">
                            <h6 class="m-0 font-weight-bold text-warning">
                                <i class="bi bi-hourglass-top me-2"></i>Reservaciones Pendientes
                            </h6>
                        </div>
                        <div class="card-body d-flex flex-column">
                            <div class="text-center py-3">
                                <h1 class="text-warning"><?php echo $resumen['total_reservaciones_pendientes'] ?? '0'; ?></h1>
                                <p class="text-muted mb-4">reservaciones por aprobar</p>
                            </div>
                            <a href="index.php?action=admin_pending_reservations" class="btn btn-warning mt-auto align-self-start">
                                <i class="bi bi-arrow-right-circle me-1"></i>Gestionar Pendientes
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>