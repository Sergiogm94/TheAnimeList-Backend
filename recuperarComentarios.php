<?php
require_once "conBD.php";

header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Credentials: true");
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

$conexion = conBD();

try {
    $sql = "
        SELECT c.id_comentario, c.contenido, c.fecha, u.nombre_usuario
        FROM comentarios c
        JOIN usuarios u ON c.id_usuario = u.id_usuario
        ORDER BY c.fecha DESC
    ";

    $result = $conexion->query($sql);

    $comentarios = [];

    while ($row = $result->fetch_assoc()) {
        $comentarios[] = $row;
    }

    echo json_encode([
        "success" => true,
        "comentarios" => $comentarios
    ]);

} catch (Exception $e) {
    echo json_encode([
        "success" => false,
        "mensaje" => "Error",
        "error" => $e->getMessage()
    ]);
}