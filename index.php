<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Credentials: true");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

header("Content-Type: application/json");

// 🔥 Parse clean URI
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

// remove /api prefix if present
$uri = str_replace("/api", "", $uri);

// ================= AUTH =================
require_once "controllers/AuthController.php";
$authController = new AuthController();

if ($uri === "/login" && $method === "POST") {
    $authController->login();
    exit;
}

if ($uri === "/me" && $method === "GET") {
    $authController->me();
    exit;
}

// ================= DASHBOARD =================
if ($uri === "/dashboard" && $method === "GET") {
    require_once "controllers/DashboardController.php";
    (new DashboardController())->index();
    exit;
}

// ================= COURSES =================
if ($uri === "/courses" && $method === "GET") {
    require_once "controllers/CourseController.php";
    (new CourseController())->index();
    exit;
}

if ($uri === "/courses" && $method === "POST") {
    require_once "controllers/CourseController.php";
    (new CourseController())->store();
    exit;
}

if ($uri === "/courses" && $method === "DELETE") {
    require_once "controllers/CourseController.php";
    (new CourseController())->delete();
    exit;
}

// ================= FALLBACK =================
echo json_encode([
    "status" => false,
    "message" => "Route not found",
    "uri" => $uri
]);