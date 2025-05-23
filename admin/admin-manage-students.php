<?php
require_once '../controller/ManageStudentController.php';
$manageStudentController = new ManageStudentController();
// Khởi tạo biến phản hồi
$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $action = isset($_POST['action']) ? $_POST['action'] : null;
  if ($action == 'add') {
    // Xử lý thêm học sinh
    $studentData = [
      'fullname' => $_POST['student-fullname'],
      'password' => $_POST['student-password'],
      'username' => $_POST['student-username'],
      'email' => $_POST['student-email'],
      'role' => 'student', // Thêm học sinh có vai trò là student
    ];

    // Gọi hàm thêm học sinh
    $result = $manageStudentController->createStudent($studentData);

    if ($result) {
      $response['success'] = true;
      $response['message'] = 'Thêm học sinh thành công!';
    } else {
      $response['message'] = 'Thêm học sinh thất bại!';
    }
  } elseif ($action == 'edit') {
    // Xử lý cập nhật học sinh
    $studentId = $_POST['studentId'];

    $studentData = [
      'fullname' => $_POST['fullname'],
      'password' => $_POST['password'],
      'username' => $_POST['username'],
      'email' => $_POST['email'],
    ];

    // Gọi hàm chỉnh sửa học sinh
    $result = $manageStudentController->editStudent($studentId, $studentData);

    if ($result) {
      $response['success'] = true;
      $response['message'] = 'Cập nhật học sinh thành công!';
    } else {
      $response['message'] = 'Cập nhật học sinh thất bại!';
    }
  } elseif ($action == 'delete') {
    // Xử lý xóa học sinh
    $studentId = $_POST['studentId'];

    // Gọi hàm xóa học sinh
    $result = $manageStudentController->deleteStudent($studentId);

    if ($result) {
      $response['success'] = true;
      $response['message'] = 'Xóa học sinh thành công!';
    } else {
      $response['message'] = 'Xóa học sinh thất bại!';
    }
  }

  // Đảm bảo trả về JSON
  header('Content-Type: application/json');
  echo json_encode($response);
  exit;
}
$page_title = "Quản Lý Học Sinh";
include '../template/head-admin.php';
$listStudents = $manageStudentController->getAllStudent();
?>



