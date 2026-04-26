<?php
require_once '../backend/config.php';
redirectIfNotLoggedIn();

if (!isset($_SESSION['quiz_results'])) {
    header('Location: index.php');
    exit();
}

$results = $_SESSION['quiz_results'];
unset($_SESSION['quiz_results']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quiz Results - F1 Quiz</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <div class="results-header">
            <h1>🏆 Quiz Results 🏆</h1>
            <div class="score-circle">
                <div class="score-percent"><?php echo $results['score_percent']; ?>%</div>
                <div class="score-details">
                    <?php echo $results['correct_count']; ?> / <?php echo $results['total_questions']; ?> correct
                </div>
            </div>
        </div>
        
        <div class="results-details">
            <h2>Detailed Answers</h2>
            <?php foreach ($results['results'] as $index => $result): ?>
                <div class="result-item <?php echo $result['is_correct'] ? 'correct' : 'incorrect'; ?>">
                    <div class="result-number">Question <?php echo $index + 1; ?></div>
                    <div class="result-track-name">
                        <strong>Correct:</strong> <?php echo htmlspecialchars($result['correct_name']); ?>
                    </div>
                    <div class="result-user-answer">
                        <strong>Your answer:</strong> <?php echo htmlspecialchars($result['user_answer'] ?: '(no answer)'); ?>
                    </div>
                    <div class="result-status">
                        <?php echo $result['is_correct'] ? '✅ Correct' : '❌ Incorrect'; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        
        <div class="results-actions">
            <a href="index.php" class="btn btn-primary">Back to Home</a>
            <a href="quiz.php" class="btn btn-secondary">Take Quiz Again</a>
        </div>
    </div>
</body>
</html>