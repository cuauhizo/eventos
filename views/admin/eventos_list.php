<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Eventos</title>
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
        
        .admin-container {
            background-color: #fff;
            border-radius: 15px;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.1);
            padding: 2rem;
            margin-top: 2rem;
        }
        
        .admin-header {
            border-bottom: 1px solid #e3e6f0;
            padding-bottom: 1rem;
            margin-bottom: 2rem;
        }
        
        .table-responsive {
            border-radius: 0.5rem;
            overflow: hidden;
        }
        
        .table {
            margin-bottom: 0;
        }
        
        .table thead th {
            background-color: var(--primary-color);
            color: white;
            border-bottom-width: 1px;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.5px;
        }
        
        .table tbody tr {
            transition: all 0.2s;
        }
        
        .table tbody tr:hover {
            background-color: rgba(78, 115, 223, 0.05);
        }
        
        .badge-category {
            padding: 0.35em 0.65em;
            font-size: 0.75em;
            font-weight: 600;
            border-radius: 0.25rem;
        }
        
        .badge-success {
            background-color: var(--success-color);
        }
        
        .badge-warning {
            background-color: var(--warning-color);
            color: #000;
        }
        
        .badge-danger {
            background-color: var(--danger-color);
        }
        
        .empty-state {
            text-align: center;
            padding: 3rem;
            background-color: #f8f9fa;
            border-radius: 0.5rem;
        }
        
        .empty-state i {
            font-size: 3rem;
            color: #d1d3e2;
            margin-bottom: 1rem;
        }
        
        .action-buttons .btn {
            margin-right: 0.5rem;
            margin-bottom: 0.5rem;
        }
        
        .cupo-progress {
            height: 0.5rem;
            border-radius: 0.25rem;
        }
        
        @media (max-width: 768px) {
            .admin-menu {
                margin-top: 1rem;
            }
            
            .admin-menu a {
                margin-left: 0 !important;
                margin-right: 1rem;
                margin-bottom: 0.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="container py-4">
        <div class="admin-container">
            <div class="admin-header d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
                <div>
                    <h1 class="h3 mb-2 text-gray-800">
                        <i class="bi bi-calendar-event me-2"></i>Gestión de Eventos
                    </h1>
                    <p class="mb-0 text-muted">Administra los eventos del sistema</p>
                </div>
                <div class="admin-menu d-flex flex-wrap mt-3 mt-md-0">
                    <a href="../public/index.php?action=admin_dashboard" class="btn btn-outline-primary btn-sm me-2 mb-2">
                        <i class="bi bi-speedometer2 me-1"></i>Dashboard
                    </a>
                    <a href="../public/index.php?action=admin_usuarios_list" class="btn btn-outline-primary btn-sm me-2 mb-2">
                        <i class="bi bi-people-fill me-1"></i>Usuarios
                    </a>
                    <a href="../public/index.php?action=admin_eventos_list" class="btn btn-outline-primary btn-sm me-2 mb-2">
                        <i class="bi bi-calendar-event me-1"></i>Eventos
                    </a>
                    <a href="../public/index.php?action=logout" class="btn btn-danger btn-sm me-2 mb-2">
                        <i class="bi bi-box-arrow-right me-1"></i>Salir
                    </a>
                </div>
            </div>
            
            <div class="d-flex justify-content-between align-items-center mb-3">
                <a href="../public/index.php?action=admin_evento_form" class="btn btn-success">
                    <i class="bi bi-plus-circle-fill me-1"></i>Crear Nuevo Evento
                </a>
                <div class="text-muted">
                    <?php if (!empty($eventos)): ?>
                        Mostrando <strong><?php echo count($eventos); ?></strong> eventos
                    <?php endif; ?>
                </div>
            </div>

            <?php if (isset($_SESSION['admin_mensaje'])): ?>
                <div class="alert alert-<?php echo ($_SESSION['admin_exito']) ? 'success' : 'danger'; ?> alert-dismissible fade show" role="alert">
                    <i class="bi <?php echo ($_SESSION['admin_exito']) ? 'bi-check-circle-fill' : 'bi-exclamation-triangle-fill'; ?> me-2"></i>
                    <?php echo $_SESSION['admin_mensaje']; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php 
                unset($_SESSION['admin_mensaje']);
                unset($_SESSION['admin_exito']);
                ?>
            <?php endif; ?>

            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Categoría</th>
                            <th>Código</th>
                            <th>Ubicación</th>
                            <th>Fecha/Hora</th>
                            <th>Cupo</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($eventos)): ?>
                            <?php foreach ($eventos as $evento): 
                                $porcentaje_ocupado = ($evento['cupo_maximo'] > 0) ? 
                                    (($evento['cupo_maximo'] - $evento['cupo_disponible']) / $evento['cupo_maximo']) * 100 : 0;
                                $progress_class = ($porcentaje_ocupado >= 90) ? 'bg-danger' : 
                                               (($porcentaje_ocupado >= 70) ? 'bg-warning' : 'bg-success');
                            ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($evento['id_evento']); ?></td>
                                    <td>
                                        <strong><?php echo htmlspecialchars($evento['nombre_evento']); ?></strong>
                                    </td>
                                    <td>
                                        <span class="badge badge-category bg-info">
                                            <?php echo htmlspecialchars($evento['nombre_categoria']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <code><?php echo htmlspecialchars($evento['codigo_evento']); ?></code>
                                    </td>
                                    <td>
                                        <small><?php echo htmlspecialchars($evento['ubicacion']); ?></small>
                                    </td>
                                    <td>
                                        <div><?php echo htmlspecialchars($evento['fecha']); ?></div>
                                        <small class="text-muted">
                                            <?php echo htmlspecialchars($evento['hora_inicio']) . ' - ' . htmlspecialchars($evento['hora_fin']); ?>
                                        </small>
                                    </td>
                                    <td>
                                        <div class="d-flex justify-content-between">
                                            <small>
                                                <?php echo ($evento['cupo_maximo'] - $evento['cupo_disponible']) . '/' . $evento['cupo_maximo']; ?>
                                            </small>
                                            <small><?php echo number_format($porcentaje_ocupado, 0); ?>%</small>
                                        </div>
                                        <div class="progress cupo-progress">
                                            <div class="progress-bar <?php echo $progress_class; ?>" 
                                                 role="progressbar" 
                                                 style="width: <?php echo $porcentaje_ocupado; ?>%" 
                                                 aria-valuenow="<?php echo $porcentaje_ocupado; ?>" 
                                                 aria-valuemin="0" 
                                                 aria-valuemax="100">
                                            </div>
                                        </div>
                                    </td>
                                    <td class="action-buttons">
                                        <a href="../public/index.php?action=admin_evento_form&id=<?php echo htmlspecialchars($evento['id_evento']); ?>" 
                                           class="btn btn-sm btn-outline-primary" 
                                           title="Editar evento">
                                            <i class="bi bi-pencil-fill"></i>
                                        </a>
                                        <a href="../public/index.php?action=admin_eliminar_evento&id=<?php echo htmlspecialchars($evento['id_evento']); ?>" 
                                           class="btn btn-sm btn-outline-danger" 
                                           title="Eliminar evento"
                                           onclick="return confirm('¿Estás seguro de que quieres eliminar este evento?');">
                                            <i class="bi bi-trash-fill"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8">
                                    <div class="empty-state">
                                        <i class="bi bi-calendar-x"></i>
                                        <h3 class="h5 text-gray-800 mb-2">No hay eventos registrados</h3>
                                        <p class="text-muted">No se encontraron eventos en la base de datos.</p>
                                        <a href="../public/index.php?action=admin_evento_form" class="btn btn-primary mt-2">
                                            <i class="bi bi-plus-circle-fill me-1"></i>Crear Primer Evento
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
            <?php if (!empty($eventos)): ?>
            <div class="d-flex justify-content-between align-items-center mt-3">
                <div class="text-muted">
                    Mostrando <strong><?php echo count($eventos); ?></strong> eventos
                </div>
                <nav aria-label="Page navigation">
                    <ul class="pagination pagination-sm mb-0">
                        <li class="page-item disabled">
                            <a class="page-link" href="#" tabindex="-1" aria-disabled="true">Anterior</a>
                        </li>
                        <li class="page-item active"><a class="page-link" href="#">1</a></li>
                        <li class="page-item"><a class="page-link" href="#">2</a></li>
                        <li class="page-item"><a class="page-link" href="#">3</a></li>
                        <li class="page-item">
                            <a class="page-link" href="#">Siguiente</a>
                        </li>
                    </ul>
                </nav>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Activar tooltips
        document.addEventListener('DOMContentLoaded', function() {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[title]'));
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        });
    </script>
</body>
</html>