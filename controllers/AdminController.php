<?php
class AdminController {

    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Método para mostrar la vista principal del dashboard
    public function mostrarDashboard() {
        // En esta vista podemos mostrar estadísticas o un menú
        require_once ROOT_PATH . '/views/admin/dashboard.php';
    }

    // Método para mostrar la lista de eventos con opciones de edición/eliminación
    public function mostrarListaEventos() {
        $sql = "SELECT * FROM eventos ORDER BY fecha DESC";
        try {
            $stmt = $this->pdo->query($sql);
            $eventos = $stmt->fetchAll(PDO::FETCH_ASSOC);
            require_once ROOT_PATH . '/views/admin/eventos_list.php';
        } catch (PDOException $e) {
            echo "Error al cargar la lista de eventos: " . $e->getMessage();
        }
    }

    // Método para mostrar el formulario para crear o editar un evento
    public function mostrarFormularioEvento($id_evento = null) {
        $evento = null;
        if ($id_evento) {
            $sql = "SELECT * FROM eventos WHERE id_evento = ?";
            try {
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute([$id_evento]);
                $evento = $stmt->fetch(PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                echo "Error al cargar el evento: " . $e->getMessage();
            }
        }
        require_once ROOT_PATH . '/views/admin/evento_form.php';
    }

    // Método para procesar la creación o actualización de un evento
    public function guardarEvento($post_data) {
        $nombre_evento = trim($post_data['nombre_evento']);
        $descripcion = trim($post_data['descripcion']);
        $fecha = trim($post_data['fecha']);
        $hora_inicio = trim($post_data['hora_inicio']);
        $hora_fin = trim($post_data['hora_fin']);
        $cupo_maximo = (int) $post_data['cupo_maximo'];
        $id_evento = $post_data['id_evento'] ?? null;

        if (empty($nombre_evento) || empty($fecha) || empty($hora_inicio) || empty($hora_fin) || empty($cupo_maximo)) {
            return "Todos los campos son obligatorios.";
        }

        if ($id_evento) {
            // Actualizar evento existente
            $sql = "UPDATE eventos SET nombre_evento = ?, descripcion = ?, fecha = ?, hora_inicio = ?, hora_fin = ?, cupo_maximo = ? WHERE id_evento = ?";
            $params = [$nombre_evento, $descripcion, $fecha, $hora_inicio, $hora_fin, $cupo_maximo, $id_evento];
        } else {
            // Crear nuevo evento
            $sql = "INSERT INTO eventos (nombre_evento, descripcion, fecha, hora_inicio, hora_fin, cupo_maximo, cupo_disponible) VALUES (?, ?, ?, ?, ?, ?, ?)";
            $params = [$nombre_evento, $descripcion, $fecha, $hora_inicio, $hora_fin, $cupo_maximo, $cupo_maximo]; // El cupo disponible es igual al máximo
        }

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return true;
        } catch (PDOException $e) {
            return "Error al guardar el evento: " . $e->getMessage();
        }
    }

