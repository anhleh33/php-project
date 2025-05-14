<?php
require_once __DIR__ . '/../model/Database.php';  // Kết nối với Database

class ResultModel
{
    private $collection;

    public function __construct()
    {
        $this->collection = Database::getInstance()->getDB()->results; // 'results' là tên collection trong MongoDB
    }

    //Gia bảo
    // 📌 Tính điểm, lưu kết quả vào collection results
    public function submitResultAndScore($exam, $studentId, $answers)
    {
        $correctCount = 0;

        foreach ($answers as $answer) {
            $index = $answer['question_index'];

            // Kiểm tra câu hỏi có tồn tại và so sánh đáp án
            if (
                isset($exam['questions'][$index]) &&
                ((int) $exam['questions'][$index]['correct_answer'] === (int) $answer['student_answer'])

            ) {
                $correctCount++;
            }
        }

        // Tính điểm theo phần trăm
        $totalQuestions = count($exam['questions']);
        $score = $totalQuestions > 0 ? round(($correctCount / $totalQuestions) * 100) : 0;

        // Ghi kết quả
        $result = [
            'exam_id' => $exam['_id'],
            'student_id' => new MongoDB\BSON\ObjectId($studentId),
            'score' => $score,
            'answers' => $answers,
            'submitted_at' => new MongoDB\BSON\UTCDateTime()
        ];
        return $this->collection->insertOne($result)->getInsertedId();
    }

    // Lấy kết quả của học sinh theo ID kỳ th
    public function getResultsByExam($examId)
    {
        return $this->collection->find([
            'exam_id' => new MongoDB\BSON\ObjectId($examId)
        ])->toArray(); // Trả về danh sách mảng kết quả
    }

    // Nhu Y
    public function getAllResults()
    {
        try {
            $cursor = $this->collection->find();
            return iterator_to_array($cursor);
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Lỗi: ' . $e->getMessage()];
        }
    }

    // Nhu Y
    public function getResultsByStudent($studentId)
    {
        try {
            return $this->collection->find([
                'student_id' => $studentId
            ])->toArray();
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Lỗi: ' . $e->getMessage()];
        }
    }

    //Hoang Anh
    public function getResultByExamAndStudent($examId, $studentId)
    {
        try {
            $examObjectId = is_string($examId) ? new MongoDB\BSON\ObjectId($examId) : $examId;
            $studentObjectId = is_string($studentId) ? new MongoDB\BSON\ObjectId($studentId) : $studentId;

            return $this->collection->findOne([
                'exam_id' => $examObjectId,
                'student_id' => $studentObjectId
            ]);
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Lỗi: ' . $e->getMessage()];
        }
    }
}
?>