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
// Get all staff or single staff member
if ($uri === "/staff" && $method === "GET") {
    (new StaffController())->index();
    exit;
}

// Get single staff member by ID
if (preg_match("/^\/staff\/(\d+)$/", $uri, $matches) && $method === "GET") {
    $id = $matches[1];
    (new StaffController())->show($id);
    exit;
}
// Alternative: Update staff member using POST with _method=PUT (for FormData)
if ($uri === "/staff" && $method === "POST" && isset($_POST['_method']) && $_POST['_method'] === 'PUT') {
    (new StaffController())->update();
    exit;
}
// Create staff member (supports multipart/form-data for photo upload)
if ($uri === "/staff" && $method === "POST") {
    (new StaffController())->store();
    exit;
}

// Update staff member (supports multipart/form-data for photo upload)
if ($uri === "/staff" && $method === "PUT") {
    (new StaffController())->update();
    exit;
}



// Delete staff member
if ($uri === "/staff" && $method === "DELETE") {
    (new StaffController())->delete();
    exit;
}

// Delete staff member by ID in URL (alternative)
if (preg_match("/^\/staff\/(\d+)$/", $uri, $matches) && $method === "DELETE") {
    $_GET['id'] = $matches[1];
    (new StaffController())->delete();
    exit;
}

// Staff photo upload endpoint (dedicated)
if ($uri === "/staff/upload-photo" && $method === "POST") {
    (new StaffController())->uploadPhoto();
    exit;
}

// Staff photo deletion endpoint
if ($uri === "/staff/delete-photo" && $method === "POST") {
    (new StaffController())->deletePhoto();
    exit;
}

// Staff photo deletion by ID
if (preg_match("/^\/staff\/(\d+)\/photo$/", $uri, $matches) && $method === "DELETE") {
    $_GET['id'] = $matches[1];
    (new StaffController())->deletePhoto();
    exit;
}

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
    "uri" => $uri,
    "method" => $method
]);