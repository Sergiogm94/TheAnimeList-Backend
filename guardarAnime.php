<?php
require_once "conBD.php";

header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

$conexion = conBD();

// Recibimos los datos desde react.
$data = json_decode(file_get_contents("php://input"), true);

// Validación básica
if (
    !isset($data["id_anime_api"]) ||
    !isset($data["titulo"]) ||
    !isset($data["imagen"])
) {
    echo json_encode([
        "success" => false,
        "mensaje" => "Faltan datos del anime"
    ]);
    exit;
}

$id_api = $data["id_anime_api"];
$titulo = $data["titulo"];
$imagen = $data["imagen"];
$tipo = $data["tipo"] ?? null;
$sipnosis = $data["sipnosis"] ?? null;

try {

    // 1. Verificar si ya existe por id_api.
    $stmt = $conexion->prepare("SELECT id_anime FROM animes WHERE titulo = ?");
    $stmt->bind_param("s", $titulo);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        //Si ya existe
        echo json_encode([
            "success" => true,
            "id_anime" => $row["id_anime"],
            "mensaje" => "Anime ya existente"
        ]);
        exit;
    }

    // 2. Insertar nuevo anime evitando el sql injection.
    $stmt = $conexion->prepare("
        INSERT INTO animes (titulo, tipo, sipnosis, imagen)
        VALUES (?, ?, ?, ?)
    ");

    $stmt->bind_param("ssss", $titulo, $tipo, $sipnosis, $imagen);
    $stmt->execute();

    $id_anime = $stmt->insert_id;

    echo json_encode([
        "success" => true,
        "id_anime" => $id_anime,
        "mensaje" => "Anime guardado correctamente"
    ]);

    $stmt->close();

} catch (Exception $e) {
    echo json_encode([
        "success" => false,
        "mensaje" => "Error",
        "error" => $e->getMessage()
    ]);
}