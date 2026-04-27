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
            <h1>F1 Track Quiz</h1>
            <div class="user-info">
                <span>Üdvözlet, <?php echo htmlspecialchars($_SESSION['username']); ?>!</span>
                <a href="../backend/logout.php" class="btn-logout">Kijelentkezés</a>
            </div>
        </div>

        <div class="stats-card">
            <h2>Statisztikáid</h2>
            <div class="stats-grid">
                <div class="stat-item">
                    <div class="stat-value"><?php echo $stats['attempts']; ?></div>
                    <div class="stat-label">Quiz próbálkozások</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value"><?php echo $stats['avg_score']; ?>%</div>
                    <div class="stat-label">Átlagos pontszám</div>
                </div>
            </div>
        </div>
        <div class="quiz-info">
            <h2>Tesztelje tudását!</h2>
            <p>Fel tudod ismerni mind a 24 Forma-1-es pályát a képek alapján?</p>
            <a href="quiz.php" class="btn btn-start">Start Quiz</a>
        </div>
    </div>
</body>
</html>
