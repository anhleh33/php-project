<?php
    class LogoutController {
        public function logout() {
            // Xóa thông tin session
            session_start();
            session_unset();
            session_destroy();

            // Chuyển hướng về trang đăng nhập
            header('Location: ./signin.php');
        }
    }
?>