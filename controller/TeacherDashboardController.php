<!-- Nhu Y -->
<?php
require_once __DIR__ . '/../model/ExamModel.php';  
require_once __DIR__ . '/../model/ResultModel.php'; 

class TeacherDashboardController {
    private $examModel;
    private $resultModel;

    public function __construct() {
        $this->examModel = new ExamModel();
        $this->resultModel = new ResultModel();
    }

    public function getCreatedExamsCount($teacherId) {
        $exams = $this->examModel->getExamsByTeacher($teacherId);
        return $exams['success'] ? count($exams['data']) : 0;
    }

    public function getParticipatedStudentsCount($teacherId) {
        $exams = $this->examModel->getExamsByTeacher($teacherId);
        
        if (!$exams['success']) {
            return 0; 
        }

        $studentsParticipated = [];
        foreach ($exams['data'] as $exam) {
            foreach ($exam['students'] as $student) {
                $studentsParticipated[] = (string)$student; 
            }
        }

        $studentsParticipated = array_unique($studentsParticipated);

        return count($studentsParticipated);
    }

    public function getCompletedStudentsCount($teacherId) {
        $exams = $this->examModel->getExamsByTeacher($teacherId);
        
        if (!$exams['success']) {
            return 0; 
        }

        $studentsCompleted = [];
        foreach ($exams['data'] as $exam) {
            $results = $this->resultModel->getResultsByExam((string)$exam['_id']);
            
            foreach ($results as $result) {
                $studentsCompleted[] = (string)$result['student_id']; 
            }
        }

        $studentsCompleted = array_unique($studentsCompleted);

        return count($studentsCompleted); 
    }


    public function getCompletionPercentage($teacherId) {
        
        $completedStudents = $this->getCompletedStudentsCount($teacherId);       
        $participatedStudents = $this->getParticipatedStudentsCount($teacherId); 
        // Tổng học sinh thực hiện (hoàn thành) bài kiểm tra / Tổng số học sinh thêm vào (trên giao diện là "Học sinh tham gia")
        return ($participatedStudents > 0) ? round(($completedStudents / $participatedStudents) * 100) : 0; 
    }

    public function getDashboardStats($teacherId) {
        return [
            'createdExamsCount' => $this->getCreatedExamsCount($teacherId), 
            'participatedStudentsCount' => $this->getParticipatedStudentsCount($teacherId), 
            'completionPercentage' => $this->getCompletionPercentage($teacherId) 
        ];
    }
}
?>