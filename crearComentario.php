<?php
require_once "conBD.php";

header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Credentials: true");
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

session_start();

$conexion = conBD();

// Verificar la sesión.
if (!isset($_SESSION["id_usuario"])) {
    echo json_encode([
        "success" => false,
        "mensaje" => "No autenticado"
    ]);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);


if (!isset($data["contenido"]) || empty($data["contenido"])) {
    echo json_encode([
        "success" => false,
        "mensaje" => "Contenido vacío"
    ]);
    exit;
}

$id_usuario = $_SESSION["id_usuario"];
$contenido = $data["contenido"];

try {
    $stmt = $conexion->prepare("
        INSERT INTO comentarios (id_usuario, contenido)
        VALUES (?, ?)
    ");

    $stmt->bind_param("is", $id_usuario, $contenido);
    $stmt->execute();

    echo json_encode([
        "success" => true,
        "mensaje" => "Comentario creado"
    ]);

    $stmt->close();

} catch (Exception $e) {
    echo json_encode([
        "success" => false,
        "mensaje" => "Error",
        "error" => $e->getMessage()
    ]);
}