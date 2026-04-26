let currentQuestion = 0;
let totalQuestions = 24;
let userAnswers = {};

document.addEventListener('DOMContentLoaded', function() {
    updateProgress();
    
    // Navigation buttons
    document.querySelectorAll('.next-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            saveCurrentAnswer();
            const nextQuestion = parseInt(this.dataset.next);
            showQuestion(nextQuestion);
        });
    });
    
    document.querySelectorAll('.prev-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            saveCurrentAnswer();
            const prevQuestion = parseInt(this.dataset.prev);
            showQuestion(prevQuestion);
        });
    });
    
    // Auto-save on input
    document.querySelectorAll('.question-input input').forEach(input => {
        input.addEventListener('input', function() {
            saveCurrentAnswer();
        });
    });
});

function saveCurrentAnswer() {
    const currentInput = document.querySelector(`.question-card[style*="display: block"] input`);
    if (currentInput && currentInput.value) {
        const questionId = currentInput.id;
        userAnswers[questionId] = currentInput.value;
    }
}

function showQuestion(questionIndex) {
    // Hide all questions
    document.querySelectorAll('.question-card').forEach(card => {
        card.style.display = 'none';
    });
    
    // Show selected question
    const targetQuestion = document.querySelector(`.question-card[data-question="${questionIndex}"]`);
    if (targetQuestion) {
        targetQuestion.style.display = 'block';
        currentQuestion = questionIndex;
        updateProgress();
        
        // Restore saved answer
        const input = targetQuestion.querySelector('input');
        const answerKey = input.id;
        if (userAnswers[answerKey]) {
            input.value = userAnswers[answerKey];
        }
    }
}

function updateProgress() {
    const progress = ((currentQuestion + 1) / totalQuestions) * 100;
    const progressBar = document.getElementById('progressBar');
    if (progressBar) {
        progressBar.style.width = `${progress}%`;
    }
    
    const questionCounter = document.getElementById('questionCounter');
    if (questionCounter) {
        questionCounter.textContent = `Question ${currentQuestion + 1}/${totalQuestions}`;
    }
}

// Form validation before submit
document.getElementById('quizForm')?.addEventListener('submit', function(e) {
    saveCurrentAnswer();
    
    // Check if all questions have answers (optional warning)
    let unanswered = 0;
    document.querySelectorAll('.question-card input').forEach(input => {
        if (!input.value.trim()) {
            unanswered++;
        }
    });
    
    if (unanswered > 0) {
        const confirmSubmit = confirm(`You have ${unanswered} unanswered questions. Are you sure you want to submit?`);
        if (!confirmSubmit) {
            e.preventDefault();
        }
    }
});