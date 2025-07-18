<?php
// Define tus credenciales de la base de datos
define('DB_HOST', 'localhost'); // Por lo general, 'localhost'
define('DB_USER', 'root');     // Tu usuario de MySQL
define('DB_PASS', '');         // Tu contraseña de MySQL (vacío si no tienes)
define('DB_NAME', 'bd_eventos_deportivos'); // El nombre de la base de datos que creaste

try {
    // Crea una nueva instancia de PDO para la conexión
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8", DB_USER, DB_PASS);
    
    // Configura el modo de error para que PDO lance excepciones en caso de problemas
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Configura el modo de obtención predeterminado para que devuelva arrays asociativos
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    // echo "Conexión a la base de datos exitosa."; // Puedes descomentar para probar la conexión
} catch (PDOException $e) {
    // Si hay un error, muestra el mensaje de error y termina el script
    die("Error de conexión a la base de datos: " . $e->getMessage());
}
?>