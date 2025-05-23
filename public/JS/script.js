console.log("script.js is loaded");
//---------------Sidebar------------------------
document.addEventListener("DOMContentLoaded", function () {
  const avatar = document.querySelector(".avatar");
  const popup = document.getElementById("userDetailsPopup");
  const signout = document.getElementById("signout");

  avatar.addEventListener("click", function () {
    popup.style.display = popup.style.display === "none" ? "block" : "none";
  });

  // Click ngoài thì ẩn popup
  document.addEventListener("click", function (e) {
    if (!avatar.contains(e.target) && !popup.contains(e.target)) {
      popup.style.display = "none";
    }
  });

  document
    .getElementById("logout-btn")
    ?.addEventListener("click", function (e) {
      e.preventDefault();
      if (confirm("Bạn có chắc chắn muốn đăng xuất?")) {
        window.location.href = "./signout.php";
      }
    });

  document
    .getElementById("admin-logout-btn")
    ?.addEventListener("click", function (e) {
      e.preventDefault();
      if (confirm("Bạn có chắc chắn muốn đăng xuất?")) {
        window.location.href = "../signout.php";
      }
    });
});

//---------------Student Dashboard------------------------
document.addEventListener("DOMContentLoaded", function () {
  const examBtn = document.getElementById("st-exam");
  const resultBtn = document.getElementById("st-result");

  examBtn.addEventListener("click", function () {
    window.location.href = "student-exam.php";
  });
  resultBtn.addEventListener("click", function () {
    window.location.href = "student-result.php";
  });
});

//---------------Student Exam------------------------
document.addEventListener("DOMContentLoaded", function () {
  // Xử lý chuyển tab
  document.querySelectorAll(".exam-tabs .tab").forEach((tab) => {
    tab.addEventListener("click", () => {
      const selectedTab = tab.getAttribute("data-tab");

      document
        .querySelectorAll(".exam-tabs .tab")
        .forEach((t) => t.classList.remove("active"));
      tab.classList.add("active");

      document
        .querySelectorAll(".exams-container .exam-card")
        .forEach((card) => {
          const cardTab = card.getAttribute("data-tab");
          card.style.display =
            selectedTab === "all" || cardTab === selectedTab ? "block" : "none";
        });
    });
  });

  // Modal bắt đầu làm bài
  const modal = document.getElementById("start-exam-modal");
  const startButtons = document.querySelectorAll(".btn-start");
  const closeButtons = document.querySelectorAll(".close-modal");
  const checkbox = document.getElementById("agree-rules");
  const confirmButton = document.getElementById("confirm-start-exam");

  let selectedExamId = null;
  let studentDoingExam = null;

  startButtons.forEach((btn) => {
    btn.addEventListener("click", () => {
      const duration = btn.getAttribute("data-duration");
      const question = btn.getAttribute("data-question");
      selectedExamId = btn.getAttribute("data-exam-id");
      studentDoingExam = btn.getAttribute("user-id");

      document.getElementById("modal-duration").innerText = duration + " phút";
      document.getElementById("modal-question").innerText = question + " câu";

      checkbox.checked = false;
      confirmButton.disabled = true;
      modal.style.display = "flex";
    });
  });

  closeButtons.forEach((btn) => {
    btn.addEventListener("click", () => {
      btn.closest(".modal").style.display = "none";
    });
  });

  checkbox.addEventListener("change", () => {
    confirmButton.disabled = !checkbox.checked;
  });

  confirmButton.addEventListener("click", () => {
    alert("Bắt đầu bài thi!");
    modal.style.display = "none";
    if (selectedExamId) {
      window.location.href =
        "student-exam-page.php?exam_id=" +
        selectedExamId +
        "&student_id=" +
        studentDoingExam;
    }
  });

  // Tìm kiếm bài thi theo tên
  document.getElementById("exam-search").addEventListener("input", function () {
    const keyword = removeVietnameseTones(this.value.trim().toLowerCase());
    const cards = document.querySelectorAll(".exams-container .exam-card");

    cards.forEach((card) => {
      const title = removeVietnameseTones(
        card.querySelector(".exam-header h3").textContent.toLowerCase()
      );
      card.style.display = title.includes(keyword) ? "block" : "none";
    });
  });

  function removeVietnameseTones(str) {
    return str
      .normalize("NFD")
      .replace(/[\u0300-\u036f]/g, "")
      .replace(/đ/g, "d")
      .replace(/Đ/g, "D");
  }

  // jQuery phần xem kết quả
  $(document).ready(function () {
    $(".view-result").click(function () {
      const examId = $(this).data("exam-id");
      $("#result-modal").fadeIn();
    });

    $(".close-modal").click(function () {
      $(this).closest(".modal").fadeOut();
    });

    $("#agree-rules").change(function () {
      $("#confirm-start-exam").prop("disabled", !$(this).is(":checked"));
    });

    $(".btn-start").click(function () {
      $("#start-exam-modal").fadeIn();
    });
  });
});

