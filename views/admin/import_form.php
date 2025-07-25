<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Importar Eventos</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        :root {
            --primary-color: #4e73df;
            --success-color: #1cc88a;
            --warning-color: #f6c23e;
            --danger-color: #e74a3b;
        }
        
        body {
            background-color: #f8f9fc;
            font-family: 'Nunito', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
        }
        
        .import-container {
            background-color: #fff;
            border-radius: 15px;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.1);
            padding: 2.5rem;
            width: 100%;
            max-width: 700px;
            margin: 2rem auto;
        }
        
        .import-header {
            text-align: center;
            margin-bottom: 2rem;
            border-bottom: 1px solid #e3e6f0;
            padding-bottom: 1rem;
        }
        
        .import-header h1 {
            font-size: 1.75rem;
            color: #5a5c69;
        }
        
        .file-upload {
            border: 2px dashed #d1d3e2;
            border-radius: 0.5rem;
            padding: 2rem;
            text-align: center;
            transition: all 0.3s;
            margin-bottom: 1.5rem;
        }
        
        .file-upload:hover {
            border-color: var(--primary-color);
            background-color: rgba(78, 115, 223, 0.05);
        }
        
        .file-upload-icon {
            font-size: 3rem;
            color: var(--primary-color);
            margin-bottom: 1rem;
        }
        
        .file-upload-label {
            font-weight: 600;
            color: #5a5c69;
            cursor: pointer;
            display: block;
        }
        
        .file-upload-input {
            display: none;
        }
        
        .file-info {
            margin-top: 1rem;
            font-size: 0.85rem;
            color: #858796;
        }
        
        .requirements-list {
            background-color: #f8f9fa;
            border-radius: 0.5rem;
            padding: 1.5rem;
            margin-top: 2rem;
        }
        
        .requirements-list h5 {
            color: var(--primary-color);
            margin-bottom: 1rem;
        }
        
        .requirements-list ul {
            padding-left: 1.5rem;
            margin-bottom: 0;
        }
        
        .requirements-list li {
            margin-bottom: 0.5rem;
        }
        
        .btn-import {
            padding: 0.75rem;
            font-weight: 600;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }
        
        .btn-back {
            color: var(--danger-color);
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s;
        }
        
        .btn-back:hover {
            color: #be2617;
            text-decoration: underline;
        }
        
        @media (max-width: 768px) {
            .import-container {
                padding: 1.5rem;
                margin: 1rem auto;
            }
        }
    </style>
</head>
<body>
    <div class="container py-4">
        <div class="import-container">
            <div class="import-header">
                <h1>
                    <i class="bi bi-upload me-2"></i>
                    Importar Eventos
                </h1>
                <p class="text-muted mb-0">Sube un archivo CSV o Excel con los datos de los eventos</p>
            </div>

            <?php if (isset($_SESSION['import_mensaje'])): ?>
                <div class="alert alert-<?php echo ($_SESSION['import_exito']) ? 'success' : 'danger'; ?> alert-dismissible fade show" role="alert">
                    <i class="bi <?php echo ($_SESSION['import_exito']) ? 'bi-check-circle-fill' : 'bi-exclamation-triangle-fill'; ?> me-2"></i>
                    <?php echo $_SESSION['import_mensaje']; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php 
                unset($_SESSION['import_mensaje']);
                unset($_SESSION['import_exito']);
                ?>
            <?php endif; ?>

            <form action="../public/index.php?action=admin_import_events" method="POST" enctype="multipart/form-data" id="importForm">
                <div class="file-upload" id="fileUploadArea">
                    <i class="bi bi-cloud-arrow-up file-upload-icon"></i>
                    <label for="eventos_file" class="file-upload-label">
                        Selecciona o arrastra tu archivo aquí
                    </label>
                    <input type="file" class="file-upload-input" id="eventos_file" name="eventos_file" accept=".csv, .xlsx, .xls" required>
                    <div class="file-info" id="fileName">Formatos aceptados: .csv, .xlsx</div>
                </div>

                <div class="d-grid gap-2 mb-3">
                    <button type="submit" class="btn btn-primary btn-import" id="submitBtn" disabled>
                        <i class="bi bi-upload me-2"></i>Importar Eventos
                    </button>
                </div>
                
                <div class="text-center">
                    <a href="../public/index.php?action=admin_dashboard" class="btn-back">
                        <i class="bi bi-arrow-left me-1"></i>Volver al Dashboard
                    </a>
                </div>
            </form>

            <div class="requirements-list">
                <h5><i class="bi bi-info-circle me-2"></i>Requisitos del archivo</h5>
                <ul>
                    <li>El archivo debe tener las columnas: Nombre, Categoría, Código, Descripción, Fecha, Hora Inicio, Hora Fin, Ubicación, Cupo Máximo</li>
                    <li>Formato de fecha: YYYY-MM-DD (ej. 2023-12-31)</li>
                    <li>Formato de hora: HH:MM (ej. 14:30)</li>
                    <li>La primera fila debe contener los encabezados</li>
                    <li>Tamaño máximo: 5MB</li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const fileInput = document.getElementById('eventos_file');
            const fileUploadArea = document.getElementById('fileUploadArea');
            const fileNameDisplay = document.getElementById('fileName');
            const submitBtn = document.getElementById('submitBtn');
            
            // Manejar la selección de archivos
            fileInput.addEventListener('change', function(e) {
                if (this.files.length > 0) {
                    const file = this.files[0];
                    fileNameDisplay.textContent = `Archivo seleccionado: ${file.name} (${(file.size / 1024 / 1024).toFixed(2)} MB)`;
                    fileUploadArea.style.borderColor = '#1cc88a';
                    fileUploadArea.style.backgroundColor = 'rgba(28, 200, 138, 0.05)';
                    submitBtn.disabled = false;
                }
            });
            
            // Manejar drag and drop
            fileUploadArea.addEventListener('dragover', (e) => {
                e.preventDefault();
                fileUploadArea.style.borderColor = '#4e73df';
                fileUploadArea.style.backgroundColor = 'rgba(78, 115, 223, 0.1)';
            });
            
            fileUploadArea.addEventListener('dragleave', () => {
                fileUploadArea.style.borderColor = '#d1d3e2';
                fileUploadArea.style.backgroundColor = '';
            });
            
            fileUploadArea.addEventListener('drop', (e) => {
                e.preventDefault();
                fileInput.files = e.dataTransfer.files;
                const event = new Event('change');
                fileInput.dispatchEvent(event);
            });
            
            // Validar tamaño máximo del archivo (5MB)
            document.getElementById('importForm').addEventListener('submit', function(e) {
                if (fileInput.files.length > 0 && fileInput.files[0].size > 5 * 1024 * 1024) {
                    e.preventDefault();
                    alert('El archivo no puede ser mayor a 5MB');
                    fileInput.value = '';
                    fileNameDisplay.textContent = 'Formatos aceptados: .csv, .xlsx';
                    submitBtn.disabled = true;
                    fileUploadArea.style.borderColor = '#d1d3e2';
                    fileUploadArea.style.backgroundColor = '';
                }
            });
        });
    </script>
</body>
</html>