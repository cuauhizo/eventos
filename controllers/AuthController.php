<?php
// Usamos ROOT_PATH que ya definimos
// require_once __DIR__ . '/../config/constants.php'; // Incluye la constante ROOT_PATH
// require_once ROOT_PATH . '/config/database.php'; // Incluye la conexión a la base de datos

class AuthController {

    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function registrarUsuario($nombre, $apellidos, $telefono, $correo, $id_empleado, $password) {
        // Validaciones básicas (puedes añadir más, como longitud mínima, etc.)
        if (empty($nombre) || empty($apellidos) || empty($telefono) || empty($correo) || empty($id_empleado) || empty($password)) {
            return "Todos los campos son obligatorios.";
        }

        if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
            return "El formato del correo electrónico no es válido.";
        }

        // Verificar si el correo o ID de empleado ya existen
        if ($this->existeUsuario($correo, $id_empleado)) {
            return "El correo electrónico o el ID de empleado ya están registrados.";
        }

        // Hashear la contraseña antes de guardarla en la base de datos
        $password_hash = password_hash($password, PASSWORD_DEFAULT);

        $sql = "INSERT INTO usuarios (nombre, apellidos, telefono, correo, id_empleado, password) VALUES (?, ?, ?, ?, ?, ?)";
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$nombre, $apellidos, $telefono, $correo, $id_empleado, $password_hash]);
            return true; // Registro exitoso
        } catch (PDOException $e) {
            // En un entorno de producción, registrar el error en un log en lugar de mostrarlo
            return "Error al registrar el usuario: " . $e->getMessage();
        }
    }

    private function existeUsuario($correo, $id_empleado) {
        $sql = "SELECT COUNT(*) FROM usuarios WHERE correo = ? OR id_empleado = ?";
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$correo, $id_empleado]);
            return $stmt->fetchColumn() > 0;
        } catch (PDOException $e) {
            return false; // Manejo de error, asume que no existe para no bloquear
        }
    }

    public function iniciarSesion($correo, $password) {
        if (empty($correo) || empty($password)) {
            return "El correo y la contraseña son obligatorios.";
        }

        $sql = "SELECT id_usuario, nombre, apellidos, rol, password FROM usuarios WHERE correo = ?";
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$correo]);
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$usuario) {
                return "Credenciales incorrectas.";
            }

            if (password_verify($password, $usuario['password'])) {
                // ¡AHORA GUARDAMOS MÁS INFORMACIÓN EN LA SESIÓN!
                $_SESSION['id_usuario'] = $usuario['id_usuario'];
                $_SESSION['loggedin'] = true;
                $_SESSION['nombre'] = $usuario['nombre'];
                $_SESSION['apellidos'] = $usuario['apellidos'];
                $_SESSION['rol'] = $usuario['rol'];

                return true;
            } else {
                return "Credenciales incorrectas.";
            }
        } catch (PDOException $e) {
            return "Error al iniciar sesión: " . $e->getMessage();
        }
    }
}
?>