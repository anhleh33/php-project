<?php
session_start();

$page_title = "Kết quả của tôi";
include 'template/head.php';

require_once 'controller/UpdateExamController.php';
require_once 'controller/GetInfoController.php';

$ExamController = new UpdateExamController();
$StudentController = new GetInfoController();
$studentID = $StudentController->getInformation($_SESSION['username'])['_id'];
$ongoing = count($ExamController->getOngoingExams($studentID));
$upcoming = count($ExamController->getUpcomingExams($studentID));

$results = $ExamController->getResultsByStudent($studentID); // Nhu Y
?>

<div>
  <?php
  $current_page = 'result';
  $role = 'student';
  include 'template/header.php';
  ?>

  <!-- Main Content -->
  <div class="main-content">
    <div class="page-header">
      <h1><i class="fas fa-poll"></i> Kết quả của tôi</h1>
    </div>

    <!-- Results Summary -->
    <div class="stats-row">
      <div class="stat-card">
        <div class="stat-icon bg-primary">
          <i class="fas fa-clipboard-list"></i>
        </div>
        <div class="stat-info">
          <h3><?= $upcoming ?></h3>
          <p>Kỳ thi sắp tới</p>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-icon bg-warning">
          <i class="fa-regular fa-star"></i>
        </div>
        <div class="stat-info">
          <h3><?= $ongoing ?></h3>
          <p>Kỳ thi đang diễn ra</p>
        </div>
      </div>
    </div>

    <!-- Results Table -->
    <div class="results-table-container">
      <table class="results-table">
        <thead>
          <tr>
            <th>Tên kỳ thi</th>
            <th>Ngày thi</th>
            <th>Điểm số</th>
            <th>Thao tác</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!isset($results['success'])): ?>
            <?php foreach ($results as $result): ?>
              <tr>
                <td>
                  <div class="exam-name">
                    <h4><?php echo $result['exam_name']; ?></h4>
                    <p>GV: <?php echo $result['teacher_name']; ?></p>
                  </div>
                </td>
                <td><?php echo $result['exam_date']->format('d/m/Y'); ?></td>
                <td>
                  <div class="score-display">
                    <span class="score-value"><?php echo $result['score']; ?></span>
                    <span class="score-max">/100</span>
                    <div class="score-bar">
                      <div class="score-progress" style="width: <?php echo $result['score']; ?>%"></div>
                    </div>
                  </div>
                </td>
                <td>
                  <a href="student-exam-detail.php?exam_id=<?php echo $result['exam_id']; ?>&student_id=<?php echo $studentID ?>"
                    class="btn btn-sm btn-primary">
                    <i class="fas fa-eye"></i> Xem chi tiết
                  </a>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr>
              <td colspan="4" class="text-center">Không có kết quả nào.</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<?php include 'template/footer.php'; ?>