<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-s cale=1.0">
    <title>Selección de Eventos - Sistema de Reservas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f4f4f4; }
        .eventos-container { 
            max-width: 1200px; margin: 50px auto; padding: 30px; 
            background-color: #fff; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); 
        }
        .evento-item { 
            cursor: pointer; transition: all 0.3s ease; 
            border: 2px solid #fff; 
        }
        .evento-item:hover { 
            border-color: #007bff; 
            box-shadow: 0 0 10px rgba(0, 123, 255, 0.25);
        }
        .evento-item.selected { 
            border-color: #28a745; 
            background-color: #e9f5e9; 
            box-shadow: 0 0 15px #28a74550;
        }
        .evento-item.disabled {
            cursor: not-allowed;
            opacity: 0.6;
            filter: grayscale(100%);
            border-color: #dc3545;
        }
        .evento-item.disabled:hover {
            box-shadow: none;
            border-color: #dc3545;
        }
        .evento-item input[type="checkbox"] { display: none; }

        .indispensables {
            cursor: default;
        }
    </style>
</head>
<body>
    <div class="eventos-container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <?php if (isset($_SESSION['loggedin'])): ?>
                <p class="mb-0">Bienvenido, <strong><?php echo htmlspecialchars($_SESSION['nombre'] ?? ''); ?></strong>!</p>
                <div>
                    <a href="../public/index.php?action=mis_reservas" class="btn btn-sm btn-outline-primary me-2">Mis Reservas</a>
                    <a href="../public/index.php?action=logout" class="btn btn-sm btn-danger">Cerrar Sesión</a>
                </div>
            <?php else: ?>
                <a href="../public/index.php?action=show_login_form">Iniciar Sesión</a>
            <?php endif; ?>
        </div>
        
        <h1 class="text-center mb-4">Eventos Deportivos</h1>
        <p class="text-center text-muted">Selecciona los eventos a los que te gustaría asistir (máximo 4).</p>

        <?php
        if (isset($_SESSION['reserva_mensaje'])):
            $alert_class = ($_SESSION['reserva_exito']) ? 'alert-success' : 'alert-danger';
            echo '<div class="alert ' . $alert_class . ' text-center">' . $_SESSION['reserva_mensaje'] . '</div>';
            unset($_SESSION['reserva_mensaje']);
            unset($_SESSION['reserva_exito']);
        endif;
        
        $eventos_preseleccionados = $_SESSION['eventos_preseleccionados'] ?? [];
        unset($_SESSION['eventos_preseleccionados']);
        
        $hay_cupo_disponible = false;
        if (!empty($eventos)) {
            foreach ($eventos as $evento) {
                if ($evento['cupo_disponible'] > 0) {
                    $hay_cupo_disponible = true;
                    break;
                }
            }
        }

        // Definir los horarios y sus títulos
        $horarios = [
            '11:45:00-12:30:00' => 'Bloque 1 (11:45 - 12:30)',
            '12:30:00-13:15:00' => 'Bloque 2 (12:30 - 13:15)',
            '13:15:00-14:00:00' => 'Bloque 3 (13:15 - 14:00)',
            '14:00:00-14:45:00' => 'Bloque 4 (14:00 - 14:45)',
        ];

        // Agrupar los eventos por horario
        $eventos_agrupados_por_horario = [];
        foreach ($eventos as $evento) {
            $hora_inicio_evento = $evento['hora_inicio'];
            
            foreach ($horarios as $rango_horas => $titulo) {
                list($hora_inicio_rango, $hora_fin_rango) = explode('-', $rango_horas);
                
                if ($hora_inicio_evento >= $hora_inicio_rango && $hora_inicio_evento < $hora_fin_rango) {
                    $eventos_agrupados_por_horario[$titulo][] = $evento;
                    break;
                }
            }
        }
        ?>

        <form action="../public/index.php?action=reservar" method="POST">
            <div class="row">
                <h2 class="mt-5 mb-3 text-center text-secondary">Indispensables</h2>
                <div class="col-md-12">
                    <p class="text-center text-muted mb-4">
                        <strong>Nota:</strong> Les pedimos su total atención y cooperación para estas actividades, ya que su participación es fundamental para el éxito colectivo de nuestro evento. ¡Estamos ansiosos por verlos en acción y celebrar juntos el espíritu deportivo!
                    </p>
                </div>
                <div class="col-md-6 mb-4">
                    <div class="card h-100 evento-item selected indispensables">
                        <div class="card-body">
                            <h5 class="card-title text-primary">Bienvenida Doug Bowles</h5>
                            <p class="card-text mb-1"><small class="text-muted"><strong>Ubicación:</strong> Gimnasio</small></p>
                            <p class="card-text mb-1"><small class="text-muted"><strong>Hora:</strong> 10:15:00 - 10:50:00</small></p>
                            <p class="card-text">lorem ipsum</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-4">
                    <div class="card h-100 evento-item selected indispensables">
                        <div class="card-body">
                            <h5 class="card-title text-primary">Team Building in Motion</h5>
                            <p class="card-text mb-1"><small class="text-muted"><strong>Ubicación:</strong> Cancha Fut A</small></p>
                            <p class="card-text mb-1"><small class="text-muted"><strong>Hora:</strong> 11:00:00 - 11:45:00</small></p>
                            <p class="card-text">lorem ipsum</p>
                        </div>
                    </div>
                </div>
            </div>
            <?php if (!empty($eventos_agrupados_por_horario) || !empty($eventos_preseleccionados)): ?>

                <?php foreach ($eventos_agrupados_por_horario as $horario_titulo => $eventos_en_horario): ?>
                    <h2 class="mt-5 mb-3 text-center text-secondary"><?php echo htmlspecialchars($horario_titulo); ?></h2>
                    <div class="row">
                        <?php foreach ($eventos_en_horario as $evento): 
                            $is_selected = in_array($evento['id_evento'], $eventos_preseleccionados);
                            $is_disabled = ($evento['cupo_disponible'] <= 0);
                            $selected_class = $is_selected ? 'selected' : '';
                            $disabled_class = $is_disabled ? 'disabled' : '';
                            $checked_attr = $is_selected ? 'checked' : '';
                            $disabled_attr = $is_disabled ? 'disabled' : '';
                        ?>
                            <div class="col-md-3 mb-4">
                                <div class="card h-100 evento-item <?php echo $selected_class . ' ' . $disabled_class; ?>" 
                                    onclick="toggleSelection(this, 'evento-<?php echo htmlspecialchars($evento['id_evento']); ?>')"
                                    data-block="<?php echo htmlspecialchars($horario_titulo); ?>">
                                    <div class="card-body">
                                        <input type="checkbox" name="eventos_seleccionados[]" value="<?php echo htmlspecialchars($evento['id_evento']); ?>" id="evento-<?php echo htmlspecialchars($evento['id_evento']); ?>" <?php echo $checked_attr . ' ' . $disabled_attr; ?>>
                                        <h5 class="card-title text-primary"><?php echo htmlspecialchars($evento['nombre_evento']); ?></h5>
                                        <p class="card-text mb-1"><small class="text-muted"><strong>Categoría:</strong> <?php echo htmlspecialchars($evento['nombre_categoria']); ?></small></p>
                                        <p class="card-text mb-1"><small class="text-muted"><strong>Código:</strong> <?php echo htmlspecialchars($evento['codigo_evento']); ?></small></p>
                                        <p class="card-text mb-1"><small class="text-muted"><strong>Ubicación:</strong> <?php echo htmlspecialchars($evento['ubicacion']); ?></small></p>
                                        <p class="card-text mb-1"><small class="text-muted"><strong>Fecha:</strong> <?php echo htmlspecialchars($evento['fecha']); ?></small></p>
                                        <p class="card-text mb-1"><small class="text-muted"><strong>Hora:</strong> <?php echo htmlspecialchars($evento['hora_inicio']) . ' - ' . htmlspecialchars($evento['hora_fin']); ?></small></p>
                                        <p class="card-text mb-1"><strong>Cupo disponible:</strong> 
                                            <span class="badge <?php echo ($evento['cupo_disponible'] > 0) ? 'bg-success' : 'bg-danger'; ?>">
                                                <?php echo htmlspecialchars($evento['cupo_disponible']); ?> / <?php echo htmlspecialchars($evento['cupo_maximo']); ?>
                                            </span>
                                        </p>
                                        <p class="card-text"><?php echo htmlspecialchars($evento['descripcion']); ?></p>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="alert alert-info text-center">No hay eventos para mostrar en este momento.</div>
            <?php endif; ?>

            <?php if ($hay_cupo_disponible): ?>
            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-success btn-lg mt-3">Reservar Eventos</button>
            </div>
            <?php endif; ?>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Función para manejar la lógica de exclusión mutua de los eventos de fútbol
        function handleFutbolConflict(checkboxId, isChecked) {
            const ID_FASE1 = 'evento-18';
            const ID_FASE2 = 'evento-24';

            // Si el checkbox no es de los eventos de fútbol, no hacer nada
            if (checkboxId !== ID_FASE1 && checkboxId !== ID_FASE2) {
                return;
            }

            const otraFaseId = (checkboxId === ID_FASE1) ? ID_FASE2 : ID_FASE1;
            const otraFaseCheckbox = document.getElementById(otraFaseId);

            if (otraFaseCheckbox) {
                const otraFaseCard = otraFaseCheckbox.closest('.evento-item');
                
                if (isChecked) {
                    // Si se selecciona uno, deshabilitar y deseleccionar el otro
                    otraFaseCheckbox.disabled = true;
                    otraFaseCheckbox.checked = false;
                    if (otraFaseCard) {
                        otraFaseCard.classList.add('disabled');
                        otraFaseCard.classList.remove('selected');
                    }
                } else {
                    // Si se deselecciona uno, habilitar el otro
                    otraFaseCheckbox.disabled = false;
                    if (otraFaseCard) {
                        otraFaseCard.classList.remove('disabled');
                    }
                }
            }
        }

        function toggleSelection(element, checkboxId) {
            // No permitir la selección si el elemento está deshabilitado
            if (element.classList.contains('disabled')) {
                return;
            }
            
            const checkbox = document.getElementById(checkboxId);
            const isAboutToBeChecked = !checkbox.checked;

            if (isAboutToBeChecked) {
                // 1. Validar por bloque de tiempo
                const blockTitle = element.getAttribute('data-block');
                const selectedInBlock = document.querySelectorAll(`[data-block="${blockTitle}"] input[type="checkbox"]:checked`);
                
                if (selectedInBlock.length > 0) {
                    alert("Solo puedes seleccionar un evento por cada bloque de tiempo.");
                    return; // Detener la ejecución
                }
                
                // 2. Validar por cantidad máxima de eventos opcionales
                // El selector busca checkboxes de eventos opcionales
                const selectedCheckboxes = document.querySelectorAll('.evento-item:not(.indispensables) input[type="checkbox"]:checked');
                const MAX_EVENTOS_OPCIONALES = 4;
                
                if (selectedCheckboxes.length >= MAX_EVENTOS_OPCIONALES) {
                    alert("Solo puedes seleccionar un máximo de " + MAX_EVENTOS_OPCIONALES + " eventos opcionales.");
                    return; // Detener la ejecución
                }
            }
            
            // Si las validaciones pasan, actualizar el estado
            checkbox.checked = isAboutToBeChecked;

            // Aplicar/quitar clases visuales
            if (checkbox.checked) {
                element.classList.add('selected');
            } else {
                element.classList.remove('selected');
            }

            // Llamar a la lógica específica para los eventos de fútbol
            handleFutbolConflict(checkboxId, checkbox.checked);
        }
        
        // Función para inicializar el estado de los eventos de fútbol al cargar la página
        document.addEventListener('DOMContentLoaded', function() {
            const ID_FASE1 = 'evento-18';
            const ID_FASE2 = 'evento-24';
            
            const fase1 = document.getElementById(ID_FASE1);
            const fase2 = document.getElementById(ID_FASE2);
            
            if (fase1 && fase2) {
                if (fase1.checked) {
                    const fase2Card = fase2.closest('.evento-item');
                    fase2.disabled = true;
                    if (fase2Card) fase2Card.classList.add('disabled');
                } else if (fase2.checked) {
                    const fase1Card = fase1.closest('.evento-item');
                    fase1.disabled = true;
                    if (fase1Card) fase1Card.classList.add('disabled');
                }
            }
        });
    </script>
</body>
</html>