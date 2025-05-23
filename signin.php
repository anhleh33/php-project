<?php
session_start();
if (isset($_SESSION['username']) && isset($_SESSION['role'])) {
    if ($_SESSION['role'] == 'admin') {
        header('Location: admin/dashboard-admin.php');
    } elseif ($_SESSION['role'] == 'teacher') {
        header('Location: dashboard-teacher.php');
    } elseif ($_SESSION['role'] == 'student') {
        header('Location: dashboard-student.php');
    }
}
require_once __DIR__ . '/controller/LoginController.php';
 
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $role = $_POST['role'];
    $loginController = new LoginController();
    $result = $loginController->login($username, $password, $role);

    if (!$result['success']) {
        $error_message = $result['message'];
    } else {
        // Để LoginController xử lý chuyển hướng
        exit; // Dừng thực thi thêm
    }
}
?>

<?php
$page_title = "Đăng nhập";
include 'template/head.php';
?>

<div class="page-wrapper">
    <div class="login-container">
        <div class="login-box">
            <div class="logo">
                <i class="fas fa-graduation-cap"></i>
                <h1>Hệ Thống Thi Trực Tuyến</h1>
                <p>ĐĂNG NHẬP</p>
            </div>

            <form id="loginForm" method="POST">
                <div class="form-group">
                    <label for="username"><i class="fas fa-user"></i> Username</label>
                    <input type="text" id="username" name="username" required>
                </div>

                <div class="form-group">
                    <label for="password"><i class="fas fa-lock"></i> Password</label>
                    <input type="password" id="password" name="password" required>
                </div>

                <div class="form-group">
                    <p class="error-message"><?php echo $error_message; ?></p>
                </div>

                <br>
                <div class="form-group">
                    <div class="role-options">
                        <label class="role-option">
                            <input type="radio" name="role" id="student" value="student" required>
                            <span><i class="fas fa-user-graduate"></i> <br> Học sinh</span>
                        </label>
                        <label class="role-option">
                            <input type="radio" name="role" id="teacher" value="teacher">
                            <span><i class="fas fa-chalkboard-teacher"></i> <br> Giáo viên</span>
                        </label>
                        <label class="role-option">
                            <input type="radio" name="role" id="admin" value="admin">
                            <span><i class="fas fa-user-shield"></i> <br> Quản trị viên</span>
                        </label>
                    </div>
                </div>

                <button type="submit" class="btn-login">Đăng nhập</button>

            </form>
        </div>
    </div>

    <?php include('template/footer.php') ?>
</div>