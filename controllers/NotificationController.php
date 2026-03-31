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
        $data = json_decode(file_get_contents("php://input"), true);

        if (!isset($data['title']) || !isset($data['content'])) {
            http_response_code(400);
            echo json_encode(["message" => "Invalid data"]);
            return;
        }

        $this->model->create($data['title'], $data['content']);
        echo json_encode(["message" => "Notification created"]);
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