function showNotification() {
  const toast = document.getElementById("notification");
  toast.classList.remove("hidden");
  toast.classList.add("show");

  setTimeout(() => {
    toast.classList.remove("show");
    toast.classList.add("hidden");
  }, 3000);
}

//---------------Student Result Detail------------------------
document.addEventListener("DOMContentLoaded", function () {
  const tabBtns = document.querySelectorAll(".tab-btn");

  //Chuyển tab
  tabBtns.forEach((btn) => {
    btn.addEventListener("click", function () {
      tabBtns.forEach((b) => b.classList.remove("active"));
      document
        .querySelectorAll(".tab-content")
        .forEach((c) => c.classList.remove("active"));

      this.classList.add("active");
      const tabId = this.getAttribute("data-tab");
      document.getElementById(tabId).classList.add("active");
    });
  });

  // Khởi tạo circle progress (pie chart)
  const scoreCircle = document.querySelector(".circle-progress");
  if (scoreCircle) {
    const value = parseFloat(scoreCircle.getAttribute("data-value"));
    const max = parseFloat(scoreCircle.getAttribute("data-max"));
    const percentage = (value / max) * 100;
    scoreCircle.style.background = `conic-gradient(#4CAF50 ${percentage}%, #f0f0f0 0)`;
  }
});

//---------------Teacher Dashboard------------------------
//Chuyển trang
document.addEventListener("DOMContentLoaded", function () {
  const examBtn = document.getElementById("create-exam");
  const resultBtn = document.getElementById("manage-exam");

  examBtn.addEventListener("click", function () {
    window.location.href = "teacher-create-exam.php";
  });
  resultBtn.addEventListener("click", function () {
    window.location.href = "teacher-manage-result.php";
  });
  console.log("demo");

  document
    .getElementById("teacher-search-exam")
    .addEventListener("input", function () {
      const searchTerm = this.value.toLowerCase();
      const rows = document.querySelectorAll("table.exam-table tbody tr");

      rows.forEach((row) => {
        const rowText = row.textContent.toLowerCase();
        row.style.display = rowText.includes(searchTerm) ? "" : "none";
      });
    });

  // Xử lý nút chi tiết kỳ thi
  $(document).ready(function () {
    $(".btn-detail").click(function () {
      const examId = $(this).data("exam-id");
      window.location.href = "teacher-exam_detail.php?id=" + examId;
    });

    // Xử lý phân trang
    $(".page-btn:not(.disabled)").click(function () {
      if ($(this).hasClass("active")) return;

      $(".page-btn").removeClass("active");
      $(this).addClass("active");
    });
  });
});

