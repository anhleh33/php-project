<?php
require_once '../controller/ManageTeacherController.php';
$manageTeacherController = new ManageTeacherController();
// Khởi tạo biến phản hồi
$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $action = isset($_POST['action']) ? $_POST['action'] : null;
  if ($action == 'add') {
    // Xử lý thêm giáo viên
    $teacherData = [
      'fullname' => $_POST['teacher-fullname'],
      'password' => $_POST['teacher-password'],
      'username' => $_POST['teacher-username'],
      'email' => $_POST['teacher-email'],
      'role' => 'teacher', // Thêm giáo viên có vai trò là teacher
    ];
    // Gọi hàm thêm giáo viên
    $result = $manageTeacherController->createTeacher($teacherData);

    
    if ($result) {
      $response['success'] = true;
      $response['message'] = 'Thêm giáo viên thành công!';
    } else {
      $response['message'] = 'Thêm giáo viên thất bại!';
    }
  } elseif ($action == 'edit') {
    // Xử lý cập nhật giáo viên
    $teacherId = $_POST['teacherId'];

    $teacherData = [
      'fullname' => $_POST['fullname'],
      'password' => $_POST['password'],
      'username' => $_POST['username'],
      'email' => $_POST['email'],
    ];

    // Gọi hàm chỉnh sửa giáo viên
    $result = $manageTeacherController->editTeacher($teacherId, $teacherData);

    if ($result) {
      $response['success'] = true;
      $response['message'] = 'Cập nhật giáo viên thành công!';
    } else {
      $response['message'] = 'Cập nhật giáo viên thất bại!';
    }
  } elseif ($action == 'delete') {
    // Xử lý xóa giáo viên
    $teacherId = $_POST['teacherId'];

    // Gọi hàm xóa giáo viên
    $result = $manageTeacherController->deleteTeacher($teacherId);

    if ($result) {
      $response['success'] = true;
      $response['message'] = 'Xóa giáo viên thành công!';
    } else {
      $response['message'] = 'Xóa giáo viên thất bại!';
    }
  }

  // Đảm bảo trả về JSON
  header('Content-Type: application/json');
  echo json_encode($response);
  exit;
}

$page_title = "Quản Lý Giáo Viên";
include '../template/head-admin.php';
$listTeacher = $manageTeacherController->getAllTeacher();
?>


