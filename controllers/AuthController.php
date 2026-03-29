<?php
require_once "models/Admin.php";

class AuthController {

    public function login() {
        $data = json_decode(file_get_contents("php://input"), true);

        $email = $data['email'];
        $password = $data['password'];

        $adminModel = new Admin();
        $admin = $adminModel->findByEmail($email);

        if (!$admin) {
            echo json_encode(["status" => false, "message" => "Invalid email"]);
            return;
        }

        // ⚠️ If using plain password (not recommended)
        if ($admin['password'] !== $password) {
            echo json_encode(["status" => false, "message" => "Invalid password"]);
            return;
        }

        // Generate token
        $token = bin2hex(random_bytes(32));
        $expiry = date("Y-m-d H:i:s", strtotime("+1 day"));

        $adminModel->updateToken($admin['id'], $token, $expiry);

        echo json_encode([
            "status" => true,
            "message" => "Login successful",
            "token" => $token
        ]);
    }

    public function me() {
        $headers = getallheaders();
        $token = $headers['Authorization'] ?? "";

        if (!$token) {
            echo json_encode(["status" => false, "message" => "No token"]);
            return;
        }

        $adminModel = new Admin();
        $admin = $adminModel->findByToken($token);

        if (!$admin) {
            echo json_encode(["status" => false, "message" => "Invalid token"]);
            return;
        }

        echo json_encode([
            "status" => true,
            "data" => $admin
        ]);
    }
}