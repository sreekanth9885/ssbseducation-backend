<?php
require_once "config/Database.php";

class Notification {
    private $conn;
    private $table = "notifications";

    public function __construct() {
        $database = new Database();
        $this->conn = $database->connect();
    }

    public function getAll() {
    $query = "SELECT * FROM " . $this->table . " ORDER BY created_at DESC";
    $stmt = $this->conn->prepare($query);

    /** @var PDOStatement $stmt */
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

    public function create($title, $content) {
        $query = "INSERT INTO " . $this->table . " (title, content) VALUES (:title, :content)";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([
            ":title" => $title,
            ":content" => $content
        ]);
    }

    public function update($id, $title, $content) {
        $query = "UPDATE " . $this->table . " 
                  SET title = :title, content = :content 
                  WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([
            ":id" => $id,
            ":title" => $title,
            ":content" => $content
        ]);
    }

    public function delete($id) {
        $query = "DELETE FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([":id" => $id]);
    }
}