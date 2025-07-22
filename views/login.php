<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - Sistema de Reservas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .login-container { max-width: 400px; margin: 80px auto; padding: 30px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); background-color: #fff; }
    </style>
</head>
<body>
    <div class="login-container">
        <h2 class="text-center mb-4">Iniciar Sesión</h2>

        <?php
        // session_start(); // Asegúrate de que session_start() esté al principio de este archivo si no lo está en core.php o index.php
        if (isset($_SESSION['login_mensaje'])):
            $alert_class = ($_SESSION['login_exito']) ? 'alert-success' : 'alert-danger';
            echo '<div class="alert ' . $alert_class . ' text-center">' . $_SESSION['login_mensaje'] . '</div>';
            unset($_SESSION['login_mensaje']);
            unset($_SESSION['login_exito']);
        endif;
        if (isset($_SESSION['registro_mensaje'])): // Si viene de un registro exitoso
            $alert_class = ($_SESSION['registro_exito']) ? 'alert-success' : 'alert-danger';
            echo '<div class="alert ' . $alert_class . ' text-center">' . $_SESSION['registro_mensaje'] . '</div>';
            unset($_SESSION['registro_mensaje']);
            unset($_SESSION['registro_exito']);
        endif;
        ?>

        <form action="index.php?action=login" method="POST">
            <div class="mb-3">
                <label for="correo" class="form-label">Correo Electrónico</label>
                <input type="email" class="form-control" id="correo" name="correo" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Iniciar Sesión</button>
        </form>
        <p class="text-center mt-3">¿No tienes cuenta? <a href="index.php?action=show_register_form">Regístrate aquí</a></p>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>