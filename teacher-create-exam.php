<?php
$page_title = "Tạo kỳ thi mới";
include 'template/head.php';
?>

<div class="teacher-dashboard">
  <?php
  $current_page = 'create_exam';
  $role = 'teacher';
  include 'template/header.php';
  ?>
  <!-- Main Content -->
  <div class="main-content">
    <div class="page-header">
      <h1><i class="fas fa-plus-circle"></i> Tạo đề thi mới</h1>
    </div>

    <!-- Exam Creation Form -->
    <div class="exam-creation-form">
      <form id="createExamForm">
        <!-- Basic Information -->
        <div class="form-section">
          <div class="section-header">
            <h3><i class="fas fa-info-circle"></i> Thông tin cơ bản</h3>
          </div>
          <div class="form-grid">
            <div class="form-group">
              <label for="exam-name">Tên kỳ thi <span class="required">*</span></label>
              <input type="text" id="exam-name" name="exam-name" placeholder="VD: Kiểm tra Toán giữa kỳ" required>
            </div>
            <div class="form-group">
              <label for="exam-description">Mô tả kỳ thi <span class="required">*</span></label>
              <textarea id="exam-description" name="exam-description" placeholder="VD: Đây là bài thi cơ bản"
                required></textarea>
            </div>
          </div>
        </div>

        <!-- Date & Time -->
        <div class="form-section">
          <div class="section-header">
            <h3><i class="fas fa-calendar-alt"></i> Thời gian</h3>
          </div>
          <div class="form-grid">
            <div class="form-group">
              <label for="exam-date">Ngày thi <span class="required">*</span></label>
              <input type="date" id="exam-date" name="exam-date" required>
            </div>
            <div class="form-group">
              <label for="exam-time">Giờ bắt đầu <span class="required">*</span></label>
              <input type="time" id="exam-time" name="exam-time" required>
            </div>
            <div class="form-group">
              <label for="exam-duration">Thời lượng (phút) <span class="required">*</span></label>
              <input type="number" id="exam-duration" name="exam-duration" min="5" max="180" value="45" required>
            </div>
          </div>
        </div>

        <!-- Questions Section -->
        <div class="form-section">
          <div class="section-header">
            <h3><i class="fas fa-question-circle"></i> Câu hỏi</h3>
            <button type="button" class="btn btn-primary btn-sm" id="add-question">
              <i class="fas fa-plus"></i> Thêm câu hỏi
            </button>
          </div>

          <!-- Question List -->
          <div class="question-list" id="question-list">
            <div class="question-item" data-question-id="1">
              <div class="question-header">
                <span class="question-number">Câu 1</span>
                <button type="button" class="btn btn-danger btn-sm remove-question">
                  <i class="fas fa-trash"></i> Xóa
                </button>
              </div>
              <div class="form-group">
                <label>Nội dung câu hỏi</label>
                <textarea class="question-content" placeholder="Nhập nội dung câu hỏi..."></textarea>
              </div>

              <div class="question-options">
                <div class="option-item">
                  <input type="radio" name="q1-answer" class="option-correct">
                  <input type="text" class="option-text" placeholder="Lựa chọn 1">
                  <button type="button" class="btn btn-sm btn-outline remove-option">
                    <i class="fas fa-times"></i>
                  </button>
                </div>
                <div class="option-item">
                  <input type="radio" name="q1-answer" class="option-correct">
                  <input type="text" class="option-text" placeholder="Lựa chọn 2">
                  <button type="button" class="btn btn-sm btn-outline remove-option">
                    <i class="fas fa-times"></i>
                  </button>
                </div>
                <button type="button" class="btn btn-sm btn-outline add-option">
                  <i class="fas fa-plus"></i> Thêm lựa chọn
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Students Section -->
        <div class="form-section">
          <div class="section-header">
            <h3><i class="fas fa-users"></i> Chọn học sinh tham gia</h3>
            <button type="button" class="btn btn-primary btn-sm" id="add-selected-students">
              <i class="fas fa-user-plus"></i> Thêm học sinh đã chọn
            </button>
          </div>
          <div class="search-filter">
            <input type="text" placeholder="Tìm kiếm học sinh..." class="search-input" id="teacher-search-student">
          </div>

          <!-- All Students List -->
          <div class="all-students-list">
            <div class="table-responsive">
              <table class="student-table">
                <thead>
                  <tr>
                    <th width="50px">Chọn</th>
                    <th>Họ và tên</th>
                    <th>Username</th>
                    <th>Email</th>
                  </tr>
                </thead>
                <tbody id="all-students-list">
                  <!-- Students will be loaded here -->
                  <tr>
                    <td colspan="4" class="text-center">Đang tải danh sách học sinh...</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>

          <!-- Selected Students -->
          <div class="selected-students-section">
            <h4><i class="fas fa-user-check"></i> Học sinh đã chọn (<span id="selected-count">0</span>)</h4>
            <div class="selected-list" id="selected-students-list">
              <div class="no-selection">Chưa có học sinh nào được chọn</div>
            </div>
          </div>
        </div>

        <!-- Form Actions -->
        <div class="form-actions">
          <button type="submit" class="btn btn-primary">
            <i class="fas fa-check-circle"></i> Tạo kỳ thi
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<?php include 'template/footer.php'; ?>