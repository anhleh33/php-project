<?php
require_once '../../model/ResultModel.php';
require_once '../../model/ExamModel.php';

class ResultControllerAPI
{
    private $resultModel;
    private $examModel;

    public function __construct()
    {
        $this->resultModel = new ResultModel();
        $this->examModel = new ExamModel();
    }

    public function submitResult()
    {
        header('Content-Type: application/json');

        $data = json_decode(file_get_contents('php://input'), true);

        if (
            !$data ||
            !isset($data['exam_id'], $data['student_id'], $data['answers'])
        ) {
            echo json_encode([
                "success" => false,
                "message" => "Missing required fields: exam_id, student_id, answers"
            ]);
            return;
        }

        $exam = $this->examModel->getExamById($data['exam_id']);
        if (!$exam) {
            echo "Không tìm thấy kỳ thi.\n";
            exit;
        }

        $resultId = $this->resultModel->submitResultAndScore($exam['data'], $data['student_id'], $data['answers']);

        echo json_encode([
            "success" => true,
            "message" => "Result submitted successfully",
            "result_id" => (string) $resultId
        ]);
    }
}

// 🔁 Run this if accessed via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $api = new ResultControllerAPI();
    $api->submitResult();
}