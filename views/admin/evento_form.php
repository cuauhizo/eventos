<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $evento ? 'Editar Evento' : 'Crear Nuevo Evento'; ?></title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f4; display: flex; justify-content: center; align-items: center; min-height: 100vh; }
        .form-container { background-color: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1); width: 500px; }
        h1 { text-align: center; color: #333; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input[type="text"], input[type="date"], input[type="time"], input[type="number"], textarea {
            width: calc(100% - 22px); padding: 10px; border: 1px solid #ddd; border-radius: 4px;
        }
        button { width: 100%; padding: 12px; background-color: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 16px; }
        .error-message { color: red; text-align: center; margin-bottom: 15px; }
    </style>
</head>
<body>
    <div class="form-container">
        <h1><?php echo $evento ? 'Editar Evento' : 'Crear Nuevo Evento'; ?></h1>
        <?php
        if (isset($_SESSION['admin_mensaje_form'])) {
            echo '<p class="error-message">' . $_SESSION['admin_mensaje_form'] . '</p>';
            unset($_SESSION['admin_mensaje_form']);
        }
        ?>
        <form action="../public/index.php?action=admin_guardar_evento" method="POST">
            <?php if ($evento): ?>
                <input type="hidden" name="id_evento" value="<?php echo htmlspecialchars($evento['id_evento']); ?>">
            <?php endif; ?>
            <div class="form-group">
                <label for="nombre_evento">Nombre del Evento</label>
                <input type="text" id="nombre_evento" name="nombre_evento" value="<?php echo $evento ? htmlspecialchars($evento['nombre_evento']) : ''; ?>" required>
            </div>
            <div class="form-group">
                <label for="descripcion">Descripción</label>
                <textarea id="descripcion" name="descripcion" rows="4"><?php echo $evento ? htmlspecialchars($evento['descripcion']) : ''; ?></textarea>
            </div>
            <div class="form-group">
                <label for="fecha">Fecha</label>
                <input type="date" id="fecha" name="fecha" value="<?php echo $evento ? htmlspecialchars($evento['fecha']) : ''; ?>" required>
            </div>
            <div class="form-group">
                <label for="hora_inicio">Hora de Inicio</label>
                <input type="time" id="hora_inicio" name="hora_inicio" value="<?php echo $evento ? htmlspecialchars($evento['hora_inicio']) : ''; ?>" required>
            </div>
            <div class="form-group">
                <label for="hora_fin">Hora de Fin</label>
                <input type="time" id="hora_fin" name="hora_fin" value="<?php echo $evento ? htmlspecialchars($evento['hora_fin']) : ''; ?>" required>
            </div>
            <div class="form-group">
                <label for="cupo_maximo">Cupo Máximo</label>
                <input type="number" id="cupo_maximo" name="cupo_maximo" value="<?php echo $evento ? htmlspecialchars($evento['cupo_maximo']) : ''; ?>" required min="1">
            </div>
            <button type="submit"><?php echo $evento ? 'Actualizar Evento' : 'Crear Evento'; ?></button>
        </form>
        <p style="text-align: center; margin-top: 10px;"><a href="../public/index.php?action=admin_eventos_list">Cancelar</a></p>
    </div>
</body>
</html>