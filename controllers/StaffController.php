<?php
require_once "models/Staff.php";

class StaffController {

    public function index() {
        $model = new Staff();
        $data = $model->getAll();

        echo json_encode($data);
    }

    public function store() {
        $data = json_decode(file_get_contents("php://input"), true);

        $model = new Staff();
        $result = $model->create($data);

        echo json_encode([
            "status" => $result
        ]);
    }

    public function delete() {
        $id = $_GET['id'];

        $model = new Staff();
        $result = $model->delete($id);

        echo json_encode([
            "status" => $result
        ]);
    }
}