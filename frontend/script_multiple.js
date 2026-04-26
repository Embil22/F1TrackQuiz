// frontend/script_multiple.js
let currentQuestion = 0;
let totalQuestions = 24;
let userAnswers = {};
let selectedOptions = {};

document.addEventListener('DOMContentLoaded', function() {
    updateProgress();
    attachOptionClickHandlers();
    
    // Navigation buttons
    document.querySelectorAll('.next-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const nextQuestion = parseInt(this.dataset.next);
            showQuestion(nextQuestion);
        });
    });
    
    document.querySelectorAll('.prev-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const prevQuestion = parseInt(this.dataset.prev);
            showQuestion(prevQuestion);
        });
    });
});

function attachOptionClickHandlers() {
    document.querySelectorAll('.option-card').forEach(option => {
        option.removeEventListener('click', optionClickHandler);
        option.addEventListener('click', optionClickHandler);
    });
}

function optionClickHandler(e) {
    const option = e.currentTarget;
    const questionCard = option.closest('.question-card');
    const questionIndex = parseInt(questionCard.dataset.question);
    const trackId = questionCard.dataset.trackId;
    const selectedTrackId = option.dataset.trackId;
    const selectedTrackName = option.dataset.trackName;
    
    // Remove selected class from all options in this question
    const optionsGrid = option.closest('.options-grid');
    optionsGrid.querySelectorAll('.option-card').forEach(opt => {
        opt.classList.remove('selected');
    });
    
    // Add selected class to clicked option
    option.classList.add('selected');
    
    // Save answer
    const isCorrect = (selectedTrackId === trackId);
    userAnswers[trackId] = {
        track_id: trackId,
        selected_id: selectedTrackId,
        selected_name: selectedTrackName,
        is_correct: isCorrect
    };
    
    // Update hidden input
    const hiddenInput = document.querySelector(`#answer_${trackId}`);
    if (hiddenInput) {
        hiddenInput.value = selectedTrackId;
    }
    
    // Show feedback
    const feedbackDiv = document.getElementById(`feedback_${questionIndex}`);
    if (feedbackDiv) {
        if (isCorrect) {
            feedbackDiv.innerHTML = '✅ Correct! Well done!';
            feedbackDiv.className = 'feedback-message feedback-correct';
        } else {
            const correctName = questionCard.querySelector(`.option-card[data-track-id="${trackId}"]`)?.dataset.trackName || 'Unknown';
            feedbackDiv.innerHTML = `❌ Wrong! The correct answer is: ${correctName}`;
            feedbackDiv.className = 'feedback-message feedback-wrong';
            
            // Highlight correct answer
            questionCard.querySelectorAll('.option-card').forEach(opt => {
                if (opt.dataset.trackId === trackId) {
                    opt.classList.add('correct-highlight');
                }
                if (opt === option && !isCorrect) {
                    opt.classList.add('wrong-highlight');
                }
            });
        }
    }
    
    updateScore();
}

function updateScore() {
    let correctCount = 0;
    let answeredCount = 0;
    
    for (const [key, answer] of Object.entries(userAnswers)) {
        if (answer.is_correct !== undefined) {
            answeredCount++;
            if (answer.is_correct) correctCount++;
        }
    }
    
    const scoreCounter = document.getElementById('scoreCounter');
    if (scoreCounter) {
        scoreCounter.textContent = `Score: ${correctCount}/${answeredCount}`;
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
        
        // Restore selected option if exists
        const trackId = targetQuestion.dataset.trackId;
        if (userAnswers[trackId]) {
            const selectedId = userAnswers[trackId].selected_id;
            const options = targetQuestion.querySelectorAll('.option-card');
            options.forEach(opt => {
                if (opt.dataset.trackId === selectedId) {
                    opt.classList.add('selected');
                }
            });
            
            // Restore feedback
            const feedbackDiv = document.getElementById(`feedback_${questionIndex}`);
            if (feedbackDiv && userAnswers[trackId].is_correct !== undefined) {
                if (userAnswers[trackId].is_correct) {
                    feedbackDiv.innerHTML = '✅ Correct! Well done!';
                    feedbackDiv.className = 'feedback-message feedback-correct';
                } else {
                    const correctName = targetQuestion.querySelector(`.option-card[data-track-id="${trackId}"]`)?.dataset.trackName || 'Unknown';
                    feedbackDiv.innerHTML = `❌ Wrong! The correct answer is: ${correctName}`;
                    feedbackDiv.className = 'feedback-message feedback-wrong';
                }
            }
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
const quizForm = document.getElementById('quizForm');
if (quizForm) {
    quizForm.addEventListener('submit', function(e) {
        let unanswered = 0;
        document.querySelectorAll('.question-card').forEach(card => {
            const trackId = card.dataset.trackId;
            if (!userAnswers[trackId] || userAnswers[trackId].is_correct === undefined) {
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
}