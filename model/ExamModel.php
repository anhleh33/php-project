<?php
require_once __DIR__ . '/../model/Database.php';  

class ExamModel
{
    private $collection;

    public function __construct()
    {
        $this->collection = Database::getInstance()->getDB()->exams; // 'exams' là tên collection trong MongoDB
    }

    // Tuấn Đạt
    public function getExamsByTeacher($teacherId)
    {
        try {
            $examsCursor = $this->collection->find(['teacher_id' => new MongoDB\BSON\ObjectId($teacherId)]);
            $exams = iterator_to_array($examsCursor); 

            if (!empty($exams)) {
                return [
                    'success' => true,
                    'data' => $exams  
                ];
            } else {
                return ['success' => false, 'message' => 'Không tìm thấy kỳ thi nào do giáo viên này tạo'];
            }
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Lỗi: ' . $e->getMessage()];
        }
    }

     // Tuấn Đạt
    public function getExamById($examId) {
        try {
            $exam = $this->collection->findOne(['_id' => new MongoDB\BSON\ObjectId($examId)]);

            if ($exam) {
                unset($exam['students']);

                return [
                    'success' => true,
                    'data' => $exam 
                ];
            } else {
                return ['success' => false, 'message' => 'Không tìm thấy kỳ thi'];
            }
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Lỗi: ' . $e->getMessage()];
        }
    }

    // Thêm câu hỏi
    public function addQuestionToExam($examId, $question)
    {
        return $this->collection->updateOne(
            ['_id' => new MongoDB\BSON\ObjectId($examId)],
            [
                '$push' => [
                    'questions' => [
                        'question_text' => $question['question_text'],
                        'choices' => $question['choices'],
                        'correct_answer' => $question['correct_answer']
                    ]
                ]
            ]
        );
    }


    // Thêm học sinh vào kỳ thi
    public function addStudentToExam($examId, $studentId)
    {
        return $this->collection->updateOne(
            ['_id' => new MongoDB\BSON\ObjectId($examId)],
            ['$addToSet' => ['students' => new MongoDB\BSON\ObjectId($studentId)]]
        );
    }

    // Xóa học sinh khỏi danh sách kỳ thi
    public function removeStudentFromExam($examId, $studentId)
    {
        return $this->collection->updateOne(
            ['_id' => new MongoDB\BSON\ObjectId($examId)],
            ['$pull' => ['students' => new MongoDB\BSON\ObjectId($studentId)]]
        );
    }

    public function createExam($data)
    {
        try {
            // Đặt timezone về UTC để tránh lệch giờ khi lưu vào MongoDB
            date_default_timezone_set('UTC');
            // Ensure the required data fields are present
            if (!isset($data['name'], $data['description'], $data['teacher_id'], $data['start_time'], $data['end_time'], $data['students'], $data['questions'])) {
                return ['success' => false, 'message' => 'Thiếu thông tin cần thiết để tạo kỳ thi'];
            }

            // Prepare the exam data for insertion
            $examData = [
                'name' => $data['name'],
                'description' => $data['description'],
                'teacher_id' => new MongoDB\BSON\ObjectId($data['teacher_id']),
                'start_time' => new MongoDB\BSON\UTCDateTime(strtotime($data['start_time']) * 1000),
                'end_time' => new MongoDB\BSON\UTCDateTime(strtotime($data['end_time']) * 1000),
                'students' => array_map(function ($studentId) {
                    return new MongoDB\BSON\ObjectId($studentId);
                }, $data['students']),
                'questions' => $data['questions']
            ];

            // Insert exam into the collection
            $result = $this->collection->insertOne($examData);

            return [
                'success' => true,
                'inserted_id' => (string) $result->getInsertedId()
            ];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Lỗi: ' . $e->getMessage()];
        }
    }

    public function updateExam($examId, $data)
    {
        try {
            // Đặt timezone về UTC để tránh lệch giờ khi lưu vào MongoDB
            date_default_timezone_set('UTC');
            // Ensure the exam exists
            $exam = $this->collection->findOne(['_id' => new MongoDB\BSON\ObjectId($examId)]);
            if (!$exam) {
                return ['success' => false, 'message' => 'Kỳ thi không tồn tại'];
            }

            // Prepare the update data
            $updateData = [];
            if (isset($data['name'])) {
                $updateData['name'] = $data['name'];
            }
            if (isset($data['description'])) {
                $updateData['description'] = $data['description'];
            }
            if (isset($data['start_time'])) {
                $updateData['start_time'] = new MongoDB\BSON\UTCDateTime(strtotime($data['start_time']) * 1000);
            }
            if (isset($data['end_time'])) {
                $updateData['end_time'] = new MongoDB\BSON\UTCDateTime(strtotime($data['end_time']) * 1000);
            }
            if (isset($data['students'])) {
                $updateData['students'] = array_map(function ($studentId) {
                    return new MongoDB\BSON\ObjectId($studentId);
                }, $data['students']);
            }
            if (isset($data['questions'])) {
                $updateData['questions'] = $data['questions'];
            }

            // Perform the update operation
            $result = $this->collection->updateOne(
                ['_id' => new MongoDB\BSON\ObjectId($examId)],
                ['$set' => $updateData]
            );

            if ($result->getModifiedCount() > 0) {
                return ['success' => true, 'message' => 'Cập nhật kỳ thi thành công'];
            } else {
                return ['success' => false, 'message' => 'Không có thay đổi nào được áp dụng'];
            }
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Lỗi: ' . $e->getMessage()];
        }
    }

    public function deleteExam($examId)
    {
        try {
            // Ensure the exam exists
            $exam = $this->collection->findOne(['_id' => new MongoDB\BSON\ObjectId($examId)]);
            if (!$exam) {
                return ['success' => false, 'message' => 'Kỳ thi không tồn tại'];
            }

            // Perform the delete operation
            $result = $this->collection->deleteOne(['_id' => new MongoDB\BSON\ObjectId($examId)]);

            if ($result->getDeletedCount() > 0) {
                return ['success' => true, 'message' => 'Đã xóa kỳ thi'];
            } else {
                return ['success' => false, 'message' => 'Không thể xóa kỳ thi'];
            }
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Lỗi: ' . $e->getMessage()];
        }
    }

    // Hoang Anh
    public function getExamByStudentID($studentID)
    {
        try {
            $objectStudentID = new MongoDB\BSON\ObjectId($studentID);

            // Tìm tất cả các kỳ thi mà học sinh có trong danh sách students
            $exams = $this->collection->find([
                'students' => $objectStudentID
            ]);

            $examList = $exams->toArray();

            if (count($examList) > 0) {
                return [
                    'success' => true,
                    'data' => $examList
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Không có kỳ thi nào cho học sinh này'
                ];
            }
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Lỗi: ' . $e->getMessage()];
        }
    }

     // lấy full thông tin kỳ thi
     //Gia Bảo
    public function getExamDetail($examId) {
        try {
            $exam = $this->collection->findOne(['_id' => new MongoDB\BSON\ObjectId($examId)]);

            if ($exam) {
                

                return [
                    'success' => true,
                    'data' => $exam 
                ];
            } else {
                return ['success' => false, 'message' => 'Không tìm thấy kỳ thi'];
            }
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Lỗi: ' . $e->getMessage()];
        }
    }

    // Nhu Y
    public function getAllExams()
    {
        try {
            $cursor = $this->collection->find();
            return ['success' => true, 'data' => iterator_to_array($cursor)];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Lỗi: ' . $e->getMessage()];
        }
    }
}
?>