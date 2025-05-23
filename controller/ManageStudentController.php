<?php
require_once __DIR__ . '/../model/UserModel.php';  // Kết nối với UserModel
session_start();
class ManageStudentController {
    public function getAllStudent(){
        $userModel = new UserModel();
        try {
            $result = $userModel->getUsersByRole("student");
            if($result['success']){
                return ['success'=> true, 'data' => $result['data']];
            }else{
                return null;
            }
        }
        catch (Exception $e) {
            return ['success' => false, 'message' => 'An error occurred: ' . $e->getMessage()];
        }
    }
    public function editStudent($id, $data){
        $userModel = new UserModel();
        $result = $userModel->updateUser($id, $data);
        if($result['success']){
            return $result['success'];
        }else{
            return $result['message'];
        }
    }
    public function createStudent($data){
        $userModel = new UserModel();
        $result = $userModel->createUser($data);
        if($result['success']){
            return $result['success'];
        }else{
            return $result['message'];
        }
    }
    public function deleteStudent($id){
        $userModel = new UserModel();
        $result = $userModel->deleteUser($id);
        if($result['success']){
            return $result['success'];
        }else{
            return $result['message'];
        }
    }
}
?>
