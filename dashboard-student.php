<?php
session_start();

if (!$_SESSION['username'] || $_SESSION['role'] != 'student') {
    header('Location: ../signin.php');
}

$page_title = "Trang chủ - Học sinh";
include 'template/head.php';

require_once 'controller/GetInfoController.php';
require_once 'controller/UpdateExamController.php';

$ExamController = new UpdateExamController();
$StudentController = new GetInfoController();
$studentID = $StudentController->getInformation($_SESSION['username'])['_id'];
$ongoing = count($ExamController->getOngoingExams($studentID));
$completed = count($ExamController->getCompletedExams($studentID));
$upcoming = count($ExamController->getUpcomingExams($studentID));

$result = $StudentController->getInformation($_SESSION['username']);

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
?>

<div class="dashbroad-wrapper">
    <?php
    $current_page = 'home';
    $role = 'student';
    include 'template/header.php';
    ?>

    <div class="main-content">
        <div class="teacher-header">
            <div class="teacher-header-content">
                <!-- Hoang Anh -->
                <h1 style="color: var(--primary)">Xin chào,
                    <?php echo $fullname ?? 'Học sinh'; ?>! <span class="emoji">📘</span></h1>
            </div>
        </div>

        <div class="dashboard-content">
            <!-- Thống kê nhanh -->
            <div class="stats-row">
                <div class="stat-card">
                    <div class="stat-icon bg-primary">
                        <i class="fas fa-clipboard-list"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?= $upcoming?></h3>
                        <p>Kỳ thi sắp tới</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon bg-success">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?= $completed?></h3>
                        <p>Kỳ thi đã hoàn thành</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon bg-warning">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?= $ongoing?></h3>
                        <p>Kỳ thi đang diễn ra</p>
                    </div>
                </div>
            </div>

            <div class="" style="margin-left: 0;">
                <!-- Quick Actions -->
                <div class="quick-actions-card">
                    <div class="card-header">
                        <h3><i class="fas fa-bolt"></i> Thao tác nhanh</h3>
                    </div>
                    <div class="actions-grid">
                        <button class="action-btn student-action" id="st-exam">
                            <i class="fas fa-book"></i>
                            <span>Kỳ thi</span>
                        </button>
                        <button class="action-btn student-action" id="st-result">
                            <i class="fas fa-chart-bar"></i>
                            <span>Kết quả</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include('template/footer.php'); ?>