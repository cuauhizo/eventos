<?php
// ¡IMPORTANTE! Descomentar session_start() para que las sesiones funcionen correctamente
// session_start();

// Incluye el archivo central
require_once __DIR__ . '/../core.php';

// Incluye los controladores que se usarán en todo el enrutador
require_once ROOT_PATH . '/controllers/AuthController.php';
require_once ROOT_PATH . '/controllers/EventoController.php';
require_once ROOT_PATH . '/controllers/AdminController.php';

$action = $_GET['action'] ?? 'home';

// =========================================================================
// Instanciar los controladores antes de cualquier lógica
// =========================================================================
$authController = new AuthController($pdo);
$eventoController = new EventoController($pdo);
$adminController = new AdminController($pdo);
// =========================================================================

// --- LÓGICA DE RESTRICCIÓN DE ACCESO PÚBLICO (REGISTRO/LOGIN) PARA NO-ADMINS ---
// Definir las acciones que deben ser accesibles solo para administradores (o redirigir a 'cerrado' para otros)
$restricted_public_auth_actions = [
    'register',         // Procesar el POST de registro
    'login',            // Procesar el POST de login
    'show_register_form', // Mostrar el formulario de registro
    'show_login_form'     // Mostrar el formulario de login
];

// Si la acción actual está en la lista de acciones restringidas PARA NO-ADMINS
if (in_array($action, $restricted_public_auth_actions)) {
    // Si el usuario NO ha iniciado sesión O si ha iniciado sesión pero NO es un admin
    if (!isset($_SESSION['loggedin']) || $_SESSION['rol'] !== 'admin') {
        // Si es una petición AJAX (solo 'register' POST usa AJAX aquí), devuelve un JSON de error
        if (isset($_GET['ajax']) && $_GET['ajax'] == 1 && $action === 'register') { 
             echo json_encode(['success' => false, 'message' => 'El registro está cerrado para usuarios no administradores.']);
             exit();
        } else { // Para peticiones normales (GET o POST sin AJAX), redirige a la página de registro cerrado
            header("Location: " . $_SERVER['PHP_SELF'] . "?action=registration_closed");
            exit();
        }
    }
}
// --- FIN LÓGICA DE RESTRICCIÓN ---


// Lógica de autenticación y autorización para el panel de administración (sigue igual)
$admin_actions = [
    'admin_dashboard', 'admin_eventos_list', 'admin_evento_form', 
    'admin_guardar_evento', 'admin_eliminar_evento', 'admin_usuarios_list', 
    'admin_cambiar_rol', 'admin_qr_validator', 'admin_validar_qr',
    'admin_confirm_reservation', 'admin_pending_reservations', // Asegúrate de que 'admin_pending_reservations' esté aquí
    'admin_update_asistencia' // <-- AÑADIDO: La acción para actualizar asistencia
];
$is_admin_action = in_array($action, $admin_actions);

if ($is_admin_action) {
    if (!isset($_SESSION['loggedin']) || $_SESSION['rol'] !== 'admin') {
        header("Location: " . $_SERVER['PHP_SELF'] . "?action=show_login_form");
        exit();
    }
}

