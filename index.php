<?php
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