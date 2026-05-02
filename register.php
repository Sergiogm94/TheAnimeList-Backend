<?php
require_once "conBD.php";
// Cors básico para evitar errores al conectar con React.
header("Access-Control-Allow-Origin: https://the-anime-list-umber.vercel.app");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

$conexion = conBD();

// Variable para poder leer los datos JSON enviados con axios desde React.
$data = json_decode(file_get_contents("php://input"), true);

// Validar que los datos enviados esten bien y existan.
if(
    !isset($data["usuario"]) ||
    !isset($data["email"]) ||
    !isset($data["contraseña"])
) {
    echo json_encode([
        "success" => false,
        "mensaje" => "No se han enviado todos los datos."
    ]);
    exit;
}

// Para obtener los datos y guarddarlos en variables.
$usuario = $data["usuario"];
$email = $data["email"];
$contraseña = $data["contraseña"];

// Validar que los campos enviados no esten vacios.
if (empty($usuario) || empty($email) || empty($contraseña)) {
    echo json_encode([
        "success" => false,
        "mensaje" => "No se han rellenado todos los campos."
    ]);
    exit;
}

// Hashear contraseña para aumentar la seguridad y que no aparezca en la base de datos de forma visible.
$hash = password_hash($contraseña, PASSWORD_DEFAULT);

// Inserts a base de datos.
/*$sql = "INSERT INTO usuarios (nombre_usuario, email, contraseña_hash)
        VALUES ('$usuario', '$email', '$hash')";*/
try {
// Inserts con seguridad para evitar el hackeo con sql injections.
    $stmt = $conexion -> prepare("INSERT INTO usuarios (nombre_usuario, email, contraseña_hash) VALUES (?, ?, ?)");

    if (!$stmt) {
        throw new Exception($conexion -> error);
    }

    // Ser vinculan los parámetros que se pasan en el insert como ?
    $stmt -> bind_param("sss", $usuario, $email, $hash);
    $stmt -> execute();
    
    echo json_encode([
        "success" => true,
        "mensaje" => "El usuario ha sido registrado."
    ]);

    $stmt -> close();
    
} catch (mysqli_sql_exception $exc) {
    // Evitar duplicados en la base de datos.

        if ($exc ->getCode() == 1062) {
        echo json_encode([
            "success" => false,
            "mensaje" => "Este nombre de usuario o email ya estan registrados"
        ]); 
}
    else {
        echo json_encode([
            "success" => false,
            "mensaje" => "Error registrando el usuario",
            "error" => $exc ->getMessage()
        ]);
    }
    } catch (Exception $exc) {

    echo json_encode([
        "success" => false,
        "mensaje" => "Error general",
        "error" => $exc->getMessage()
    ]);
}

?>