<?php
require_once __DIR__ . "/controllers/NotificationController.php";
require_once __DIR__ . "/controllers/StaffController.php";
require_once __DIR__ . "/controllers/StudentController.php";
require_once __DIR__ . "/controllers/CourseController.php";
require_once "controllers/AuthController.php";
require_once "controllers/DashboardController.php";

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
    (new DashboardController())->index();
    exit;
}

// ================= COURSES =================
if ($uri === "/courses" && $method === "GET") {
    (new CourseController())->index();
    exit;
}

if ($uri === "/courses" && $method === "POST") {
    (new CourseController())->store();
    exit;
}

if ($uri === "/courses" && $method === "DELETE") {
    (new CourseController())->delete();
    exit;
}

// ================= STUDENTS =================
if ($uri === "/students" && $method === "GET") {
    (new StudentController())->index();
    exit;
}

if ($uri === "/students" && $method === "POST") {
    (new StudentController())->store();
    exit;
}

if ($uri === "/students" && $method === "DELETE") {
    (new StudentController())->delete();
    exit;
}
// ================= STAFF =================
if ($uri === "/staff" && $method === "GET") {
    (new StaffController())->index();
    exit;
}

if ($uri === "/staff" && $method === "POST") {
    (new StaffController())->store();
    exit;
}

if ($uri === "/staff" && $method === "PUT") {
    (new StaffController())->update();
    exit;
}    

if ($uri === "/staff" && $method === "DELETE") {
    (new StaffController())->delete();
    exit;
}

// ================= NOTIFICATIONS =================

// ================= NOTIFICATIONS =================

if ($uri === "/notifications" && $method === "GET") {
    (new NotificationController())->index();
    exit;
}

if ($uri === "/notifications" && $method === "POST") {
    (new NotificationController())->store();
    exit;
}

if (preg_match("/^\/notifications\/(\d+)$/", $uri, $matches)) {
    $id = $matches[1];

    if ($method === "PUT") {
        (new NotificationController())->update($id);
        exit;
    }

    if ($method === "DELETE") {
        (new NotificationController())->delete($id);
        exit;
    }
}
// ================= FALLBACK =================
echo json_encode([
    "status" => false,
    "message" => "Route not found",
    "uri" => $uri
]);