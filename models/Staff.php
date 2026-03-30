<?php
require_once "config/Database.php";

class Staff {
    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->connect();
    }

    public function getAll() {
        $result = $this->conn->query("SELECT * FROM staff ORDER BY id DESC");

        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }

        return $data;
    }

    public function create($data) {
        $stmt = $this->conn->prepare(
            "INSERT INTO staff (name, email, phone, designation, department)
             VALUES (?, ?, ?, ?, ?)"
        );

        $stmt->bind_param(
            "sssss",
            $data['name'],
            $data['email'],
            $data['phone'],
            $data['designation'],
            $data['department']
        );

        return $stmt->execute();
    }

    public function delete($id) {
        $stmt = $this->conn->prepare("DELETE FROM staff WHERE id=?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}