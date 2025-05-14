<?php
session_start();

$page_title = "Bài thi của tôi";
include 'template/head.php';

require_once 'controller/UpdateExamController.php';
require_once 'controller/GetInfoController.php';
require_once 'controller/GetResultController.php';

$ExamController = new UpdateExamController();
$ResultController = new GetResultController();
$StudentController = new GetInfoController();

$studentID = $StudentController->getInformation($_SESSION['username'])['_id'];
$examList = $ExamController->ExamListForStudent($studentID);
?>

<div>
  <?php
  $current_page = 'exam';
  $role = 'student';
  include 'template/header.php';
  ?>

  <!-- Main Content -->
  <div class="main-content">
    <div class="page-header">
      <h1><i class="fas fa-clipboard-list"></i> Bài thi của tôi</h1>
      <div class="filter-options">
        <div class="filter-group"></div>
      </div>
    </div>
    <div class="search-box">
      <input id="exam-search" type="text" placeholder="Tìm kiếm bài thi...">
    </div>

    <!-- Exam Tabs -->
    <div class="exam-tabs">
      <div class="tab active" data-tab="all">
        <i class="fas fa-list"></i>
        <span>Tất cả</span>
      </div>
      <div class="tab" data-tab="upcoming">
        <i class="fas fa-calendar-check"></i>
        <span>Sắp diễn ra</span>
      </div>
      <div class="tab" data-tab="ongoing">
        <i class="fas fa-clock"></i>
        <span>Đang diễn ra</span>
      </div>
      <div class="tab" data-tab="completed">
        <i class="fas fa-check-circle"></i>
        <span>Đã hoàn thành</span>
      </div>
    </div>

    <!-- Exams List -->
    <div class="exams-container">
      <?php foreach ($examList as $exam):
        $duration = $ExamController->getDuration($exam);
        $status = $ExamController->getStatus($exam);
        $question = $ExamController->countQuestions($exam);
        $start = $ExamController->formatStartTime($exam);
        $isAvailable = $ResultController->isAvailable($exam['_id'], $studentID);
        ?>
        <div class="exam-card <?= $status ?>" data-tab="<?= $status ?>">
          <div class="exam-header">
            <h3><?= $exam['name'] ?></h3>
          </div>
          <div class="exam-details">
            <div class="detail-item">
              <i class="fas fa-calendar-day"></i>
              <span><?= $start ?></span>
            </div>
            <div class="detail-item">
              <i class="fas fa-clock"></i>
              <span><?= $duration ?> phút</span>
            </div>
            <div class="detail-item">
              <i class="fas fa-question-circle"></i>
              <span><?= $question ?> câu hỏi</span>
            </div>
          </div>
          <!-- Tùy thuộc vào status để hiện button -->
          <div class="exam-actions">
            <?php if ($status === 'upcoming'): ?>
              <button class="btn btn-disabled">
                <i class="fas fa-lock"></i> Chưa đến giờ làm bài
              </button>

            <?php elseif ($status === 'ongoing' && $isAvailable): ?>
              <button id="confirm-doing-exam" class="btn btn-start open-modal" data-duration="<?= $duration ?>"
                data-exam-id="<?= $exam['_id'] ?>" data-question="<?= $question ?>" user-id="<?= $studentID ?>">
                <i class="fas fa-play"></i> Bắt đầu làm bài
              </button>

            <?php else: ?>
              <?php if (!$isAvailable): ?>
                <a class="btn btn-primary" style="width: 100%"
                  href="student-exam-detail.php?exam_id=<?= $exam['_id'] ?>&student_id=<?= $studentID ?>">
                  <i class="fas fa-rotate-right"></i> Xem lại
                </a>
              <?php else: ?>
                <button class="btn btn-primary" style="width: 100%" onclick="showNotification()">
                  <i class="fas fa-rotate-right"></i> Xem lại
                </button>
              <?php endif; ?>
            <?php endif; ?>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</div>

<!-- Modal Thông báo học sinh chưa tham gia kì thi -->
<div id="notification" class="custom-toast hidden">
  <i class="fas fa-info-circle"></i> Bạn chưa làm bài thi này.
</div>

<!-- Modal Xác nhận bắt đầu bài thi -->
<div class="modal" id="start-exam-modal">
  <div class="modal-content">
    <div class="modal-header">
      <h3><i class="fas fa-play-circle"></i> Bắt đầu bài thi</h3>
    </div>
    <div class="modal-body">
      <div class="exam-rules">
        <h4>Quy định bài thi:</h4>
        <ul>
          <li><i class="fas fa-check-circle"></i> Thời gian làm bài: <strong id="modal-duration">?</strong></li>
          <li><i class="fas fa-check-circle"></i> Số câu hỏi: <strong id="modal-question">?</strong></li>
          <li><i class="fas fa-check-circle"></i> Không được mở tab khác trong trình duyệt</li>
          <li><i class="fas fa-check-circle"></i> Bài thi sẽ tự động nộp khi hết giờ</li>
        </ul>
        <div class="agree-rules">
          <input type="checkbox" id="agree-rules">
          <label for="agree-rules">Tôi đồng ý với các quy định trên</label>
        </div>
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-outline close-modal">Hủy bỏ</button>
      <button class="btn btn-primary" id="confirm-start-exam" disabled>Bắt đầu thi</button>
    </div>
  </div>
</div>
<?php include 'template/footer.php'; ?>