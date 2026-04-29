let currentQuestion = 0;
let totalQuestions = 24;
let userAnswers = {};
let autoTransitionTimeout = null;

document.addEventListener("DOMContentLoaded", function () {
  updateProgress();
  attachOptionClickHandlers();
});

function attachOptionClickHandlers() {
  document.querySelectorAll(".option-card").forEach((option) => {
    option.removeEventListener("click", optionClickHandler);
    option.addEventListener("click", optionClickHandler);
  });
}

function optionClickHandler(e) {
  const option = e.currentTarget;
  const questionCard = option.closest(".question-card");

  // Ellenőrizzük, hogy a kérdés már meg lett-e válaszolva
  const questionIndex = parseInt(questionCard.dataset.question);
  if (userAnswers[questionIndex] && userAnswers[questionIndex].answered) {
    return; // Ha már válaszolt, ne engedjük módosítani
  }

  const correctTrackId = questionCard.dataset.trackId;
  const selectedTrackId = option.dataset.trackId;
  const selectedTrackName = option.dataset.trackName;
  const correctTrackName = questionCard.dataset.correctName;
  const questionId = questionCard
    .querySelector('input[type="hidden"]')
    .id.replace("answer_", "");

  // Letiltjuk az összes opciót ebben a kérdésben
  const optionsGrid = option.closest(".options-grid");
  const allOptions = optionsGrid.querySelectorAll(".option-card");

  // Kiemeljük a helyes és helytelen választ
  allOptions.forEach((opt) => {
    opt.style.pointerEvents = "none";
    opt.classList.add("disabled");

    if (opt.dataset.trackId === correctTrackId) {
      opt.classList.add("correct-answer");
    }
    if (opt === option && selectedTrackId !== correctTrackId) {
      opt.classList.add("wrong-answer");
    }
  });

  // Kiértékelés
  const isCorrect = selectedTrackId === correctTrackId;

  // Mentés
  userAnswers[questionIndex] = {
    track_id: questionId,
    selected_id: selectedTrackId,
    selected_name: selectedTrackName,
    is_correct: isCorrect,
    answered: true,
  };

  // Hidden input frissítése
  const hiddenInput = document.querySelector(`#answer_${questionId}`);
  if (hiddenInput) {
    hiddenInput.value = selectedTrackId;
  }

  // Feedback megjelenítése
  const feedbackDiv = document.getElementById(`feedback_${questionIndex}`);
  if (feedbackDiv) {
    if (isCorrect) {
      feedbackDiv.innerHTML = "✅ Helyes! Szép volt!";
      feedbackDiv.className = "feedback-message feedback-correct";
    } else {
      feedbackDiv.innerHTML = `❌ Helytelen! A jó válasz: ${correctTrackName}`;
      feedbackDiv.className = "feedback-message feedback-wrong";
    }
  }

  // Score frissítése
  updateScore();

  // Továbblépés jelzése
  const nextIndicator = document.getElementById(
    `next_indicator_${questionIndex}`
  );
  if (nextIndicator) {
    nextIndicator.style.display = "block";
  }

  // Automatikus továbblépés a következő kérdésre
  if (autoTransitionTimeout) {
    clearTimeout(autoTransitionTimeout);
  }

  autoTransitionTimeout = setTimeout(() => {
    if (currentQuestion + 1 < totalQuestions) {
      showQuestion(currentQuestion + 1);
    } else {
      // Utolsó kérdés volt, beküldjük a kvízt
      showSubmitButton();
    }
  }, 1500);
}

function updateScore() {
  let correctCount = 0;
  let answeredCount = 0;

  for (const [key, answer] of Object.entries(userAnswers)) {
    if (answer.answered) {
      answeredCount++;
      if (answer.is_correct) correctCount++;
    }
  }

  const scoreCounter = document.getElementById("scoreCounter");
  if (scoreCounter) {
    scoreCounter.textContent = `Pontszám: ${correctCount}/${answeredCount}`;
  }
}

function showQuestion(questionIndex) {
  // Clear any pending timeout
  if (autoTransitionTimeout) {
    clearTimeout(autoTransitionTimeout);
  }

  // Hide all questions
  document.querySelectorAll(".question-card").forEach((card) => {
    card.style.display = "none";
  });

  // Show selected question
  const targetQuestion = document.querySelector(
    `.question-card[data-question="${questionIndex}"]`
  );
  if (targetQuestion) {
    targetQuestion.style.display = "block";
    currentQuestion = questionIndex;
    updateProgress();

    // Ha a kérdés már meg lett válaszolva, ne csináljunk semmit
    // (már úgyis látszik a feedback és a letiltott opciók)
  }
}

function showSubmitButton() {
  const submitBtn = document.getElementById("submitBtn");

  // Opcionális: üzenet, hogy kész a kvíz
  const container = document.querySelector("#questionsContainer");
  const lastQuestion = document.querySelector(".question-card:last-child");
  if (lastQuestion) {
    const completeDiv = document.createElement("div");
    completeDiv.className = "quiz-complete";
    completeDiv.innerHTML = `
                    <h2>Kitöltötted az összes kérdést!</h2>
                    <p>Nyomja meg a beküldés gombot hogy lássa az eredményét.</p>
                    <div class="navigation-buttons">
                            <button type="submit" class="btn-submit" id="submitBtn";">Beküldés ✅</button>
                    </div>
                `;
    lastQuestion.appendChild(completeDiv);
  }
}

function updateProgress() {
  const progress = ((currentQuestion + 1) / totalQuestions) * 100;
  const progressBar = document.getElementById("progressBar");
  if (progressBar) {
    progressBar.style.width = `${progress}%`;
  }

  const questionCounter = document.getElementById("questionCounter");
  if (questionCounter) {
    questionCounter.textContent = `Kérdés ${
      currentQuestion + 1
    }/${totalQuestions}`;
  }
}
