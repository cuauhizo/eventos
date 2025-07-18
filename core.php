<?php
session_start();

// Define la ruta raíz del proyecto de forma absoluta
define('ROOT_PATH', __DIR__);

// Incluye el autoloader de Composer
require_once ROOT_PATH . '/vendor/autoload.php';

// Incluye el archivo de conexión a la base de datos
require_once ROOT_PATH . '/config/database.php';

// Incluye la nueva clase de ayuda para el correo
require_once ROOT_PATH . '/helpers/MailHelper.php';

// Incluye todos los controladores que necesitarás
require_once ROOT_PATH . '/controllers/AuthController.php';
require_once ROOT_PATH . '/controllers/EventoController.php';
require_once ROOT_PATH . '/controllers/AdminController.php';
?>