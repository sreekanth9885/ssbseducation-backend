<?php
require_once "config/Database.php";

class Admin {
    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->connect();
    }

    public function findByEmail($email) {
        $stmt = $this->conn->prepare("SELECT * FROM admins WHERE email=?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function updateToken($id, $token, $expiry) {
        $stmt = $this->conn->prepare(
            "UPDATE admins SET auth_token=?, token_expires=? WHERE id=?"
        );
        $stmt->bind_param("ssi", $token, $expiry, $id);
        return $stmt->execute();
    }

    public function findByToken($token) {
        $stmt = $this->conn->prepare(
            "SELECT * FROM admins WHERE auth_token=? AND token_expires > NOW()"
        );
        $stmt->bind_param("s", $token);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
}