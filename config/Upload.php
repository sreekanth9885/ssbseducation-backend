<?php
class Upload {
    private $uploadDir = "uploads/staff/";
    private $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif'];
    private $maxSize = 5 * 1024 * 1024; // 5MB
    
    public function __construct() {
        // Create upload directory if it doesn't exist
        if (!file_exists($this->uploadDir)) {
            mkdir($this->uploadDir, 0777, true);
        }
    }
    
    public function uploadPhoto($file, $staffId = null) {
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return ['status' => false, 'message' => 'Upload failed: ' . $this->getUploadErrorMessage($file['error'])];
        }
        
        // Check file type
        if (!in_array($file['type'], $this->allowedTypes)) {
            return ['status' => false, 'message' => 'Invalid file type. Allowed: JPG, JPEG, PNG, GIF'];
        }
        
        // Check file size
        if ($file['size'] > $this->maxSize) {
            return ['status' => false, 'message' => 'File too large. Max size: 5MB'];
        }
        
        // Generate unique filename
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = ($staffId ? $staffId . '_' : '') . time() . '_' . uniqid() . '.' . $extension;
        $filepath = $this->uploadDir . $filename;
        
        // Upload file
        if (move_uploaded_file($file['tmp_name'], $filepath)) {
            return ['status' => true, 'filename' => $filename, 'filepath' => $filepath];
        } else {
            return ['status' => false, 'message' => 'Failed to save file'];
        }
    }
    
    public function deletePhoto($filename) {
        $filepath = $this->uploadDir . $filename;
        if (file_exists($filepath)) {
            return unlink($filepath);
        }
        return false;
    }
    
    private function getUploadErrorMessage($errorCode) {
        switch ($errorCode) {
            case UPLOAD_ERR_INI_SIZE:
                return 'File exceeds server upload limit';
            case UPLOAD_ERR_FORM_SIZE:
                return 'File exceeds form upload limit';
            case UPLOAD_ERR_PARTIAL:
                return 'File was only partially uploaded';
            case UPLOAD_ERR_NO_FILE:
                return 'No file was uploaded';
            case UPLOAD_ERR_NO_TMP_DIR:
                return 'Missing temporary folder';
            case UPLOAD_ERR_CANT_WRITE:
                return 'Failed to write file to disk';
            case UPLOAD_ERR_EXTENSION:
                return 'File upload stopped by extension';
            default:
                return 'Unknown upload error';
        }
    }
}
?>