<?php
require_once __DIR__ . '/../models/Contact.php';

class ContactController {

    // ✅ SAVE MESSAGE
    public function save() {
        $data = json_decode(file_get_contents("php://input"), true);

        if (!$data || !isset($data['name']) || !isset($data['message'])) {
            echo json_encode([
                "status" => false,
                "message" => "Invalid data"
            ]);
            return;
        }

        $contact = new Contact();
        $success = $contact->create($data);

        if ($success) {
            echo json_encode([
                "status" => true,
                "message" => "Message saved successfully"
            ]);
        } else {
            echo json_encode([
                "status" => false,
                "message" => "Database error"
            ]);
        }
    }

    public function delete($id) {
    $contact = new Contact();
    $success = $contact->delete($id);

    echo json_encode([
        "status" => $success,
        "message" => $success ? "Deleted successfully" : "Delete failed"
    ]);
}

    // ✅ GET ALL MESSAGES
    public function index() {
        $contact = new Contact();
        $data = $contact->getAll();

        echo json_encode([
            "status" => true,
            "data" => $data
        ]);
    }

   public function paginate() {
    $page = $_GET['page'] ?? 1;
    $limit = 5;

    $offset = ($page - 1) * $limit;

    $contact = new Contact();

    $data = $contact->getPaginated($limit, $offset);
    $total = $contact->getTotalCount();

    echo json_encode([
        "status" => true,
        "data" => $data,
        "total" => $total,
        "page" => (int)$page,
        "limit" => $limit
    ]);
    }

}