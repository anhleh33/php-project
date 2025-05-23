<?php
require_once __DIR__ . '/../model/ExamModel.php';
require_once __DIR__ . '/../model/ResultModel.php';
require_once __DIR__ . '/../model/UserModel.php';
class ExamController {
    private $examModel;
    private $resultModel;
    private $userModel;

    public function __construct() {
        $this->examModel = new ExamModel();
        $this->resultModel = new ResultModel();
        $this->userModel = new UserModel();
    }
    //Gia Bảo
    public function getExamDetail($examId) {
        return $this->examModel->getExamDetail($examId);
    }

    public function showExamsForTeacher($teacherId) {
        $result = $this->examModel->getExamsByTeacher($teacherId);

        if ($result['success']) {
            return[
                'exams' => $result['data'],
                'error_message' => null
            ];
        } else {
           return [
                'exams' => [],
                'error_message' => $result['message']
            ];
        }
    }
     public function getResultsByExam($examId) {
        $results = $this->resultModel->getResultsByExam($examId);
        $finalResults = [];

        foreach ($results as $result) {
            $studentId = $result['student_id'];
            $user = $this->userModel->getUserById($studentId);

            $finalResults[] = [
                'fullname' => $user['fullname'] ?? 'Không rõ',
                'score' => $result['score'] ?? 0
            ];
        }

        return $finalResults;
    }

    //Quang
    public function createExam($data){
        try {
            $result = $this->examModel->createExam($data);
            return $result;
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }
}