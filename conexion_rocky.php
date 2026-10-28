
<?php
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$config = require __DIR__ . "/config.local.php";

try {
    $conexion_remota = new mysqli(
        $config["servidor"],
        $config["usuario"],
        $config["contrasena"],
        $config["base_datos"],
        $config["puerto"]
    );

    $conexion_remota->set_charset("utf8mb4");
} catch (mysqli_sql_exception $error) {
    error_log($error->getMessage());
    http_response_code(500);
    exit("No se pudo conectar con MariaDB en Rocky Linux.");
}
?>
