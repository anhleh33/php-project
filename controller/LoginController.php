<?php
require_once __DIR__ . '/../model/UserModel.php';

class LoginController {
    public function login($username, $password, $role) {
        $userModel = new UserModel();
        $result = $userModel->login($username, $password, $role);
        echo $result['success'];
        if ($result['success']) {
            session_start();
            $_SESSION['username'] = $username;
            $_SESSION['role'] = $result['role'];
            $_SESSION['id'] = $result['id'];
            $_SESSION['fullname'] = $result['fullname'];

            if ($result['role'] === 'admin') {
                header('Location: admin/dashboard-admin.php');
                exit;
            }

            switch ($result['role']) {
                case 'teacher':
                    header('Location: dashboard-teacher.php');
                    exit;
                case 'student':
                    header('Location: dashboard-student.php');
                    exit;
                default:
                    return ['success' => false, 'message' => 'Thông tin đăng nhập không chính xác'];
            }   
        } else {
            return ['success' => false, 'message' => 'Thông tin đăng nhập không chính xác'];
        }
    }
}
?>