switch ($action) {
    // --- LÓGICA DE AUTENTICACIÓN (simplificada, la restricción está arriba) ---
    case 'register':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nombre = trim($_POST['nombre'] ?? '');
            $apellidos = trim($_POST['apellidos'] ?? '');
            $telefono = trim($_POST['telefono'] ?? '');
            $correo = trim($_POST['correo'] ?? '');
            $id_empleado = trim($_POST['id_empleado'] ?? '');
            $acepta_contacto = isset($_POST['acepta_contacto']) ? 1 : 0;

            $resultado = $authController->registrarUsuario($nombre, $apellidos, $telefono, $correo, $id_empleado, $acepta_contacto);

            if (isset($_GET['ajax']) && $_GET['ajax'] == 1) { // Este bloque solo es alcanzado por admins
                if ($resultado === true) {
                    echo json_encode(['success' => true, 'message' => '¡Registro exitoso! Ahora puedes iniciar sesión.']);
                } else {
                    echo json_encode(['success' => false, 'message' => $resultado]);
                }
                exit();
            }

            if ($resultado === true) { // Este bloque solo es alcanzado por admins
                $_SESSION['registro_exito'] = true;
                $_SESSION['registro_mensaje'] = "¡Registro exitoso! Ya puedes iniciar sesión.";
                header("Location: " . $_SERVER['PHP_SELF'] . "?action=show_login_form");
                exit();
            } else {
                $_SESSION['registro_exito'] = false;
                $_SESSION['registro_mensaje'] = $resultado;
                header("Location: " . $_SERVER['PHP_SELF'] . "?action=show_register_form");
                exit();
            }
        } else { // Si es un GET, solo admins llegan aquí
            header("Location: " . $_SERVER['PHP_SELF'] . "?action=show_register_form");
            exit();
        }
        break;

    case 'login':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $correo = trim($_POST['correo'] ?? '');
            $resultado = $authController->iniciarSesion($correo); 
            if ($resultado === true) {
                if ($_SESSION['rol'] === 'admin') {
                    header("Location: " . $_SERVER['PHP_SELF'] . "?action=admin_dashboard");
                } else {
                    header("Location: " . $_SERVER['PHP_SELF'] . "?action=eventos");
                }
                exit();
            } else {
                $_SESSION['login_exito'] = false;
                $_SESSION['login_mensaje'] = $resultado;
                header("Location: " . $_SERVER['PHP_SELF'] . "?action=show_login_form");
                exit();
            }
        }
        break;

    case 'logout':
        session_destroy();
        header("Location: " . $_SERVER['PHP_SELF'] . "?action=show_login_form");
        exit();
        break;

    case 'registration_closed': // <-- NUEVA ACCIÓN para la pantalla de agradecimiento
        require_once ROOT_PATH . '/views/registration_closed.php';
        break;

    // --- ACCIONES DE USUARIO GENERAL (requieren login estándar, si no, redirige a login normal) ---
    case 'eventos':
    case 'mis_reservas':
    case 'reservar':
    case 'resumen_reservas':
    case 'finalizar_reserva':
    case 'cancelar_reserva':
        // Estas acciones requieren que el usuario esté logueado (rol 'user' o 'admin')
        if (!isset($_SESSION['loggedin'])) {
            header("Location: " . $_SERVER['PHP_SELF'] . "?action=show_login_form");
            exit();
        }
        // Lógica específica de cada acción (sin cambios en su contenido aquí)
        if ($action === 'eventos') {
            $eventoController->mostrarEventos();
        } elseif ($action === 'mis_reservas') {
             if (!isset($_SESSION['id_usuario'])) { 
                header("Location: " . $_SERVER['PHP_SELF'] . "?action=show_login_form");
                exit();
            }
            $reservaciones = $eventoController->getReservacionesDeUsuario($_SESSION['id_usuario']);
            require_once ROOT_PATH . '/views/mis_reservas.php';
        } elseif ($action === 'reservar') {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                if (!isset($_SESSION['id_usuario'])) { 
                    header("Location: " . $_SERVER['PHP_SELF'] . "?action=show_login_form");
                    exit();
                }
                $id_usuario = $_SESSION['id_usuario'];
                $eventos_seleccionados_opcionales = $_POST['eventos_seleccionados'] ?? []; 
                $resultado = $eventoController->procesarReservacion($id_usuario, $eventos_seleccionados_opcionales); 
                if (is_numeric($resultado)) {
                    $_SESSION['reserva_exito'] = true;
                    $_SESSION['reserva_mensaje'] = "Tu reservación ha sido pre-confirmada. Por favor, confirma a continuación para finalizar.";
                    $_SESSION['id_grupo'] = (int) $resultado;
                    header("Location: " . $_SERVER['PHP_SELF'] . "?action=resumen_reservas");
                    exit();
                } else {
                    $_SESSION['reserva_exito'] = false;
                    $_SESSION['reserva_mensaje'] = $resultado;
                    header("Location: " . $_SERVER['PHP_SELF'] . "?action=eventos");
                    exit();
                }
            } else {
                header("Location: " . $_SERVER['PHP_SELF'] . "?action=eventos");
                exit();
            }
        } elseif ($action === 'resumen_reservas') {
            if (!isset($_SESSION['id_usuario']) || !isset($_SESSION['id_grupo'])) {
                header("Location: " . $_SERVER['PHP_SELF'] . "?action=eventos"); // Redirige a eventos si no hay sesión o grupo
                exit();
            }
            $id_grupo = $_SESSION['id_grupo'];
            $reservaciones = $eventoController->getReservacionesPorGrupo($id_grupo);
            require_once ROOT_PATH . '/views/resumen.php';
        } elseif ($action === 'finalizar_reserva') {
            if (!isset($_SESSION['id_usuario']) || !isset($_SESSION['id_grupo'])) {
                header("Location: " . $_SERVER['PHP_SELF'] . "?action=eventos"); // Redirige a eventos si no hay sesión o grupo
                exit();
            }
            $id_grupo = $_SESSION['id_grupo'];
            $resultado = $eventoController->finalizarReservacion($id_grupo);
            if ($resultado === true) {
                unset($_SESSION['id_grupo']);
                $_SESSION['reserva_exito'] = true;
                $_SESSION['reserva_mensaje'] = "¡Reservación finalizada y correo de confirmación con PDF enviado!";
                header("Location: " . $_SERVER['PHP_SELF'] . "?action=mis_reservas");
                exit();
            } else {
                $_SESSION['reserva_exito'] = false;
                $_SESSION['reserva_mensaje'] = $resultado;
                header("Location: " . $_SERVER['PHP_SELF'] . "?action=resumen_reservas");
                exit();
            }
        } elseif ($action === 'cancelar_reserva') {
            if (!isset($_SESSION['id_usuario']) || !isset($_GET['id'])) {
                header("Location: " . $_SERVER['PHP_SELF'] . "?action=show_login_form");
                exit();
            }
            $id_reservacion = $_GET['id'];
            $eventoController->cancelarReservacion($id_reservacion);
            header("Location: " . $_SERVER['PHP_SELF'] . "?action=mis_reservas");
            exit();
        }
        break;

    // --- ACCIONES DEL PANEL DE ADMINISTRACIÓN ---
    case 'admin_dashboard':
    case 'admin_eventos_list':
    case 'admin_usuarios_list':
    case 'admin_evento_form':
    case 'admin_guardar_evento':
    case 'admin_eliminar_evento':
    case 'admin_qr_validator':
    case 'admin_validar_qr':
    case 'admin_import_form':
    case 'admin_import_events':
    case 'admin_pending_reservations':
    case 'admin_confirm_reservation': 
    case 'admin_update_asistencia': // <-- AÑADIDO: Nuevo case para la acción AJAX
        // Estas acciones son manejadas por el AdminController y su acceso ya se verifica arriba.
        if ($action === 'admin_dashboard') {
            $resumen = $adminController->getResumenDashboard();
            require_once ROOT_PATH . '/views/admin/dashboard.php';
        } elseif ($action === 'admin_eventos_list') {
            $adminController->mostrarListaEventos();
        } elseif ($action === 'admin_usuarios_list') {
            $adminController->mostrarListaUsuarios();
        } elseif ($action === 'admin_evento_form') {
            $id = $_GET['id'] ?? null;
            $adminController->mostrarFormularioEvento($id);
        } elseif ($action === 'admin_guardar_evento') {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $resultado = $adminController->guardarEvento($_POST);
                if ($resultado === true) {
                    $_SESSION['admin_exito'] = true;
                    $_SESSION['admin_mensaje'] = "El evento se guardó correctamente.";
                } else {
                    $_SESSION['admin_exito'] = false;
                    $_SESSION['admin_mensaje'] = $resultado;
                }
            }
            header("Location: " . $_SERVER['PHP_SELF'] . "?action=admin_eventos_list");
            exit();
        } elseif ($action === 'admin_eliminar_evento') {
            $id = $_GET['id'] ?? null;
            $resultado = $adminController->eliminarEvento($id);
            if ($resultado === true) {
                $_SESSION['admin_exito'] = true;
                $_SESSION['admin_mensaje'] = "Evento eliminado correctamente.";
            } else {
                $_SESSION['admin_exito'] = false;
                $_SESSION['admin_mensaje'] = $resultado;
            }
            header("Location: " . $_SERVER['PHP_SELF'] . "?action=admin_eventos_list");
            exit();
        } elseif ($action === 'admin_qr_validator') {
            require_once ROOT_PATH . '/views/admin/qr_validator.php';
        } elseif ($action === 'admin_validar_qr') {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $qr_content = $_POST['qr_content'] ?? '';
                $resultado = $adminController->validarQR($qr_content);
                if ($resultado === true) {
                    $_SESSION['validator_exito'] = true;
                    $_SESSION['validator_mensaje'] = "Validación exitosa. Acceso permitido.";
                } else {
                    $_SESSION['validator_exito'] = false;
                    $_SESSION['validator_mensaje'] = $resultado;
                }
            }
            header("Location: " . $_SERVER['PHP_SELF'] . "?action=admin_qr_validator");
            exit();
        } elseif ($action === 'admin_usuarios_list') {
            $adminController->mostrarListaUsuarios();
        } elseif ($action === 'admin_import_form') {
            require_once ROOT_PATH . '/views/admin/import_form.php';
        } elseif ($action === 'admin_import_events') {
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['eventos_file'])) {
                $resultado = $adminController->importarEventos($_FILES['eventos_file']);
                if ($resultado === true) {
                    // El controlador ya se encarga del mensaje
                } else {
                    $_SESSION['import_mensaje'] = $resultado;
                    $_SESSION['import_exito'] = false;
                }
            }
            header("Location: " . $_SERVER['PHP_SELF'] . "?action=admin_import_form");
            exit();
        } elseif ($action === 'admin_pending_reservations') {
            $adminController->mostrarReservasPendientesAdmin();
        } elseif ($action === 'admin_confirm_reservation') {
            $id_grupo = (int)$_GET['id_grupo']; // El ID del grupo ya se pasa y se valida en el AdminController
            $adminController->confirmarReservaAdmin($id_grupo);
        } elseif ($action === 'admin_update_asistencia') { // <-- AÑADIDO: Nuevo case para la acción AJAX
            $adminController->marcarAsistencia(); // Llama al método AJAX
        }
        break;


    case 'home':
    default:
        // Si el usuario ya está logueado, lo enviamos a su página de inicio
        if (isset($_SESSION['loggedin'])) {
            if ($_SESSION['rol'] === 'admin') {
                header("Location: " . $_SERVER['PHP_SELF'] . "?action=admin_dashboard");
            } else {
                header("Location: " . $_SERVER['PHP_SELF'] . "?action=eventos");
            }
            exit();
        }
        // Si no está logueado, lo enviamos directamente a la página de registro cerrado
        header("Location: " . $_SERVER['PHP_SELF'] . "?action=registration_closed");
        exit();
        break;
}
?>