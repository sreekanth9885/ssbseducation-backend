<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once "config/Database.php";
require_once "config/Upload.php";

class Staff {
    private $conn;
    private $upload;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->connect();
        $this->upload = new Upload();
    }

    public function getAll() {
        $result = $this->conn->query("SELECT * FROM staff ORDER BY id DESC");

        $data = [];
        while ($row = $result->fetch_assoc()) {
            // Add full photo URL
            if ($row['photo']) {
                $row['photo_url'] = 'uploads/staff/' . $row['photo'];
            } else {
                $row['photo_url'] = null;
            }
            $data[] = $row;
        }

        return $data;
    }

    public function getById($id)
    {
        $stmt = $this->conn->prepare("SELECT * FROM staff WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            if ($row['photo']) {
                $row['photo_url'] = 'uploads/staff/' . $row['photo'];
            }
            return $row;
        }
        return null;
    }

    public function create($data, $photo = null)
    {
        $photoFilename = null;

        // Upload photo if provided
        if ($photo && $photo['error'] === UPLOAD_ERR_OK) {
            $uploadResult = $this->upload->uploadPhoto($photo);
            if ($uploadResult['status']) {
                $photoFilename = $uploadResult['filename'];
            } else {
                return ['status' => false, 'message' => $uploadResult['message']];
            }
        }

        $stmt = $this->conn->prepare(
            "INSERT INTO staff (name, email, phone, designation, department, photo)
             VALUES (?, ?, ?, ?, ?, ?)"
        );

        $stmt->bind_param(
            "ssssss",
            $data['name'],
            $data['email'],
            $data['phone'],
            $data['designation'],
            $data['department'],
            $photoFilename
        );

        if ($stmt->execute()) {
            return ['status' => true, 'message' => 'Staff added successfully', 'id' => $this->conn->insert_id];
        } else {
            return ['status' => false, 'message' => 'Insert failed: ' . $stmt->error];
        }
    }

    public function update($id, $data, $photo = null)
    {
        // Get current staff data
        $currentStaff = $this->getById($id);
        if (!$currentStaff) {
            return ['status' => false, 'message' => 'Staff not found'];
        }

        $photoFilename = $currentStaff['photo'];

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
                return ['status' => false, 'message' => $uploadResult['message']];
            }
        }

        $stmt = $this->conn->prepare("
            UPDATE staff 
            SET name=?, email=?, phone=?, designation=?, department=?, photo=?
            WHERE id=?
        ");

        $stmt->bind_param(
            "ssssssi",
            $data['name'],
            $data['email'],
            $data['phone'],
            $data['designation'],
            $data['department'],
            $photoFilename,
            $id
        );

        if ($stmt->execute()) {
            return ['status' => true, 'message' => 'Staff updated successfully'];
        } else {
            return ['status' => false, 'message' => 'Update failed: ' . $stmt->error];
        }
    }

    public function delete($id) {
        // Get staff photo before deletion
        $staff = $this->getById($id);

        $stmt = $this->conn->prepare("DELETE FROM staff WHERE id=?");
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            // Delete photo file
            if ($staff && $staff['photo']) {
                $this->upload->deletePhoto($staff['photo']);
            }
            return ['status' => true, 'message' => 'Staff deleted successfully'];
        } else {
            return ['status' => false, 'message' => 'Delete failed: ' . $stmt->error];
        }
    }
}
?>