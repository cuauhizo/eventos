<?php
// Contenido del boleto PDF
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Boleto de Acceso</title>
</head>
<body style="font-family: Arial, sans-serif; margin: 0; padding: 0;">
<table style="width: 100%; border-collapse: collapse; border: 2px solid #000; box-sizing: border-box;">
    <tr>
        <td style="width: 25%; border-right: 2px dashed #000; padding: 10px; text-align: center; vertical-align: top;">
            <table style="width: 100%; height: 100%; border-collapse: collapse;">
                <tr>
                    <td style="vertical-align: middle; text-align: center;">
                        <img src="data:image/png;base64,<?php echo $qr_data_base64; ?>" alt="QR" style="width: 80px; height: 80px; margin-top: 20px;">
                    </td>
                </tr>
                <tr>
                    <td style="font-size: 8px; text-transform: uppercase; font-weight: bold; text-align: center;">
                        <p style="margin: 0;"><?php echo htmlspecialchars($qr_content); ?></p>
                        <p style="margin: 5px 0;"><?php echo htmlspecialchars($nombre_evento); ?></p>
                        <p style="margin: 0;"><?php echo htmlspecialchars($fecha_evento); ?><br><?php echo htmlspecialchars($hora_inicio); ?></p>
                    </td>
                </tr>
            </table>
        </td>
        <td style="width: 75%; padding: 20px; vertical-align: top; position: relative;">
            <table style="width: 100%; border-collapse: collapse;">
                <tr>
                    <td style="text-align: right; font-size: 14px; font-weight: bold; color: #cc0032;">tolkogroup.com</td>
                </tr>
                <tr>
                    <td style="font-size: 18px; font-weight: bold; margin-bottom: 5px; text-transform: uppercase;"><?php echo htmlspecialchars($nombre_usuario); ?></td>
                </tr>
                <tr>
                    <td style="font-size: 28px; font-weight: bold; color: #cc0032;"><?php echo htmlspecialchars($nombre_evento); ?></td>
                </tr>
                <tr>
                    <td>
                        <table style="width: 100%; border-collapse: collapse; margin-top: 15px;">
                            <tr>
                                <td style="width: 50%; font-size: 10px; color: #666; text-transform: uppercase;">
                                    Fecha
                                </td>
                                <td style="width: 50%; font-size: 10px; color: #666; text-transform: uppercase;">
                                    Hora
                                </td>
                            </tr>
                            <tr>
                                <td style="width: 50%; font-size: 14px; font-weight: bold; color: #333; padding-right: 10px;"><?php echo htmlspecialchars($fecha_evento); ?></td>
                                <td style="width: 50%; font-size: 14px; font-weight: bold; color: #333;"><?php echo htmlspecialchars($hora_inicio); ?></td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td style="font-size: 10px; color: #666; padding-top: 10px;">
                        168 Avenida Río San Joaquín, 11529, Ciudad de México
                    </td>
                </tr>
                <tr>
                    <td style="font-size: 14px; font-weight: bold; margin-top: 10px;"><?php echo htmlspecialchars($qr_content); ?></td>
                </tr>
            </table>
            <div style="position: absolute; bottom: 20px; right: 20px;">
                <img src="data:image/png;base64,<?php echo $qr_data_base64; ?>" alt="QR" style="width: 120px; height: 120px;">
            </div>
        </td>
    </tr>
</table>
</body>
</html>