<?php
require_once '../../model/ExamModel.php';
require_once '../ExamController.php';

class ExamControllerAPI
{
    private $examModel;
    private $examController;

    public function __construct()
    {
        $this->examModel = new ExamModel();
        $this->examController = new ExamController();
    }

    public function createExamFromRequest()
    {
        header('Content-Type: application/json');

        $data = json_decode(file_get_contents('php://input'), true);

        if (!$data) {
            echo json_encode(["success" => false, "message" => "Invalid JSON input"]);
            return;
        }

        session_start();
        if (!isset($_SESSION['id'])) {
            echo json_encode(["success" => false, "message" => "Chưa đăng nhập"]);
            return;
        }

        $data['teacher_id'] = $_SESSION['id'];

        $result = $this->examController->createExam($data);
        echo json_encode($result);
    }
}

// 🧠 Execute if directly accessed via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $api = new ExamControllerAPI();
    $api->createExamFromRequest();
}