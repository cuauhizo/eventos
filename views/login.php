<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - Sistema de Reservas</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f4; display: flex; justify-content: center; align-items: center; min-height: 100vh; margin: 0; }
        .login-container { background-color: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1); width: 400px; }
        .login-container h2 { text-align: center; color: #333; margin-bottom: 20px; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; color: #555; }
        .form-group input[type="email"],
        .form-group input[type="password"] {
            width: calc(100% - 20px);
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .form-group input[type="submit"] {
            width: 100%;
            padding: 10px;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s ease;
        }
        .form-group input[type="submit"]:hover {
            background-color: #218838;
        }
        .error-message { color: red; text-align: center; margin-bottom: 15px; }
        .success-message { color: green; text-align: center; margin-bottom: 15px; }
        .register-link { text-align: center; margin-top: 20px; }
        .register-link a { color: #007bff; text-decoration: none; }
        .register-link a:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>Iniciar Sesión</h2>
        <?php
        // var_dump($_SESSION['id_usuario']);
        // exit(); // Detiene el script para que puedas ver el valor

        // Mostramos mensajes de éxito/error de la sesión
        // Es importante que la sesión se inicie en el archivo principal (index.php/core.php)
        if (isset($_SESSION['login_mensaje'])) {
            $clase_mensaje = ($_SESSION['login_exito']) ? 'success-message' : 'error-message';
            echo '<p class="' . $clase_mensaje . '">' . $_SESSION['login_mensaje'] . '</p>';
            unset($_SESSION['login_mensaje']); // Limpiar el mensaje
            unset($_SESSION['login_exito']);
        }
        ?>
        <form action="../public/index.php?action=login" method="POST">
            <div class="form-group">
                <label for="correo">Correo Electrónico:</label>
                <input type="email" id="correo" name="correo" required>
            </div>
            <div class="form-group">
                <label for="password">Contraseña:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div class="form-group">
                <input type="submit" value="Iniciar Sesión">
            </div>
        </form>
        <div class="register-link">
            ¿No tienes una cuenta? <a href="../public/index.php?action=show_register_form">Regístrate aquí</a>
        </div>
    </div>
</body>
</html>