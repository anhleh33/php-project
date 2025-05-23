<?php
session_start();

$page_title = "Chi tiết kỳ thi";
include 'template/head.php';

require_once 'controller/GetResultController.php';
require_once 'controller/UpdateExamController.php';
require_once 'controller/GetInfoController.php';

$ResultController = new GetResultController();
$ExamController = new UpdateExamController();
$UserController = new GetInfoController();

$exam = $ExamController->getExaminExamList($_GET['exam_id'], $_GET['student_id']);
$teacherName = $UserController->getNameByID($exam->teacher_id);
$examResult = $ResultController->getResult($_GET['exam_id'], $_GET['student_id']);
$count = $ResultController->countCorrectAndWrongAnswers($examResult['answers'], $exam['questions']);
$finalResult = $ResultController->getResultDetails($_GET['exam_id'], $_GET['student_id']);
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
      <div class="header-actions">
        <a href="student-result.php" class="btn btn-back">
          <i class="fas fa-arrow-left"></i> Quay lại
        </a>
      </div>
      <h1><i class="fas fa-poll"></i> Chi tiết kỳ thi</h1>
    </div>

    <!-- Exam Info Section -->
    <div class="exam-info-section">
      <div class="exam-header">
        <div class="exam-title">
          <h2 style="font-size: 30px;"><?= $exam['name'] ?></h2>
          <p class="exam-meta">
            <span><i class="fas fa-user-tie"></i> GV: <?= $teacherName ?></span>
            <span><i class="fas fa-calendar-alt"></i> Ngày thi:
              <?= $examResult['submitted_at']->toDateTime()->format('d/m/Y') ?></span>
            <span><i class="fas fa-clock"></i> Thời gian làm bài: <?= $ExamController->getDuration($exam) ?> phút</span>
          </p>
        </div>
        <div class="exam-score">
          <div class="score-circle">
            <div class="circle-progress" data-value="<?= $examResult['score'] ?>" data-max="100"></div>
            <div class="score-text" style>
              <span class="score-value"><?= $examResult['score'] ?></span>
              <span class="score-max">/100</span>
            </div>
          </div>
          <p class="score-label">Điểm số</p>
        </div>
      </div>
    </div>

    <!-- Question Review -->
    <div class="detail-section">
      <h3><i class="fas fa-question-circle"></i> Xem lại bài làm</h3>
      <div class="question-review">
        <div class="review-tabs">
          <button class="tab-btn active" data-tab="all">Tất cả câu hỏi (<?= count($examResult['answers']) ?>)</button>
          <button class="tab-btn" data-tab="incorrect">Câu sai (<?= $count['wrong'] ?>)</button>
          <button class="tab-btn" data-tab="correct">Câu đúng (<?= $count['correct'] ?>)</button>
        </div>

        <!-- Incorrect Questions -->
        <div class="tab-content" id="incorrect">
          <?php foreach ($finalResult['data'] as $i => $question): ?>
            <?php if (!$finalResult['correct'][$i]): ?>
              <div class="question-item incorrect">
                <div class="question-header">
                  <span class="question-number">Câu <?= $i + 1 ?></span>
                  <span class="question-status incorrect"><i class="fas fa-times"></i> Sai</span>
                </div>
                <div class="question-content">
                  <p><?= htmlspecialchars($question['question_text']) ?></p>
                </div>
                <div class="question-answers">
                  <div class="answer-row">
                    <span class="answer-label">Đáp án của bạn:</span>
                    <span
                      class="answer-value wrong-answer"><?= htmlspecialchars($question['options'][$question['student_answer']]) ?></span>
                  </div>
                  <div class="answer-row">
                    <span class="answer-label">Đáp án đúng:</span>
                    <span
                      class="answer-value correct-answer"><?= htmlspecialchars($question['options'][$question['correct_answer']]) ?></span>
                  </div>
                </div>
              </div>
            <?php endif; ?>
          <?php endforeach; ?>
        </div>

        <!-- Correct Questions -->
        <div class="tab-content" id="correct">
          <?php foreach ($finalResult['data'] as $i => $question): ?>
            <?php if ($finalResult['correct'][$i]): ?>
              <div class="question-item correct">
                <div class="question-header">
                  <span class="question-number">Câu <?= $i + 1 ?></span>
                  <span class="question-status correct"><i class="fas fa-check"></i> Đúng</span>
                </div>
                <div class="question-content">
                  <p><?= htmlspecialchars($question['question_text']) ?></p>
                </div>
                <div class="question-answers">
                  <div class="answer-row">
                    <span class="answer-label">Đáp án của bạn:</span>
                    <span
                      class="answer-value correct-answer"><?= htmlspecialchars($question['options'][$question['student_answer']]) ?></span>
                  </div>
                </div>
              </div>
            <?php endif; ?>
          <?php endforeach; ?>
        </div>

        <!-- All Questions -->
        <div class="tab-content active" id="all">
          <?php foreach ($finalResult['data'] as $i => $question): ?>
            <div class="question-item <?= $finalResult['correct'][$i] ? 'correct' : 'incorrect' ?>">
              <div class="question-header">
                <span class="question-number">Câu <?= $i + 1 ?></span>
                <span class="question-status <?= $finalResult['correct'][$i] ? 'correct' : 'incorrect' ?>">
                  <i class="fas <?= $finalResult['correct'][$i] ? 'fa-check' : 'fa-times' ?>"></i>
                  <?= $finalResult['correct'][$i] ? 'Đúng' : 'Sai' ?>
                </span>
              </div>
              <div class="question-content">
                <p><?= htmlspecialchars($question['question_text']) ?></p>
              </div>
              <div class="question-answers">
                <div class="answer-row">
                  <span class="answer-label">Đáp án của bạn:</span>
                  <span class="answer-value <?= $finalResult['correct'][$i] ? 'correct-answer' : 'wrong-answer' ?>">
                    <?= htmlspecialchars($question['options'][$question['student_answer']]) ?>
                  </span>
                </div>
                <?php if (!$finalResult['correct'][$i]): ?>
                  <div class="answer-row">
                    <span class="answer-label">Đáp án đúng:</span>
                    <span
                      class="answer-value correct-answer"><?= htmlspecialchars($question['options'][$question['correct_answer']]) ?></span>
                  </div>
                <?php endif; ?>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
  </div>
</div>

<?php include 'template/footer.php'; ?>