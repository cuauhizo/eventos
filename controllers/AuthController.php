<?php
// Este archivo actúa como el controlador de autenticación,
// manejando toda la lógica de negocio relacionada con el registro e inicio de sesión.

// Incluye el modelo de usuario, que maneja la interacción con la base de datos.
require_once ROOT_PATH . '/models/Usuario.php';

class AuthController {
    // La propiedad 'usuarioModel' contendrá una instancia de la clase Usuario.
    private $usuarioModel;

    public function __construct($pdo) {
        $this->usuarioModel = new Usuario($pdo);
    }

    /**
     * Registra a un nuevo usuario en el sistema, sin requerir contraseña.
     * @param string $nombre, $apellidos, etc. Los datos del usuario.
     * @param int $acepta_contacto 1 si acepta, 0 si no.
     * @return bool|string Retorna true si el registro fue exitoso, o un mensaje de error si falló.
     */
    // MODIFICADO: El método registrarUsuario ya no recibe $password
    public function registrarUsuario($nombre, $apellidos, $telefono, $correo, $id_empleado, $acepta_contacto) { // Eliminado $password del parámetro
        // --- Validaciones del lado del servidor (cruciales para la seguridad) ---
        // 1. Verificación de que los campos obligatorios no estén vacíos.
        // MODIFICADO: empty($password) ha sido eliminado de la validación
        if (empty($nombre) || empty($apellidos) || empty($telefono) || empty($correo) || empty($id_empleado)) {
            return "Todos los campos son obligatorios.";
        }
        
        // 2. Validaciones de formato con expresiones regulares.
        if (!preg_match("/^[\p{L}\s]+$/u", $nombre)) {
            return "El nombre solo puede contener letras y espacios.";
        }
        if (!preg_match("/^[\p{L}\s]+$/u", $apellidos)) {
            return "Los apellidos solo pueden contener letras y espacios.";
        }
        if (!preg_match("/^[0-9]+$/", $telefono)) {
            return "El teléfono solo puede contener números.";
        }
        if (!preg_match("/^[A-Za-z0-9]+$/", $id_empleado)) {
            return "El ID de empleado solo puede contener letras y números.";
        }

        // 3. Validación del formato de correo electrónico.
        if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
            return "El formato del correo electrónico no es válido.";
        }

        // 4. Validación de la regla de negocio (dominios permitidos).
        // if (substr($correo, -9) !== '@nike.com') {
        //     return "Solo se aceptan correos electrónicos con el dominio @nike.com.";
        // }

        // Se cambió el dominio y la longitud del substring.
        if (substr($correo, -15) !== '@tolkogroup.com') { // CAMBIO AQUÍ: -15 y '@tolkogroup.com'
            return "Solo se aceptan correos electrónicos con el dominio @tolkogroup.com.";
        }

        // 5. Verificación de unicidad del correo electrónico.
        // MODIFICADO: Usar buscarPorCorreo en lugar de existeUsuario si tu modelo no tiene existeUsuario
        if ($this->usuarioModel->existeUsuario($correo)) {
            return "El correo electrónico ya está registrado.";
        }
        // Asumiendo que existe buscarPorIdEmpleado en tu modelo Usuario
        if ($this->usuarioModel->buscarPorIdEmpleado($id_empleado)) {
            return "El ID de empleado ya está registrado.";
        }

        // CAMBIO CRÍTICO: Eliminado el cifrado de la contraseña.
        // Se pasa un valor nulo o vacío, o se ajusta el modelo para no esperar la contraseña.
        $password_placeholder = null; // O un valor vacío si la columna 'password' en tu DB no es NULLABLE

        // 6. Llamada al modelo para ejecutar la inserción en la base de datos.
        // MODIFICADO: La llamada a crearUsuario ya no pasa $password directamente
        $exito = $this->usuarioModel->crearUsuario($nombre, $apellidos, $telefono, $correo, $id_empleado, $password_placeholder, $acepta_contacto);

        if ($exito) {
            return true;
        } else {
            return "Error al registrar el usuario. Inténtalo de nuevo.";
        }
    }

    /**
     * Inicia sesión de un usuario solo con su correo electrónico.
     * @param string $correo El correo del usuario.
     * @return bool|string Retorna true si el inicio de sesión es exitoso, o un mensaje de error.
     */
    // MODIFICADO: El método iniciarSesion ya no recibe $password
    public function iniciarSesion($correo) { // Eliminado $password del parámetro
        // 1. Obtener los datos del usuario por su correo.
        $usuario = $this->usuarioModel->obtenerUsuarioPorCorreo($correo);

        // CAMBIO CRÍTICO: SOLO SE VERIFICA SI EL USUARIO EXISTE POR CORREO
        if ($usuario) {
            // 2. Si el usuario existe, se inicia la sesión y se guardan los datos clave.
            $_SESSION['loggedin'] = true;
            $_SESSION['id_usuario'] = $usuario['id_usuario'];
            $_SESSION['nombre'] = $usuario['nombre'];
            $_SESSION['rol'] = $usuario['rol'];
            return true;
        } else {
            // Si el correo no está registrado
            return "Correo electrónico no registrado.";
        }
    }

    /**
     * Cierra la sesión del usuario.
     */
    public function logout() {
        session_destroy();
        // La redirección después del logout se maneja en el enrutador (index.php)
    }
}