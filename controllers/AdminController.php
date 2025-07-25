<?php
// Incluye los modelos que va a usar el controlador
require_once ROOT_PATH . '/models/Evento.php';
require_once ROOT_PATH . '/models/Reservacion.php';
require_once ROOT_PATH . '/models/Usuario.php';
require_once ROOT_PATH . '/controllers/EventoController.php';

class AdminController {

    private $eventoModel;
    private $reservacionModel;
    private $usuarioModel;
    private $pdo;
    private $eventoController;

    public function __construct($pdo) {
        $this->pdo = $pdo;
        $this->eventoModel = new Evento($pdo);
        $this->reservacionModel = new Reservacion($pdo);
        $this->usuarioModel = new Usuario($pdo);
        $this->eventoController = new EventoController($pdo);
    }
    
    public function getCategorias() {
        $sql = "SELECT id_categoria, nombre_categoria FROM categorias ORDER BY nombre_categoria ASC";
        try {
            $stmt = $this->pdo->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al obtener categorías: " . $e->getMessage());
            return [];
        }
    }

    public function mostrarDashboard() {
        $resumen = $this->getResumenDashboard();
        require_once ROOT_PATH . '/views/admin/dashboard.php';
    }

    public function getResumenDashboard() {
        $resumen = [
            'total_eventos' => 0,
            'total_reservaciones' => 0,
            'evento_mas_reservado' => null,
            'eventos_recientes' => [],
            'total_reservaciones_pendientes' => 0
        ];

        try {
            $sql1 = "SELECT COUNT(*) FROM eventos";
            $resumen['total_eventos'] = $this->pdo->query($sql1)->fetchColumn();
            $sql2 = "SELECT
                        COUNT(DISTINCT id_grupo) AS total_grupos_confirmados
                        FROM reservaciones
                        WHERE estado = 'confirmada';";
            $resumen['total_reservaciones'] = $this->pdo->query($sql2)->fetchColumn();

            $sql3 = "SELECT e.nombre_evento, COUNT(r.id_reservacion) as total_reservas
                        FROM reservaciones r
                        JOIN eventos e ON r.id_evento = e.id_evento
                        WHERE e.id_evento NOT IN (1, 2) -- Omite los eventos con ID 1 y 2
                        GROUP BY e.id_evento
                        ORDER BY total_reservas DESC
                        LIMIT 1";
            $stmt3 = $this->pdo->query($sql3);
            $resumen['evento_mas_reservado'] = $stmt3->fetch(PDO::FETCH_ASSOC);

            $sql4 = "SELECT nombre_evento, fecha, hora_inicio FROM eventos ORDER BY fecha DESC, hora_inicio DESC LIMIT 3";
            $stmt4 = $this->pdo->query($sql4);
            $resumen['eventos_recientes'] = $stmt4->fetchAll(PDO::FETCH_ASSOC);
          
            $sql5 = "SELECT COUNT(DISTINCT id_grupo) AS total_grupos_confirmados FROM reservaciones WHERE estado = 'pendiente';";
            $stmt5 = $this->pdo->query($sql5);
            $resumen['total_reservaciones_pendientes'] = $this->pdo->query($sql5)->fetchColumn();

            

        } catch (PDOException $e) {
            return $resumen;
        }
        return $resumen;
    }

    public function mostrarListaEventos() {
        $eventos = $this->eventoModel->obtenerTodosConCategorias();
        require_once ROOT_PATH . '/views/admin/eventos_list.php';
    }

    public function mostrarFormularioEvento($id_evento = null) {
        $evento = null;
        $categorias = $this->getCategorias();
        if ($id_evento) {
            $evento = $this->eventoModel->obtenerPorId($id_evento);
        }
        require_once ROOT_PATH . '/views/admin/evento_form.php';
    }

    public function guardarEvento($post_data) {
        $resultado = $this->eventoModel->guardar($post_data);
        if ($resultado === true) {
            return true;
        } else {
            return "Error al guardar el evento: " . $resultado;
        }
    }

    public function eliminarEvento($id_evento) {
        $resultado = $this->eventoModel->eliminar($id_evento);
        if ($resultado === true) {
            return true;
        } else {
            return "Error al eliminar el evento: " . $resultado;
        }
    }
    
