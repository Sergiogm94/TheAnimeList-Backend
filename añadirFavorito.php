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

//Verificamos la sesión.
if (!isset($_SESSION["id_usuario"])) {
    echo json_encode([
        "success" => false,
        "mensaje" => "No autenticado"
    ]);
    exit;
}

$id_usuario = $_SESSION["id_usuario"];

//Datos que se reciben desde react.
$data = json_decode(file_get_contents("php://input"), true);

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

    // 1. Comprobar si el anime ya existe
    $stmt = $conexion->prepare("SELECT id_anime FROM animes WHERE titulo = ?");
    $stmt->bind_param("s", $titulo);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        $id_anime = $row["id_anime"];
    } else {
    // 2. Insertar anime
        $stmt = $conexion->prepare("
            INSERT INTO animes (titulo, tipo, sipnosis, imagen)
            VALUES (?, ?, ?, ?)
        ");

        $stmt->bind_param("ssss", $titulo, $tipo, $sipnosis, $imagen);
        $stmt->execute();

        $id_anime = $stmt->insert_id;
    }

    // 3. Comprobar si ya es favorito
    $stmt = $conexion->prepare("
        SELECT * FROM usuario_anime 
        WHERE id_usuario = ? AND id_anime = ? AND estado = 'favorito'
    ");
    $stmt->bind_param("ii", $id_usuario, $id_anime);
    $stmt->execute();

    if ($stmt->get_result()->num_rows > 0) {
        echo json_encode([
            "success" => false,
            "mensaje" => "Ya está en favoritos"
        ]);
        exit;
    }

    // 4. Contar los favoritos actuales
    $stmt = $conexion->prepare("
        SELECT COUNT(*) as total 
        FROM usuario_anime 
        WHERE id_usuario = ? AND estado = 'favorito'
    ");
    $stmt->bind_param("i", $id_usuario);
    $stmt->execute();

    $total = $stmt->get_result()->fetch_assoc()["total"];

    if ($total >= 5) {
        echo json_encode([
            "success" => false,
            "mensaje" => "Máximo 5 favoritos"
        ]);
        exit;
    }

    // 5. Insertar el favorito.
    $stmt = $conexion->prepare("
        INSERT INTO usuario_anime (id_usuario, id_anime, estado)
        VALUES (?, ?, 'favorito')
    ");

    $stmt->bind_param("ii", $id_usuario, $id_anime);
    $stmt->execute();

    echo json_encode([
        "success" => true,
        "mensaje" => "Añadido a favoritos"
    ]);

} catch (Exception $e) {
    echo json_encode([
        "success" => false,
        "mensaje" => "Error",
        "error" => $e->getMessage()
    ]);
}