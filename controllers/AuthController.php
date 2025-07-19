<?php
// Este archivo actúa como el controlador de autenticación,
// manejando toda la lógica de negocio relacionada con el registro e inicio de sesión.

// Incluye el modelo de usuario, que maneja la interacción con la base de datos.
require_once ROOT_PATH . '/models/Usuario.php';

class AuthController {
    // La propiedad 'usuarioModel' contendrá una instancia de la clase Usuario.
    // Esto es un ejemplo de Inyección de Dependencias.
    private $usuarioModel;

    public function __construct($pdo) {
        // Al crear el controlador, se le pasa la conexión a la base de datos ($pdo),
        // y se usa para inicializar el modelo de usuario.
        $this->usuarioModel = new Usuario($pdo);
    }

    /**
     * Registra a un nuevo usuario en el sistema.
     * @param string $nombre, $apellidos, etc. Los datos del usuario.
     * @param int $acepta_contacto 1 si acepta, 0 si no.
     * @return bool|string Retorna true si el registro fue exitoso, o un mensaje de error si falló.
     */
    public function registrarUsuario($nombre, $apellidos, $telefono, $correo, $id_empleado, $password, $acepta_contacto) {
        // --- Validaciones del lado del servidor (cruciales para la seguridad) ---
        // 1. Verificación de que los campos obligatorios no estén vacíos.
        if (empty($nombre) || empty($apellidos) || empty($telefono) || empty($correo) || empty($id_empleado) || empty($password)) {
            return "Todos los campos son obligatorios.";
        }
        
        // 2. Validaciones de formato con expresiones regulares.
        // ^[\p{L}\s]+$ :
        // ^    -> Inicia la cadena.
        // [\p{L}\s]+ -> Acepta una o más letras de cualquier idioma (\p{L}) o espacios (\s).
        // $    -> Termina la cadena.
        // /u   -> Modificador que permite el soporte para caracteres Unicode (como acentos, ñ, etc.).
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
        if (substr($correo, -9) !== '@nike.com') {
            return "Solo se aceptan correos electrónicos con el dominio @nike.com.";
        }

        // 5. Verificación de unicidad del correo electrónico.
        if ($this->usuarioModel->existeUsuario($correo)) {
            return "El correo electrónico ya está registrado.";
        }

        // 6. Cifrado de la contraseña.
        // password_hash() usa un algoritmo de hashing seguro para que la contraseña
        // nunca se guarde en texto plano, protegiendo a los usuarios.
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // 7. Llamada al modelo para ejecutar la inserción en la base de datos.
        $exito = $this->usuarioModel->crearUsuario($nombre, $apellidos, $telefono, $correo, $id_empleado, $hashed_password, $acepta_contacto);

        if ($exito) {
            return true;
        } else {
            return "Error al registrar el usuario. Inténtalo de nuevo.";
        }
    }

    /**
     * Inicia sesión de un usuario.
     * @param string $correo El correo del usuario.
     * @param string $password La contraseña en texto plano.
     * @return bool|string Retorna true si el inicio de sesión es exitoso, o un mensaje de error.
     */
    public function iniciarSesion($correo, $password) {
        // 1. Obtener los datos del usuario por su correo.
        $usuario = $this->usuarioModel->obtenerUsuarioPorCorreo($correo);

        // 2. Verificar si el usuario existe y si la contraseña es correcta.
        // password_verify() compara la contraseña en texto plano con el hash guardado.
        if ($usuario && password_verify($password, $usuario['password'])) {
            // 3. Si es correcto, se inicia la sesión y se guardan los datos clave.
            $_SESSION['loggedin'] = true;
            $_SESSION['id_usuario'] = $usuario['id_usuario'];
            $_SESSION['nombre'] = $usuario['nombre'];
            $_SESSION['rol'] = $usuario['rol'];
            return true;
        } else {
            return "Correo o contraseña incorrectos.";
        }
    }
}