    // Método para eliminar un evento
    public function eliminarEvento($id_evento) {
        if (!$id_evento) {
            return "ID de evento no especificado.";
        }

        // El TRUNCATE ya no funciona por el Foreign Key, así que usamos DELETE
        $sql = "DELETE FROM eventos WHERE id_evento = ?";
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$id_evento]);
            return true;
        } catch (PDOException $e) {
            return "Error al eliminar el evento: " . $e->getMessage();
        }
    }

    public function getResumenDashboard() {
        $resumen = [
            'total_eventos' => 0,
            'total_reservaciones' => 0,
            'evento_mas_reservado' => null,
            'eventos_recientes' => []
        ];

        try {
            // 1. Obtener el número total de eventos
            $sql1 = "SELECT COUNT(*) FROM eventos";
            $resumen['total_eventos'] = $this->pdo->query($sql1)->fetchColumn();

            // 2. Obtener el número total de reservaciones
            $sql2 = "SELECT COUNT(*) FROM reservaciones";
            $resumen['total_reservaciones'] = $this->pdo->query($sql2)->fetchColumn();

            // 3. Obtener el evento más popular (el más reservado)
            $sql3 = "SELECT e.nombre_evento, COUNT(r.id_reservacion) as total_reservas
                      FROM reservaciones r
                      JOIN eventos e ON r.id_evento = e.id_evento
                      GROUP BY e.id_evento
                      ORDER BY total_reservas DESC
                      LIMIT 1";
            $stmt3 = $this->pdo->query($sql3);
            $resumen['evento_mas_reservado'] = $stmt3->fetch(PDO::FETCH_ASSOC);

            // 4. Obtener los 3 eventos más recientes (o todos si son menos)
            $sql4 = "SELECT nombre_evento, fecha, hora_inicio FROM eventos ORDER BY fecha DESC, hora_inicio DESC LIMIT 3";
            $stmt4 = $this->pdo->query($sql4);
            $resumen['eventos_recientes'] = $stmt4->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            // En caso de error, retorna un array vacío para no detener la aplicación
            // En un entorno real, registrarías este error en un log
            return $resumen;
        }
        return $resumen;
    }

    public function mostrarListaUsuarios() {
        $sql = "SELECT id_usuario, nombre, apellidos, correo, id_empleado, rol, fecha_registro FROM usuarios ORDER BY fecha_registro DESC";
        try {
            $stmt = $this->pdo->query($sql);
            $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
            require_once ROOT_PATH . '/views/admin/usuarios_list.php';
        } catch (PDOException $e) {
            echo "Error al cargar la lista de usuarios: " . $e->getMessage();
        }
    }

    public function validarQR($qr_content) {
        // El contenido del QR es GRUPO_RESERVACION_ID:X
        $parts = explode(':', $qr_content);
        
        if (count($parts) !== 2 || $parts[0] !== 'GRUPO_RESERVACION_ID' || !is_numeric($parts[1])) {
            return "Código QR inválido. Formato incorrecto.";
        }
        
        $id_grupo = (int) $parts[1];
        
        // 1. Verificar si el grupo de reservas existe
        $sql1 = "SELECT id_grupo FROM grupos_reservas WHERE id_grupo = ?";
        $stmt1 = $this->pdo->prepare($sql1);
        $stmt1->execute([$id_grupo]);
        if (!$stmt1->fetch()) {
            return "Código QR no reconocido. El grupo de reservas no existe.";
        }

        // 2. Verificar el estado de las reservaciones en el grupo
        $sql2 = "SELECT id_reservacion, estado FROM reservaciones WHERE id_grupo = ?";
        $stmt2 = $this->pdo->prepare($sql2);
        $stmt2->execute([$id_grupo]);
        $reservaciones = $stmt2->fetchAll(PDO::FETCH_ASSOC);
        
        if (empty($reservaciones)) {
            return "Este código QR no tiene reservaciones asociadas.";
        }
        
        $reservas_pendientes = 0;
        foreach ($reservaciones as $reserva) {
            if ($reserva['estado'] === 'pendiente') {
                $reservas_pendientes++;
            }
        }
        
        if ($reservas_pendientes === 0) {
            return "Este código QR ya ha sido utilizado o todas las reservas han sido canceladas.";
        }
        
        // 3. Si todo es válido, marcar las reservaciones como "usadas"
        $this->pdo->beginTransaction();
        try {
            $sql3 = "UPDATE reservaciones SET estado = 'usada' WHERE id_grupo = ? AND estado = 'pendiente'";
            $stmt3 = $this->pdo->prepare($sql3);
            $stmt3->execute([$id_grupo]);
            $this->pdo->commit();
            return true; // Validación exitosa
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            return "Error al actualizar el estado de las reservas: " . $e->getMessage();
        }
    }
}
?>