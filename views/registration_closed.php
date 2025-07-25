<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro Finalizado</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link as="font" crossorigin="crossorigin" href="https://www.nike.com/static/ncss/5.0/dotcom/fonts/Nike-Futura.woff2" rel="preload" type="font/woff2">
    <link rel="stylesheet" href="../public/css/styles.css"> 
    <style>
        /* Estilos específicos de esta página, sobreescribiendo si es necesario */
        body { 
            /* Se asume que estos estilos ya están en ../public/css/styles.css y por lo tanto, no se repetirían aquí */
            /* body { background: #fc3000; background-image: url('../img/back.png'); background-attachment: fixed; background-size: cover; background-position: center; background-repeat: repeat-y; } */
            
            display: flex; 
            justify-content: center; 
            align-items: center; 
            min-height: 100vh; 
            margin: 0; 
            font-family: sans-serif; /* Puedes ajustar la fuente para que coincida con Nike Futura si está cargada */
        }
        .message-container { 
            max-width: 600px; 
            padding: 40px; 
            text-align: center; 
            /* Adaptando colores del diseño */
            background-color: #000; /* Fondo negro como tus .register-container, etc. */
            color: #FFF; /* Color de texto base blanco */
            border-radius: 8px; 
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        .message-container h1 { 
            /* Adaptando colores del diseño */
            color: #CEFF00; /* Verde brillante para el título principal */
            margin-bottom: 20px; 
            font-weight: bold; /* Hacer el título más impactante */
        }
        .message-container p { 
            /* Adaptando colores del diseño */
            font-size: 1.1em; 
            color: #eeeeee; /* Un gris muy claro para los párrafos, como tu text-muted */
            margin-bottom: 10px;
        }
        .message-container .btn-primary { /* Estilo para el botón */
            /* Se asume que .btn-primary ya tiene tus colores definidos en styles.css */
            margin-top: 20px;
            background-color: #CEFF00;
            border-color: #CEFF00;
            color: #333;
            font-weight: bold;
        }
        .message-container .btn-primary:hover {
            background-color: #ccff00c8;
            border-color: #ccff00c8;
        }
    </style>
</head>
<body>
    <div class="message-container">
        <h1>¡Registro Finalizado!</h1>
        <p>Agradecemos sinceramente tu interés en participar en nuestro evento.</p>
        <p>El periodo de registro ha concluido.</p>
        <p>Te esperamos en el evento. ¡Prepárate para vivir una experiencia inolvidable!</p>
        <div class="mt-4">
            <a href="index.php" class="btn btn-primary">Volver al Inicio</a>
        </div>
    </div>
</body>
</html>