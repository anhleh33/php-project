<?php
require_once __DIR__ . '/../model/UserModel.php';

//Hoang Anh
class GetInfoController
{
    public function getInformation($username)
    {
        $userModel = new UserModel();
        $result = $userModel->getUserByName($username);

        if ($result['success']) {
            return $result['data'];
        } else {
            return null;
        }
    }

    public function getNameByID($userID)
    {
        $userModel = new UserModel();

        $user = $userModel->getUserById($userID);

        if ($user && isset($user['fullname'])) {
            return $user['fullname'];
        }

        return null;
    }
}
?>