//---------------Teacher Create Exam------------------------
document.addEventListener("DOMContentLoaded", function () {
  jQuery(document).ready(function ($) {
    let fetchedStudents = [];
    let selectedStudents = [];

    function fetchStudents() {
      fetch("controller/handler/get_students_api.php") // Adjust this URL if necessary
        .then((response) => {
          if (!response.ok) {
            throw new Error("Network response was not ok");
          }
          return response.json(); // Parse JSON data
        })
        .then((response) => {
          // Ensure the response contains a 'success' field and that data is an array
          if (response.success && Array.isArray(response.data)) {
            fetchedStudents = response.data.map((student, index) => ({
              id: student._id, // Use the _id from the API as the ID
              name: student.fullname,
              studentId: student.username,
              email: student.email,
            }));

            loadAllStudents();
            // return data;
          } else {
            console.error("Response data is not valid:", response);
            return []; // Return an empty array if the data is not in the expected format
          }
        })
        .catch((error) => {
          console.error("Error fetching students:", error); // Log any errors that occur
        });
    }

    fetchStudents();

    // Load all students
    function loadAllStudents() {
      const $tbody = $("#all-students-list");
      $tbody.empty();

      if (fetchedStudents.length === 0) {
        $tbody.append(
          '<tr><td colspan="4" class="text-center">Không có học sinh nào</td></tr>'
        );
        return;
      }

      fetchedStudents.forEach((student) => {
        const isSelected = selectedStudents.some((s) => s.id === student.id);

        $tbody.append(`
          <tr data-id="${student.id}">
            <td><input type="checkbox" class="student-checkbox" ${
              isSelected ? "checked" : ""
            }></td>
            <td>${student.name}</td>
            <td>${student.studentId}</td>
            <td>${student.email}</td>
          </tr>
        `);
      });
    }

    // Update danh sách học sinh được chọn
    function updateSelectedList() {
      const $selectedList = $("#selected-students-list");
      $selectedList.empty();

      $("#selected-count").text(selectedStudents.length);

      if (selectedStudents.length === 0) {
        $selectedList.append(
          '<div class="no-selection">Chưa có học sinh nào được chọn</div>'
        );
      } else {
        selectedStudents.forEach((student) => {
          $selectedList.append(`
            <div class="student-tag" data-id="${student.id}">
              <span>${student.name} (${student.studentId})</span>
              <i class="fas fa-times student-tag-remove"></i>
            </div>
          `);
        });
      }
    }

    // Toggle lựa chọn học sinh trong "Học sinh đã chọn"
    $(document).on("change", ".student-checkbox", function () {
      const studentId = $(this).closest("tr").data("id");
      const student = fetchedStudents.find((s) => s.id === studentId);

      if ($(this).is(":checked")) {
        if (!selectedStudents.some((s) => s.id === studentId)) {
          selectedStudents.push(student);
        }
      } else {
        selectedStudents = selectedStudents.filter((s) => s.id !== studentId);
      }

      updateSelectedList();
    });

    // Xóa học sinh đã chọn
    $(document).on("click", ".student-tag-remove", function () {
      const studentId = $(this).closest(".student-tag").data("id");
      selectedStudents = selectedStudents.filter((s) => s.id !== studentId);

      // Uncheck checkbox
      $(`tr[data-id="${studentId}"] .student-checkbox`).prop("checked", false);

      updateSelectedList();
    });

    // Thêm học sinh
    $("#add-selected-students").click(function () {
      if (selectedStudents.length === 0) {
        alert("Vui lòng chọn ít nhất một học sinh");
        return;
      }
      alert(`Đã thêm ${selectedStudents.length} học sinh vào kỳ thi`);
    });

    // Form submission
    $("#createExamForm").on("submit", function (e) {
      e.preventDefault();

      if (selectedStudents.length === 0) {
        alert("Vui lòng chọn ít nhất một học sinh tham gia kỳ thi");
        return;
      }

      //================================================================
      // Tính câu hỏi
      const questions = [];

      $(".question-item").each(function () {
        const content = $(this).find(".question-content").val().trim();
        const options = [];
        let correctAnswerIndex = null;

        $(this)
          .find(".option-item")
          .each(function (i) {
            const text = $(this).find(".option-text").val().trim();
            const isCorrect = $(this).find(".option-correct").is(":checked");
            options.push(text);

            if (isCorrect) {
              correctAnswerIndex = i;
            }
          });

        const question = {
          question_text: content,
          correct_answer: correctAnswerIndex,
          options: options,
        };

        questions.push(question);
      });
      //=================================================================

      //=================================================================
      // Tính thời gian kết thúc
      function calculateEndTime(dateStr, timeStr, durationMinutes) {
        const [year, month, day] = dateStr.split("-").map(Number);
        const [hours, minutes] = timeStr.split(":").map(Number);

        const startDate = new Date(year, month - 1, day, hours, minutes);
        const endDate = new Date(startDate.getTime() + durationMinutes * 60000);

        const pad = (n) => String(n).padStart(2, "0");
        return `${endDate.getFullYear()}-${pad(endDate.getMonth() + 1)}-${pad(endDate.getDate())} ${pad(endDate.getHours())}:${pad(endDate.getMinutes())}:00`;
      }

      const startDateStr = $("#exam-date").val();
      const startTimeStr = $("#exam-time").val();
      const duration = Number($("#exam-duration").val());
      //====================================================================
      // Prepare exam data
      const examData = {
        name: $("#exam-name").val(),
        description: $("#exam-description").val(),
        teacher_id: "some_teacher_id", // Replace this with real value
        start_time: $("#exam-date").val() + " " + $("#exam-time").val(),
        end_time: calculateEndTime(startDateStr, startTimeStr, duration),
        date: $("#exam-date").val(),
        time: $("#exam-time").val(),
        duration: $("#exam-duration").val(),
        students: selectedStudents.map((student) => student.id),
        questions: questions,
      };

      console.log("Exam data to submit:", examData);

      // Send the data to the API
      fetch("controller/handler/create_exam_api.php", {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify(examData), // Send the exam data as JSON
      })
        .then((response) => response.json())
        .then((result) => {
          if (result.success) {
            alert("✅ Tạo kỳ thi thành công! ID: " + result.inserted_id);
          } else {
            alert("❌ Lỗi: " + result.message);
          }
        })
        .catch((error) => {
          console.error("Error posting exam data:", error);
          alert("❌ Lỗi: Không thể tạo kỳ thi. Vui lòng thử lại.");
        });

      alert("Kỳ thi đã được tạo thành công!");
    });

    loadAllStudents();
    updateSelectedList();
  });

  // Xử lý nút chi tiết kỳ thi
  $(document).ready(function () {
    $(".btn-detail").click(function () {
      const examId = $(this).data("exam-id");
      window.location.href = "teacher-exam_detail.php?id=" + examId;
    });

    // Xử lý phân trang
    $(".page-btn:not(.disabled)").click(function () {
      if ($(this).hasClass("active")) return;

      $(".page-btn").removeClass("active");
      $(this).addClass("active");
    });
  });

  document
    .getElementById("teacher-search-student")
    .addEventListener("input", function () {
      const searchTerm = this.value.toLowerCase();
      const rows = document.querySelectorAll("table.student-table tbody tr");

      rows.forEach((row) => {
        const rowText = row.textContent.toLowerCase();
        row.style.display = rowText.includes(searchTerm) ? "" : "none";
      });
    });
});

