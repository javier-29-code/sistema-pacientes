<?php
require_once __DIR__ . "/funciones.php";
require_once __DIR__ . "/conexion_rocky.php";

$id = filter_var(
    $_GET["id"] ?? "",
    FILTER_VALIDATE_INT,
    ["options" => ["min_range" => 1]]
);

if (!$id) {
    http_response_code(400);
    exit("ID de paciente no válido.");
}

$stmt = $conexion_remota->prepare(
    "SELECT * FROM pacientes WHERE id = ?"
);
$stmt->bind_param("i", $id);
$stmt->execute();
$datos = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$datos) {
    http_response_code(404);
    exit("El paciente no existe.");
}

$errores = [];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    verificarToken();
    $datos = leerPaciente();
    $errores = validarPaciente($datos);

    if (!$errores) {
        try {
            $stmt = $conexion_remota->prepare(
                "UPDATE pacientes SET
                 cedula = ?, nombres = ?, apellidos = ?,
                 fecha_nacimiento = ?, telefono = ?, direccion = ?,
                 tipo_sangre = ?, alergias = ?, motivo_ingreso = ?
                 WHERE id = ?"
            );

            $stmt->bind_param(
                "sssssssssi",
                $datos["cedula"],
                $datos["nombres"],
                $datos["apellidos"],
                $datos["fecha_nacimiento"],
                $datos["telefono"],
                $datos["direccion"],
                $datos["tipo_sangre"],
                $datos["alergias"],
                $datos["motivo_ingreso"],
                $id
            );

            $stmt->execute();
            $stmt->close();

            $_SESSION["mensaje"] = "Paciente actualizado correctamente.";
            header("Location: crud_remoto.php");
            exit;
        } catch (mysqli_sql_exception $error) {
            error_log($error->getMessage());
            $errores[] = $error->getCode() === 1062
                ? "Esa cédula pertenece a otro paciente."
                : "No se pudieron guardar los cambios.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Editar paciente</title>
    <link rel="stylesheet" href="estilos.css">
</head>
<body>
    <h1>Editar paciente</h1>

    <?php foreach ($errores as $error): ?>
        <p class="error"><?= e($error) ?></p>
    <?php endforeach; ?>

    <section class="panel">
        <form method="post">
            <?php require __DIR__ . "/formulario_paciente.php"; ?>
            <a class="boton" href="crud_remoto.php">Cancelar</a>
        </form>
    </section>
</body>
</html>
<?php $conexion_remota->close(); ?>