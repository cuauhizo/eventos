<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Usuario - Sistema de Reservas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../public/css/styles.css">
    <style>
        /* CSS personalizado para el contenedor del formulario */
        /* body { background-color: #f4f4f4; } */
        .register-container {
            max-width: 450px;
            margin: 50px auto;
            padding: 30px;
            /* background-color: #fff; */
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        #message-container { margin-bottom: 15px; }
    </style>
</head>
<body>
    <div class="register-container">
        <h1 class="text-center mb-4">Registro</h1>
        <div id="message-container">
            <?php
            // session_start(); // Asegúrate de que session_start() esté al principio de tu archivo principal (ej. core.php o index.php)
            if (isset($_SESSION['registro_mensaje'])):
                $alert_class = ($_SESSION['registro_exito']) ? 'alert-success' : 'alert-danger';
                echo '<div class="alert ' . $alert_class . ' text-center">' . $_SESSION['registro_mensaje'] . '</div>';
                unset($_SESSION['registro_mensaje']);
                unset($_SESSION['registro_exito']);
            endif;
            ?>
        </div>
        
        <form id="registroForm" action="index.php?action=register" method="POST" class="needs-validation" novalidate>
            <div class="mb-3">
                <label for="nombre" class="form-label">Nombre:</label>
                <input type="text" class="form-control" id="nombre" name="nombre" value="<?php echo htmlspecialchars($_POST['nombre'] ?? ''); ?>" required>
                <div class="invalid-feedback">Por favor, introduce tu nombre.</div>
            </div>
            <div class="mb-3">
                <label for="apellidos" class="form-label">Apellidos:</label>
                <input type="text" class="form-control" id="apellidos" name="apellidos" value="<?php echo htmlspecialchars($_POST['apellidos'] ?? ''); ?>" required>
                <div class="invalid-feedback">Por favor, introduce tus apellidos.</div>
            </div>
            <div class="mb-3">
                <label for="telefono" class="form-label">Número de WhatsApp:</label>
                <input type="tel" class="form-control" id="telefono" name="telefono" placeholder="Ej. 5512345678" required pattern="[0-9]{10}" maxlength="10" title="Solo números y 10 dígitos.">
                <div class="invalid-feedback">Por favor, introduce 10 dígitos numéricos para el teléfono.</div>
            </div>
            <div class="mb-3">
                <label for="correo" class="form-label">Correo Electrónico:</label>
                <input type="email" class="form-control" id="correo" name="correo" value="<?php echo htmlspecialchars($_POST['correo'] ?? ''); ?>" required pattern=".*@tolkogroup\.com$" title="Solo se aceptan correos con el dominio @tolkogroup.com.">
                <div class="invalid-feedback">Por favor, introduce un correo electrónico válido del dominio @tolkogroup.com.</div>
            </div>
            <div class="mb-3">
                <label for="id_empleado" class="form-label">ID de Empleado:</label>
                <input type="text" class="form-control" id="id_empleado" name="id_empleado" value="<?php echo htmlspecialchars($_POST['id_empleado'] ?? ''); ?>" required pattern="^[A-Za-z0-9]+$" title="Solo letras y números.">
                <div class="invalid-feedback">Por favor, introduce tu ID de empleado (solo letras y números).</div>
            </div>
            <div class="mb-3 form-check">
                <input type="checkbox" class="form-check-input" id="acepta_contacto_checkbox" value="1" required <?php echo isset($_POST['acepta_contacto']) ? 'checked' : ''; ?>>
                <label class="form-check-label" for="acepta_contacto_checkbox">Acepto que se me contacte por este medio para recibir noticias del evento.</label>
                <div class="invalid-feedback">Debes aceptar los términos para continuar.</div>
                
                <input type="hidden" name="acepta_contacto" id="acepta_contacto_hidden" value="<?php echo isset($_POST['acepta_contacto']) ? '1' : '0'; ?>">
            </div>
            <div class="d-grid gap-2">
                <input type="submit" class="btn btn-primary" value="Registrarse">
            </div>
        </form>
        <p class="text-center mt-3">¿Ya tienes una cuenta? <a href="index.php?action=show_login_form">Inicia sesión aquí</a></p>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Funciones de validación de entrada en tiempo real (limpian caracteres mientras se escribe).
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

        document.addEventListener('DOMContentLoaded', function() {
            // Asignación de los eventos de filtrado de entrada a cada campo por su ID.
            document.getElementById('nombre').addEventListener('input', validarSoloTexto);
            document.getElementById('apellidos').addEventListener('input', validarSoloTexto);
            document.getElementById('telefono').addEventListener('input', validarSoloNumeros);
            document.getElementById('id_empleado').addEventListener('input', validarAlfanumerico);

            // === Lógica para sincronizar el checkbox visible con el campo oculto ===
            // Esto asegura que el valor de 'acepta_contacto' siempre se envíe correctamente (0 o 1).
            const aceptaContactoCheckbox = document.getElementById('acepta_contacto_checkbox');
            const aceptaContactoHidden = document.getElementById('acepta_contacto_hidden');

            // Sincronizar el campo oculto cuando cambia el checkbox visible
            aceptaContactoCheckbox.addEventListener('change', function() {
                aceptaContactoHidden.value = this.checked ? '1' : '0';
            });
            // Asegurarse de que el campo oculto tenga el valor correcto al cargar la página (ej. si el formulario se repobló después de un error PHP).
            aceptaContactoHidden.value = aceptaContactoCheckbox.checked ? '1' : '0';


            // ==========================================================
            // Lógica de validación de Bootstrap y envío con AJAX
            // ==========================================================
            const form = document.getElementById('registroForm'); // El ID del formulario
            const messageContainer = document.getElementById('message-container'); // El contenedor para mensajes

            // Escucha el evento 'submit' del formulario.
            form.addEventListener('submit', function(event) {
                event.preventDefault(); // Evita el envío normal del formulario

                // PASO 1: Validar con Bootstrap (client-side validation)
                if (!form.checkValidity()) {
                    event.stopPropagation(); // Detiene la propagación del evento
                    form.classList.add('was-validated'); // Aplica clases de Bootstrap para mostrar errores
                    return; // No enviar AJAX si hay errores de cliente
                }

                // PASO 2: Si la validación de Bootstrap pasa, se procede con AJAX
                messageContainer.innerHTML = ''; // Limpiar mensajes anteriores de AJAX o PHP
                form.classList.add('was-validated'); // Asegurar que los estilos de campos válidos se apliquen (verdes)
                
                const formData = new FormData(form);
                
                // PASO 3: Forzar el valor del checkbox 'acepta_contacto' en FormData
                // Esto garantiza que el valor correcto (0 o 1) siempre se envíe, incluso si hay quirks del navegador.
                formData.set('acepta_contacto', aceptaContactoCheckbox.checked ? '1' : '0');

                const url = form.action + '&ajax=1'; // Añadir parámetro 'ajax=1' para el servidor

                // Muestra un indicador de carga en el botón (mejora UX)
                const submitButton = form.querySelector('input[type="submit"]');
                submitButton.value = 'Cargando...';
                submitButton.disabled = true; // Deshabilita el botón para evitar múltiples envíos

                // Enviar la petición AJAX usando fetch
                fetch(url, {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json()) // Espera la respuesta en formato JSON
                .then(data => {
                    // Restaurar el botón de envío
                    submitButton.value = 'Registrarse';
                    submitButton.disabled = false;

                    // Mostrar mensaje de éxito o error de la respuesta AJAX
                    let alertClass = data.success ? 'alert-success' : 'alert-danger';
                    messageContainer.innerHTML = `<div class="alert ${alertClass} text-center">${data.message}</div>`;

                    if (data.success) {
                        form.reset(); // Limpia los campos del formulario en caso de éxito
                        form.classList.remove('was-validated'); // Opcional: Remover validación visual si el formulario se limpia
                        // AÑADIR ESTA LÓGICA DE REDIRECCIÓN AQUÍ:
                        // Redirige al usuario a la página de login después de un breve retraso
                        // para que el usuario pueda leer el mensaje de éxito.
                        setTimeout(function() {
                            window.location.href = 'index.php?action=show_login_form';
                        }, 2000); // Redirige después de 2 segundos (2000 milisegundos)
                    } else {
                        // Si el registro falla, no remover 'was-validated' para que los errores de los campos sigan visibles.
                        // Si la respuesta del servidor incluye el nombre del campo que falló (ej. data.fieldName = 'correo'),
                        // podrías añadir 'is-invalid' a ese campo específico para resaltarlo más.
                        // Ejemplo: if (data.fieldName) { document.getElementById(data.fieldName).classList.add('is-invalid'); }
                    }
                })
                .catch(error => {
                    // Manejo de errores de la red o del servidor
                    submitButton.value = 'Registrarse';
                    submitButton.disabled = false;
                    console.error('Error de red o servidor:', error);
                    messageContainer.innerHTML = `<div class="alert alert-danger text-center">Ocurrió un error al procesar tu solicitud. Por favor, inténtalo de nuevo más tarde.</div>`;
                });
            });
        });
    </script>
</body>
</html>