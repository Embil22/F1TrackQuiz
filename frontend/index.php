<?php
require_once '../backend/config.php';
redirectIfNotLoggedIn();
$stats = getUserStats($pdo, $_SESSION['user_id']);

$stats = getUserStats($pdo, $_SESSION['user_id']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>F1 Quiz - Home</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🏎️ F1 Track Quiz 🏁</h1>
            <div class="user-info">
                <span>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</span>
                <a href="../backend/logout.php" class="btn-logout">Logout</a>
            </div>
        </div>
        
        <div class="stats-card">
            <h2>Your Statistics</h2>
            <div class="stats-grid">
                <div class="stat-item">
                    <div class="stat-value"><?php echo $stats['attempts']; ?></div>
                    <div class="stat-label">Quiz Attempts</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value"><?php echo $stats['avg_score']; ?>%</div>
                    <div class="stat-label">Average Score</div>
                </div>
            </div>
        </div>
        
        <div class="quiz-info">
            <h2>Test Your Knowledge!</h2>
            <p>Can you identify all 24 Formula 1 circuits from their images?</p>
            <ul>
                <li>📸 24 tracks to identify</li>
                <li>✏️ Type the exact track name</li>
                <li>📊 Track your progress</li>
                <li>🏆 Compare your scores</li>
            </ul>
            <a href="quiz.php" class="btn btn-start">Start New Quiz 🚀</a>
        </div>
    </div>
</body>
</html>
