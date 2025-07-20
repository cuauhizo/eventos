<?php
// Incluye los modelos que va a usar el controlador
require_once ROOT_PATH . '/models/Evento.php';
require_once ROOT_PATH . '/models/Reservacion.php';
require_once ROOT_PATH . '/models/Usuario.php';

class AdminController {

    private $eventoModel;
    private $reservacionModel;
    private $usuarioModel;
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
        $this->eventoModel = new Evento($pdo);
        $this->reservacionModel = new Reservacion($pdo);
        $this->usuarioModel = new Usuario($pdo);
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
            'eventos_recientes' => []
        ];

        try {
            $sql1 = "SELECT COUNT(*) FROM eventos";
            $resumen['total_eventos'] = $this->pdo->query($sql1)->fetchColumn();
            $sql2 = "SELECT COUNT(*) FROM reservaciones";
            $resumen['total_reservaciones'] = $this->pdo->query($sql2)->fetchColumn();
            $sql3 = "SELECT e.nombre_evento, COUNT(r.id_reservacion) as total_reservas
                     FROM reservaciones r
                     JOIN eventos e ON r.id_evento = e.id_evento
                     GROUP BY e.id_evento
                     ORDER BY total_reservas DESC
                     LIMIT 1";
            $stmt3 = $this->pdo->query($sql3);
            $resumen['evento_mas_reservado'] = $stmt3->fetch(PDO::FETCH_ASSOC);
            $sql4 = "SELECT nombre_evento, fecha, hora_inicio FROM eventos ORDER BY fecha DESC, hora_inicio DESC LIMIT 3";
            $stmt4 = $this->pdo->query($sql4);
            $resumen['eventos_recientes'] = $stmt4->fetchAll(PDO::FETCH_ASSOC);

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
    
    public function mostrarListaUsuarios() {
        $usuarios = $this->usuarioModel->obtenerTodosConRol();
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
}