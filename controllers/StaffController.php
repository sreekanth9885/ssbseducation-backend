<?php
require_once "config/Database.php";
require_once "config/Upload.php";

class StaffController {

    private $conn;
    private $upload;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->connect();
        $this->upload = new Upload();
    }

    // ✅ GET ALL STAFF
    public function index() {
        $result = $this->conn->query("SELECT * FROM staff ORDER BY id DESC");

        $staff = [];

        while ($row = $result->fetch_assoc()) {
            if ($row['photo']) {
                $row['photo_url'] = 'uploads/staff/' . $row['photo'];
            } else {
                $row['photo_url'] = null;
            }
            $staff[] = $row;
        }

        echo json_encode($staff);
    }

    // ✅ GET SINGLE STAFF
    public function show($id)
    {
        $stmt = $this->conn->prepare("SELECT * FROM staff WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            if ($row['photo']) {
                $row['photo_url'] = 'uploads/staff/' . $row['photo'];
            }
            echo json_encode($row);
        } else {
            echo json_encode(["status" => false, "message" => "Staff not found"]);
        }
    }

    // ✅ CREATE STAFF WITH PHOTO
    public function store() {
        // Check if this is a multipart form data request (with file upload)
        $isMultipart = isset($_FILES) && count($_FILES) > 0;

        if (!$isMultipart && $_SERVER['CONTENT_TYPE'] === 'application/json') {
            // Handle JSON request (without file)
            $data = json_decode(file_get_contents("php://input"), true);
            $photo = null;
        } else {
            // Handle form data request (with file)
            $data = $_POST;
            $photo = $_FILES['photo'] ?? null;
        }

        // Validate required fields
        if (empty($data['name'])) {
            echo json_encode(["status" => false, "message" => "Name is required"]);
            return;
        }

        $name = $data['name'];
        $email = $data['email'] ?? null;
        $phone = $data['phone'] ?? null;
        $designation = $data['designation'] ?? null;
        $department = $data['department'] ?? null;

        $photoFilename = null;

        // Upload photo if provided
        if ($photo && $photo['error'] === UPLOAD_ERR_OK) {
            $uploadResult = $this->upload->uploadPhoto($photo);
            if ($uploadResult['status']) {
                $photoFilename = $uploadResult['filename'];
            } else {
                echo json_encode(["status" => false, "message" => $uploadResult['message']]);
                return;
            }
        }

        $stmt = $this->conn->prepare("
            INSERT INTO staff (name, email, phone, designation, department, photo)
            VALUES (?, ?, ?, ?, ?, ?)
        ");

        $stmt->bind_param("ssssss", $name, $email, $phone, $designation, $department, $photoFilename);

        if ($stmt->execute()) {
            $id = $this->conn->insert_id;
            echo json_encode([
                "status" => true,
                "message" => "Faculty added successfully",
                "id" => $id,
                "photo" => $photoFilename ? 'uploads/staff/' . $photoFilename : null
            ]);
        } else {
            echo json_encode(["status" => false, "message" => "Insert failed: " . $stmt->error]);
        }
    }

    // ✅ UPDATE STAFF WITH PHOTO
    public function update()
    {
        // Check if this is a multipart form data request (with file upload)
        $isMultipart = isset($_FILES) && count($_FILES) > 0;

        if (!$isMultipart && ($_SERVER['CONTENT_TYPE'] === 'application/json' || empty($_POST))) {
            // Handle JSON request (without file)
            $data = json_decode(file_get_contents("php://input"), true);
            $photo = null;
        } else {
            // Handle form data request (with file)
            $data = $_POST;
            $photo = $_FILES['photo'] ?? null;
        }

        if (!$data || !isset($data['id'])) {
            echo json_encode(["status" => false, "message" => "No data received or ID missing"]);
            return;
        }

        $id = $data['id'];
        $name = $data['name'];
        $email = $data['email'] ?? null;
        $phone = $data['phone'] ?? null;
        $designation = $data['designation'] ?? null;
        $department = $data['department'] ?? null;

        // Get current photo
        $stmt = $this->conn->prepare("SELECT photo FROM staff WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $currentStaff = $result->fetch_assoc();

        if (!$currentStaff) {
            echo json_encode(["status" => false, "message" => "Staff not found"]);
            return;
        }

        $photoFilename = $currentStaff['photo'] ?? null;

        // Upload new photo if provided
        if ($photo && $photo['error'] === UPLOAD_ERR_OK) {
            // Delete old photo
            if ($photoFilename) {
                $this->upload->deletePhoto($photoFilename);
            }

            // Upload new photo
            $uploadResult = $this->upload->uploadPhoto($photo, $id);
            if ($uploadResult['status']) {
                $photoFilename = $uploadResult['filename'];
            } else {
                echo json_encode(["status" => false, "message" => $uploadResult['message']]);
                return;
            }
        }

        $stmt = $this->conn->prepare("
            UPDATE staff 
            SET name=?, email=?, phone=?, designation=?, department=?, photo=? 
            WHERE id=?
        ");

        $stmt->bind_param("ssssssi", $name, $email, $phone, $designation, $department, $photoFilename, $id);

        if ($stmt->execute()) {
            echo json_encode([
                "status" => true,
                "message" => "Faculty updated successfully",
                "photo" => $photoFilename ? 'uploads/staff/' . $photoFilename : null
            ]);
        } else {
            echo json_encode(["status" => false, "message" => "Update failed: " . $stmt->error]);
        }
    }

    // ✅ DELETE STAFF WITH PHOTO
    public function delete() {
        $id = $_GET['id'] ?? null;

        if (!$id) {
            echo json_encode(["status" => false, "message" => "ID missing"]);
            return;
        }

        // Get photo filename before deletion
        $stmt = $this->conn->prepare("SELECT photo FROM staff WHERE id=?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $staff = $result->fetch_assoc();

        $stmt = $this->conn->prepare("DELETE FROM staff WHERE id=?");
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            // Delete photo file
            if ($staff && $staff['photo']) {
                $this->upload->deletePhoto($staff['photo']);
            }
            echo json_encode(["status" => true, "message" => "Faculty deleted successfully"]);
        } else {
            echo json_encode(["status" => false, "message" => "Delete failed: " . $stmt->error]);
        }
    }

    // ✅ DEDICATED PHOTO UPLOAD ENDPOINT
    public function uploadPhoto()
    {
        $id = $_POST['id'] ?? $_GET['id'] ?? null;

        if (!$id) {
            echo json_encode(["status" => false, "message" => "Staff ID is required"]);
            return;
        }

        if (!isset($_FILES['photo']) || $_FILES['photo']['error'] !== UPLOAD_ERR_OK) {
            echo json_encode(["status" => false, "message" => "No valid photo uploaded"]);
            return;
        }

        // Get current staff
        $stmt = $this->conn->prepare("SELECT photo FROM staff WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $staff = $result->fetch_assoc();

        if (!$staff) {
            echo json_encode(["status" => false, "message" => "Staff not found"]);
            return;
        }

        // Delete old photo if exists
        if ($staff['photo']) {
            $this->upload->deletePhoto($staff['photo']);
        }

        // Upload new photo
        $uploadResult = $this->upload->uploadPhoto($_FILES['photo'], $id);

        if (!$uploadResult['status']) {
            echo json_encode(["status" => false, "message" => $uploadResult['message']]);
            return;
        }

        // Update database
        $stmt = $this->conn->prepare("UPDATE staff SET photo = ? WHERE id = ?");
        $stmt->bind_param("si", $uploadResult['filename'], $id);

        if ($stmt->execute()) {
            echo json_encode([
                "status" => true,
                "message" => "Photo uploaded successfully",
                "photo" => 'uploads/staff/' . $uploadResult['filename'],
                "filename" => $uploadResult['filename']
            ]);
        } else {
            echo json_encode(["status" => false, "message" => "Failed to update database"]);
        }
    }

    // ✅ DELETE PHOTO ONLY (without deleting staff)
    public function deletePhoto()
    {
        $id = $_GET['id'] ?? $_POST['id'] ?? null;

        if (!$id) {
            echo json_encode(["status" => false, "message" => "Staff ID is required"]);
            return;
        }

        // Get current photo
        $stmt = $this->conn->prepare("SELECT photo FROM staff WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $staff = $result->fetch_assoc();

        if (!$staff) {
            echo json_encode(["status" => false, "message" => "Staff not found"]);
            return;
        }

        if (!$staff['photo']) {
            echo json_encode(["status" => false, "message" => "No photo to delete"]);
            return;
        }

        // Delete file
        $deleted = $this->upload->deletePhoto($staff['photo']);

        // Update database
        $stmt = $this->conn->prepare("UPDATE staff SET photo = NULL WHERE id = ?");
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            echo json_encode([
                "status" => true,
                "message" => "Photo deleted successfully",
                "file_deleted" => $deleted
            ]);
        } else {
            echo json_encode(["status" => false, "message" => "Failed to update database"]);
        }
    }
}
?>