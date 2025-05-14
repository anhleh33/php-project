<?php
    require_once __DIR__ . '/controller/LogoutController.php';

    $logoutController = new LogoutController();
    $result = $logoutController->logout($username, $password, $role);
?>