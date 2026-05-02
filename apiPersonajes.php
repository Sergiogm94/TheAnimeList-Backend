<?php

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: https://the-anime-list-umber.vercel.app");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

// Código para evitar errores de Cors.
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

$page = isset($_GET['page']) ? $_GET['page'] : 1;

$url = "https://api.jikan.moe/v4/characters?page=" . $page;

// se crea una variable curl que sirve para hacer peticiones HTTP a Api u otros servidores.
$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);

if (curl_errno($ch)) {
    echo json_encode([
        "success" => false,
        "mensaje" => "Error al conectar con la API",
        "error" => curl_error($ch)
    ]);
    curl_close($ch);
    exit;
}

curl_close($ch);

echo $response;