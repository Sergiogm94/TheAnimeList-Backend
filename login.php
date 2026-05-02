<?php
require_once "conBD.php";

session_start();

// Cors para que funciona al conectar con React.
header("Access-Control-Allow-Origin: https://the-anime-list-umber.vercel.app");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Credentials: true");
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

$conexion = conBD();

// Para leer los datos Json que se envian desde react.
$data = json_decode(file_get_contents("php://input"), true);

// Validar los datos que se introducen.
if (
    !isset($data["usuario"]) ||
    !isset($data["contraseña"])
) {
    echo json_encode([
        "success" => false,
        "mensaje" => "Faltan datos por introducir."
    ]);
    exit;
}

$usuario = $data["usuario"];
$contraseña = $data["contraseña"];

if (empty($usuario) || empty($contraseña)) {
    echo json_encode([
        "success" => false,
        "mensaje" => "Campos vacíos."
    ]);
    exit;
}

// Busqueda básica en la base de datos.
/* $sql = "SELECT * FROM usuarios
        WHERE nombre_usuario = '$usuario',
        OR email = '$usuario'";

$resultado = $conexion ->query($sql); */

try {
    // SELECT seguro usando prepared statements para evitar hackeos con sql injection.
    $stmt = $conexion->prepare(
        "SELECT * FROM usuarios WHERE nombre_usuario = ? OR email = ?"
    );

    if (!$stmt) {
        throw new Exception($conexion->error);
    }

    $stmt->bind_param("ss", $usuario, $usuario);
    $stmt->execute();

    $resultado = $stmt->get_result();

    if ($resultado->num_rows === 1) {

        $fila = $resultado->fetch_assoc();

        //Verificamos la contraseña.
        if (password_verify($contraseña, $fila["contraseña_hash"])) {

            // Guardamos la sesion en variables globales de Session.
            $_SESSION["id_usuario"] = $fila["id_usuario"];
            $_SESSION["usuario"] = $fila["nombre_usuario"];
            $_SESSION["email"] = $fila["email"];

            echo json_encode([
                "success" => true,
                "mensaje" => "Login correcto",
                "id_usuario" => $fila["id_usuario"],
                "usuario" => $fila["nombre_usuario"],
                "email" => $fila["email"]
            ]);

        } else {
            echo json_encode([
                "success" => false,
                "mensaje" => "Contraseña incorrecta"
            ]);
        }

    } else {
        echo json_encode([
            "success" => false,
            "mensaje" => "Usuario no encontrado"
        ]);
    }

    $stmt->close();

} catch (Exception $e) {

    echo json_encode([
        "success" => false,
        "mensaje" => "Error en el servidor",
        "error" => $e->getMessage()
    ]);
}

$conexion->close();
?>