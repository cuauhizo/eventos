<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Selección de Eventos - Sistema de Reservas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link as="font" crossorigin="crossorigin" href="https://www.nike.com/static/ncss/5.0/dotcom/fonts/Nike-Futura.woff2" rel="preload" type="font/woff2">
    <link rel="stylesheet" href="../public/css/styles.css">
    <style>
        /* Tus estilos CSS personalizados */
        .eventos-container {
            max-width: 1200px;
            margin: 50px auto;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .card {
            background: #fc3000;
            color: #fff;
        }
        .card-text, p.card-text > small {
            color: #fff!important;
        }

        .evento-item {
            cursor: pointer;
            transition: all 0.3s ease;
            border: 2px solid #fc3000;
        }

        .evento-item:hover {
            border-color: #CEFF00;
            box-shadow: 0 0 10px rgba(0, 123, 255, 0.25);
        }

        .evento-item.selected {
            border-color: #CEFF00;
            background-color: #CEFF00;
            box-shadow: 0 0 15px #28a74550;
            color:#333!important;
        }

        .evento-item.selected .card-title{
            color:#fc3000!important;
        }

        .evento-item.selected .card-text small, .evento-item.selected .card-text {
            color: #333!important;
        }

        .evento-item.selected .cupo {
            color: #fff !important;
            background: #fc3000;
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

        .evento-item input[type="checkbox"] {
            display: none;
        }

        .indispensables {
            cursor: default;
        }

        .cupo{
            font-weight: bold;
            padding: 5px 10px;
            border-radius: 4px;
            background: #CEFF00;
            color:#333!important;
            display: inline-block;
        }
    </style>
</head>

<body>
    <div class="eventos-container">
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
        
        <h1 class="text-center mb-4">Eventos Deportivos</h1>
        <p class="text-center text-muted">Selecciona los eventos a los que te gustaría asistir (máximo 4).</p>
        <p>Comenzaremos nuestro JDI Day como un solo equipo con una bienvenida a cargo de
        nuestro VP/GM, Doug Bowles, seguida de la Ceremonia de Reconocimiento de Maxims.</p>
        <p>La segunda actividad será una sesión de Team Building in Motion liderada por el Nike
        Trainer Gabriel Rojo de la Vega. Invitamos a todo nuestro equipo a participar en estos dos
        eventos. No requieren registro.</p>
        <p>Las actividades a elegir están separadas por bloque. Podrás elegir máximo una actividad
        por bloque para un máximo de 4 actividades en el día. Por favor considera lo siguiente al
        escoger tus actividades:</p>
        <ul>
          <li>Algunas actividades tienen un cupo limitado por lo cual te recomendamos completar
    tu registro tan pronto decidas cuáles quieres.</li>
          <li>Puedes elegir no participar en actividades en ciertos periodos. Para esto,
    simplemente no selecciones nada en ese bloque.</li>
          <li>Hay distintos formatos para algunas de las actividades. Elige el que más te guste.</li>
          <ul>
            <li>Retas: juegos informales entre equipos de los cuales saldrá más de un
    equipo ganador.</li>
            <li>Sports Labs: tu primer acercamiento a ese deporte. Este nuevo formato
    mezcla la teoría, el contexto sobre el juego, el repaso de reglas o técnicas y
    finalmente la práctica del deporte.</li>
            <li>Torneo: juegos formales competitivos entre equipos.</li>
          </ul>
          <li>Como su nombre lo dice, las Actividades Abiertas son juegos recreativos en los
    cuales puedes participar con o sin registro y no tienen cupo limitado. Estarán
    abiertas durante los 4 bloques.</li>
        </ul>
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

            // Definir los IDs de los eventos indispensables
            // ¡IMPORTANTE! Asegúrate de que estos IDs coincidan con los de tu DB
            $indispensable_event_ids = [1, 2]; // Ejemplo: ID 1 para Bienvenida, ID 2 para Team Building (según tu DB)

            // Filtrar eventos: separar indispensables de opcionales
            $eventos_indispensables = [];
            $eventos_opcionales = [];

            foreach ($eventos as $evento) {
                if (in_array($evento['id_evento'], $indispensable_event_ids)) {
                    $eventos_indispensables[] = $evento;
                } else {
                    $eventos_opcionales[] = $evento;
                }
            }

            // Definir los horarios y sus títulos para los eventos opcionales
            $horarios = [
                '11:45:00-12:30:00' => 'Bloque 1 (11:45 - 12:30)',
                '12:30:00-13:15:00' => 'Bloque 2 (12:30 - 13:15)',
                '13:15:00-14:00:00' => 'Bloque 3 (13:15 - 14:00)',
                '14:00:00-14:45:00' => 'Bloque 4 (14:00 - 14:45)',
            ];

            // Agrupar los eventos opcionales por horario
            $eventos_agrupados_por_horario = [];
            foreach ($eventos_opcionales as $evento) { // Usamos $eventos_opcionales aquí
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
                <h2 class="mt-3 mb-5 text-center">Indispensables</h2>
                <div class="col-md-12">
                </div>
                <?php foreach ($eventos_indispensables as $evento): ?>
                    <div class="col-md-6 mb-4">
                        <div class="card h-100 evento-item selected indispensables">
                            <div class="card-body">
                                <h5 class="card-title text-primary"><?php echo htmlspecialchars($evento['nombre_evento']); ?></h5>
                                <p class="card-text mb-1"><small class="text-muted"><strong>Ubicación:</strong> <?php echo htmlspecialchars($evento['ubicacion']); ?></small></p>
                                <p class="card-text mb-1"><small class="text-muted"><strong>Hora:</strong> <?php echo htmlspecialchars($evento['hora_inicio']) . ' - ' . htmlspecialchars($evento['hora_fin']); ?></small></p>
                                <p class="card-text"><?php echo htmlspecialchars($evento['descripcion']); ?></p>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" name="eventos_seleccionados[]" value="<?php echo htmlspecialchars($evento['id_evento']); ?>">
                <?php endforeach; ?>
            </div>

            <?php if (!empty($eventos_agrupados_por_horario) || !empty($eventos_preseleccionados)): ?>

            <?php foreach ($eventos_agrupados_por_horario as $horario_titulo => $eventos_en_horario): ?>
            <h2 class="mt-3 mb-5 text-center"><?php echo htmlspecialchars($horario_titulo); ?></h2>
            <div class="row">
                <?php foreach ($eventos_en_horario as $evento): 
                    $is_selected = in_array($evento['id_evento'], $eventos_preseleccionados);
                    $is_disabled = ($evento['cupo_disponible'] <= 0);
                    $selected_class = $is_selected ? 'selected' : '';
                    $disabled_class = $is_disabled ? 'disabled' : '';
                    $checked_attr = $is_selected ? 'checked' : '';
                    $disabled_attr = $is_disabled ? 'disabled' : '';
                ?>
                <div class="col-md-6 col-lg-3 mb-4">
                    <div class="card h-100 evento-item <?php echo $selected_class . ' ' . $disabled_class; ?>"
                        onclick="toggleSelection(this, 'evento-<?php echo htmlspecialchars($evento['id_evento']); ?>')"
                        data-block="<?php echo htmlspecialchars($horario_titulo); ?>">
                        <div class="card-body">
                            <input type="checkbox" name="eventos_seleccionados[]"
                                value="<?php echo htmlspecialchars($evento['id_evento']); ?>"
                                id="evento-<?php echo htmlspecialchars($evento['id_evento']); ?>"
                                <?php echo $checked_attr . ' ' . $disabled_attr; ?>>
                            <h5 class="card-title text-primary"><?php echo htmlspecialchars($evento['nombre_evento']); ?></h5>
                            <p class="card-text mb-1"><small class="text-muted"><strong>Ubicación:</strong>
                                    <?php echo htmlspecialchars($evento['ubicacion']); ?></small></p>
                            <p class="card-text mb-1"><small class="text-muted"><strong>Hora:</strong>
                                    <?php echo htmlspecialchars($evento['hora_inicio']) . ' - ' . htmlspecialchars($evento['hora_fin']); ?></small>
                            </p>
                            <p class="card-text mb-1 cupo"><strong>Cupo disponible:</strong>
                                <span class="">
                                    <?php echo htmlspecialchars($evento['cupo_disponible']); ?> /
                                    <?php echo htmlspecialchars($evento['cupo_maximo']); ?>
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
                <button type="submit" class="btn btn-primary btn-lg mt-3">Reservar Eventos</button>
            </div>
            <?php endif; ?>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Función para manejar la lógica de exclusión mutua de los eventos de fútbol
        function handleFutbolConflict(checkboxId, isChecked) {
            // Asegúrate de que estos IDs (evento-11 y evento-18) sean los correctos para tus Fases de Fútbol
            const ID_FASE1 = "evento-11"; 
            const ID_FASE2 = "evento-18";

            if (checkboxId !== ID_FASE1 && checkboxId !== ID_FASE2) {
                return;
            }

            const otraFaseId = (checkboxId === ID_FASE1) ? ID_FASE2 : ID_FASE1;
            const otraFaseCheckbox = document.getElementById(otraFaseId);

            if (otraFaseCheckbox) {
                const otraFaseCard = otraFaseCheckbox.closest(".evento-item");

                if (isChecked) {
                    otraFaseCheckbox.disabled = true;
                    otraFaseCheckbox.checked = false;
                    if (otraFaseCard) {
                        otraFaseCard.classList.add("disabled");
                        otraFaseCard.classList.remove("selected");
                    }
                } else {
                    otraFaseCheckbox.disabled = false;
                    if (otraFaseCard) {
                        otraFaseCard.classList.remove("disabled");
                    }
                }
            }
        }

        function toggleSelection(element, checkboxId) {
            const checkbox = document.getElementById(checkboxId);
            const isCurrentlySelected = checkbox.checked; 

            // Permitir deseleccionar un evento ya seleccionado, incluso si ahora está disabled.
            // Si el elemento está disabled Y NO está actualmente seleccionado (es decir, intentamos seleccionarlo)
            if (element.classList.contains("disabled") && !isCurrentlySelected) {
                alert("Este evento no está disponible."); 
                return; 
            }

            const isAboutToBeChecked = !isCurrentlySelected;

            if (isAboutToBeChecked) { // Solo validamos si el intento es de SELECCIONAR
                // 1. Validar por bloque de tiempo (un solo checkbox por bloque)
                const blockTitle = element.getAttribute("data-block");
                const selectedInBlock = document.querySelectorAll(`[data-block="${blockTitle}"] input[type="checkbox"]:checked`);

                if (selectedInBlock.length > 0) {
                    alert("Solo puedes seleccionar un evento por cada bloque de tiempo.");
                    return; 
                }

                // 2. Validar por cantidad máxima de eventos opcionales (máximo 4)
                const selectedCheckboxes = document.querySelectorAll(
                    ".evento-item:not(.indispensables) input[type=checkbox]:checked");
                const MAX_EVENTOS_OPCIONALES = 4;

                if (selectedCheckboxes.length >= MAX_EVENTOS_OPCIONALES) {
                    alert("Solo puedes seleccionar un máximo de " + MAX_EVENTOS_OPCIONALES + " eventos opcionales.");
                    return; 
                }
            }
            checkbox.checked = isAboutToBeChecked;
            if (checkbox.checked) {
                element.classList.add("selected");
            } else {
                element.classList.remove("selected");
            }
            handleFutbolConflict(checkboxId, checkbox.checked);
        }

        document.addEventListener("DOMContentLoaded", function() {
            // IDs de las fases de fútbol, asegúrate que sean los correctos
            const ID_FASE1 = "evento-11"; 
            const ID_FASE2 = "evento-18";

            const fase1 = document.getElementById(ID_FASE1);
            const fase2 = document.getElementById(ID_FASE2);

            if (fase1 && fase2) {
                if (fase1.checked) {
                    const fase2Card = fase2.closest(".evento-item");
                    fase2.disabled = true;
                    if (fase2Card) fase2Card.classList.add("disabled");
                } else if (fase2.checked) {
                    const fase1Card = fase1.closest(".evento-item");
                    fase1.disabled = true;
                    if (fase1Card) fase1Card.classList.add("disabled");
                }
            }
            // Inicializar visualmente las selecciones preexistentes (si las hay)
            document.querySelectorAll(".evento-item input[type=checkbox]").forEach(checkbox => {
                if (checkbox.checked) {
                    checkbox.closest(".evento-item").classList.add("selected");
                }
            });
        });
    </script>
</body>

</html>