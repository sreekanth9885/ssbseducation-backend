<?php
require_once "config/Database.php";

class CourseController {

    public function index() {
        $db = (new Database())->connect();

        $res = $db->query("SELECT * FROM subjects ORDER BY id DESC");

        $courses = [];

        while ($row = $res->fetch_assoc()) {
            $courses[] = $row;
        }

        // ✅ Always return array
        echo json_encode($courses);
    }

    public function store() {
        $db = (new Database())->connect();
        $data = json_decode(file_get_contents("php://input"), true);

        if (!isset($data['name']) || empty($data['name'])) {
            echo json_encode(["status" => false, "message" => "Name required"]);
            return;
        }

        $stmt = $db->prepare("INSERT INTO subjects(name) VALUES(?)");
        $stmt->bind_param("s", $data['name']);
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

        $stmt = $db->prepare("DELETE FROM subjects WHERE id=?");
        $stmt->bind_param("i", $id);
        $stmt->execute();

        echo json_encode(["status" => true]);
    }
}