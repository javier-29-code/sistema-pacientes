<?php
session_start();
date_default_timezone_set("America/Guayaquil");

function e($valor) {
    return htmlspecialchars((string)$valor, ENT_QUOTES, "UTF-8");
}

function tiposSangre() {
    return ["Desconocido", "A+", "A-", "B+", "B-", "AB+", "AB-", "O+", "O-"];
}

function camposPaciente() {
    return [
        "cedula", "nombres", "apellidos", "fecha_nacimiento",
        "telefono", "direccion", "tipo_sangre", "alergias",
        "motivo_ingreso"
    ];
}

function leerPaciente() {
    $datos = [];

    foreach (camposPaciente() as $campo) {
        $valor = $_POST[$campo] ?? "";
        $datos[$campo] = is_string($valor) ? trim($valor) : "";
    }

    return $datos;
}

function validarPaciente($datos) {
    $errores = [];

    foreach ([
        "cedula", "nombres", "apellidos",
        "fecha_nacimiento", "motivo_ingreso"
    ] as $campo) {
        if ($datos[$campo] === "") {
            $errores[] = "Completa todos los campos obligatorios.";
            break;
        }
    }

    if (!preg_match('/^[0-9]{10}$/', $datos["cedula"])) {
        $errores[] = "La cédula debe contener 10 dígitos.";
    }

    $fecha = DateTimeImmutable::createFromFormat(
        "!Y-m-d",
        $datos["fecha_nacimiento"]
    );

    if (
        !$fecha ||
        $fecha->format("Y-m-d") !== $datos["fecha_nacimiento"] ||
        $fecha > new DateTimeImmutable("today")
    ) {
        $errores[] = "La fecha de nacimiento no es válida.";
    }

    if (!in_array($datos["tipo_sangre"], tiposSangre(), true)) {
        $errores[] = "Selecciona un tipo de sangre válido.";
    }

    foreach ([
        "nombres" => 100,
        "apellidos" => 100,
        "telefono" => 20,
        "direccion" => 200,
        "alergias" => 255,
        "motivo_ingreso" => 255
    ] as $campo => $limite) {
        if (mb_strlen($datos[$campo], "UTF-8") > $limite) {
            $errores[] = "El campo $campo supera el límite permitido.";
        }
    }

    return $errores;
}

if (empty($_SESSION["csrf"])) {
    $_SESSION["csrf"] = bin2hex(random_bytes(32));
}

function verificarToken() {
    $token = $_POST["csrf"] ?? "";

    if (
        !is_string($token) ||
        !hash_equals($_SESSION["csrf"], $token)
    ) {
        http_response_code(403);
        exit("Solicitud no válida. Recarga la página.");
    }
}