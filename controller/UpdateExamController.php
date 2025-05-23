<?php
require_once __DIR__ . '/../model/ExamModel.php';
require_once __DIR__ . '/../model/ResultModel.php';
require_once __DIR__ . '/../controller/GetResultController.php';

class UpdateExamController
{
    // Hoang Anh
    public function ExamListForStudent($studentID)
    {
        $examModel = new ExamModel();

        $result = $examModel->getExamByStudentID($studentID);
        return $result['success'] ? $result['data'] : null;
    }

    // Hoang Anh
    public function getCompletedExams($studentId)
    {
        $now = new DateTime('now', new DateTimeZone('Asia/Ho_Chi_Minh'));
        $exams = $this->ExamListForStudent($studentId);

        if (!is_array($exams))
            return [];

        return array_filter($exams, function ($exam) use ($now) {
            return $exam->end_time->toDateTime()->modify('-7 hours') < $now;
        });
    }

    // Hoang Anh
    public function getOngoingExams($studentId)
    {
        $now = new DateTime('now', new DateTimeZone('Asia/Ho_Chi_Minh'));
        $exams = $this->ExamListForStudent($studentId);

        if (!is_array($exams))
            return [];

        return array_filter($exams, function ($exam) use ($now) {
            $start = $exam->start_time->toDateTime();
            $end = $exam->end_time->toDateTime();
            return $start->modify('-7 hours') <= $now && $now <= $end->modify('-7 hours');
        });
    }

    // Hoang Anh
    public function getUpcomingExams($studentId)
    {
        $now = new DateTime('now', new DateTimeZone('Asia/Ho_Chi_Minh'));
        $exams = $this->ExamListForStudent($studentId);

        if (!is_array($exams))
            return [];

        return array_filter($exams, function ($exam) use ($now) {
            return $exam->start_time->toDateTime()->modify('-7 hours') > $now;
        });
    }

    // Hoang Anh
    public function getStatus($exam)
    {
        $now = new DateTime('now', new DateTimeZone('Asia/Ho_Chi_Minh'));
        $start = $exam->start_time->toDateTime() ->modify('-7 hours');
        $end = $exam->end_time->toDateTime()->modify('-7 hours');

        if ($now < $start) {
            return 'upcoming';
        } elseif ($now >= $start && $now <= $end) {
            return 'ongoing';
        } else {
            return 'completed';
        }
    }

    // Hoang Anh
    public function getDuration($exam)
    {
        $start = $exam->start_time->toDateTime()->setTimezone(new DateTimeZone('+00:00'));
        $end = $exam->end_time->toDateTime()->setTimezone(new DateTimeZone('+00:00'));

        return ($end->getTimestamp() - $start->getTimestamp()) / 60;
    }

    // Hoang Anh
    public function countQuestions($exam)
    {
        return count($exam['questions']);
    }

    // Hoang Anh
    public function formatStartTime($exam)
    {
        $start = $exam->start_time->toDateTime()->setTimezone(new DateTimeZone('+00:00'));
        return $start->format('d/m/Y - H:i');
    }

    //Hoang Anh
    public function getExamID($exam)
    {
        return $exam->_id;
    }

    //Hoang Anh
    public function getExaminExamList($examID, $studentID)
    {
        $exams = $this->ExamListForStudent($studentID);

        if (!is_array($exams)) {
            return null;
        }

        foreach ($exams as $exam) {
            if ((string) $exam->_id === (string) $examID) {
                return $exam;
            }
        }

        return null;
    }

    // Hoang Anh
    public function getQuestions($exam)
    {
        if (is_object($exam) && isset($exam->questions)) {
            return $exam->questions;
        }

        if (is_array($exam) && isset($exam['questions'])) {
            return $exam['questions'];
        }

        return [];
    }

    // Nhu Y
    public function getResultsByStudent($studentId) {
        try {
            $resultModel = new ResultModel();
            $examModel = new ExamModel();
            $userModel = new UserModel();

            $results = $resultModel->getResultsByStudent($studentId);

            if (!$results) {
                return ['success' => false, 'message' => 'Không có kết quả nào cho học sinh này.'];
            }

            $examDetails = [];
            foreach ($results as $result) {
                $examId = (string) $result['exam_id'];
                $exam = $examModel->getExamById($examId);

                
                if ($exam['success']) {
                    $teacher = $userModel->getUserById($exam['data'] ['teacher_id']);
                    
                    if ($teacher != null) {
                        $examDetails[] = [
                            'exam_id' => (string)$exam['data']['_id'],
                            'exam_name' => $exam['data']['name'],
                            'exam_date' => $exam['data']['start_time']->toDateTime()->setTimezone(new DateTimeZone('+00:00')),
                            'score' => $result['score'],
                            'teacher_name' => $teacher['fullname']
                        ];
                    } else {
                        return ['success' => false, 'message' => 'Không tìm thấy thông tin học sinh.'];
                    }
                }
            }
            return $examDetails;

        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Lỗi: ' . $e->getMessage()];
        }
    }

    public function submitResultAndScore($json) {
        $resultModel = new ResultModel();
        try {
            $exam = $json['exam'] ?? null;
            $studentId = $json['student_id'] ?? null;
            $answers = $json['answers'] ?? [];

            if (!$exam || !$studentId || empty($answers)) {
                throw new Exception('Invalid data');
            }

            $result = $resultModel->submitResultAndScore($exam, $studentId, $answers);
            
            if (!$result) {
                throw new Exception('Failed to submit result');
            }
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }
}
?>
