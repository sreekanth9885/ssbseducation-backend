<?php
require_once "models/Notification.php";

class NotificationController {
    private $model;

    public function __construct() {
        $this->model = new Notification();
    }

    public function index() {
        echo json_encode($this->model->getAll());
    }

    public function store() {

    $title = $_POST['title'] ?? '';
    $content = $_POST['content'] ?? '';

    if (empty($title) || empty($content)) {
        http_response_code(400);
        echo json_encode(["message" => "Title and content required"]);
        return;
    }

    $filePath = null;

    if (isset($_FILES['file']) && $_FILES['file']['error'] === 0) {

        $uploadDir = __DIR__ . "/../uploads/notifications/";

        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $fileName = time() . "_" . basename($_FILES['file']['name']);
        $targetPath = $uploadDir . $fileName;

        if (move_uploaded_file($_FILES['file']['tmp_name'], $targetPath)) {
            $filePath = "uploads/notifications/" . $fileName;
        } else {
            http_response_code(500);
            echo json_encode(["message" => "File upload failed"]);
            return;
        }
    }

    $this->model->create($title, $content, $filePath);

    echo json_encode([
        "message" => "Notification created",
        "file" => $filePath
    ]);
    }

    public function update($id) {
        $data = json_decode(file_get_contents("php://input"), true);
        $this->model->update($id, $data['title'], $data['content']);
        echo json_encode(["message" => "Updated"]);
    }

    public function delete($id) {
        $this->model->delete($id);
        echo json_encode(["message" => "Deleted"]);
    }
}