// Thêm câu hỏi mới
document.addEventListener("DOMContentLoaded", function () {
  let questionCount = 1;
  document
    .getElementById("add-question")
    .addEventListener("click", function () {
      questionCount++;
      const newQuestionId = questionCount;

      const newQuestion = document.createElement("div");
      newQuestion.className = "question-item";
      newQuestion.dataset.questionId = newQuestionId;
      newQuestion.innerHTML = `
            <div class="question-header">
                <span class="question-number">Câu ${newQuestionId}</span>
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
                    <input type="radio" name="q${newQuestionId}-answer" class="option-correct">
                    <input type="text" class="option-text" placeholder="Lựa chọn 1">
                    <button type="button" class="btn btn-sm btn-outline remove-option">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="option-item">
                    <input type="radio" name="q${newQuestionId}-answer" class="option-correct">
                    <input type="text" class="option-text" placeholder="Lựa chọn 2">
                    <button type="button" class="btn btn-sm btn-outline remove-option">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <button type="button" class="btn btn-sm btn-outline add-option">
                    <i class="fas fa-plus"></i> Thêm lựa chọn
                </button>
            </div>
        `;

      document.getElementById("question-list").appendChild(newQuestion);

      newQuestion.scrollIntoView({ behavior: "smooth" });
    });

  // Xóa câu hỏi
  document.addEventListener("click", function (e) {
    if (e.target.closest(".remove-question")) {
      const questionItem = e.target.closest(".question-item");
      if (confirm("Bạn có chắc chắn muốn xóa câu hỏi này?")) {
        questionItem.remove();

        const questions = document.querySelectorAll(".question-item");
        questions.forEach((question, index) => {
          question.querySelector(".question-number").textContent = `Câu ${
            index + 1
          }`;
        });

        questionCount = questions.length;
      }
    }

    // Thêm option
    if (e.target.closest(".add-option")) {
      const optionsContainer = e.target.closest(".question-options");
      const questionId = e.target.closest(".question-item").dataset.questionId;
      const optionType = optionsContainer.querySelector(".option-correct").type;

      const optionItem = document.createElement("div");
      optionItem.className = "option-item";
      optionItem.innerHTML = `
                <input type="${optionType}" name="q${questionId}-answer" class="option-correct">
                <input type="text" class="option-text" placeholder="Lựa chọn mới">
                <button type="button" class="btn btn-sm btn-outline remove-option">
                    <i class="fas fa-times"></i>
                </button>
            `;

      optionsContainer.insertBefore(
        optionItem,
        e.target.closest(".add-option")
      );
    }

    // Xóa option
    if (e.target.closest(".remove-option")) {
      const optionItem = e.target.closest(".option-item");
      const optionsContainer = e.target.closest(".question-options");
      const optionItems = optionsContainer.querySelectorAll(".option-item");

      if (optionItems.length > 2) {
        optionItem.remove();
      } else {
        alert("Mỗi câu hỏi cần ít nhất 2 lựa chọn!");
      }
    }
  });
});

//---------------Teacher Manage Result------------------------
document.addEventListener("DOMContentLoaded", function () {
  // Chuyển tab
  const tabs = document.querySelectorAll(".results-tabs .tab");
  const tabContents = document.querySelectorAll(".tab-content");

  // Xử lý sự kiện click tab
  tabs.forEach((tab) => {
    tab.addEventListener("click", function () {
      const tabName = this.getAttribute("data-tab");

      tabs.forEach((t) => t.classList.remove("active"));
      this.classList.add("active");

      tabContents.forEach((content) => {
        content.classList.remove("active");
        if (content.id === tabName) {
          content.classList.add("active");
        }
      });
    });
  });
});

