<?php
require_once "conBD.php";
session_start();

header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Credentials: true");
header("Content-Type: application/json");

$conexion = conBD();

if (!isset($_SESSION["id_usuario"])) {
    echo json_encode([
        "success" => false,
        "mensaje" => "No autenticado"
    ]);
    exit;
}

$id_usuario = $_SESSION["id_usuario"];

$sql = "
    SELECT a.id_anime, a.titulo, a.imagen
    FROM usuario_anime ua
    JOIN animes a ON ua.id_anime = a.id_anime
    WHERE ua.id_usuario = ? AND ua.estado = 'favorito'
";

$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $id_usuario);
$stmt->execute();

$result = $stmt->get_result();

$favoritos = [];

while ($row = $result->fetch_assoc()) {
    $favoritos[] = $row;
}

echo json_encode([
    "success" => true,
    "favoritos" => $favoritos
]);