<?php
require_once __DIR__ . '/../model/Database.php';  // Kết nối với Database

class UserModel
{
    private $collection;

    public function __construct()
    {
        $this->collection = Database::getInstance()->getDB()->users;

        // Tạo chỉ mục unique cho username (chỉ cần chạy 1 lần, MongoDB sẽ ghi nhớ)
        $this->collection->createIndex(['username' => 1], ['unique' => true]);
    }

    //Tuấn Đạt
    public function createUser($data)
    {
        try {
            $existingUser = $this->collection->findOne(['username' => $data['username']]);

            if ($existingUser) {
                return ['success' => false, 'message' => 'Username đã tồn tại'];
            }

            $result = $this->collection->insertOne($data);

            if ($result->getInsertedCount() > 0) {
                return [
                    'success' => true,
                    'inserted_id' => (string) $result->getInsertedId()
                ];
            } else {
                return ['success' => false, 'message' => 'Không thể thêm người dùng'];
            }
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Lỗi: ' . $e->getMessage()];
        }
    }

    //Tuấn Đạt
    public function updateUser($userId, $data)
    {
        try {

            $existingUser = $this->collection->findOne(['_id' => new MongoDB\BSON\ObjectId($userId)]);
            if (!$existingUser) {
                return ['success' => false, 'message' => 'Người dùng không tồn tại'];
            }

            $updateResult = $this->collection->updateOne(
                ['_id' => new MongoDB\BSON\ObjectId($userId)],
                ['$set' => $data]
            );

            if ($updateResult->getModifiedCount() > 0) {
                return ['success' => true, 'message' => 'Cập nhật thông tin thành công'];
            } else {
                return ['success' => false, 'message' => 'Không có thay đổi nào được thực hiện'];
            }
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Lỗi: ' . $e->getMessage()];
        }
    }

    //Tuấn Đạt
    public function deleteUser($userId)
    {
        try {

            $existingUser = $this->collection->findOne(['_id' => new MongoDB\BSON\ObjectId($userId)]);

            if (!$existingUser) {
                return ['success' => false, 'message' => 'Người dùng không tồn tại'];
            }

            $deleteResult = $this->collection->deleteOne(['_id' => new MongoDB\BSON\ObjectId($userId)]);

            if ($deleteResult->getDeletedCount() > 0) {
                return ['success' => true, 'message' => 'Xóa người dùng thành công'];
            } else {
                return ['success' => false, 'message' => 'Không thể xóa người dùng'];
            }
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Lỗi: ' . $e->getMessage()];
        }
    }

    // Nhu Y cap nhat (11/05/2025)
    public function getUsersByRole($role)
    {
        try {
            $cursor = $this->collection->find(['role' => $role]);

            $usersArray = iterator_to_array($cursor, false);

            foreach ($usersArray as &$user) {
                $user['_id'] = (string) $user['_id']; 
            }

            if (!empty($usersArray)) {
                return [
                    'success' => true,
                    'data' => $usersArray
                ];
            } else {
                return ['success' => false, 'message' => 'Không tìm thấy người dùng với vai trò này'];
            }
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Lỗi: ' . $e->getMessage()];
        }
    }

    // Nhu Y
    public function login($username, $password, $role)
    {
        try {
            // Tìm người dùng trong MongoDB với username, password và role
            $user = $this->collection->findOne(['username' => $username, 'password' => $password, 'role' => $role]);
            if ($user) {
                return [
                    'success' => true,
                    'role' => $user['role'],         // Trả về vai trò của người dùng
                    'id' => $user['_id'],            // Trả về id của người dùng
                    'fullname' => $user['fullname']  // Trả về fullname của người dùng
                ];
            } else {
                return ['success' => false, 'message' => 'Thông tin đăng nhập không chính xác'];
            }
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Lỗi: ' . $e->getMessage()];
        }
    }

    // Hoang Anh
    public function getUserByName($username)
    {
        try {
            // Tìm người dùng theo username
            $user = $this->collection->findOne(['username' => $username]);

            if ($user) {
                return [
                    'success' => true,
                    'data' => $user
                ];
            } else {
                return ['success' => false, 'message' => 'Không tìm thấy người dùng'];
            }
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Lỗi: ' . $e->getMessage()];
        }
    }

    //Hoang Anh
    public function getUserById($userID)
    {
        try {
            $user = $this->collection->findOne(['_id' => new MongoDB\BSON\ObjectId($userID)]);

            if ($user) {
                return $user;
            } else {
                return null;
            }
        } catch (Exception $e) {
            return null;
        }
    }

}
?>