//---------------Admin Dashboard------------------------
//Chuyển tab
document.addEventListener("DOMContentLoaded", function () {
  const mgTButton = document.getElementById("mg-teacher");
  const mgSButton = document.getElementById("mg-student");

  mgTButton.addEventListener("click", function () {
    window.location.href = "admin-manage-teachers.php";
  });

  mgSButton.addEventListener("click", function () {
    window.location.href = "admin-manage-students.php";
  });
});

//---------------Exam Page------------------------
document.addEventListener("DOMContentLoaded", function () {
  let currentQuestion = 1;

  const examDataElement = document.getElementById("exam-data");
  const totalQuestions = parseInt(examDataElement.getAttribute("data-questions"), 10);

  // Khởi tạo trạng thái ban đầu cho tất cả câu hỏi
  function initializeQuestionStates() {
    document.querySelectorAll(".question-number").forEach((el, index) => {
      const qid = index + 1;
      el.classList.remove("active", "answered", "unanswered", "current");

      if (qid === 1) {
        el.classList.add("active", "current");
      } else {
        el.classList.add("unanswered");
      }
    });
  }

  // Cập nhật hiển thị câu hỏi hiện tại
  function updateQuestionDisplay(num) {
    // Ẩn tất cả các câu hỏi
    document.querySelectorAll(".question-current").forEach((el) => {
      el.style.display = "none";
    });

    // Hiển thị câu hỏi hiện tại
    const currentQuestionElement = document.getElementById(
      "question-" + (num - 1)
    );
    if (currentQuestionElement) {
      currentQuestionElement.style.display = "block";
    }

    // Cập nhật trạng thái active cho câu hỏi
    updateAllQuestionStatuses();
  }

  // Cập nhật trạng thái tất cả câu hỏi
  function updateAllQuestionStatuses() {
    document.querySelectorAll(".question-number").forEach((el) => {
      const qid = parseInt(el.getAttribute("data-qid"));
      const questionIndex = qid - 1;
      const isAnswered = document.querySelector(
        `input[name="q${questionIndex}"]:checked`
      );

      // Xóa tất cả class trạng thái trước
      el.classList.remove("active", "current", "answered", "unanswered");

      if (qid === currentQuestion) {
        el.classList.add("active", "current");
      } else if (isAnswered) {
        el.classList.add("answered");
      } else {
        el.classList.add("unanswered");
      }
    });
  }

  // Khởi tạo ban đầu
  initializeQuestionStates();
  updateQuestionDisplay(currentQuestion);

  // Chuyển câu hỏi khi click ô bên sidebar
  document.querySelectorAll(".question-number").forEach((el) => {
    el.addEventListener("click", function () {
      currentQuestion = parseInt(this.getAttribute("data-qid"));
      updateQuestionDisplay(currentQuestion);
    });
  });

  // Nút câu trước
  document.getElementById("prev-question").addEventListener("click", () => {
    if (currentQuestion > 1) {
      currentQuestion--;
      updateQuestionDisplay(currentQuestion);
    }
  });

  // Nút câu sau
  document.getElementById("next-question").addEventListener("click", () => {
    if (currentQuestion < totalQuestions) {
      currentQuestion++;
      updateQuestionDisplay(currentQuestion);
    }
  });

  // Xử lý câu trả lời khi chọn radio button
  document
    .querySelectorAll('.question-options input[type="radio"]')
    .forEach((radio) => {
      radio.addEventListener("change", function () {
        const questionIndex = this.name.replace("q", "");
        const qid = parseInt(questionIndex) + 1;

        // Cập nhật trạng thái
        updateAllQuestionStatuses();
        updateAnsweredCount();
      });
    });

  // Cập nhật số câu đã trả lời
  function updateAnsweredCount() {
    const count = document.querySelectorAll(
      '.question-options input[type="radio"]:checked'
    ).length;
    document.querySelector(".question-count .answered").textContent = count;
    document.getElementById("answered-count").textContent = count;
  }

  // Modal xác nhận nộp bài
  const submitBtn = document.querySelector(".btn-submit-exam");
  const submitModal = document.getElementById("submit-exam-modal");
  const closeModalBtns = document.querySelectorAll(".close-modal");
  const confirmSubmitBtn = document.getElementById("confirm-submit");

  submitBtn.addEventListener("click", () => {
    updateAnsweredCount();
    submitModal.style.display = "flex";
  });

  closeModalBtns.forEach((btn) => {
    btn.addEventListener("click", () => {
      submitModal.style.display = "none";
    });
  });

  confirmSubmitBtn.addEventListener("click", () => {
    const examDataElement = document.getElementById("exam-data");
    const examId = examDataElement.getAttribute("data-exam-id");
    const studentId = examDataElement.getAttribute("data-student-id");
    const teacherId = examDataElement.getAttribute("data-teacher-id");
    const answers = [];

    for (let i = 0; i < totalQuestions; i++) {
      const selected = document.querySelector(`input[name="q${i}"]:checked`);

      answers.push({
        question_index: i,
        student_answer: selected ? selected.value : null, // null if unanswered
      });
    }

    const payload = {
      exam_id: examId,
      student_id: studentId,
      answers: answers,
    };
    console.log(payload);

    fetch("controller/handler/submit_exam_api.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify(payload),
    })
      .then((res) => {
        if (!res.ok) throw new Error("Server error");
        return res.json();
      })
      .then((data) => {
        alert("Bài thi đã được nộp!");
        window.location.href = "student-exam.php";
      })
      .catch((err) => {
        alert("Có lỗi khi nộp bài. Vui lòng thử lại.");
        console.error(err);
      });

    submitModal.style.display = "none";
  });

  // Đếm ngược thời gian
  const timeDisplay = document.getElementById("time-remaining");
  const durationText = timeDisplay.textContent.trim();
  const [minutes, seconds] = durationText.split(":").map(Number);

  let timeRemaining = minutes * 60 + seconds;

  function updateTimer() {
    const m = Math.floor(timeRemaining / 60);
    const s = timeRemaining % 60;
    timeDisplay.textContent =
      String(m).padStart(2, "0") + ":" + String(s).padStart(2, "0");

    if (timeRemaining > 0) {
      timeRemaining--;
    } else {
      clearInterval(timerInterval);
      alert("Hết thời gian! Bài thi sẽ được nộp.");
      window.location.href = "student-exam.php";
    }
  }

  // Bắt đầu đồng hồ
  const timerInterval = setInterval(updateTimer, 1000);
  updateTimer(); // cập nhật lần đầu
});

