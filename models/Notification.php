<?php
require_once "config/Database.php";

class Notification {
    private $conn;
    private $table = "notifications";

    public function __construct() {
        $database = new Database();
        $this->conn = $database->connect();
    }

    // ✅ GET ALL
    public function getAll() {
        $query = "SELECT * FROM " . $this->table . " ORDER BY created_at DESC";
        $result = $this->conn->query($query);

        if (!$result) {
            die("SQL Error: " . $this->conn->error);
        }

        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }

        return $data;
    }

    // ✅ CREATE
    public function create($title, $content) {
        $stmt = $this->conn->prepare(
            "INSERT INTO " . $this->table . " (title, content) VALUES (?, ?)"
        );

        if (!$stmt) {
            die("Prepare failed: " . $this->conn->error);
        }

        $stmt->bind_param("ss", $title, $content);

        if (!$stmt->execute()) {
            die("Execute failed: " . $stmt->error);
        }

        return true;
    }

    // ✅ UPDATE
    public function update($id, $title, $content) {
        $stmt = $this->conn->prepare(
            "UPDATE " . $this->table . " SET title = ?, content = ? WHERE id = ?"
        );

        if (!$stmt) {
            die("Prepare failed: " . $this->conn->error);
        }

        $stmt->bind_param("ssi", $title, $content, $id);

        if (!$stmt->execute()) {
            die("Execute failed: " . $stmt->error);
        }

        return true;
    }

    // ✅ DELETE
    public function delete($id) {
        $stmt = $this->conn->prepare(
            "DELETE FROM " . $this->table . " WHERE id = ?"
        );

        if (!$stmt) {
            die("Prepare failed: " . $this->conn->error);
        }

        $stmt->bind_param("i", $id);

        if (!$stmt->execute()) {
            die("Execute failed: " . $stmt->error);
        }

        return true;
    }
}