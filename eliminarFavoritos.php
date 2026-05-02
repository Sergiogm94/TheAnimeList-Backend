<?php
require_once "conBD.php";
session_start();

header("Access-Control-Allow-Origin: https://the-anime-list-umber.vercel.app");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Credentials: true");
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

$conexion = conBD();

if (!isset($_SESSION["id_usuario"])) {
    echo json_encode([
        "success" => false,
        "mensaje" => "No autenticado"
    ]);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data["id_anime"])) {
    echo json_encode([
        "success" => false,
        "mensaje" => "Falta id_anime"
    ]);
    exit;
}

$id_usuario = $_SESSION["id_usuario"];
$id_anime = $data["id_anime"];

try {
    $stmt = $conexion->prepare("
        DELETE FROM usuario_anime
        WHERE id_usuario = ? AND id_anime = ?
    ");

    $stmt->bind_param("ii", $id_usuario, $id_anime);
    $stmt->execute();

    echo json_encode([
        "success" => true,
        "mensaje" => "Eliminado de favoritos"
    ]);

    $stmt->close();

} catch (Exception $e) {
    echo json_encode([
        "success" => false,
        "mensaje" => "Error",
        "error" => $e->getMessage()
    ]);
}