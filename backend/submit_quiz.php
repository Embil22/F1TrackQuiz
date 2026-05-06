<?php
// backend/submit_quiz_multiple.php
require_once '../backend/config.php';
redirectIfNotLoggedIn();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $answers = $_POST['answers'] ?? [];
    $track_ids = $_POST['track_ids'] ?? [];
    
    $correct_count = 0;
    $total_questions = count($track_ids);
    
    // Get all correct track names
    $placeholders = str_repeat('?,', count($track_ids) - 1) . '?';
    $stmt = $pdo->prepare("SELECT id, name FROM tracks WHERE id IN ($placeholders)");
    $stmt->execute($track_ids);
    $correct_tracks = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
    
    // Evaluate answers
    $results = [];
    foreach ($track_ids as $track_id) {
        $user_selected_id = $answers[$track_id] ?? null;
        $correct_name = $correct_tracks[$track_id] ?? '';
        
        // Get selected track name
        $selected_name = '';
        if ($user_selected_id) {
            $stmt2 = $pdo->prepare("SELECT name FROM tracks WHERE id = ?");
            $stmt2->execute([$user_selected_id]);
            $selected = $stmt2->fetch(PDO::FETCH_ASSOC);
            $selected_name = $selected['name'] ?? '';
        }
        
        $is_correct = ($user_selected_id == $track_id);
        
        if ($is_correct) {
            $correct_count++;
        }
        
        $results[] = [
            'track_id' => $track_id,
            'correct_name' => $correct_name,
            'user_answer' => $selected_name ?: '(no answer)',
            'is_correct' => $is_correct
        ];
    }
    
    $score_percent = round(($correct_count / $total_questions) * 100);
    
    // Save attempt to database
    $stmt = $pdo->prepare("INSERT INTO quiz_attempts (user_id, score_percent, total_questions, correct_answers) VALUES (?, ?, ?, ?)");
    $stmt->execute([$_SESSION['user_id'], $score_percent, $total_questions, $correct_count]);
    
    // Store results in session for display
    $_SESSION['quiz_results'] = [
        'score_percent' => $score_percent,
        'correct_count' => $correct_count,
        'total_questions' => $total_questions,
        'results' => $results
    ];
    
    header('Location: ../frontend/results.php');
    exit();
} else {
    header('Location: ../frontend/quiz.php');
    exit();
}
?>