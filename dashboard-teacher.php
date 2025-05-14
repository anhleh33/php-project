<?php
session_start();
if(!$_SESSION['username'] || $_SESSION['role'] != 'teacher') {
  header('Location: ../signin.php');
}


$page_title = "Trang chủ - Giáo viên";
include 'template/head.php';

// Nhu Y
require_once 'controller/TeacherDashboardController.php'; 
$dashboardController = new TeacherDashboardController();

$teacherId = $_SESSION['id']; // Lấy ID giáo viên từ session

// Lấy các thông số thống kê từ MongoDB
$dashboardStats = $dashboardController->getDashboardStats($teacherId);

// Hoang Anh
require_once 'controller/GetInfoController.php';
$controller = new GetInfoController();

$result = $controller->getInformation($_SESSION['username']);

if ($result) {
    $role = $result['role'] ?? 'unknown';
    $username = $result['username'] ?? '';
    $fullname = $result['fullname'] ?? '';
    $email = $result['email'] ?? '';
} else {
    $role = 'unknown';
    $fullname = '';
    $username = '';
    $email = '';
}

//Gia Bảo
require_once 'controller/ExamController.php';
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

<div class="dashbroad-wrapper teacher-theme">
    <?php
    $current_page = 'home';
    $role = 'teacher';
    include 'template/header.php';
    ?>

    <div class="main-content">
        <div class="teacher-header">
            <div class="teacher-header-content">
                <!-- Hoang Anh -->
                <h1>Xin chào, <?php echo $fullname ?? 'Giáo viên'; ?>! <span
                        class="emoji">✏️</span></h1>
            </div>
        </div>

        <div class="stats-row">
            <div class="stat-card">
                <div class="stat-icon" style="background: #ffcd56;">
                    <i class="fas fa-gamepad"></i>
                </div>
                <div class="stat-info">
                    <h3><?php echo $dashboardStats['createdExamsCount']; ?></h3> <!-- Nhu Y -->
                    <p>Kỳ thi đã tạo</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background: #36a2eb;">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-info">
                    <h3><?php echo $dashboardStats['participatedStudentsCount']; ?></h3> <!-- Nhu Y -->
                    <p>Học sinh tham gia</p>
                </div>
            </div>
            <!-- Nhu Y -->
            <div class="stat-card">
                <div class="stat-icon" style="background: #4cc790; color: white;">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-info">
                    <h3><?php echo $dashboardStats['completionPercentage']; ?>%</h3>
                    <p>Hoàn thành</p>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="quick-actions-card">
            <div class="card-header">
                <h3><i class="fas fa-bolt"></i> Thao tác nhanh</h3>
            </div>
            <div class="actions-grid">
                <button class="action-btn teacher-action" id="create-exam">
                    <i class="fas fa-plus-square"></i>
                    <span>Tạo đề thi</span>
                </button>
                <button class="action-btn teacher-action" id="manage-exam">
                    <i class="fas fa-tasks"></i>
                    <span>Quản lý kết quả</span>
                </button>
            </div>
        </div>

        <!-- Exam List Section -->
        <div class="exam-list-section">
            <div class="section-header">
                <h3><i class="fas fa-clipboard-list"></i> Danh sách kỳ thi của tôi</h3>
                <div class="search-filter">
                    <input type="text" placeholder="Tìm kiếm kỳ thi..." class="search-input" id="teacher-search-exam">
                </div>
            </div>

            <div class="exam-table-container">
                <table class="exam-table">
                    <thead>
                        <tr>
                            <th>Tên kỳ thi</th>
                            <th>Ngày thi</th>
                            <th>Thời gian</th>
                            <th>Số học sinh</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($exams)): ?>
                            <?php foreach ($exams as $exam): ?>
                                <tr>
                                    <td>
                                        <div class="exam-name">
                                            <h4><?= $exam['name'] ?? 'Chưa có tên kỳ thi' ?></h4>
                                            <!-- <p>Mã: <?= (string)$exam['_id'] ?? 'Không có mã' ?></p> -->
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
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="5">Không có kỳ thi nào.</td></tr>
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

<?php include 'template/footer.php'; ?>