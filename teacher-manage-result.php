<?php
$page_title = "Quản lý kết quả";
include 'template/head.php';
date_default_timezone_set('UTC');
?>

<?php
require_once 'controller/ExamController.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
// Lấy ID giáo viên từ session (đã được lưu khi đăng nhập)
$teacherId = isset($_SESSION['id']) ? $_SESSION['id']->__toString() : null;


if ($teacherId) {
    $controller = new ExamController();
    $exams = $controller->showExamsForTeacher($teacherId);
    if (isset($exams['error_message'])) {
        echo "<p class='error-message'>Lỗi: " . $exams['error_message'] . "</p>";
    } else {
        $exams = $exams['exams'];
    }
} else {
    echo "Bạn chưa đăng nhập.";
}
?>

<div>
    <?php
    $current_page = 'manage_result';
    $role = 'teacher';
    include 'template/header.php';
    ?>

    <!-- Main Content -->
    <div class="main-content">
        <div class="page-header">
            <h1><i class="fas fa-poll"></i> Quản lý kết quả</h1>
        </div>

        <!-- Results Content -->
        <div class="results-content">
            <!-- By Exam Tab -->
            <div class="tab-content active" id="by-exam">
                <div class="exam-list-section">
                    <div class="exam-table-container">
                        <table class="exam-table">
                            <thead>
                                <tr>
                                    <th>Tên kỳ thi</th>
                                    <th>Ngày thi</th>
                                    <th>Thời gian</th>
                                    <th>Số học sinh</th>
                                    <th>Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($exams)): ?>
                                    <?php foreach ($exams as $exam): ?>
                                        <tr>
                                            <td>
                                                <div class="exam-name">
                                                    <h4><?= $exam['name'] ?? 'Chưa có tên kỳ thi' ?></h4>
                                                    <!-- <p>Mã: <?= (string) $exam['_id'] ?? 'Không có mã' ?></p> -->
                                                </div>
                                            </td>
                                            <td>
                                                <?php
                                                // Chuyển đổi thời gian MongoDB\BSON\UTCDateTime sang DateTime (mặc định là UTC)
                                                $startTime = $exam['start_time']->toDateTime();
                                                echo $startTime->format('d/m/Y H:i');  // Hiển thị theo UTC
                                                ?>

                                            </td>
                                            <td>
                                                <?php
                                                $startTime = $exam['start_time']->toDateTime();
                                                $endTime = $exam['end_time']->toDateTime();
                                                $duration = ($endTime->getTimestamp() - $startTime->getTimestamp()) / 60;
                                                echo $duration . ' phút';


                                                ?>

                                            </td>

                                            <td>
                                                <?php
                                                $studentCount = isset($exam['students']) ? count($exam['students']) : 0;
                                                echo "$studentCount học sinh";
                                                ?>
                                            </td>
                                            <td>
                                                <button class="btn btn-detail" data-exam-id="<?= (string) $exam['_id'] ?>">
                                                    <i class="fas fa-eye"></i> Chi tiết
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="5">Không có kỳ thi nào.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>


                        </table>
                    </div>

                    <div class="table-footer">
                        <div class="pagination">
                            <button class="page-btn disabled"><i class="fas fa-chevron-left"></i></button>
                            <button class="page-btn active">1</button>
                            <button class="page-btn">2</button>
                            <button class="page-btn">3</button>
                            <button class="page-btn"><i class="fas fa-chevron-right"></i></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
</div>

<?php include 'template/footer.php'; ?>