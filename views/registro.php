<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Usuario - Sistema de Reservas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* CSS personalizado para el contenedor del formulario */
        body { background-color: #f4f4f4; }
        .register-container {
            max-width: 450px;
            margin: 50px auto;
            padding: 30px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        #message-container { margin-bottom: 15px; }
    </style>
</head>
<body>
    <div class="register-container">
        <h1 class="text-center mb-4">Registro</h1>
        <div id="message-container"></div>
        
        <form id="registroForm" action="index.php?action=register" method="POST">
            <div class="mb-3">
                <label for="nombre" class="form-label">Nombre:</label>
                <input type="text" class="form-control" id="nombre" name="nombre" required pattern="[A-Za-záéíóúÁÉÍÓÚñÑ\s]+" title="Solo letras y espacios.">
            </div>
            <div class="mb-3">
                <label for="apellidos" class="form-label">Apellidos:</label>
                <input type="text" class="form-control" id="apellidos" name="apellidos" required pattern="[A-Za-záéíóúÁÉÍÓÚñÑ\s]+" title="Solo letras y espacios.">
            </div>
            <div class="mb-3">
                <label for="telefono" class="form-label">Número de WhatsApp:</label>
                <input type="tel" class="form-control" id="telefono" name="telefono" placeholder="Ej. 5512345678" required pattern="[0-9]+" maxlength="10" title="Solo números.">
            </div>
            <div class="mb-3">
                <label for="correo" class="form-label">Correo Electrónico:</label>
                <input type="email" class="form-control" id="correo" name="correo" required>
            </div>
            <div class="mb-3">
                <label for="id_empleado" class="form-label">ID de Empleado:</label>
                <input type="text" class="form-control" id="id_empleado" name="id_empleado" required pattern="[A-Za-z0-9]+" title="Solo letras y números.">
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Contraseña:</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <div class="mb-3 form-check">
                <input type="checkbox" class="form-check-input" id="acepta_contacto" name="acepta_contacto" value="1" required>
                <label class="form-check-label" for="acepta_contacto">Acepto que se me contacte por este medio para recibir noticias del evento.</label>
            </div>
            <div class="d-grid gap-2">
                <input type="submit" class="btn btn-primary" value="Registrarse">
            </div>
        </form>
        <p class="text-center mt-3">¿Ya tienes una cuenta? <a href="index.php?action=show_login_form">Inicia sesión aquí</a></p>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Funciones de validación en tiempo real.
        // Se disparan con el evento 'input' para limpiar los campos mientras el usuario escribe.
        function validarSoloTexto(event) {
            const input = event.target;
            input.value = input.value.replace(/[^A-Za-záéíóúÁÉÍÓÚñÑ\s]/g, '');
        }
        function validarSoloNumeros(event) {
            const input = event.target;
            input.value = input.value.replace(/[^0-9]/g, '');
        }
        function validarAlfanumerico(event) {
            const input = event.target;
            input.value = input.value.replace(/[^A-Za-z0-9]/g, '');
        }
        // Asignación de los eventos a cada campo por su ID.
        document.getElementById('nombre').addEventListener('input', validarSoloTexto);
        document.getElementById('apellidos').addEventListener('input', validarSoloTexto);
        document.getElementById('telefono').addEventListener('input', validarSoloNumeros);
        document.getElementById('id_empleado').addEventListener('input', validarAlfanumerico);

        // ==========================================================
        // Lógica de validación con AJAX
        // ==========================================================
        const form = document.getElementById('registroForm');
        const messageContainer = document.getElementById('message-container');

        // Escucha el evento 'submit' del formulario.
        form.addEventListener('submit', function(event) {
            event.preventDefault(); // Evita el envío normal del formulario y la recarga de la página.

            const formData = new FormData(form);
            const url = form.action + '&ajax=1'; // Se añade el parámetro 'ajax=1' para que el servidor sepa que es una petición AJAX.

            // Usa la API fetch para enviar los datos de forma asíncrona.
            fetch(url, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json()) // Espera la respuesta en formato JSON.
            .then(data => {
                // Se actualiza el DOM (el HTML) con el mensaje de éxito o error del servidor.
                let alertClass = data.success ? 'alert-success' : 'alert-danger';
                messageContainer.innerHTML = `<div class="alert ${alertClass} text-center">${data.message}</div>`;

                if (data.success) {
                    form.reset(); // Si el registro fue exitoso, limpia los campos del formulario.
                }
            })
            .catch(error => {
                // Manejo de errores de la red o del servidor.
                messageContainer.innerHTML = `<div class="alert alert-danger text-center">Ocurrió un error al procesar tu solicitud.</div>`;
            });
        });
    </script>
</body>
</html>