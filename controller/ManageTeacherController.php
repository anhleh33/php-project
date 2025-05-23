<?php
require_once __DIR__ . '/../model/UserModel.php';  // Kết nối với UserModel
session_start();
class ManageTeacherController {
    public function getAllTeacher(){
        $userModel = new UserModel();
        $result = $userModel->getUsersByRole("teacher");
        if($result['success']){
            return ['success'=> true, 'data' => $result['data']];
        }else{
            return null;
        }
    }
    public function editTeacher($id, $data){
        $userModel = new UserModel();
        $result = $userModel->updateUser($id, $data);
        if($result['success']){
            return $result['success'];
        }else{
            return $result['message'];
        }
    }
    public function createTeacher($data){
        $userModel = new UserModel();
        $result = $userModel->createUser($data);
        if($result['success']){
            return $result['success'];
        }else{
            return $result['message'];
        }
    }
    public function deleteTeacher($id){
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
