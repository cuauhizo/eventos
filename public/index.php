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

// Lógica de autenticación y autorización para el panel de administración
$admin_actions = ['admin_dashboard', 'admin_eventos_list', 'admin_evento_form', 'admin_guardar_evento', 'admin_eliminar_evento', 'admin_usuarios_list', 'admin_cambiar_rol', 'admin_qr_validator', 'admin_validar_qr'];
$is_admin_action = in_array($action, $admin_actions);

if ($is_admin_action) {
    if (!isset($_SESSION['loggedin']) || $_SESSION['rol'] !== 'admin') {
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
            // $telefono = trim($_POST['telefono'] ?? '');
            $correo = trim($_POST['correo'] ?? '');
            $id_empleado = trim($_POST['id_empleado'] ?? '');
            // CAMBIO: La variable $password ya no es necesaria, AuthController no la usa
            // $password = $_POST['password'] ?? ''; 
            $acepta_contacto = isset($_POST['acepta_contacto']) ? 1 : 0;

            // CAMBIO: No pasar $password al registrarUsuario
            // $resultado = $authController->registrarUsuario($nombre, $apellidos, $telefono, $correo, $id_empleado, $acepta_contacto);
            $resultado = $authController->registrarUsuario($nombre, $apellidos, $correo, $id_empleado, $acepta_contacto);

            if (isset($_GET['ajax']) && $_GET['ajax'] == 1) {
                if ($resultado === true) {
                    echo json_encode(['success' => true, 'message' => '¡Registro exitoso! Ahora puedes iniciar sesión.']);
                } else {
                    echo json_encode(['success' => false, 'message' => $resultado]);
                }
                exit();
            }

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
            // CAMBIO: La variable $password ya no es necesaria, AuthController no la usa
            // $password = trim($_POST['password'] ?? '');

            // CAMBIO: No pasar $password al iniciarSesion
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
        header("Location: " . $_SERVER['PHP_SELF'] . "?action=show_register_form"); // Redirige a login después de logout
        exit();
        break;

    // --- ACCIONES DE USUARIO ---
    case 'eventos':
        if (!isset($_SESSION['loggedin'])) {
            header("Location: " . $_SERVER['PHP_SELF'] . "?action=show_register_form");
            exit();
        }
        $eventoController->mostrarEventos();
        break;

    case 'mis_reservas':
        if (!isset($_SESSION['loggedin']) || !isset($_SESSION['id_usuario'])) { // Asegurarse de que 'loggedin' y 'id_usuario' estén seteados
            header("Location: " . $_SERVER['PHP_SELF'] . "?action=show_register_form");
            exit();
        }

        $reservaciones = $eventoController->getReservacionesDeUsuario($_SESSION['id_usuario']);
        require_once ROOT_PATH . '/views/mis_reservas.php';
        break;

    case 'reservar':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Asegurarse de que el usuario esté logueado antes de permitir la reserva
            if (!isset($_SESSION['loggedin']) || !isset($_SESSION['id_usuario'])) {
                header("Location: " . $_SERVER['PHP_SELF'] . "?action=show_login_form");
                exit();
            }

            $id_usuario = $_SESSION['id_usuario'];
            // Asume que eventos_seleccionados del POST son solo los opcionales
            $eventos_seleccionados_opcionales = $_POST['eventos_seleccionados'] ?? [];
            
            // La lógica para eliminar reservas pendientes y procesar ahora está encapsulada en procesarReservacion
            // y no se llama directamente aquí para evitar inconsistencias con los cupos
            // if ($eventoController->eliminarReservasPendientes($id_usuario)) { /* ... */ } // Esta llamada está en procesarReservacion

            // CAMBIO: procesarReservacion recibe solo los eventos opcionales
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
        break;

    case 'resumen_reservas':
        // Asegurarse de que el usuario esté logueado y tenga un grupo de reserva activo
        if (!isset($_SESSION['loggedin']) || !isset($_SESSION['id_usuario']) || !isset($_SESSION['id_grupo'])) {
            header("Location: " . $_SERVER['PHP_SELF'] . "?action=eventos");
            exit();
        }
        $id_grupo = $_SESSION['id_grupo'];
        
        $reservaciones = $eventoController->getReservacionesPorGrupo($id_grupo);
        // $eventos_seleccionados_ids = array_column($reservaciones, 'id_evento'); // Esta variable no se usa en resumen.php
        // $_SESSION['eventos_preseleccionados'] = $eventos_seleccionados_ids; // No es necesario si eventos.php los carga de DB
        require_once ROOT_PATH . '/views/resumen.php';
        break;

    // CAMBIO: Se consolida la acción 'finalizar_reserva_old' con 'finalizar_reserva'
    case 'finalizar_reserva': 
        if (!isset($_SESSION['loggedin']) || !isset($_SESSION['id_usuario']) || !isset($_SESSION['id_grupo'])) {
            header("Location: " . $_SERVER['PHP_SELF'] . "?action=eventos");
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
        break;

    case 'cancelar_reserva':
        if (!isset($_SESSION['loggedin']) || !isset($_SESSION['id_usuario']) || !isset($_GET['id'])) {
            header("Location: " . $_SERVER['PHP_SELF'] . "?action=show_login_form");
            exit();
        }
        $id_reservacion = $_GET['id'];
        $eventoController->cancelarReservacion($id_reservacion);
        header("Location: " . $_SERVER['PHP_SELF'] . "?action=mis_reservas");
        exit();
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

    case 'admin_qr_validator':
        require_once ROOT_PATH . '/views/admin/qr_validator.php';
        break;
    
    case 'admin_validar_qr':
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

    case 'admin_usuarios_list':
        $adminController->mostrarListaUsuarios();
        break;

    case 'admin_import_form':
        require_once ROOT_PATH . '/views/admin/import_form.php';
        break;

    case 'admin_import_events':
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
        break;

    // --- ACCIONES PARA MOSTRAR VISTAS ---
    case 'show_register_form':
        require_once ROOT_PATH . '/views/registro.php';
        break;

    case 'show_login_form':
        require_once ROOT_PATH . '/views/login.php';
        break;
        
    // --- PÁGINA PRINCIPAL ---
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
        // Si no, lo enviamos al formulario de registro
        header("Location: " . $_SERVER['PHP_SELF'] . "?action=show_register_form");
        exit();
        break;
}
?>