//---------------Teacher Exam Details------------------------
document.addEventListener("DOMContentLoaded", function () {
  $(document).ready(function () {
    // Xử lý chuyển tab
    $(".exam-tabs .tab").click(function () {
      const tabId = $(this).data("tab");

      // Xóa active class từ tất cả các tab
      $(".exam-tabs .tab").removeClass("active");
      $(".tab-content").removeClass("active");

      // Thêm active class vào tab được click
      $(this).addClass("active");
      $(`#${tabId}-tab`).addClass("active");
    });

    document
      .getElementById("teacher-search-marks")
      .addEventListener("input", function () {
        const searchTerm = this.value.toLowerCase();
        const rows = document.querySelectorAll("table.results-table tbody tr");

        rows.forEach((row) => {
          const rowText = row.textContent.toLowerCase();
          row.style.display = rowText.includes(searchTerm) ? "" : "none";
        });
      });
  });

  const filterSelect = document.querySelector(".filter-select");
  const resultsTable = document.querySelector(".results-table tbody");

  // Hàm đao ngược tên tiếng Việt
  function reverseVietnameseName(fullName) {
    const parts = fullName.trim().split(/\s+/);
    if (parts.length <= 1) return fullName;

    const lastName = parts.shift();
    const givenName = parts.pop();
    const middleName = parts.join(" ");

    return [givenName, middleName, lastName].filter(Boolean).join(" ");
  }

  //Sắp xếp bảng
  function sortResultsTable(sortOption) {
    const rows = Array.from(resultsTable.querySelectorAll("tr"));

    rows.sort((rowA, rowB) => {
      const originalNameA = rowA.cells[1].textContent.trim();
      const originalNameB = rowB.cells[1].textContent.trim();

      // Đảo tên để so sánh
      const reversedNameA = reverseVietnameseName(originalNameA).toLowerCase();
      const reversedNameB = reverseVietnameseName(originalNameB).toLowerCase();

      const scoreA = parseFloat(rowA.cells[2].textContent);
      const scoreB = parseFloat(rowB.cells[2].textContent);

      switch (sortOption) {
        case "score-desc":
          return scoreB - scoreA;
        case "score-asc":
          return scoreA - scoreB;
        case "name-asc":
          return reversedNameA.localeCompare(reversedNameB);
        case "name-desc":
          return reversedNameB.localeCompare(reversedNameA);
        default:
          return 0;
      }
    });

    // Cập nhật lại bảng
    resultsTable.innerHTML = "";
    rows.forEach((row, index) => {
      row.cells[0].textContent = index + 1;
      resultsTable.appendChild(row);
    });
  }

  filterSelect.addEventListener("change", function () {
    sortResultsTable(this.value);
  });
  // Sắp xếp mặc định khi tải trang
  sortResultsTable("score-desc");
});

