<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - Sistema de Reservas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../public/css/styles.css"> <!-- Asegúrate de que este archivo exista -->
    <style>

        .login-container { max-width: 400px; margin: 80px auto; padding: 30px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        /* Estilo para el contenedor de mensajes, similar al de registro */
        #message-container { margin-bottom: 15px; }
    </style>
</head>
<body>
    <div class="login-container">
        <h1 class="text-center mb-4">Iniciar Sesión</h1>

        <div id="message-container">
            <?php
            // session_start(); // Asegúrate de que session_start() esté al principio de tu archivo principal
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
        </div>

        <form id="loginForm" action="index.php?action=login" method="POST" class="needs-validation" novalidate>
            <div class="mb-3">
                <label for="correo" class="form-label">Correo Electrónico:</label>
                <input type="email" class="form-control" id="correo" name="correo" value="<?php echo htmlspecialchars($_POST['correo'] ?? ''); ?>" required>
                <div class="invalid-feedback">Por favor, introduce un correo electrónico válido.</div>
            </div>
            <button type="submit" class="btn btn-primary w-100">Iniciar Sesión</button>
        </form>
        <p class="text-center mt-3">¿No tienes cuenta? <a href="index.php?action=show_register_form">Regístrate aquí</a></p>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // ==========================================================
            // Lógica de validación de Bootstrap para el formulario de login
            // ==========================================================
            const form = document.getElementById('loginForm');
            const messageContainer = document.getElementById('message-container'); // Reutilizamos este para mensajes AJAX si se implementa

            form.addEventListener('submit', function(event) {
                // Prevenir el envío si la validación de Bootstrap no pasa
                if (!form.checkValidity()) {
                    event.preventDefault(); // Evita el envío normal
                    event.stopPropagation(); // Detiene la propagación del evento
                }

                // Aplica las clases de validación (rojo/verde) a los campos
                form.classList.add('was-validated');

                // Opcional: Si deseas implementar AJAX para el login
                // Puedes añadir aquí la lógica de fetch similar a la del registro,
                // para evitar la recarga de la página y mostrar el mensaje de error inline.
                // Si decides NO usar AJAX para login, esta parte de JS es suficiente
                // para la validación visual y el formulario se enviará normalmente después.
            }, false); // El 'false' es para el useCapture
        });
    </script>
</body>
</html>