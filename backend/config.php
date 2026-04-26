<?php
// backend/config.php
session_start();

$host = 'localhost';
$dbname = 'f1_quiz';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function redirectIfNotLoggedIn() {
    if (!isLoggedIn()) {
        header('Location: ../frontend/login.php');  // Útvonal módosítva!
        exit();
    }
}

function getUserStats($pdo, $user_id) {
    $stmt = $pdo->prepare("SELECT COUNT(*) as attempts, AVG(score_percent) as avg_score FROM quiz_attempts WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    return [
        'attempts' => $result['attempts'] ?? 0,
        'avg_score' => round($result['avg_score'] ?? 0, 1)
    ];
}
?>