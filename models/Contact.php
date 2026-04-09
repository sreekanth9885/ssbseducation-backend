<?php
require_once __DIR__ . '/../config/Database.php';

class Contact {
    private $conn;
    private $table = "contact_messages";

    public function __construct() {
        $database = new Database();
        $this->conn = $database->connect();
    }

    // ✅ CREATE
    public function create($data) {
        $query = "INSERT INTO " . $this->table . " 
                  (name, email, phone, message) 
                  VALUES (?, ?, ?, ?)";

        $stmt = $this->conn->prepare($query);

        if (!$stmt) return false;

        $name = $data['name'];
        $email = $data['email'] ?? '';
        $phone = $data['phone'] ?? '';
        $message = $data['message'];

        $stmt->bind_param("ssss", $name, $email, $phone, $message);

        return $stmt->execute();
    }

    // ✅ GET ALL
    public function getAll() {
        $query = "SELECT * FROM " . $this->table . " ORDER BY id DESC";
        $result = $this->conn->query($query);

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // ✅ DELETE (INSIDE CLASS — IMPORTANT)
    public function delete($id) {
        $query = "DELETE FROM " . $this->table . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);

        if (!$stmt) return false;

        $stmt->bind_param("i", $id);

        return $stmt->execute();
    }

    public function getPaginated($limit, $offset) {
    $query = "SELECT * FROM " . $this->table . " 
              ORDER BY id DESC 
              LIMIT ? OFFSET ?";

    $stmt = $this->conn->prepare($query);
    $stmt->bind_param("ii", $limit, $offset);
    $stmt->execute();

    $result = $stmt->get_result();

    return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getTotalCount() {
    $query = "SELECT COUNT(*) as total FROM " . $this->table;
    $result = $this->conn->query($query);
    return $result->fetch_assoc()['total'];
    }

}