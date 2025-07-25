<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $evento ? 'Editar Evento' : 'Crear Nuevo Evento'; ?></title>
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
        }
        
        body {
            background-color: #f8f9fc;
            font-family: 'Nunito', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
        }
        
        .form-container {
            background-color: #fff;
            border-radius: 15px;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.1);
            padding: 2.5rem;
            width: 100%;
            max-width: 700px;
            margin: 2rem auto;
        }
        
        .form-header {
            text-align: center;
            margin-bottom: 2rem;
            border-bottom: 1px solid #e3e6f0;
            padding-bottom: 1rem;
        }
        
        .form-header h1 {
            font-size: 1.75rem;
            color: #5a5c69;
        }
        
        .form-label {
            font-weight: 600;
            color: #5a5c69;
        }
        
        .form-control, .form-select {
            padding: 0.75rem 1rem;
            border-radius: 0.35rem;
            border: 1px solid #d1d3e2;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.25rem rgba(78, 115, 223, 0.25);
        }
        
        .btn-submit {
            padding: 0.75rem;
            font-weight: 600;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }
        
        .btn-cancel {
            color: var(--danger-color);
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s;
        }
        
        .btn-cancel:hover {
            color: #be2617;
            text-decoration: underline;
        }
        
        .form-note {
            font-size: 0.85rem;
            color: #858796;
            font-style: italic;
        }
        
        .time-inputs {
            display: flex;
            gap: 1rem;
        }
        
        .time-inputs .form-group {
            flex: 1;
        }
        
        @media (max-width: 768px) {
            .form-container {
                padding: 1.5rem;
                margin: 1rem auto;
            }
            
            .time-inputs {
                flex-direction: column;
                gap: 0;
            }
        }
    </style>
</head>
<body>
    <div class="container py-4">
        <div class="form-container">
            <div class="form-header">
                <h1>
                    <i class="bi bi-calendar-event me-2"></i>
                    <?php echo $evento ? 'Editar Evento' : 'Crear Nuevo Evento'; ?>
                </h1>
            </div>

            <?php if (isset($_SESSION['admin_mensaje_form'])): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    <?php echo $_SESSION['admin_mensaje_form']; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php unset($_SESSION['admin_mensaje_form']); ?>
            <?php endif; ?>

            <form action="../public/index.php?action=admin_guardar_evento" method="POST">
                <?php if ($evento): ?>
                    <input type="hidden" name="id_evento" value="<?php echo htmlspecialchars($evento['id_evento']); ?>">
                <?php endif; ?>
                
                <div class="row mb-4">
                    <div class="col-md-8 mb-3 mb-md-0">
                        <label for="nombre_evento" class="form-label">Nombre del Evento</label>
                        <input type="text" class="form-control" id="nombre_evento" name="nombre_evento" 
                               value="<?php echo $evento ? htmlspecialchars($evento['nombre_evento']) : ''; ?>" required>
                    </div>
                    
                    <div class="col-md-4">
                        <label for="id_categoria" class="form-label">Categoría</label>
                        <select class="form-select" id="id_categoria" name="id_categoria" required>
                            <?php foreach ($categorias as $categoria): ?>
                                <option value="<?php echo htmlspecialchars($categoria['id_categoria']); ?>"
                                        <?php echo ($evento && $evento['id_categoria'] == $categoria['id_categoria']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($categoria['nombre_categoria']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                
                <div class="row mb-4">
                    <div class="col-md-6 mb-3 mb-md-0">
                        <label for="codigo_evento" class="form-label">Código de Evento</label>
                        <input type="text" class="form-control" id="codigo_evento" name="codigo_evento" 
                               value="<?php echo $evento ? htmlspecialchars($evento['codigo_evento']) : ''; ?>" required>
                        <div class="form-note mt-1">Código único para identificar el evento</div>
                    </div>
                    
                    <div class="col-md-6">
                        <label for="cupo_maximo" class="form-label">Cupo Máximo</label>
                        <input type="number" class="form-control" id="cupo_maximo" name="cupo_maximo" 
                               value="<?php echo $evento ? htmlspecialchars($evento['cupo_maximo']) : ''; ?>" required min="1">
                    </div>
                </div>
                
                <div class="mb-4">
                    <label for="descripcion" class="form-label">Descripción</label>
                    <textarea class="form-control" id="descripcion" name="descripcion" rows="4"><?php echo $evento ? htmlspecialchars($evento['descripcion']) : ''; ?></textarea>
                </div>
                
                <div class="row mb-4">
                    <div class="col-md-6 mb-3 mb-md-0">
                        <label for="fecha" class="form-label">Fecha del Evento</label>
                        <input type="date" class="form-control" id="fecha" name="fecha" 
                               value="<?php echo $evento ? htmlspecialchars($evento['fecha']) : ''; ?>" required>
                    </div>
                    
                    <div class="col-md-6">
                        <label class="form-label">Horario</label>
                        <div class="time-inputs">
                            <div class="form-group mb-0">
                                <input type="time" class="form-control" id="hora_inicio" name="hora_inicio" 
                                       value="<?php echo $evento ? htmlspecialchars($evento['hora_inicio']) : ''; ?>" required>
                                <div class="form-note mt-1">Inicio</div>
                            </div>
                            <div class="form-group mb-0">
                                <input type="time" class="form-control" id="hora_fin" name="hora_fin" 
                                       value="<?php echo $evento ? htmlspecialchars($evento['hora_fin']) : ''; ?>" required>
                                <div class="form-note mt-1">Fin</div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="mb-4">
                    <label for="ubicacion" class="form-label">Ubicación</label>
                    <input type="text" class="form-control" id="ubicacion" name="ubicacion" 
                           value="<?php echo $evento ? htmlspecialchars($evento['ubicacion']) : ''; ?>" required>
                </div>
                
                <div class="d-grid gap-2 mb-3">
                    <button type="submit" class="btn btn-primary btn-submit">
                        <i class="bi bi-save-fill me-2"></i>
                        <?php echo $evento ? 'Actualizar Evento' : 'Crear Evento'; ?>
                    </button>
                </div>
                
                <div class="text-center">
                    <a href="../public/index.php?action=admin_eventos_list" class="btn-cancel">
                        <i class="bi bi-x-circle me-1"></i>Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Validación básica de fechas
        document.addEventListener('DOMContentLoaded', function() {
            const fechaInput = document.getElementById('fecha');
            const hoy = new Date().toISOString().split('T')[0];
            
            // Establecer fecha mínima como hoy para nuevos eventos
            if (!<?php echo $evento ? 'true' : 'false'; ?>) {
                fechaInput.min = hoy;
            }
            
            // Validar que hora fin sea mayor que hora inicio
            const horaInicio = document.getElementById('hora_inicio');
            const horaFin = document.getElementById('hora_fin');
            
            function validarHoras() {
                if (horaInicio.value && horaFin.value && horaInicio.value >= horaFin.value) {
                    horaFin.setCustomValidity('La hora de fin debe ser posterior a la hora de inicio');
                } else {
                    horaFin.setCustomValidity('');
                }
            }
            
            horaInicio.addEventListener('change', validarHoras);
            horaFin.addEventListener('change', validarHoras);
        });
    </script>
</body>
</html>