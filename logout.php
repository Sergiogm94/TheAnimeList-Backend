<?php
session_start();

//Cabeceras para evitar Cors.
header("Access-Control-Allow-Origin: https://the-anime-list-umber.vercel.app");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Credentials: true");
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Destruir la sesión actual.
$_SESSION = [];
session_unset();
session_destroy();

echo json_encode([
    "success" => true,
    "mensaje" => "Sesión cerrada correctamente"
]);
?>