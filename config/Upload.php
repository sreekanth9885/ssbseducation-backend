<?php
class Upload {
    private $uploadDir;
    private $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif'];
    private $maxSize = 5 * 1024 * 1024;

    public function __construct() {
        $this->uploadDir = __DIR__ . "/../uploads/staff/";

        if (!file_exists($this->uploadDir)) {
            mkdir($this->uploadDir, 0777, true);
        }
    }

    public function uploadPhoto($file, $staffId = null) {
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return ['status' => false, 'message' => 'Upload failed'];
        }

        if (!in_array($file['type'], $this->allowedTypes)) {
            return ['status' => false, 'message' => 'Invalid file type'];
        }

        if ($file['size'] > $this->maxSize) {
            return ['status' => false, 'message' => 'File too large'];
        }

        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = ($staffId ? $staffId . '_' : '') . time() . '_' . uniqid() . '.' . $extension;
        $filepath = $this->uploadDir . $filename;

        if (move_uploaded_file($file['tmp_name'], $filepath)) {
            return ['status' => true, 'filename' => $filename];
        }

        return ['status' => false, 'message' => 'Failed to save file'];
    }

    public function deletePhoto($filename) {
        $filepath = $this->uploadDir . $filename;

        if (file_exists($filepath)) {
            return @unlink($filepath);
        }

        return false;
    }
}
?>