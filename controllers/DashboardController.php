<?php
require_once "config/Database.php";

class DashboardController {

    public function index() {
        $db = (new Database())->connect();

        $courses = $db->query("SELECT COUNT(*) as total FROM subjects")->fetch_assoc();
        $students = $db->query("SELECT COUNT(*) as total FROM students")->fetch_assoc();

        echo json_encode([
            "courses" => (int)$courses['total'],
            "students" => (int)$students['total']
        ]);
    }
}