<?php
// frontend/quiz.php
require_once '../backend/config.php';
redirectIfNotLoggedIn();

// Get all tracks for the quiz (random sorrendben)
$stmt = $pdo->query("SELECT id, name, country, image_url FROM tracks ORDER BY RAND()");
$tracks = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Minden pályához gyűjtsünk 3 random másik pályát (helytelen válaszok)
$all_track_names = [];
$stmt = $pdo->query("SELECT id, name FROM tracks");
$all_tracks = $stmt->fetchAll(PDO::FETCH_ASSOC);
foreach ($all_tracks as $track) {
    $all_track_names[$track['id']] = $track['name'];
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>F1 Quiz - Take the Quiz</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .question-card {
            background: white;
            padding: 30px;
            border-radius: 10px;
            margin-bottom: 20px;
        }

        .quiz-layout {
            display: flex;
            gap: 30px;
            flex-wrap: wrap;
        }

        .track-image-side {
            flex: 1;
            min-width: 300px;
        }

        .track-image-side img {
            width: 100%;
            max-height: 400px;
            object-fit: cover;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        }

        .options-side {
            flex: 1;
            min-width: 300px;
        }

        .options-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
            margin-top: 20px;
        }

        .option-card {
            background: #f8f9fa;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            padding: 15px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .option-card:hover:not(.disabled) {
            background: #667eea;
            border-color: #667eea;
            color: white;
            transform: translateY(-2px);
        }

        .option-card.disabled {
            cursor: not-allowed;
            opacity: 0.7;
        }

        .option-card.correct-answer {
            background: #28a745;
            border-color: #28a745;
            color: white;
        }

        .option-card.wrong-answer {
            background: #dc3545;
            border-color: #dc3545;
            color: white;
        }

        .feedback-message {
            margin-top: 20px;
            padding: 10px;
            border-radius: 5px;
            text-align: center;
            font-weight: bold;
        }

        .feedback-correct {
            background: #d4edda;
            color: #155724;
        }

        .feedback-wrong {
            background: #f8d7da;
            color: #721c24;
        }

        .next-indicator {
            text-align: center;
            margin-top: 20px;
            color: #667eea;
            font-size: 14px;
        }

        .navigation-buttons {
            display: flex;
            justify-content: flex-end;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e0e0e0;
        }

        @media (max-width: 768px) {
            .quiz-layout {
                flex-direction: column;
            }

            .options-grid {
                grid-template-columns: 1fr;
            }
        }

        .quiz-complete {
            text-align: center;
            padding: 40px;
            background: white;
            border-radius: 10px;
        }

        .quiz-complete h2 {
            color: #28a745;
            margin-bottom: 20px;
        }

        .btn:hover {
            transform: scale(1.1);
            transition: 0.3s;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="quiz-header">
            <h1>F1 Track Quiz</h1>
            <div class="progress-bar-container">
                <div class="progress-bar" id="progressBar"></div>
            </div>
            <div class="quiz-stats">
                <span id="questionCounter">Kérdés 1/24</span>
                <span id="scoreCounter">Pontszám: 0</span>
            </div>
        </div>

        <form id="quizForm" method="POST" action="../backend/submit_quiz_multiple.php">
            <div id="questionsContainer">
                <?php foreach ($tracks as $index => $track):
                    // Generáljunk 3 random másik pályát (helytelen válaszok)
                    $other_tracks = array_diff(array_keys($all_track_names), [$track['id']]);
                    shuffle($other_tracks);
                    $wrong_options = array_slice($other_tracks, 0, 3);

                    $options = [];
                    $options[] = ['id' => $track['id'], 'name' => $track['name']]; // helyes

                    foreach ($wrong_options as $wrong_id) {
                        $options[] = ['id' => $wrong_id, 'name' => $all_track_names[$wrong_id]];
                    }

                    // Megkeverjük a válaszlehetőségeket
                    shuffle($options);
                ?>
                    <div class="question-card" data-question="<?php echo $index; ?>" data-track-id="<?php echo $track['id']; ?>" data-correct-name="<?php echo htmlspecialchars($track['name']); ?>" style="display: <?php echo $index === 0 ? 'block' : 'none'; ?>">
                        <div class="question-number">Kérdés <?php echo $index + 1; ?> / 24</div>

                        <div class="quiz-layout">
                            <div class="track-image-side">
                                <img src="<?php echo htmlspecialchars($track['image_url']); ?>" alt="Track <?php echo $index + 1; ?>">
                            </div>

                            <div class="options-side">
                                <h3>Melyik pálya ez?</h3>
                                <div class="options-grid" data-question-idx="<?php echo $index; ?>">
                                    <?php foreach ($options as $opt): ?>
                                        <div class="option-card" data-track-id="<?php echo $opt['id']; ?>" data-track-name="<?php echo htmlspecialchars($opt['name']); ?>">
                                            <?php echo htmlspecialchars($opt['name']); ?>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                                <div class="feedback-message" id="feedback_<?php echo $index; ?>"></div>
                                <div class="next-indicator" id="next_indicator_<?php echo $index; ?>" style="display: none;">
                                    ⏩ Következő kérdés...
                                </div>
                            </div>
                        </div>

                        <input type="hidden" name="answers[<?php echo $track['id']; ?>]" id="answer_<?php echo $track['id']; ?>" value="">
                        <input type="hidden" name="track_ids[]" value="<?php echo $track['id']; ?>">
                    </div>
                <?php endforeach; ?>
            </div>
        </form>
        <a href="index.php" style="background-color: #2c50f0; color: white" class="btn">Vissza a főoldalra</a>
    </div>

    <script>
        let currentQuestion = 0;
        let totalQuestions = 24;
        let userAnswers = {};
        let autoTransitionTimeout = null;

        document.addEventListener('DOMContentLoaded', function() {
            updateProgress();
            attachOptionClickHandlers();
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

            // Ellenőrizzük, hogy a kérdés már meg lett-e válaszolva
            const questionIndex = parseInt(questionCard.dataset.question);
            if (userAnswers[questionIndex] && userAnswers[questionIndex].answered) {
                return; // Ha már válaszolt, ne engedjük módosítani
            }

            const correctTrackId = questionCard.dataset.trackId;
            const selectedTrackId = option.dataset.trackId;
            const selectedTrackName = option.dataset.trackName;
            const correctTrackName = questionCard.dataset.correctName;
            const questionId = questionCard.querySelector('input[type="hidden"]').id.replace('answer_', '');

            // Letiltjuk az összes opciót ebben a kérdésben
            const optionsGrid = option.closest('.options-grid');
            const allOptions = optionsGrid.querySelectorAll('.option-card');

            // Kiemeljük a helyes és helytelen választ
            allOptions.forEach(opt => {
                opt.style.pointerEvents = 'none';
                opt.classList.add('disabled');

                if (opt.dataset.trackId === correctTrackId) {
                    opt.classList.add('correct-answer');
                }
                if (opt === option && selectedTrackId !== correctTrackId) {
                    opt.classList.add('wrong-answer');
                }
            });

            // Kiértékelés
            const isCorrect = (selectedTrackId === correctTrackId);

            // Mentés
            userAnswers[questionIndex] = {
                track_id: questionId,
                selected_id: selectedTrackId,
                selected_name: selectedTrackName,
                is_correct: isCorrect,
                answered: true
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
                    feedbackDiv.innerHTML = '✅ Helyes! Szép volt!';
                    feedbackDiv.className = 'feedback-message feedback-correct';
                } else {
                    feedbackDiv.innerHTML = `❌ Helytelen! A jó válasz: ${correctTrackName}`;
                    feedbackDiv.className = 'feedback-message feedback-wrong';
                }
            }

            // Score frissítése
            updateScore();

            // Továbblépés jelzése
            const nextIndicator = document.getElementById(`next_indicator_${questionIndex}`);
            if (nextIndicator) {
                nextIndicator.style.display = 'block';
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

            const scoreCounter = document.getElementById('scoreCounter');
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
            document.querySelectorAll('.question-card').forEach(card => {
                card.style.display = 'none';
            });

            // Show selected question
            const targetQuestion = document.querySelector(`.question-card[data-question="${questionIndex}"]`);
            if (targetQuestion) {
                targetQuestion.style.display = 'block';
                currentQuestion = questionIndex;
                updateProgress();

                // Ha a kérdés már meg lett válaszolva, ne csináljunk semmit
                // (már úgyis látszik a feedback és a letiltott opciók)
            }
        }

        function showSubmitButton() {
            const submitBtn = document.getElementById('submitBtn');

            // Opcionális: üzenet, hogy kész a kvíz
            const container = document.querySelector('#questionsContainer');
            const lastQuestion = document.querySelector('.question-card:last-child');
            if (lastQuestion) {
                const completeDiv = document.createElement('div');
                completeDiv.className = 'quiz-complete';
                completeDiv.innerHTML = `
                    <h2>You've completed all questions!</h2>
                    <p>Click the submit button to see your results.</p>
                    <div class="navigation-buttons">
                            <button type="submit" class="btn-submit" id="submitBtn";">Beküldés ✅</button>
                    </div>
                `;
                lastQuestion.appendChild(completeDiv);
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
                questionCounter.textContent = `Kérdés ${currentQuestion + 1}/${totalQuestions}`;
            }
        }
    </script>
</body>

</html>
