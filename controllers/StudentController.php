<?php
require_once "config/Database.php";

class StudentController {

    public function index() {
        $db = (new Database())->connect();

        $res = $db->query("SELECT * FROM students ORDER BY id DESC");

        $students = [];
        while ($row = $res->fetch_assoc()) {
            $students[] = $row;
        }

        echo json_encode($students);
    }

    public function store() {
        $db = (new Database())->connect();
        $data = json_decode(file_get_contents("php://input"), true);

        if (empty($data['name']) || empty($data['email'])) {
            echo json_encode(["status" => false, "message" => "All fields required"]);
            return;
        }

        $stmt = $db->prepare("INSERT INTO students(name, email) VALUES(?, ?)");
        $stmt->bind_param("ss", $data['name'], $data['email']);
        $stmt->execute();

        echo json_encode(["status" => true]);
    }

    public function delete() {
        $db = (new Database())->connect();

        $id = $_GET['id'] ?? null;

        if (!$id) {
            echo json_encode(["status" => false]);
            return;
        }

        $stmt = $db->prepare("DELETE FROM students WHERE id=?");
        $stmt->bind_param("i", $id);
        $stmt->execute();

        echo json_encode(["status" => true]);
    }
}