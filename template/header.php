<?php
//Hoang Anh
require_once 'controller/GetInfoController.php';
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$userModel = new UserModel();
$result = $userModel->getUserByName($_SESSION['username']);

if ($result['success']) {
    $userData = $result['data'];

    $role = $userData['role'] ?? 'unknown';
    $username = $userData['username'] ?? '';
    $fullname = $userData['fullname'] ?? '';
    $email = $userData['email'] ?? '';
} else {
    $role = 'unknown';
    $fullname = '';
    $username = '';
    $email = '';
}

?>

<div class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <div class="user-info">
            <div class="avatar admin">
                <i class="fas fa-user-circle"></i>
            </div>

            <div class="user-details">
                <!-- Hoang Anh -->
                <h4 id="usernameDisplay"><?php echo $fullname  ?></h4>
                <?php if ($role === 'student'): ?>
                    <span class="role-badge student">Học sinh</span>
                <?php endif; ?>
                <?php if ($role === 'teacher'): ?>
                    <span class="role-badge teacher">Giáo viên</span>
                <?php endif; ?>
                <?php if ($role === 'admin'): ?>
                    <span class="role-badge admin">Admin</span>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div id="userDetailsPopup" class="user-details-popup" style="display: none;">
        <!-- Hoang Anh -->
        <p><strong>Username:</strong> <?php echo $username  ?></p>
        <p><strong>Họ tên:</strong> <?php echo $fullname  ?></p>
        <p><strong>Email:</strong> <?php echo $email  ?></p>
        <p><strong>Vai trò:</strong>
            <?php if ($role === 'student'): ?>
                <span class="role-badge student">Học sinh</span>
            <?php endif; ?>
            <?php if ($role === 'teacher'): ?>
                <span class="role-badge teacher">Giáo viên</span>
            <?php endif; ?>
            <?php if ($role === 'admin'): ?>
                <span class="role-badge admin">Admin</span>
            <?php endif; ?>
        </p>
    </div>

    <nav class="sidebar-menu">
        <ul>
            <!-- Student-only -->
            <?php if ($role === 'student'): ?>
                <li class="<?= ($current_page == 'home') ? 'active' : '' ?>"><a href="dashboard-student.php"><i
                            class="fas fa-home"></i> Trang chủ</a></li>
                <li class="<?= ($current_page == 'exam') ? 'active' : '' ?>"><a href="student-exam.php"><i
                            class="fas fa-book"></i>
                        Kỳ thi</a></li>
                <li class="<?= ($current_page == 'result') ? 'active' : '' ?>"><a href="student-result.php"><i
                            class="fas fa-chart-bar"></i> Kết quả</a></li>
            <?php endif; ?>

            <!-- Teacher-only -->
            <?php if ($role === 'teacher'): ?>
                <li class="<?= ($current_page == 'home') ? 'active' : '' ?>"><a href="dashboard-teacher.php"><i
                            class="fas fa-home"></i> Trang chủ</a></li>
                <li class="<?= ($current_page == 'create_exam') ? 'active' : '' ?>"><a href="teacher-create-exam.php"><i
                            class="fas fa-plus-square"></i> Tạo đề thi</a></li>
                <li class="<?= ($current_page == 'manage_result') ? 'active' : '' ?>"><a href="teacher-manage-result.php"><i
                            class="fas fa-tasks"></i> Quản lý kết quả</a></li>
            <?php endif; ?>

            <!-- Admin-only -->
            <?php if ($role === 'admin'): ?>
                <li class="<?= ($current_page == 'home') ? 'active' : '' ?>"><a href="../admin/dashboard-admin.php"><i
                            class="fas fa-home"></i> Trang chủ</a></li>
                <li class="<?= ($current_page == 'manage_teachers') ? 'active' : '' ?>"><a
                        href="../admin/admin-manage-teachers.php"><i class="fas fa-chalkboard-teacher"></i> Quản lý giáo
                        viên</a></li>
                <li class="<?= ($current_page == 'manage_students') ? 'active' : '' ?>"><a
                        href="../admin/admin-manage-students.php"><i class="fa-solid fa-book-open-reader"></i> Quản lý học
                        sinh</a></li>
            <?php endif; ?>

            <!-- Vai trò không hợp lệ -->
            <?php if (!in_array($role, ['student', 'teacher', 'admin'])): ?>
                <li><i class="fas fa-exclamation-circle"></i> Vai trò không hợp lệ</li>
            <?php endif; ?>

            <!-- Đăng xuất -->
            <li><a href="" id="logout-btn"><i class="fas fa-sign-out-alt"></i> Đăng xuất</a></li>
        </ul>
    </nav>
</div>