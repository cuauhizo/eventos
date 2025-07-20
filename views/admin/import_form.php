<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Importar Eventos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f4f4f4; }
        .container { max-width: 600px; margin: 50px auto; padding: 30px; background-color: #fff; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="text-center mb-4">Importar Eventos</h1>
        <?php
        // session_start();
        if (isset($_SESSION['import_mensaje'])):
            $alert_class = ($_SESSION['import_exito']) ? 'alert-success' : 'alert-danger';
            echo '<div class="alert ' . $alert_class . ' text-center">' . $_SESSION['import_mensaje'] . '</div>';
            unset($_SESSION['import_mensaje']);
            unset($_SESSION['import_exito']);
        endif;
        ?>
        <p class="text-center">Sube un archivo CSV o Excel con los datos de los eventos.</p>
        <form action="../public/index.php?action=admin_import_events" method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="eventos_file" class="form-label">Selecciona un archivo (.csv, .xlsx)</label>
                <input type="file" class="form-control" id="eventos_file" name="eventos_file" required>
            </div>
            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary">Importar</button>
            </div>
        </form>
        <p class="text-center mt-3"><a href="../public/index.php?action=admin_dashboard">Volver al Dashboard</a></p>
    </div>
</body>
</html>