//---------------Admin Manage Students------------------------
document.addEventListener("DOMContentLoaded", function () {
  function openModal(modalId) {
    document.getElementById(modalId).style.display = "block";
  }

  function closeModal(modalId) {
    document.getElementById(modalId).style.display = "none";
  }

  document
    .getElementById("add-student-btn")
    .addEventListener("click", function () {
      document.getElementById("add-student-form").reset();
      openModal("add-student-modal");
    });

  document
    .getElementById("add-student-form")
    .addEventListener("submit", function (e) {
      e.preventDefault();
      const form = document.getElementById("add-student-form");
      const formData = new FormData(form);
      formData.append("action", "add");
      fetch("admin-manage-students.php", {
        method: "POST",
        body: formData,
      })
        .then((response) => response.json())
        .then((data) => {
          if (data.success) {
            alert(data.message);
            location.reload();
          } else {
            alert(data.message);
          }
        })
        .catch((error) => {
          console.error("Error:", error);
          alert("Đã có lỗi xảy ra. Vui lòng thử lại.");
        });
    });

  document
    .getElementById("save-student-changes")
    .addEventListener("click", function (e) {
      const form = document.getElementById("edit-student-form");
      const formData = new FormData(form);
      formData.append("action", "edit");
      // Sử dụng AJAX để gửi dữ liệu mà không tải lại trang
      fetch("admin-manage-students.php", {
        method: "POST",
        body: formData,
      })
        .then((response) => response.json()) // Nhận dữ liệu JSON từ PHP
        .then((data) => {
          if (data.success) {
            // Hiển thị thông báo thành công
            alert(data.message);
            location.reload();
            closeModal("edit-student-modal");
          } else {
            // Hiển thị thông báo lỗi
            alert(data.message);
          }
        })
        .catch((error) => {
          console.error("Error:", error);
          alert("Đã có lỗi xảy ra. Vui lòng thử lại.");
        });
    });

  document.addEventListener("click", function (e) {
    // Sửa học sinh
    if (e.target.closest(".btn-edit")) {
      const button = e.target.closest(".btn-edit");
      const studentId = button.dataset.id;
      const studentEmail = button.dataset.email;
      const studentUsername = button.dataset.username;
      const studentFullname = button.dataset.fullname;
      const studentPassword = button.dataset.password;

      document.getElementById("edit-student-id").value = studentId;
      document.getElementById("edit-student-fullname").value = studentFullname;
      document.getElementById("edit-student-password").value = studentPassword;
      document.getElementById("edit-student-username").value = studentUsername;
      document.getElementById("edit-student-email").value = studentEmail;
      openModal("edit-student-modal");
    }

    // Xóa học sinh
    if (e.target.closest(".btn-reject")) {
      const studentId = e.target.closest(".btn-reject").dataset.id;
      document.getElementById("delete-student-id").value = studentId;
      openModal("delete-student-modal");
    }

    if (
      e.target.classList.contains("close-modal") ||
      e.target.classList.contains("modal")
    ) {
      const modal = e.target.closest(".modal");
      if (modal) closeModal(modal.id);
    }
  });

  document.addEventListener("click", function (e) {
    // Kiểm tra nếu người dùng nhấn vào nút "Xóa"
    if (e.target.closest(".btn-reject")) {
      const studentId = e.target.closest(".btn-reject").dataset.id;
      document.getElementById("delete-student-id").value = studentId;
      openModal("delete-student-modal");
    }

    // Xác nhận xóa học sinh
    document
      .getElementById("confirm-delete-student")
      .addEventListener("click", function () {
        const studentId = document.getElementById("delete-student-id").value;

        // Gửi yêu cầu xóa qua AJAX
        const formData = new FormData();
        formData.append("action", "delete"); // Gửi action là 'delete'
        formData.append("studentId", studentId); // Gửi studentId cần xóa

        fetch("admin-manage-students.php", {
          method: "POST",
          body: formData,
        })
          .then((response) => response.json())
          .then((data) => {
            if (data.success) {
              alert(data.message);
              location.reload(); // Tải lại trang sau khi xóa học sinh thành công
            } else {
              alert(data.message);
            }
          })
          .catch((error) => {
            console.error("Error:", error);
            alert("Đã có lỗi xảy ra. Vui lòng thử lại.");
          });

        closeModal("delete-student-modal");
      });
  });

  // Tìm kiếm học sinh
  document
    .getElementById("student-search")
    .addEventListener("input", function () {
      const searchTerm = this.value.toLowerCase();
      const rows = document.querySelectorAll("table.users-table tbody tr");

      rows.forEach((row) => {
        const rowText = row.textContent.toLowerCase();
        row.style.display = rowText.includes(searchTerm) ? "" : "none";
      });
    });
});

