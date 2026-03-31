<?php
require_once "config/Database.php";

class StaffController {

    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->connect();
    }

    // ✅ GET ALL STAFF
    public function index() {
        $result = $this->conn->query("SELECT * FROM staff ORDER BY id DESC");

        $staff = [];

        while ($row = $result->fetch_assoc()) {
            $staff[] = $row;
        }

        echo json_encode($staff);
    }

    // ✅ CREATE STAFF
    public function store() {
        $data = json_decode(file_get_contents("php://input"), true);

        $name = $data['name'];
        $email = $data['email'] ?? null;
        $phone = $data['phone'] ?? null;
        $designation = $data['designation'] ?? null;
        $department = $data['department'] ?? null;

        $stmt = $this->conn->prepare("
            INSERT INTO staff (name, email, phone, designation, department)
            VALUES (?, ?, ?, ?, ?)
        ");

        $stmt->bind_param("sssss", $name, $email, $phone, $designation, $department);

        if ($stmt->execute()) {
            echo json_encode(["status" => true, "message" => "Faculty added"]);
        } else {
            echo json_encode(["status" => false, "message" => "Insert failed"]);
        }
    }

    // ✅ UPDATE STAFF
   public function update() {

    // 🔥 FIX: Read raw input for PUT
    $data = json_decode(file_get_contents("php://input"), true);

    if (!$data) {
        echo json_encode(["status" => false, "message" => "No data received"]);
        return;
    }

    $id = $data['id'];
    $name = $data['name'];
    $email = $data['email'] ?? null;
    $phone = $data['phone'] ?? null;
    $designation = $data['designation'] ?? null;
    $department = $data['department'] ?? null;

    $stmt = $this->conn->prepare("
        UPDATE staff 
        SET name=?, email=?, phone=?, designation=?, department=? 
        WHERE id=?
    ");

    $stmt->bind_param("sssssi", $name, $email, $phone, $designation, $department, $id);

    if ($stmt->execute()) {
        echo json_encode(["status" => true, "message" => "Updated"]);
    } else {
        echo json_encode(["status" => false, "message" => "Update failed"]);
    }
}

    // ✅ DELETE STAFF
    public function delete() {
        $id = $_GET['id'] ?? null;

        if (!$id) {
            echo json_encode(["status" => false, "message" => "ID missing"]);
            return;
        }

        $stmt = $this->conn->prepare("DELETE FROM staff WHERE id=?");
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            echo json_encode(["status" => true, "message" => "Faculty deleted"]);
        } else {
            echo json_encode(["status" => false, "message" => "Delete failed"]);
        }
    }
}