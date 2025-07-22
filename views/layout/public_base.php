<?php
// views/layout/public_base.php
// Esta será la plantilla base para las páginas públicas.
// Incluirá Bootstrap, tu CSS/JS global y renderizará el contenido específico de cada vista.
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle ?? 'Sistema de Reservas'; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../public/css/global.css" rel="stylesheet"> 
    
    <?php if (isset($pageSpecificCss)): ?>
        <style><?php echo $pageSpecificCss; ?></style>
    <?php endif; ?>
</head>
<body>
    <header>
        <nav class="navbar navbar-expand-lg navbar-light bg-light">
            <div class="container-fluid">
                <a class="navbar-brand" href="../public/index.php?action=home">
                    Sistema de Eventos
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                        <?php if (isset($_SESSION['loggedin'])): ?>
                            <li class="nav-item">
                                <span class="nav-link text-primary">Bienvenido, <strong><?php echo htmlspecialchars($_SESSION['nombre'] ?? ''); ?></strong>!</span>
                            </li>
                            <li class="nav-item">
                                <a class="btn btn-sm btn-outline-primary me-2" href="../public/index.php?action=mis_reservas">Mis Reservas</a>
                            </li>
                            <li class="nav-item">
                                <a class="btn btn-sm btn-danger" href="../public/index.php?action=logout">Cerrar Sesión</a>
                            </li>
                        <?php else: ?>
                            <li class="nav-item">
                                <a class="nav-link" href="../public/index.php?action=show_login_form">Iniciar Sesión</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="../public/index.php?action=show_register_form">Registrarse</a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    <main>
        <?php echo $content ?? ''; ?>
    </main>

    <footer>
        <div class="container text-center mt-5 py-3">
            <p>&copy; <?php echo date('Y'); ?> Sistema de Eventos. Todos los derechos reservados.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../public/js/global.js"></script>

    <?php if (isset($pageSpecificJs)): ?>
        <script><?php echo $pageSpecificJs; ?></script>
    <?php endif; ?>
</body>
</html>