<div class="">
  <?php
  $current_page = 'manage_students';
  $role = 'admin';
  include '../template/header-admin.php';
  ?>

  <!-- Main Content -->
  <div class="main-content">
    <div class="page-header">
      <h1><i class="fa-solid fa-book-open-reader"></i> Quản lý học sinh</h1>
      <div class="header-actions">
        <button class="btn btn-primary" id="add-student-btn">
          <i class="fas fa-user-plus"></i> Thêm học sinh
        </button>
      </div>
    </div>

    <!-- Search Box -->
    <div class="search-box">
      <input type="text" placeholder="Tìm kiếm học sinh..." id="student-search">
    </div>

    <!-- Students Table -->
    <div class="card">
      <div class="table-responsive">
        <table class="users-table">
          <thead>
            <tr>
              <th>ID</th>
              <th>Họ tên</th>
              <th>Username</th>
              <th>Email</th>
              <th>Thao tác</th>
            </tr>
          </thead>
          <tbody>
            <?php if ($listStudents && $listStudents['success']) : ?>
              <?php foreach ($listStudents['data'] as $student) : ?>
                <?php
                // Chuyển đổi BSONDocument thành mảng
                $studentArray = $student->getArrayCopy();
                ?>
                <tr>
                  <td><?php echo $studentArray['_id']; ?></td>
                  <td><?php echo $studentArray['fullname']; ?></td>
                  <td><?php echo $studentArray['username']; ?></td>
                  <td><?php echo $studentArray['email']; ?></td>
                  <td>
                    <button class="btn btn-sm btn-edit"
                      data-email="<?php echo $studentArray['email']; ?>"
                      data-username="<?php echo $studentArray['username']; ?>"
                      data-fullname="<?php echo $studentArray['fullname']; ?>"
                      data-password="<?php echo $studentArray['password']; ?>"
                      data-id="<?php echo $studentArray['_id']; ?>">
                      <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-sm btn-reject" data-id="<?php echo $studentArray['_id']; ?>">
                      <i class="fas fa-trash-alt"></i>
                    </button>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php else : ?>
              <tr>
                <td colspan="5">Không có học sinh nào trong cơ sở dữ liệu.</td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div class="pagination">
        <button class="btn btn-outline" disabled>
          <i class="fas fa-chevron-left"></i>
        </button>
        <button class="btn btn-outline active">1</button>
        <button class="btn btn-outline">2</button>
        <button class="btn btn-outline">3</button>
        <button class="btn btn-outline">
          <i class="fas fa-chevron-right"></i>
        </button>
      </div>
    </div>

    <!-- Add Student Modal -->
    <div class="modal" id="add-student-modal">
      <div class="modal-content">
        <div class="modal-header">
          <h3><i class="fas fa-user-plus"></i> Thêm học sinh mới</h3>
          <button class="btn btn-icon close-modal">
            <i class="fas fa-times"></i>
          </button>
        </div>
        <div class="modal-body">
          <form id="add-student-form">
            <div class="form-group">
              <label for="student-fullname">Họ và tên <span class="required">*</span></label>
              <input type="text" name="student-fullname" id="student-fullname" required>
            </div>
            <div class="form-group">
              <label for="student-password">Password <span class="required">*</span></label>
              <input type="text" name="student-password" id="student-password" required>
            </div>
            <div class="form-group">
              <label for="student-username">Username <span class="required">*</span></label>
              <input type="text" name="student-username" id="student-username" required>
            </div>
            <div class="form-group">
              <label for="student-email">Email <span class="required">*</span></label>
              <input type="email" name="student-email" id="student-email" required>
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button class="btn btn-outline close-modal">Hủy bỏ</button>
          <button class="btn btn-primary" form="add-student-form">Thêm học sinh</button>
        </div>
      </div>
    </div>

    <!-- Edit Student Modal -->
    <div class="modal" id="edit-student-modal">
      <div class="modal-content">
        <div class="modal-header">
          <h3><i class="fas fa-user-edit"></i> Chỉnh sửa thông tin học sinh</h3>
          <button class="btn btn-icon close-modal">
            <i class="fas fa-times"></i>
          </button>
        </div>
        <div class="modal-body">
          <form id="edit-student-form" method="POST" action="admin-manage-students.php">
            <input type="hidden" name="studentId" id="edit-student-id">
            <div class="form-group">
              <label>Họ và tên</label>
              <input type="text" name="fullname" id="edit-student-fullname" class="form-control" required>
            </div>
            <div class="form-group">
              <label>Mật khẩu</label>
              <input type="text" name="password" id="edit-student-password" class="form-control">
            </div>
            <div class="form-group">
              <label for="student-email">Username <span class="required">*</span></label>
              <input type="text" name="username" id="edit-student-username" required>
            </div>
            <div class="form-group">
              <label>Email</label>
              <input type="email" name="email" id="edit-student-email" class="form-control" required>
            </div>
          </form>

        </div>
        <div class="modal-footer">
          <button class="btn btn-outline close-modal">Đóng</button>
          <button name="updateStudent" class="btn btn-primary" id="save-student-changes">Lưu thay đổi</button>
        </div>
      </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal" id="delete-student-modal">
      <div class="modal-content">
        <div class="modal-header">
          <h3><i class="fas fa-exclamation-triangle"></i> Xác nhận xóa</h3>
          <button class="btn btn-icon close-modal">
            <i class="fas fa-times"></i>
          </button>
        </div>
        <div class="modal-body">
          <p>Bạn có chắc chắn muốn xóa học sinh này? Hành động này không thể hoàn tác.</p>
          <input type="hidden" id="delete-student-id">
        </div>
        <div class="modal-footer">
          <button class="btn btn-outline close-modal">Hủy</button>
          <button class="btn btn-danger" id="confirm-delete-student">Xóa</button>
        </div>
      </div>
    </div>
  </div>
</div>



<?php include '../template/footer-admin.php'; ?>