<?php
require_once __DIR__ . '/../ManageStudentController.php';  // Adjust path if needed

header('Content-Type: application/json');

$controller = new ManageStudentController();
$response = $controller->getAllStudent();

echo json_encode($response);
?>