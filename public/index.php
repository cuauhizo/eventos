<?php

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

// Lógica de autenticación y autorización para el panel de administración
$admin_actions = ['admin_dashboard', 'admin_eventos_list', 'admin_evento_form', 'admin_guardar_evento', 'admin_eliminar_evento', 'admin_usuarios_list', 'admin_cambiar_rol'];
$is_admin_action = in_array($action, $admin_actions);

if ($is_admin_action) {
    if (!isset($_SESSION['loggedin']) || $_SESSION['rol'] !== 'admin') {
        // Redirigir a login si no está logueado o no es admin
        header("Location: " . $_SERVER['PHP_SELF'] . "?action=show_login_form");
        exit();
    }
}

switch ($action) {
    // --- LÓGICA DE AUTENTICACIÓN ---
    case 'register':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nombre = trim($_POST['nombre'] ?? '');
            $apellidos = trim($_POST['apellidos'] ?? '');
            $telefono = trim($_POST['telefono'] ?? '');
            $correo = trim($_POST['correo'] ?? '');
            $id_empleado = trim($_POST['id_empleado'] ?? '');
            $password = $_POST['password'] ?? '';

            $resultado = $authController->registrarUsuario($nombre, $apellidos, $telefono, $correo, $id_empleado, $password);

            if ($resultado === true) {
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
        } else {
            header("Location: " . $_SERVER['PHP_SELF'] . "?action=show_register_form");
            exit();
        }
        break;

    case 'login':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $correo = trim($_POST['correo'] ?? '');
            $password = $_POST['password'] ?? '';

            $resultado = $authController->iniciarSesion($correo, $password);
            if ($resultado === true) {
                // El método iniciarSesion ya guarda toda la información necesaria en la sesión
                // Redirige al dashboard si es admin, o a eventos si es usuario normal
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

    // --- ACCIONES DE USUARIO ---
    case 'eventos':
        $eventos = $eventoController->getEventosDisponibles();
        require_once ROOT_PATH . '/views/eventos.php';
        break;

    case 'mis_reservas':
        if (!isset($_SESSION['id_usuario'])) {
            header("Location: " . $_SERVER['PHP_SELF'] . "?action=show_login_form");
            exit();
        }
        $reservaciones = $eventoController->getReservacionesDeUsuario($_SESSION['id_usuario']);
        require_once ROOT_PATH . '/views/mis_reservas.php';
        break;

    case 'reservar':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id_usuario = $_SESSION['id_usuario'];
            $eventos_seleccionados = $_POST['eventos_seleccionados'] ?? [];
            
            $resultado = $eventoController->procesarReservacion($id_usuario, $eventos_seleccionados);

            // var_dump($resultado);
            // exit;

            
            // if (is_int($resultado)) { // Si el resultado es un entero, es el ID del grupo
            if (is_numeric($resultado)) { 
                $_SESSION['reserva_exito'] = true;
                $_SESSION['reserva_mensaje'] = "Tu reservación ha sido pre-confirmada. Por favor, confirma a continuación para finalizar y recibir tu QR.";
                $_SESSION['id_grupo'] = $resultado;
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
        break;

    case 'resumen_reservas':
        $id_grupo = $_SESSION['id_grupo'] ?? null;
        if (!$id_grupo) {
            header("Location: " . $_SERVER['PHP_SELF'] . "?action=eventos");
            exit();
        }
        $reservaciones = $eventoController->getReservacionesPorGrupo($id_grupo);
        require_once ROOT_PATH . '/views/resumen.php';
        break;

    case 'finalizar_reserva':
        if (!isset($_SESSION['id_grupo'])) {
            header("Location: " . $_SERVER['PHP_SELF'] . "?action=eventos");
            exit();
        }
        $id_grupo = $_SESSION['id_grupo'];
        $resultado = $eventoController->finalizarReservacion($id_grupo);
        
        if ($resultado === true) {
            unset($_SESSION['id_grupo']);
            $_SESSION['reserva_exito'] = true;
            $_SESSION['reserva_mensaje'] = "¡Reservación finalizada y correo de confirmación enviado!";
            header("Location: " . $_SERVER['PHP_SELF'] . "?action=mis_reservas");
            exit();
        } else {
            $_SESSION['reserva_exito'] = false;
            $_SESSION['reserva_mensaje'] = $resultado;
            header("Location: " . $_SERVER['PHP_SELF'] . "?action=resumen_reservas");
            exit();
        }
        break;

    // --- ACCIONES DEL PANEL DE ADMINISTRACIÓN ---
    case 'admin_dashboard':
        $resumen = $adminController->getResumenDashboard();
        require_once ROOT_PATH . '/views/admin/dashboard.php';
        break;

    case 'admin_eventos_list':
        $adminController->mostrarListaEventos();
        break;

    case 'admin_usuarios_list':
        $adminController->mostrarListaUsuarios();
        break;

    case 'admin_evento_form':
        $id = $_GET['id'] ?? null;
        $adminController->mostrarFormularioEvento($id);
        break;
    
    case 'admin_guardar_evento':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $resultado = $adminController->guardarEvento($_POST);
            if ($resultado === true) {
                $_SESSION['admin_exito'] = true;
                $_SESSION['admin_mensaje'] = "El evento se guardó correctamente.";
                header("Location: " . $_SERVER['PHP_SELF'] . "?action=admin_eventos_list");
                exit();
            } else {
                $_SESSION['admin_exito'] = false;
                $_SESSION['admin_mensaje'] = $resultado;
                header("Location: " . $_SERVER['PHP_SELF'] . "?action=admin_eventos_list");
                exit();
            }
        } else {
            header("Location: " . $_SERVER['PHP_SELF'] . "?action=admin_eventos_list");
            exit();
        }
        break;
    
    case 'admin_eliminar_evento':
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
        break;

     case 'admin_qr_validator': // NUEVO: Muestra el formulario del validador
        require_once ROOT_PATH . '/views/admin/qr_validator.php';
        break;
    
    case 'admin_validar_qr': // NUEVO: Procesa la validación del QR
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
        break;

    // --- ACCIONES PARA MOSTRAR VISTAS ---
    case 'show_register_form':
        require_once ROOT_PATH . '/views/registro.php';
        break;

    case 'show_login_form':
        require_once ROOT_PATH . '/views/login.php';
        break;

    case 'home':
    default:
        if (isset($_SESSION['loggedin'])) {
            if ($_SESSION['rol'] === 'admin') {
                header("Location: " . $_SERVER['PHP_SELF'] . "?action=admin_dashboard");
            } else {
                header("Location: " . $_SERVER['PHP_SELF'] . "?action=eventos");
            }
            exit();
        }
        header("Location: " . $_SERVER['PHP_SELF'] . "?action=show_login_form");
        exit();
        break;
}
?>