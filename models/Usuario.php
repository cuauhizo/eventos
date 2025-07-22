<?php
// Este archivo actúa como el Modelo de Usuario. Su único propósito es
// interactuar con la base de datos para la entidad 'usuarios'.

class Usuario {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    /**
     * Inserta un nuevo usuario en la tabla 'usuarios'.
     * @param string $nombre, $apellidos, $telefono, $correo, $id_empleado.
     * @param string|null $password La contraseña hasheada (ahora opcional/null).
     * @param int $acepta_contacto 1 si acepta, 0 si no.
     * @return bool Retorna true si la inserción fue exitosa, de lo contrario false.
     */
    // MODIFICADO: $password se hace opcional con un valor por defecto null
    public function crearUsuario($nombre, $apellidos, $telefono, $correo, $id_empleado, $password = null, $acepta_contacto) {
        try {
            // Asegúrate de que la columna 'password' en tu base de datos sea NULLABLE.
            // Si no lo es, y quieres que no haya contraseña, tendrías que insertar una cadena vacía ''
            // o un hash de una cadena vacía. Pero lo ideal es que sea NULLABLE.
            $sql = "INSERT INTO usuarios (nombre, apellidos, telefono, correo, id_empleado, password, acepta_contacto, rol) VALUES (?, ?, ?, ?, ?, ?, ?, 'user')"; // Se añade 'rol' por defecto
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([$nombre, $apellidos, $telefono, $correo, $id_empleado, $password, $acepta_contacto]);
        } catch (PDOException $e) {
            error_log("Error al crear usuario: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Verifica si un usuario ya existe en la base de datos por su correo.
     * @param string $correo El correo a verificar.
     * @return bool Retorna true si el usuario existe, de lo contrario false.
     */
    public function existeUsuario($correo) {
        $sql = "SELECT COUNT(*) FROM usuarios WHERE correo = ?";
        try { // Añadido try-catch para manejar errores de PDO
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$correo]);
            return $stmt->fetchColumn() > 0;
        } catch (PDOException $e) {
            error_log("Error al verificar existencia de usuario: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Busca un usuario por ID de empleado.
     * (Este método lo mencionó tu AuthController, pero no estaba en este modelo. Lo añadimos.)
     * @param string $id_empleado El ID de empleado a buscar.
     * @return array|false Retorna un array asociativo con los datos del usuario, o false si no se encuentra.
     */
    public function buscarPorIdEmpleado($id_empleado) {
        $sql = "SELECT * FROM usuarios WHERE id_empleado = ?";
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$id_empleado]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al buscar usuario por ID de empleado: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Obtiene los datos de un usuario por su correo electrónico.
     * @param string $correo El correo del usuario.
     * @return array|false Retorna un array asociativo con los datos del usuario, o false si no se encuentra.
     */
    public function obtenerUsuarioPorCorreo($correo) {
        $sql = "SELECT * FROM usuarios WHERE correo = ?";
        try { // Añadido try-catch para manejar errores de PDO
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$correo]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al obtener usuario por correo: " . $e->getMessage());
            return false;
        }
    }

    public function obtenerTodosLosUsuarios() {
        $sql = "SELECT * FROM usuarios ORDER BY nombre ASC";
        try { // Añadido try-catch
            $stmt = $this->pdo->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al obtener todos los usuarios: " . $e->getMessage());
            return [];
        }
    }

    public function obtenerTodosConRol() {
        $sql = "SELECT id_usuario, nombre, apellidos, correo, id_empleado, rol, fecha_registro FROM usuarios ORDER BY fecha_registro DESC";
        try { // Añadido try-catch
            $stmt = $this->pdo->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al cargar la lista de usuarios con rol: " . $e->getMessage());
            return [];
        }
    }

    public function obtenerUsuarioPorId($id_usuario) {
        $sql = "SELECT * FROM usuarios WHERE id_usuario = ?";
        try { // Añadido try-catch
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$id_usuario]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al obtener usuario por ID: " . $e->getMessage());
            return false;
        }
    }

    public function actualizarRol($id_usuario, $rol) {
        $sql = "UPDATE usuarios SET rol = ? WHERE id_usuario = ?";
        try { // Añadido try-catch
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([$rol, $id_usuario]);
        } catch (PDOException $e) {
            error_log("Error al actualizar rol de usuario: " . $e->getMessage());
            return false;
        }
    }
}