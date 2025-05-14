<?php
require_once __DIR__ . '/../model/ExamModel.php';
require_once __DIR__ . '/../model/ResultModel.php';
require_once __DIR__ . '/../controller/UpdateExamController.php';
require_once __DIR__ . '/../controller/GetInfoController.php';

// Hoang Anh
class GetResultController
{
    public function getResult($examId, $studentId)
    {
        try {
            $resultModel = new ResultModel();
            $result = $resultModel->getResultByExamAndStudent($examId, $studentId);

            return $result ?: null;
        } catch (Exception $e) {
            return null;
        }
    }

    public function isCorrectAnswer($answer, $question)
    {
        return (int) ($question['correct_answer'] === (int) $answer['student_answer']);
    }

    public function getCorrectAnswers($resultData, $examData)
    {
        $correctAnswers = [];
        for ($i = 0; $i < count($resultData['answers']); $i++) {
            $correctAnswers[] = $this->isCorrectAnswer($resultData['answers'][$i], $examData['data']['questions'][$i]);
        }
        return $correctAnswers;
    }

    public function countCorrectAndWrongAnswers($answers, $questions)
    {
        $correct = 0;
        $wrong = 0;

        for ($i = 0; $i < count($answers); $i++) {
            if ($this->isCorrectAnswer($answers[$i], $questions[$i])) {
                $correct++;
            } else {
                $wrong++;
            }
        }
        return [
            'correct' => $correct,
            'wrong' => $wrong
        ];
    }

    public function getResultDetails($examId, $studentId)
    {
        $finalResult = [
            'data' => [],
            'correct' => [],
            'error' => null
        ];

        try {
            $resultModel = new ResultModel();
            $examModel = new ExamModel();

            $resultData = $resultModel->getResultByExamAndStudent($examId, $studentId);
            $examData = $examModel->getExamById($examId);
            if (
                empty($resultData) || !isset($resultData['answers']) ||
                empty($examData) || !isset($examData['data']['questions'])
            ) {
                throw new Exception("Dữ liệu bài thi hoặc kết quả không hợp lệ");
            }

            // Gọi một lần để lấy danh sách đúng/sai
            $correctList = $this->getCorrectAnswers($resultData, $examData);

            foreach ($resultData['answers'] as $index => $answer) {
                if (!isset($answer['question_index'], $answer['student_answer'])) {
                    continue;
                }

                $questionIndex = $answer['question_index'];

                if (!isset($examData['data']['questions'][$questionIndex])) {
                    continue;
                }

                $question = $examData['data']['questions'][$questionIndex];
                $studentAnswer = (int) $answer['student_answer'];

                if (!isset($question['question_text'], $question['options'], $question['correct_answer'])) {
                    continue;
                }

                // Thêm thông tin chi tiết câu hỏi
                $finalResult['data'][] = [
                    'question_text' => $question['question_text'],
                    'options' => $question['options'],
                    'student_answer' => $studentAnswer,
                    'correct_answer' => $question['correct_answer']
                ];

                // Lưu đúng/sai theo index
                $finalResult['correct'][] = $correctList[$index];
            }

            if (empty($finalResult['data'])) {
                throw new Exception("Không có câu hỏi nào được xử lý thành công");
            }

        } catch (Exception $e) {
            $finalResult['error'] = $e->getMessage();
            error_log("Error in getResultDetails: " . $e->getMessage());
        }

        return $finalResult;
    }

    public function isAvailable($examId, $studentId)
    {
        try {
            $resultModel = new ResultModel();
            $result = $resultModel->getResultByExamAndStudent($examId, $studentId);

            if (is_array($result) && isset($result['success']) && $result['success'] === false) {
                error_log("Error in isAvailable: " . $result['message']);
                return false;
            }

            return empty($result);
        } catch (Exception $e) {
            error_log("Exception in isAvailable: " . $e->getMessage());
            return false;
        }
    }

    public function calculateScore($examId, $studentId)
    {
        try {
            $resultModel = new ResultModel();
            $examModel = new ExamModel();

            $resultData = $resultModel->getResultByExamAndStudent($examId, $studentId);
            $examData = $examModel->getExamById($examId);

            if (
                empty($resultData) || !isset($resultData['answers']) ||
                empty($examData) || !isset($examData['data']['questions'])
            ) {
                throw new Exception("Dữ liệu không hợp lệ để tính điểm");
            }

            $totalQuestions = count($examData['data']['questions']);
            if ($totalQuestions === 0) {
                throw new Exception("Bài thi không có câu hỏi");
            }

            $correctAnswers = $this->getCorrectAnswers($resultData, $examData);
            $correctCount = array_sum($correctAnswers);

            $score = ($correctCount / $totalQuestions) * 100;

            return round($score, 2);

        } catch (Exception $e) {
            error_log("Error in calculateScore: " . $e->getMessage());
            return null;
        }
    }

    public function getFullResult($examId, $studentId)
    {
        $finalResult = [
            'data' => [],
            'correct' => [],
            'score' => null,
            'error' => null
        ];

        try {
            $resultDetails = $this->getResultDetails($examId, $studentId);

            if (isset($resultDetails['error'])) {
                throw new Exception($resultDetails['error']);
            }

            $score = $this->calculateScore($examId, $studentId);

            if ($score === null) {
                throw new Exception("Không thể tính điểm cho bài thi này");
            }

            $finalResult['data'] = $resultDetails['data'];
            $finalResult['correct'] = $resultDetails['correct'];
            $finalResult['score'] = $score;

        } catch (Exception $e) {
            $finalResult['error'] = $e->getMessage();
            error_log("Error in getFullResult: " . $e->getMessage());
        }

        return $finalResult;
    }
}
?>