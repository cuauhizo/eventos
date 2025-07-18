<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Validador de QR</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f4; display: flex; justify-content: center; align-items: center; min-height: 100vh; }
        .validator-container { background-color: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1); width: 500px; text-align: center; }
        .validator-container h1 { color: #333; }
        .validator-form { margin-top: 20px; }
        .validator-form input[type="text"] { width: calc(100% - 22px); padding: 10px; border: 1px solid #ddd; border-radius: 4px; }
        .validator-form button { padding: 10px 20px; background-color: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer; margin-top: 10px; }
        .success-message, .error-message { padding: 10px; margin-top: 20px; border-radius: 4px; }
        .success-message { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .error-message { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
    </style>
</head>
<body>
    <div class="validator-container">
        <h1>Validador de Códigos QR</h1>
        <p>Ingresa el código QR o escanéalo para validar la entrada.</p>

        <?php
        // session_start();
        if (isset($_SESSION['validator_mensaje'])) {
            $clase = ($_SESSION['validator_exito']) ? 'success-message' : 'error-message';
            echo '<div class="' . $clase . '">' . $_SESSION['validator_mensaje'] . '</div>';
            unset($_SESSION['validator_mensaje']);
            unset($_SESSION['validator_exito']);
        }
        ?>

        <form class="validator-form" action="../public/index.php?action=admin_validar_qr" method="POST">
            <input type="text" name="qr_content" placeholder="Ej: GRUPO_RESERVACION_ID:1" required>
            <br>
            <button type="submit">Validar</button>
        </form>

        <p style="margin-top: 20px;"><a href="../public/index.php?action=admin_dashboard">Volver al Dashboard</a></p>
    </div>
</body>
</html>