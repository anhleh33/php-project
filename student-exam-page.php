<?php
session_start();
$page_title = "Làm bài thi";
include 'template/head.php';

require_once 'controller/UpdateExamController.php';
require_once 'controller/GetInfoController.php';

$ExamController = new UpdateExamController();
$UserController = new GetInfoController();
$exam = $ExamController->getExaminExamList($_GET['exam_id'], $_GET['student_id']);
$teacherName = $UserController->getNameByID($exam->teacher_id);
$questions = $ExamController->getQuestions($exam);
?>

<div style="height: 97vh;">
  <div id="exam-data" data-exam-id=<?= $_GET['exam_id'] ?> data-student-id=<?= $_GET['student_id'] ?>
    data-teacher-id="<?= htmlspecialchars($exam->teacher_id) ?>" data-questions=<?= count($exam->questions) ?>>
  </div>

  <div>
    <!-- Main Content -->
    <div>
      <!-- Exam Header with Timer -->
      <div class="exam-header" style="display: grid; grid-column: 1;">
        <div class="exam-title">
          <h2 style="font-size: 45px;"><i class="fas fa-clipboard-question"></i> <?= $exam->name ?></h2>
          <p class="exam-meta" style="font-size: 20px; margin-left: 35px;">
            <span><i class="fas fa-user-tie"></i> GV: <?= $teacherName ?></span>
            <span><i class="fas fa-question-circle"></i> <?= count($exam->questions) ?> câu hỏi</span>
          </p>
        </div>
        <div class="exam-timer">
          <div class="timer-display">
            <i class="fas fa-clock"></i>
            <span id="time-remaining"><?= $ExamController->getDuration($exam) ?>:00</span>
          </div>
          <button class="btn btn-submit-exam">
            <i class="fas fa-paper-plane"></i> Nộp bài
          </button>
        </div>
      </div>

      <!-- Exam Content -->
      <div class="exam-content">
        <!-- Question Navigation Sidebar -->
        <div class="question-nav">
          <div class="nav-header">
            <h4>Danh sách câu hỏi</h4>
            <div class="question-count">
              <span class="answered">0</span>/<span id="number-of-questions"
                class="total"><?= count($exam->questions) ?></span>
            </div>
          </div>
          <div class="question-grid">
            <?php for ($i = 1; $i <= count($exam->questions); $i++): ?>
              <div class="question-number <?= $i == 1 ? 'active' : '' ?> <?= $i <= 5 ? 'answered' : '' ?>"
                data-qid="<?= $i ?>">
                <?= $i ?>
              </div>
            <?php endfor; ?>
          </div>
          <div class="nav-legend">
            <div><span class="legend-box current"></span> Đang xem</div>
            <div><span class="legend-box answered"></span> Đã trả lời</div>
            <div><span class="legend-box unanswered"></span> Chưa trả lời</div>
          </div>
        </div>

        <!-- Question Display Area -->
        <div class="question-area">
          <!-- Current Question -->
          <?php foreach ($questions as $index => $q): ?>
            <div class="question-current" id="question-<?= $index ?>" style="<?= $index !== 0 ? 'display:none;' : '' ?>">
              <div class="question-header">
                <h3>Câu hỏi số <span class="q-number"><?= $index + 1 ?></span>:</h3>
              </div>

              <div class="question-text" style="font-size: 23px;">
                <p><?= htmlspecialchars($q->question_text) ?></p>
              </div>

              <div class="question-options" style="font-size: 18px; border:none;">
                <?php foreach ($q->options as $optIndex => $option): ?>
                  <?php
                  $optionLabel = chr(65 + $optIndex);
                  $inputId = "q{$index}-option{$optIndex}";
                  ?>
                  <div class="option">
                    <input type="radio" name="q<?= $index ?>" id="<?= $inputId ?>" value="<?= $optIndex ?>">
                    <label for="<?= $inputId ?>"><?= $optionLabel ?>. <?= htmlspecialchars($option) ?></label>
                  </div>
                <?php endforeach; ?>
              </div>
            </div>
          <?php endforeach; ?>

          <!-- Navigation Buttons -->
          <div class="question-navigation">
            <button class="btn btn-outline" id="prev-question">
              <i class="fas fa-arrow-left"></i> Câu trước
            </button>
            <button class="btn btn-primary" id="next-question">
              Câu sau <i class="fas fa-arrow-right"></i>
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal xác nhận nộp bài -->
  <div class="modal" id="submit-exam-modal">
    <div class="modal-content">
      <div class="modal-header">
        <h3><i class="fas fa-paper-plane"></i> Xác nhận nộp bài</h3>
      </div>
      <div class="modal-body">
        <p>Bạn có chắc chắn muốn nộp bài thi ngay bây giờ?</p>
        <div class="answer-stats">
          <p><i class="fas fa-check-circle"></i> Đã trả lời: <span id="answered-count">5</span>/
            <?= count($exam->questions) ?> câu
          </p>
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-outline close-modal">Tiếp tục làm bài</button>
        <button class="btn btn-danger" id="confirm-submit">Xác nhận nộp bài</button>
      </div>
    </div>
  </div>
</div>

<?php include 'template/footer.php'; ?>