<div class="">
  <?php
  $current_page = 'manage_teachers';
  $role = 'admin';
  include '../template/header-admin.php';
  ?>

  <!-- Main Content -->
  <div class="main-content">
    <div class="page-header">
      <h1><i class="fas fa-chalkboard-teacher"></i> Quản lý giáo viên</h1>
      <div class="header-actions">
        <button class="btn btn-primary" id="add-teacher-btn">
          <i class="fas fa-user-plus"></i> Thêm giáo viên
        </button>
      </div>
    </div>

    <!-- Search Box -->
    <div class="search-box">
      <input type="text" placeholder="Tìm kiếm giáo viên..." id="teacher-search">
    </div>

    <!-- Teachers Table -->
    <div class="card">
      <div class="table-responsive">
        <table class="users-table">
          <thead>
            <tr>
              <th>ID</th>
              <th>Thông tin</th>
              <th>Username</th>
              <th>Email</th>
              <th>Thao tác</th>
            </tr>
          </thead>
          <tbody>
          <tbody>
            <?php if ($listTeacher && $listTeacher['success']) : ?>
              <?php foreach ($listTeacher['data'] as $student) : ?>
                <?php
                // Chuyển đổi BSONDocument thành mảng
                $teacherArray = $student->getArrayCopy();
                ?>
                <tr>
                  <td><?php echo $teacherArray['_id']; ?></td>
                  <td><?php echo $teacherArray['fullname']; ?></td>
                  <td><?php echo $teacherArray['username']; ?></td>
                  <td><?php echo $teacherArray['email']; ?></td>
                  <td>
                    <button class="btn btn-sm btn-edit"
                      data-email="<?php echo $teacherArray['email']; ?>"
                      data-username="<?php echo $teacherArray['username']; ?>"
                      data-fullname="<?php echo $teacherArray['fullname']; ?>"
                      data-password="<?php echo $teacherArray['password']; ?>"
                      data-id="<?php echo $teacherArray['_id']; ?>">
                      <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-sm btn-reject" data-id="<?php echo $teacherArray['_id']; ?>">
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

    <!-- Add Teacher Modal -->
    <div class="modal" id="add-teacher-modal">
      <div class="modal-content">
        <div class="modal-header">
          <h3><i class="fas fa-user-plus"></i> Thêm giáo viên mới</h3>
          <button class="btn btn-icon close-modal">
            <i class="fas fa-times"></i>
          </button>
        </div>
        <div class="modal-body">
          <form id="add-teacher-form">
            <div class="form-group">
              <label for="teacher-name">Họ và tên <span class="required">*</span></label>
              <input type="text" name="teacher-fullname" id="teacher-name" required>
            </div>
            <div class="form-group">
              <label for="teacher-password">Password <span class="required">*</span></label>
              <input type="text" name="teacher-password" id="teacher-password" required>
            </div>
            <div class="form-group">
              <label for="teacher-username">Username<span class="required">*</span></label>
              <input type="text" name="teacher-username" id="teacher-username" required>
            </div>
            <div class="form-group">
              <label for="teacher-email">Email <span class="required">*</span></label>
              <input type="email" name="teacher-email" id="teacher-email" required>
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button class="btn btn-outline close-modal">Hủy bỏ</button>
          <button class="btn btn-primary" form="add-teacher-form">Thêm giáo viên</button>
        </div>
      </div>
    </div>

    <!-- Edit Teacher Modal -->
    <div class="modal" id="edit-teacher-modal">
      <div class="modal-content">
        <div class="modal-header">
          <h3><i class="fas fa-user-edit"></i> Chỉnh sửa thông tin giáo viên</h3>
          <button class="btn btn-icon close-modal">
            <i class="fas fa-times"></i>
          </button>
        </div>
        <div class="modal-body">
          <form id="edit-teacher-form" method="POST" action="admin-manage-teachers.php>">
            <input name="teacherId" type="hidden" id="edit-teacher-id">
            <div class="form-group">
              <label>Họ và tên</label>
              <input type="text" name="fullname" id="edit-teacher-fullname" class="form-control" required>
            </div>
            <div class="form-group">
              <label>Password</label>
              <input type="text" name="password" id="edit-teacher-password" class="form-control" required>
            </div>
            <div class="form-group">
              <label>Username</label>
              <input type="text" name="username" id="edit-teacher-username" class="form-control" required>
            </div>
            <div class="form-group">
              <label>Email</label>
              <input type="email" name="email" id="edit-teacher-email" class="form-control" required>
            </div>

          </form>
        </div>
        <div class="modal-footer">
          <button class="btn btn-outline close-modal">Đóng</button>
          <button class="btn btn-primary" id="save-teacher-changes">Lưu thay đổi</button>
        </div>
      </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal" id="delete-teacher-modal">
      <div class="modal-content">
        <div class="modal-header">
          <h3><i class="fas fa-exclamation-triangle"></i> Xác nhận xóa</h3>
          <button class="btn btn-icon close-modal">
            <i class="fas fa-times"></i>
          </button>
        </div>
        <div class="modal-body">
          <p>Bạn có chắc chắn muốn xóa giáo viên này? Hành động này không thể hoàn tác.</p>
          <input type="hidden" id="delete-teacher-id">
        </div>
        <div class="modal-footer">
          <button class="btn btn-outline close-modal">Hủy</button>
          <button class="btn btn-danger" id="confirm-delete-teacher">Xóa</button>
        </div>
      </div>
    </div>
  </div>
</div>


<?php include '../template/footer-admin.php'; ?>