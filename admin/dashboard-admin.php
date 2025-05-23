<?php
session_start();

if (!$_SESSION['username'] || $_SESSION['role'] != 'admin') {
    header('Location: ../signin.php');
}

$page_title = "Trang chủ - Admin";
include '../template/head-admin.php';

// Nhu Y
require_once '../controller/AdminDashboardController.php'; 
$adminController = new AdminDashboardController();

$adminStats = $adminController->getDashboardStats();

// Hoang Anh
require_once '../controller/GetInfoController.php';
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
?>

<div class="admin-dashboard">
    <?php
    $current_page = 'home';
    $role = 'admin';
    include '../template/header-admin.php';
    ?>

    <div class="dashboard-container main-content">
        <div class="teacher-header">
            <div class="teacher-header-content">
                <!-- Hoang Anh -->
                <h1 style="color: var(--danger)">Xin chào,
                    <?php echo $fullname ?? 'Quản trị viên'; ?>! <span class="emoji">👑</span>
                </h1>
            </div>
        </div>

        <div class="stats-overview">
            <div class="stat-card">
                <div class="stat-icon bg-blue">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-content">
                    <h3><?php echo $adminStats['studentsCount']; ?></h3> <!-- Nhu Y -->
                    <p>Học sinh</p>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon bg-orange">
                    <i class="fas fa-chalkboard-teacher"></i>
                </div>
                <div class="stat-content">
                    <h3><?php echo $adminStats['teachersCount']; ?></h3> <!-- Nhu Y -->
                    <p>Giáo viên</p>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon bg-purple">
                    <i class="fas fa-clipboard-list"></i>
                </div>
                <div class="stat-content">
                    <h3><?php echo $adminStats['examsCount']; ?></h3> <!-- Nhu Y -->
                    <p>Kỳ thi</p>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon bg-green">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-content">
                    <h3><?php echo $adminStats['submissionsCount']; ?></h3> <!-- Nhu Y -->
                    <p>Lượt nộp bài</p>
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
                    <button class="action-btn admin-action" id="mg-teacher">
                        <i class="fas fa-chalkboard-teacher"></i>
                        <span>Quản lý giáo viên</span>
                    </button>
                    <button class="action-btn admin-action" id="mg-student">
                        <i class="fa-solid fa-book-open-reader"></i>
                        <span>Quản lý học sinh</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../template/footer-admin.php'; ?>