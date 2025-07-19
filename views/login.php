<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - Sistema de Reservas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { 
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
        }
        .login-container {
            width: 450px;
            padding: 30px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h1 class="text-center mb-4">Iniciar Sesión</h1>
        <?php
        // var_dump($_SESSION['id_usuario']);
        // exit(); // Detiene el script para que puedas ver el valor
        // session_start();
        if (isset($_SESSION['login_mensaje'])):
            $alert_class = ($_SESSION['login_exito']) ? 'alert-success' : 'alert-danger';
            echo '<div class="alert ' . $alert_class . ' text-center">' . $_SESSION['login_mensaje'] . '</div>';
            unset($_SESSION['login_mensaje']);
            unset($_SESSION['login_exito']);
        endif;

        if (isset($_SESSION['registro_mensaje'])):
            $alert_class = ($_SESSION['registro_exito']) ? 'alert-success' : 'alert-danger';
            echo '<div class="alert ' . $alert_class . ' text-center">' . $_SESSION['registro_mensaje'] . '</div>';
            unset($_SESSION['registro_mensaje']);
            unset($_SESSION['registro_exito']);
        endif;
        ?>
        <form action="index.php?action=login" method="POST">
            <div class="mb-3">
                <label for="correo" class="form-label">Correo Electrónico:</label>
                <input type="email" class="form-control" id="correo" name="correo" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Contraseña:</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <div class="d-grid gap-2">
                <input type="submit" class="btn btn-primary" value="Iniciar Sesión">
            </div>
        </form>
        <p class="text-center mt-3">¿No tienes una cuenta? <a href="index.php?action=show_register_form">Regístrate aquí</a></p>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>