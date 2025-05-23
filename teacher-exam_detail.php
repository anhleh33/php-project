<?php
require_once 'controller/ExamController.php';

$page_title = "Chi tiết kỳ thi";
include 'template/head.php';

$examController = new ExamController();
$exam_id = $_GET['id'] ?? null;
$exam_data = null;
$result_of_exam = null;
if ($exam_id) {
    $result = $examController->getExamDetail($exam_id);
    if ($result['success']) {
        $exam_data = $result['data'];
        $result_of_exam = $examController->getResultsByExam($exam_id);
    } else {
        die("Lỗi: " . $result['message']);
    }
} else {
    die("Thiếu ID kỳ thi");
}
?>

<?php
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    $teacherName = $_SESSION['fullname'] ?? 'Chưa rõ';
    
?>

<div class="dashbroad-wrapper teacher-theme">
    <?php
    $current_page = 'exam';
    $role = 'teacher';
    include 'template/header.php';
    ?>

    <div class="main-content">

        <!-- Exam Header -->
        <div class="exam-detail-header">
            <div class="header-left">
                <h1 style="font-size: 40px;"><i class="fas fa-clipboard-list"></i> <?= $exam_data['name'] ?? '---' ?></h1>
                <p class="exam-code">Mã kỳ thi: <?= (string)$exam_data['_id'] ?></p>
            </div>
        </div>

        <!-- Exam Stats -->
        <div class="exam-stats">
            <div class="stat-card">
                <div class="stat-icon" style="background: #ffcd56; color: white">
                    <i class="fa-solid fa-question"></i>
                </div>
                <div class="stat-info">
                    <h3><?= isset($exam_data['questions']) ? count($exam_data['questions']) : '---' ?></h3>
                    <p>Số câu hỏi</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background: #36a2eb; color: white">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-info">
                    <h3><?= isset($exam_data['students']) ? count($exam_data['students']) : '---' ?></h3>
                    <p>Học sinh tham gia</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background: #4cc790; color: white">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-info">
                    <?php
                        $participated = count($result_of_exam) ?? 0;
                        $total = count($exam_data['students']) ?? 0;
                        $percent = ($total > 0) ? round(($participated / $total) * 100) : 0;
                    ?>
                    <h3><?= $percent ?>%</h3>
                    <p>Hoàn thành</p>
                </div>
            </div>
        </div>


        <!-- Exam Tabs -->
        <div class="exam-tabs">
            <div class="tab active" data-tab="info">
                <i class="fas fa-info-circle"></i>
                <span>Thông tin chung</span>
            </div>
            <div class="tab" data-tab="questions">
                <i class="fas fa-question-circle"></i>
                <span>Danh sách câu hỏi</span>
            </div>
            <div class="tab" data-tab="results">
                <i class="fas fa-poll"></i>
                <span>Kết quả thi</span>
            </div>
        </div>

        <!-- Tab Content -->
        <div class="tab-content active" id="info-tab">
            <!-- Basic Info -->
            <div class="detail-section">
                <h3><i class="fas fa-info-circle"></i> Thông tin cơ bản</h3>
                <?php
                // Chuyển đổi từ UTCDateTime của MongoDB sang DateTime PHP
                $startTime = $exam_data['start_time']->toDateTime();
                $endTime = $exam_data['end_time']->toDateTime();
                $duration = round(($endTime->getTimestamp() - $startTime->getTimestamp()) / 60);
                ?>
                <div class="detail-grid">
                    <div class="detail-item">
                        <span class="detail-label">Ngày thi:</span>
                        <span class="detail-value"><?= $startTime->format('d/m/Y') ?></span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Thời gian làm bài:</span>
                        <span class="detail-value"><?= $duration ?> phút</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Thời gian bắt đầu:</span>
                        <span class="detail-value"><?= $startTime->format('H:i') ?></span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Thời gian kết thúc:</span>
                        <span class="detail-value"><?= $endTime->format('H:i') ?></span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Giáo viên:</span>
                        <span class="detail-value"><?php echo $teacherName ?></span> 
                    </div>
                </div>

            </div>

            <!-- Description -->
            <div class="detail-section">
                <h3><i class="fas fa-align-left"></i> Mô tả kỳ thi</h3>
                <div class="description-content">
                    <?= $exam_data['description'] ?? 'Không có mô tả' ?>
                </div>

            </div>
        </div>

        <!-- Questions Tab (hidden by default) -->
        <div class="tab-content" id="questions-tab">
            <div class="questions-header">
                <h3><i class="fas fa-question-circle"></i> Danh sách câu hỏi</h3>
            </div>

            <div class="questions-list">
                <?php if (isset($exam_data['questions']) && count($exam_data['questions']) > 0): ?>
                    <?php foreach ($exam_data['questions'] as $index => $question): ?>
                        <?php
                            $questionText = $question['question_text'] ?? 'Không có nội dung';
                            $options = $question['options'] ?? [];
                            $correctIndex = $question['correct_answer'] ?? -1;
                            $optionLabels = ['A', 'B', 'C', 'D'];
                        ?>
                        <div class="question-item">
                            <div class="question-header">
                                <span class="question-number">Câu <?= $index + 1 ?></span>
                            </div>
                            <div class="question-content">
                                <p><?= htmlspecialchars($questionText) ?></p>
                            </div>
                            <div class="question-options">
                                <?php foreach ($options as $optIndex => $optText): ?>
                                    <div class="option <?= ($optIndex === $correctIndex) ? 'correct' : '' ?>">
                                        <span class="option-label"><?= $optionLabels[$optIndex] ?? '' ?>.</span>
                                        <span class="option-text"><?= htmlspecialchars($optText) ?></span>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>Không có câu hỏi nào trong kỳ thi này.</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Results Tab (hidden by default) -->
        <div class="tab-content" id="results-tab">
            <div class="results-header">
                <h3><i class="fas fa-poll"></i> Kết quả thi</h3>
            </div>

            <div class="results-filters">
                <div class="filter-group">
                    <label>Sắp xếp theo:</label>
                    <select class="filter-select">
                        <option value="score-desc">Điểm cao → thấp</option>
                        <option value="score-asc">Điểm thấp → cao</option>
                        <option value="name-asc">Tên A-Z</option>
                        <option value="name-desc">Tên Z-A</option>
                    </select>
                </div>
                <div class="filter-group">
                    <input type="text" placeholder="Tìm học sinh..." class="search-input" id="teacher-search-marks">
                </div>
            </div>

            <div class="results-table-container">
                <table class="results-table">
                    <thead>
                        <tr>
                            <th>STT</th>
                            <th>Học sinh</th>
                            <th>Điểm số</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($result_of_exam)): ?>
                            <?php foreach ($result_of_exam as $index => $result): ?>
                                <tr>
                                    <td><?= $index + 1 ?></td>
                                    <td><?= htmlspecialchars($result['fullname']) ?></td>
                                    <td><?= htmlspecialchars($result['score']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="3">Chưa có kết quả nào.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>


            <div class="table-footer">
                <div class="pagination">
                    <button class="page-btn disabled"><i class="fas fa-chevron-left"></i></button>
                    <button class="page-btn active">1</button>
                    <button class="page-btn"><i class="fas fa-chevron-right"></i></button>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'template/footer.php'; ?>