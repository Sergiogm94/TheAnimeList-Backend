<?php

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Credentials: true");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

$page = isset($_GET['page']) ? $_GET['page'] : 1;
// Obtener el parámetro para realizar la busqueda, en este caso el nombre del anime.
$query = isset($_GET['q']) ? $_GET['q'] : '';

//Si hay búsqueda se usa.
if (!empty($query)) {
    $url = "https://api.jikan.moe/v4/anime?q=" . urlencode($query) . "&page=" . $page;
} else {
    //Si NO hay búsqueda trae la lista por defecto.
    $url = "https://api.jikan.moe/v4/anime?page=" . $page;
}

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