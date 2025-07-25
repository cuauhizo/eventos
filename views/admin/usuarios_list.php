<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Usuarios</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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
        
        .badge-role {
            padding: 0.35em 0.65em;
            font-size: 0.75em;
            font-weight: 600;
            border-radius: 0.25rem;
            text-transform: uppercase;
        }
        
        .badge-admin {
            background-color: var(--danger-color);
            color: white;
        }
        
        .badge-user {
            background-color: var(--success-color);
            color: white;
        }
        
        .badge-staff {
            background-color: var(--info-color);
            color: white;
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
        
        /* ESTILOS NUEVOS PARA ASISTENCIA */
        .asistencia-si {
            background-color: #d4edda; /* Verde claro para filas de asistidos */
            color: #155724;
            font-weight: bold;
        }
        .asistencia-si .badge { /* Ajuste del badge en fila asistida */
            background-color: #155724!important;
        }
        .asistencia-checkbox {
            cursor: pointer;
            transform: scale(1.2); /* Hacer el checkbox un poco más grande */
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
                        <i class="bi bi-people-fill me-2"></i>Gestión de Usuarios
                    </h1>
                    <p class="mb-0 text-muted">Administra los usuarios registrados en el sistema</p>
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
            
            <form action="../public/index.php" method="GET" class="mb-4">
                <input type="hidden" name="action" value="admin_usuarios_list"> <div class="input-group">
                    <input type="text" class="form-control" placeholder="Buscar usuario por nombre, correo, ID..." name="search" value="<?php echo htmlspecialchars($search_query ?? ''); ?>">
                    <button class="btn btn-primary" type="submit">
                        <i class="bi bi-search"></i> Buscar
                    </button>
                    <?php if (!empty($search_query)): ?>
                        <a href="../public/index.php?action=admin_usuarios_list" class="btn btn-outline-secondary">
                            <i class="bi bi-x-lg"></i> Limpiar
                        </a>
                    <?php endif; ?>
                </div>
            </form>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre Completo</th>
                            <th>Correo</th>
                            <th>ID Empleado</th>
                            <th>Rol</th>
                            <th>Registro</th>
                            <th>Asistencia</th> 
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($usuarios)): ?>
                            <?php foreach ($usuarios as $usuario): 
                                $badgeClass = '';
                                switch(strtolower($usuario['rol'])) {
                                    case 'admin':
                                        $badgeClass = 'badge-admin';
                                        break;
                                    case 'staff':
                                        $badgeClass = 'badge-staff';
                                        break;
                                    default:
                                        $badgeClass = 'badge-user';
                                }
                                $asistenciaClass = ($usuario['asistencia'] == 1) ? 'asistencia-si' : ''; 
                            ?>
                                <tr class="<?php echo $asistenciaClass; ?>"> 
                                    <td><?php echo htmlspecialchars($usuario['id_usuario']); ?></td>
                                    <td>
                                        <strong><?php echo htmlspecialchars($usuario['nombre'] . ' ' . $usuario['apellidos']); ?></strong>
                                    </td>
                                    <td>
                                        <a href="mailto:<?php echo htmlspecialchars($usuario['correo']); ?>">
                                            <?php echo htmlspecialchars($usuario['correo']); ?>
                                        </a>
                                    </td>
                                    <td><?php echo htmlspecialchars($usuario['id_empleado']); ?></td>
                                    <td>
                                        <span class="badge <?php echo $badgeClass; ?>">
                                            <?php echo htmlspecialchars($usuario['rol']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <small><?php echo htmlspecialchars($usuario['fecha_registro']); ?></small>
                                    </td>
                                    <td>
                                        <input type="checkbox" 
                                               class="form-check-input asistencia-checkbox" 
                                               data-user-id="<?php echo htmlspecialchars($usuario['id_usuario']); ?>"
                                               <?php echo ($usuario['asistencia'] == 1) ? 'checked' : ''; ?>>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7"> 
                                    <div class="empty-state">
                                        <i class="bi bi-people"></i>
                                        <h3 class="h5 text-gray-800 mb-2">No hay usuarios registrados</h3>
                                        <p class="text-muted">No se encontraron usuarios en la base de datos.</p>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
            <?php if (!empty($usuarios)): ?>
            <div class="d-flex justify-content-between align-items-center mt-3">
                <div class="text-muted">
                    Mostrando <strong><?php echo count($usuarios); ?></strong> usuarios
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // JavaScript para manejar la actualización de asistencia por AJAX
        document.addEventListener('DOMContentLoaded', function() {
            // Seleccionar todos los checkboxes de asistencia
            const asistenciaCheckboxes = document.querySelectorAll('.asistencia-checkbox');

            asistenciaCheckboxes.forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    const userId = this.dataset.userId; 
                    const isChecked = this.checked; 
                    const row = this.closest('tr'); 

                    fetch('../public/index.php?action=admin_update_asistencia', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: `id_usuario=${userId}&asistencia=${isChecked ? 1 : 0}`
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            if (isChecked) {
                                row.classList.add('asistencia-si');
                            } else {
                                row.classList.remove('asistencia-si');
                            }
                        } else {
                            this.checked = !isChecked; 
                            alert('Error al actualizar la asistencia: ' + data.message);
                        }
                    })
                    .catch(error => {
                        this.checked = !isChecked; 
                        console.error('Error de red al actualizar asistencia:', error);
                        alert('Error de conexión al actualizar asistencia. Inténtalo de nuevo.');
                    });
                });
            });

            // Activar tooltips (código existente)
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[title]'));
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        });
    </script>
</body>
</html>