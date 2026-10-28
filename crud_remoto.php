<?php
require_once __DIR__ . "/funciones.php";
require_once __DIR__ . "/conexion_rocky.php";

$datos = [];
$errores = [];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    verificarToken();
    $accion = $_POST["accion"] ?? "";

    try {
        if ($accion === "agregar") {
            $datos = leerPaciente();
            $errores = validarPaciente($datos);

            if (!$errores) {
                $stmt = $conexion_remota->prepare(
                    "INSERT INTO pacientes
                    (cedula, nombres, apellidos, fecha_nacimiento,
                     telefono, direccion, tipo_sangre, alergias, motivo_ingreso)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)"
                );

                $stmt->bind_param(
                    "sssssssss",
                    $datos["cedula"],
                    $datos["nombres"],
                    $datos["apellidos"],
                    $datos["fecha_nacimiento"],
                    $datos["telefono"],
                    $datos["direccion"],
                    $datos["tipo_sangre"],
                    $datos["alergias"],
                    $datos["motivo_ingreso"]
                );

                $stmt->execute();
                $stmt->close();

                $_SESSION["mensaje"] = "Paciente registrado correctamente.";
                header("Location: crud_remoto.php");
                exit;
            }
        } elseif ($accion === "eliminar") {
            $id = filter_var(
                $_POST["id"] ?? "",
                FILTER_VALIDATE_INT,
                ["options" => ["min_range" => 1]]
            );

            if (!$id) {
                $errores[] = "ID no válido.";
            } else {
                $stmt = $conexion_remota->prepare(
                    "DELETE FROM pacientes WHERE id = ?"
                );
                $stmt->bind_param("i", $id);
                $stmt->execute();

                $_SESSION["mensaje"] = $stmt->affected_rows > 0
                    ? "Paciente eliminado."
                    : "El paciente ya no existe.";

                $stmt->close();
                header("Location: crud_remoto.php");
                exit;
            }
        } else {
            $errores[] = "Acción no válida.";
        }
    } catch (mysqli_sql_exception $error) {
        error_log($error->getMessage());
        $errores[] = $error->getCode() === 1062
            ? "Ya existe un paciente con esa cédula."
            : "No se pudo completar la operación.";
    }
}

$mensaje = $_SESSION["mensaje"] ?? "";
unset($_SESSION["mensaje"]);

$resultado = $conexion_remota->query(
    "SELECT *,
     TIMESTAMPDIFF(YEAR, fecha_nacimiento, CURDATE()) AS edad
     FROM pacientes ORDER BY id DESC"
);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Ingreso de pacientes</title>
    <link rel="stylesheet" href="estilos.css">
</head>
<body>
    <h1>Sistema de ingreso de pacientes</h1>

    <?php if ($mensaje): ?>
        <p class="mensaje"><?= e($mensaje) ?></p>
    <?php endif; ?>

    <?php foreach ($errores as $error): ?>
        <p class="error"><?= e($error) ?></p>
    <?php endforeach; ?>

    <section class="panel">
        <h2>Registrar paciente</h2>
        <form method="post">
            <input type="hidden" name="accion" value="agregar">
            <?php require __DIR__ . "/formulario_paciente.php"; ?>
        </form>
    </section>

    <section class="panel">
        <h2>Pacientes registrados</h2>
        <div class="tabla">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Cédula</th>
                        <th>Paciente</th>
                        <th>Edad</th>
                        <th>Teléfono</th>
                        <th>Sangre</th>
                        <th>Dirección</th>
                        <th>Alergias</th>
                        <th>Motivo de ingreso</th>
                        <th>Fecha de ingreso</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($fila = $resultado->fetch_assoc()): ?>
                        <tr>
                            <td><?= e($fila["id"]) ?></td>
                            <td><?= e($fila["cedula"]) ?></td>
                            <td><?= e($fila["nombres"] . " " . $fila["apellidos"]) ?></td>
                            <td><?= e($fila["edad"]) ?></td>
                            <td><?= e($fila["telefono"]) ?></td>
                            <td><?= e($fila["tipo_sangre"]) ?></td>
                            <td><?= e($fila["direccion"]) ?></td>
                            <td><?= e($fila["alergias"]) ?></td>
                            <td><?= e($fila["motivo_ingreso"]) ?></td>
                            <td><?= e($fila["fecha_ingreso"]) ?></td>
                            <td>
                                <a class="boton"
                                   href="editar_remoto.php?id=<?= (int)$fila["id"] ?>">
                                    Editar
                                </a>

                                <form method="post"
                                      onsubmit="return confirm('¿Eliminar este paciente?');">
                                    <input type="hidden" name="csrf"
                                           value="<?= e($_SESSION["csrf"]) ?>">
                                    <input type="hidden" name="accion" value="eliminar">
                                    <input type="hidden" name="id"
                                           value="<?= (int)$fila["id"] ?>">
                                    <button class="eliminar" type="submit">
                                        Eliminar
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>

                    <?php if ($resultado->num_rows === 0): ?>
                        <tr>
                            <td colspan="11">No hay pacientes registrados.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>
</body>
</html>
<?php
$resultado->free();
$conexion_remota->close();
?>