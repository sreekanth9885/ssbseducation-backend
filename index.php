<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Credentials: true");

// Handle preflight request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}
header("Content-Type: application/json");

require_once "config/Database.php";
require_once "controllers/AuthController.php";

$uri = $_SERVER['REQUEST_URI'];
$method = $_SERVER['REQUEST_METHOD'];

$controller = new AuthController();

if ($uri === "/api/login" && $method === "POST") {
    $controller->login();
} elseif ($uri === "/api/me" && $method === "GET") {
    $controller->me();
} else {
    echo json_encode(["status" => false, "message" => "Route not found"]);
}