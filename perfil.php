<?php
require_once "conBD.php";

session_start();

header("Access-Control-Allow-Origin: https://the-anime-list-umber.vercel.app");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Credentials: true");
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Ahora se comprueba si la sesion existe y se ha creado al loguearse el usuario.
if(isset($_SESSION["usuario"])) {
    echo json_encode([
        "logged" => true,
        "usuario" => $_SESSION["usuario"],
        "email" => $_SESSION["email"]

    ]);
} else {
    echo json_encode([
        "logged" => false,
        "mensaje" => "La sesión no esta activa."
    ]);
}
?>