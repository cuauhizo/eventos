<?php
// check_time.php

// Muestra la zona horaria por defecto que PHP está usando
echo "Zona Horaria por defecto de PHP: " . date_default_timezone_get() . "<br>";

// Muestra la fecha y hora actual según la zona horaria por defecto
echo "Fecha y Hora Actual (según la zona horaria de PHP): " . date('Y-m-d H:i:s') . "<br>";

// Puedes forzar una zona horaria para ver la hora en esa zona (útil para UTC)
date_default_timezone_set('UTC');
echo "Fecha y Hora Actual (en UTC): " . date('Y-m-d H:i:s') . "<br>";

// Reinicia a la zona horaria por defecto del servidor si es necesario (opcional)
// date_default_timezone_set(date_default_timezone_get()); 
?>