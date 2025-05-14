<!-- Nhu Y -->
<?php
require_once __DIR__ . '/../model/UserModel.php';  
require_once __DIR__ . '/../model/ExamModel.php';  
require_once __DIR__ . '/../model/ResultModel.php'; 

class AdminDashboardController {

    private $userModel;
    private $examModel;
    private $resultModel;

    public function __construct() {
        $this->userModel = new UserModel();
        $this->examModel = new ExamModel();
        $this->resultModel = new ResultModel();
    }

    public function getTotalStudents() {
        $students = $this->userModel->getUsersByRole('student'); 
        return $students['success'] ? count($students['data']) : 0;
    }

    public function getTotalTeachers() {
        $teachers = $this->userModel->getUsersByRole('teacher'); 
        return $teachers['success'] ? count($teachers['data']) : 0;
    }

    public function getTotalExams() {
        $exams = $this->examModel->getAllExams(); 
        return $exams['success'] ? count($exams['data']) : 0;
    }

    public function getTotalSubmissions() {
        $submissions = $this->resultModel->getAllResults(); 
        return $submissions ? count($submissions) : 0;
    }

    public function getDashboardStats() {
        return [
            'studentsCount' => $this->getTotalStudents(),
            'teachersCount' => $this->getTotalTeachers(), 
            'examsCount' => $this->getTotalExams(), 
            'submissionsCount' => $this->getTotalSubmissions() 
        ];
    }
}
?>