//---------------Admin Manage Teacher------------------------
document.addEventListener("DOMContentLoaded", function () {
  function openModal(modalId) {
    document.getElementById(modalId).style.display = "block";
  }

  function closeModal(modalId) {
    document.getElementById(modalId).style.display = "none";
  }

  // Thêm giáo viên
  document
    .getElementById("add-teacher-btn")
    .addEventListener("click", function () {
      document.getElementById("add-teacher-form").reset();
      openModal("add-teacher-modal");
    });

  document
    .getElementById("add-teacher-form")
    .addEventListener("submit", function (e) {
      e.preventDefault();

      // Lấy dữ liệu từ form
      const form = document.getElementById("add-teacher-form");
      const formData = new FormData(form);
      formData.append("action", "add");

      fetch("admin-manage-teachers.php", {
        method: "POST",
        body: formData,
      })
        .then((response) => response.json()) // Nhận dữ liệu JSON từ PHP
        .then((data) => {
          if (data.success) {
            // Hiển thị thông báo thành công
            alert(data.message);

            // Tải lại trang sau khi thêm học sinh thành công
            location.reload(); // Tải lại trang để làm mới danh sách học sinh
          } else {
            alert(data.message);
          }
        })
        .catch((error) => {
          console.error("Error:", error);
          alert("Đã có lỗi xảy ra. Vui lòng thử lại.");
        });
    });

  document.addEventListener("click", function (e) {
    // Sửa giáo viên
    if (e.target.closest(".btn-edit")) {
      const button = e.target.closest(".btn-edit");
      const teacherId = button.dataset.id;
      const teacherEmail = button.dataset.email;
      const teacherUsername = button.dataset.username;
      const teacherFullname = button.dataset.fullname;
      const teacherPassword = button.dataset.password;

      document.getElementById("edit-teacher-id").value = teacherId;
      document.getElementById("edit-teacher-fullname").value = teacherFullname;
      document.getElementById("edit-teacher-password").value = teacherPassword;
      document.getElementById("edit-teacher-username").value = teacherUsername;
      document.getElementById("edit-teacher-email").value = teacherEmail;
      openModal("edit-teacher-modal");
    }

    // Xóa giáo viên
    if (e.target.closest(".btn-reject")) {
      const teacherId = e.target.closest(".btn-reject").dataset.id;
      document.getElementById("delete-teacher-id").value = teacherId;
      openModal("delete-teacher-modal");
    }

    if (
      e.target.classList.contains("close-modal") ||
      e.target.classList.contains("modal")
    ) {
      const modal = e.target.closest(".modal");
      if (modal) closeModal(modal.id);
    }
  });

  document
    .getElementById("save-teacher-changes")
    .addEventListener("click", function () {
      const form = document.getElementById("edit-teacher-form");
      const formData = new FormData(form);
      formData.append("action", "edit");
      fetch("admin-manage-teachers.php", {
        method: "POST",
        body: formData,
      })
        .then((response) => response.json()) // Nhận dữ liệu JSON từ PHP
        .then((data) => {
          if (data.success) {
            // Hiển thị thông báo thành công
            alert(data.message);
            location.reload();
            closeModal("edit-teacher-modal");
          } else {
            // Hiển thị thông báo lỗi
            alert(data.message);
          }
        })
        .catch((error) => {
          console.error("Error:", error);
          alert("Đã có lỗi xảy ra. Vui lòng thử lại.");
        });
    });

  // Xác nhận xóa giáo viên
  document
    .getElementById("confirm-delete-teacher")
    .addEventListener("click", function () {
      const teacherId = document.getElementById("delete-teacher-id").value;

      const formData = new FormData();
      formData.append("action", "delete"); // Gửi action là 'delete'
      formData.append("teacherId", teacherId); // Gửi studentId cần xóa

      fetch("admin-manage-teachers.php", {
        method: "POST",
        body: formData,
      })
        .then((response) => response.json())
        .then((data) => {
          if (data.success) {
            alert(data.message);
            location.reload(); // Tải lại trang sau khi xóa học sinh thành công
          } else {
            alert(data.message);
          }
        })
        .catch((error) => {
          console.error("Error:", error);
          alert("Đã có lỗi xảy ra. Vui lòng thử lại.");
        });

      closeModal("delete-teacher-modal");
    });

  // Tìm kiếm giáo viên
  document
    .getElementById("teacher-search")
    .addEventListener("input", function () {
      const searchTerm = this.value.toLowerCase();
      const rows = document.querySelectorAll("table.users-table tbody tr");

      rows.forEach((row) => {
        const rowText = row.textContent.toLowerCase();
        row.style.display = rowText.includes(searchTerm) ? "" : "none";
      });
    });
});
