<?php
session_start(); // Inicia la sesión

// Configura las variables de sesión para el usuario administrador
$_SESSION['loggedin'] = true;
$_SESSION['id_usuario'] = 1; // <-- REEMPLAZA CON EL ID REAL DE UN USUARIO ADMIN EN TU DB
$_SESSION['nombre'] = 'Admin Principal'; // <-- REEMPLAZA CON EL NOMBRE REAL DEL ADMIN
$_SESSION['rol'] = 'admin';

// Redirige al panel de administración
header("Location: index.php?action=admin_dashboard");
exit();
?>