    // public function mostrarListaUsuarios() {
    //     $usuarios = $this->usuarioModel->obtenerTodosConRol();
    //     require_once ROOT_PATH . '/views/admin/usuarios_list.php';
    // }

    // MODIFICADO: Ahora el método puede recibir un parámetro de búsqueda
    public function mostrarListaUsuarios() {
        // Obtener el término de búsqueda de la URL (si existe)
        $search_query = $_GET['search'] ?? null; 
        $search_query = trim($search_query); // Limpiar espacios

        // Pasar el término de búsqueda al modelo
        $usuarios = $this->usuarioModel->obtenerTodosConRol($search_query);
        
        // Pasa el término de búsqueda a la vista para mantenerlo en el input
        require_once ROOT_PATH . '/views/admin/usuarios_list.php';
    }
    
    public function validarQR($qr_content) {
        $parts = explode(':', $qr_content);
        if (count($parts) !== 2 || $parts[0] !== 'GRUPO_RESERVACION_ID' || !is_numeric($parts[1])) {
            return "Código QR inválido. Formato incorrecto.";
        }
        $id_grupo = (int) $parts[1];
        
        $resultado = $this->reservacionModel->validarReservacionesPorGrupo($id_grupo);
        return $resultado;
    }

    public function importarEventos($file) {    
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return "Error al subir el archivo.";
        }

        $file_path = $file['tmp_name'];
        $resultado = $this->eventoModel->importarEventos($file_path);

        if ($resultado === true) {
            $_SESSION['admin_mensaje'] = "El archivo se ha importado correctamente.";
            $_SESSION['admin_exito'] = true;
        } else {
            $_SESSION['admin_mensaje'] = $resultado; // El modelo devuelve el mensaje de error.
            $_SESSION['admin_exito'] = false;
        }

        return true;
    }

    public function confirmarReservaAdmin($id_grupo) {
        $resultado = $this->eventoController->finalizarReservacion($id_grupo);

        if ($resultado === true) {
            $_SESSION['admin_exito'] = true;
            $_SESSION['admin_mensaje'] = "Grupo de reserva #{$id_grupo} confirmado exitosamente y correo enviado.";
        } else {
            $_SESSION['admin_exito'] = false;
            $_SESSION['admin_mensaje'] = "Error al confirmar grupo de reserva #{$id_grupo}: {$resultado}";
        }
        // Redirige de vuelta al dashboard o a una lista de reservas de admin
        header("Location: " . $_SERVER['PHP_SELF'] . "?action=admin_dashboard");
        exit();
    }

    // NOTA: Para listar las reservas pendientes en el admin, necesitarías un método en ReservacionModel
    // y una vista admin/reservas_pendientes.php similar a mostrarListaEventos o mostrarListaUsuarios.
    // Ejemplo de método para listar reservas pendientes (en AdminController):
    
    public function mostrarReservasPendientesAdmin() {
        $reservas_pendientes = $this->reservacionModel->getReservacionesConEstado('pendiente'); // Necesitarás este método
        require_once ROOT_PATH . '/views/admin/pending_reservations.php'; // Vista para mostrarlas
    }

    /**
     * Procesa la solicitud AJAX para marcar/desmarcar la asistencia de un usuario.
     * Recibe id_usuario y el estado de asistencia (0 o 1).
     * @return void Retorna una respuesta JSON.
     */
    public function marcarAsistencia() {
        // Solo permitir solicitudes POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Método no permitido.']);
            exit();
        }

        $id_usuario = filter_input(INPUT_POST, 'id_usuario', FILTER_VALIDATE_INT);
        $asistencia_estado = filter_input(INPUT_POST, 'asistencia', FILTER_VALIDATE_INT);

        // Validar que los IDs y estados sean válidos
        if ($id_usuario === null || $id_usuario === false || $asistencia_estado === null || $asistencia_estado === false || ($asistencia_estado !== 0 && $asistencia_estado !== 1)) {
            echo json_encode(['success' => false, 'message' => 'Datos inválidos.']);
            exit();
        }

        // Actualizar la asistencia en el modelo de usuario
        $resultado = $this->usuarioModel->actualizarAsistencia($id_usuario, $asistencia_estado);

        if ($resultado) {
            echo json_encode(['success' => true, 'message' => 'Asistencia actualizada correctamente.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al actualizar asistencia en la base de datos.']);
        }
        exit(); // Finalizar el script después de enviar la respuesta JSON
    }

}