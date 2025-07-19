<?php
// Este archivo actúa como el Modelo de Usuario. Su único propósito es
// interactuar con la base de datos para la entidad 'usuarios'.

class Usuario {
    // La propiedad '$pdo' almacenará la conexión a la base de datos.
    private $pdo;

    public function __construct($pdo) {
        // Al instanciar esta clase, se le pasa la conexión a la base de datos.
        // Esto desacopla la clase del método de conexión, lo cual es una buena práctica.
        $this->pdo = $pdo;
    }

    /**
     * Inserta un nuevo usuario en la tabla 'usuarios'.
     * @param mixed ...$params Todos los parámetros necesarios para la inserción.
     * @return bool Retorna true si la inserción fue exitosa, de lo contrario false.
     */
    public function crearUsuario($nombre, $apellidos, $telefono, $correo, $id_empleado, $password, $acepta_contacto) {
        try {
            // Se utiliza una sentencia preparada (prepared statement) con marcadores de posición '?'
            // para prevenir ataques de inyección SQL. Esta es una práctica de seguridad CRÍTICA.
            $sql = "INSERT INTO usuarios (nombre, apellidos, telefono, correo, id_empleado, password, acepta_contacto) VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = $this->pdo->prepare($sql);
            // El método execute() vincula los valores a los marcadores de posición de forma segura.
            return $stmt->execute([$nombre, $apellidos, $telefono, $correo, $id_empleado, $password, $acepta_contacto]);
        } catch (PDOException $e) {
            // El manejo de errores es importante. 'error_log' registra el error
            // en un archivo para depuración, pero no lo muestra al usuario final.
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
        // Se utiliza COUNT(*) que es más eficiente, ya que solo cuenta filas
        // y no necesita traer todos los datos del usuario.
        $sql = "SELECT COUNT(*) FROM usuarios WHERE correo = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$correo]);
        // fetchColumn() devuelve el valor de la primera columna del primer resultado.
        return $stmt->fetchColumn() > 0;
    }

    /**
     * Obtiene los datos de un usuario por su correo electrónico.
     * @param string $correo El correo del usuario.
     * @return array|false Retorna un array asociativo con los datos del usuario, o false si no se encuentra.
     */
    public function obtenerUsuarioPorCorreo($correo) {
        $sql = "SELECT * FROM usuarios WHERE correo = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$correo]);
        // PDO::FETCH_ASSOC devuelve un array con las columnas como claves.
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene los datos de todos los usuarios.
     * @return array Retorna un array con todos los usuarios.
     */
    public function obtenerTodosLosUsuarios() {
        // En este caso, no hay parámetros, por lo que se puede usar el método query() directamente.
        $sql = "SELECT * FROM usuarios ORDER BY nombre ASC";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene los datos de un usuario por su ID.
     * @param int $id_usuario El ID del usuario.
     * @return array|false Retorna un array asociativo con los datos del usuario, o false si no se encuentra.
     */
    public function obtenerUsuarioPorId($id_usuario) {
        $sql = "SELECT * FROM usuarios WHERE id_usuario = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id_usuario]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Actualiza el rol de un usuario.
     * @param int $id_usuario El ID del usuario.
     * @param string $rol El nuevo rol ('user' o 'admin').
     * @return bool Retorna true si la actualización fue exitosa, de lo contrario false.
     */
    public function actualizarRol($id_usuario, $rol) {
        $sql = "UPDATE usuarios SET rol = ? WHERE id_usuario = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$rol, $id_usuario]);
    }
}