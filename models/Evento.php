<?php

class Evento {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    public function getEventosDisponibles() {
        $sql = "SELECT e.*, c.nombre_categoria FROM eventos e JOIN categorias c ON e.id_categoria = c.id_categoria ORDER BY e.fecha, e.hora_inicio ASC";
        try {
            $stmt = $this->pdo->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al obtener eventos disponibles: " . $e->getMessage());
            return [];
        }
    }

    public function obtenerPorId($id_evento) {
        $sql = "SELECT e.*, c.nombre_categoria FROM eventos e JOIN categorias c ON e.id_categoria = c.id_categoria WHERE e.id_evento = ?";
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$id_evento]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al obtener evento por ID: " . $e->getMessage());
            return false;
        }
    }

    public function obtenerTodosConCategorias() {
        $sql = "SELECT e.*, c.nombre_categoria FROM eventos e JOIN categorias c ON e.id_categoria = c.id_categoria ORDER BY e.fecha DESC";
        try {
            $stmt = $this->pdo->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al obtener todos los eventos: " . $e->getMessage());
            return [];
        }
    }

    public function guardar($data) {
        if (isset($data['id_evento']) && !empty($data['id_evento'])) {
            $sql = "UPDATE eventos SET nombre_evento = ?, id_categoria = ?, codigo_evento = ?, descripcion = ?, fecha = ?, hora_inicio = ?, hora_fin = ?, ubicacion = ?, cupo_maximo = ? WHERE id_evento = ?";
            $params = [$data['nombre_evento'], $data['id_categoria'], $data['codigo_evento'], $data['descripcion'], $data['fecha'], $data['hora_inicio'], $data['hora_fin'], $data['ubicacion'], $data['cupo_maximo'], $data['id_evento']];
        } else {
            $sql = "INSERT INTO eventos (nombre_evento, id_categoria, codigo_evento, descripcion, fecha, hora_inicio, hora_fin, ubicacion, cupo_maximo, cupo_disponible) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $params = [$data['nombre_evento'], $data['id_categoria'], $data['codigo_evento'], $data['descripcion'], $data['fecha'], $data['hora_inicio'], $data['hora_fin'], $data['ubicacion'], $data['cupo_maximo'], $data['cupo_maximo']];
        }
        
        try {
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute($params);
        } catch (PDOException $e) {
            error_log("Error al guardar evento: " . $e->getMessage());
            return false;
        }
    }

    public function eliminar($id_evento) {
        $sql = "DELETE FROM eventos WHERE id_evento = ?";
        try {
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([$id_evento]);
        } catch (PDOException $e) {
            error_log("Error al eliminar el evento: " . $e->getMessage());
            return false;
        }
    }

    public function actualizarCupo($id_evento) {
        $sql = "UPDATE eventos SET cupo_disponible = cupo_disponible - 1 WHERE id_evento = ?";
        try {
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([$id_evento]);
        } catch (PDOException $e) {
            error_log("Error al actualizar cupo: " . $e->getMessage());
            return false;
        }
    }
    
    // Método para obtener todos los eventos agrupados por categoría
    public function getEventosDisponiblesPorCategoria() {
        $sql = "SELECT e.*, c.nombre_categoria FROM eventos e JOIN categorias c ON e.id_categoria = c.id_categoria ORDER BY c.nombre_categoria ASC, e.fecha ASC, e.hora_inicio ASC";
        try {
            $stmt = $this->pdo->query($sql);
            $eventos = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $eventos_agrupados = [];
            foreach ($eventos as $evento) {
                $eventos_agrupados[$evento['nombre_categoria']][] = $evento;
            }
            return $eventos_agrupados;
        } catch (PDOException $e) {
            error_log("Error al obtener eventos por categoría: " . $e->getMessage());
            return [];
        }
    }

    public function importarEventos($file_path) {
        try {
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file_path);
            $sheet = $spreadsheet->getActiveSheet();
            $highestRow = $sheet->getHighestRow();

            $this->pdo->beginTransaction();
            
            // Recorrer cada fila del archivo (desde la segunda, para omitir la cabecera)
            for ($row = 2; $row <= $highestRow; $row++) {
                $nombre_evento = $sheet->getCell('A' . $row)->getValue();
                $id_categoria = (int) $sheet->getCell('B' . $row)->getValue();
                $codigo_evento = $sheet->getCell('C' . $row)->getValue();
                $descripcion = $sheet->getCell('D' . $row)->getValue();
                $fecha = $sheet->getCell('E' . $row)->getFormattedValue();
                $hora_inicio = $sheet->getCell('F' . $row)->getFormattedValue();
                $hora_fin = $sheet->getCell('G' . $row)->getFormattedValue();
                $ubicacion = $sheet->getCell('H' . $row)->getValue();
                $cupo_maximo = (int) $sheet->getCell('I' . $row)->getValue();
                $cupo_disponible = (int) $sheet->getCell('J' . $row)->getValue();

                // Validaciones básicas antes de insertar
                if (empty($nombre_evento) || empty($id_categoria) || empty($fecha) || empty($cupo_maximo)) {
                    $this->pdo->rollBack();
                    return "Error de validación en la fila " . $row . ". Campos obligatorios faltantes.";
                }

                // Insertar en la base de datos
                $sql = "INSERT INTO eventos (nombre_evento, id_categoria, codigo_evento, descripcion, fecha, hora_inicio, hora_fin, ubicacion, cupo_maximo, cupo_disponible) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute([
                    $nombre_evento, $id_categoria, $codigo_evento, $descripcion, $fecha, $hora_inicio, $hora_fin, $ubicacion, $cupo_maximo, $cupo_disponible
                ]);
            }
            
            $this->pdo->commit();
            return true;

        } catch (Exception $e) {
            $this->pdo->rollBack();
            error_log("Error de importación: " . $e->getMessage());
            return "Error al importar el archivo: " . $e->getMessage();
        }
    }
}