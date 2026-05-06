<?php
// frontend/results.php
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
<html lang="hu">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
    <title>Eredmények - F1 Quiz</title>
    <link rel="stylesheet" href="style.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            position: relative;
            overflow-x: hidden;
            min-height: 100vh;
            margin: 0;
            padding: 0;
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
        }

        /* Piros buborék animáció */
        .bg-bubbles {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 0;
            pointer-events: none;
            margin: 0;
            padding: 0;
            overflow: hidden;
        }

        .bg-bubbles li {
            position: absolute;
            list-style: none;
            display: block;
            width: 40px;
            height: 40px;
            background: red;
            bottom: -160px;
            animation: square 25s infinite;
            animation-timing-function: linear;
            border-radius: 50%;
            opacity: 0.3;
        }

        .bg-bubbles li:nth-child(1) { left: 10%; width: 80px; height: 80px; animation-delay: 0s; }
        .bg-bubbles li:nth-child(2) { left: 20%; width: 40px; height: 40px; animation-delay: 2s; animation-duration: 17s; }
        .bg-bubbles li:nth-child(3) { left: 25%; width: 120px; height: 120px; animation-delay: 4s; }
        .bg-bubbles li:nth-child(4) { left: 40%; width: 60px; height: 60px; animation-delay: 0s; animation-duration: 22s; }
        .bg-bubbles li:nth-child(5) { left: 70%; width: 50px; height: 50px; animation-delay: 0s; }
        .bg-bubbles li:nth-child(6) { left: 80%; width: 110px; height: 110px; animation-delay: 3s; }
        .bg-bubbles li:nth-child(7) { left: 32%; width: 150px; height: 150px; animation-delay: 7s; }
        .bg-bubbles li:nth-child(8) { left: 55%; width: 45px; height: 45px; animation-delay: 15s; animation-duration: 40s; }
        .bg-bubbles li:nth-child(9) { left: 15%; width: 35px; height: 35px; animation-delay: 2s; animation-duration: 40s; }
        .bg-bubbles li:nth-child(10) { left: 90%; width: 140px; height: 140px; animation-delay: 11s; }

        @keyframes square {
            0% { transform: translateY(0) rotate(0deg); opacity: 0.3; }
            100% { transform: translateY(-1000px) rotate(720deg); opacity: 0; }
        }

        .container {
            position: relative;
            z-index: 1;
            max-width: 1000px;
            margin: 0 auto;
            padding: 20px;
            animation: fadeIn 0.8s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Header - üveghatás */
        .results-header {
            background: rgba(41, 36, 36, 0.95);
            backdrop-filter: blur(10px);
            padding: 30px;
            border-radius: 16px;
            text-align: center;
            margin-bottom: 30px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .results-header:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.3);
        }

        /* Logó konténer */
        .logo-container {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 15px;
            margin-bottom: 20px;
            animation: slideIn 0.8s ease-out;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateX(-30px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .f1-logo-header {
            width: 150px;
            height: auto;
            transition: all 0.3s ease;
            filter: drop-shadow(0 0 5px rgba(225, 6, 0, 0.5));
            animation: logoPulse 2s infinite;
        }

        @keyframes logoPulse {
            0% {
                transform: scale(1);
                filter: drop-shadow(0 0 5px rgba(225, 6, 0, 0.5));
            }
            50% {
                transform: scale(1.05);
                filter: drop-shadow(0 0 15px rgba(225, 6, 0, 0.8));
            }
            100% {
                transform: scale(1);
                filter: drop-shadow(0 0 5px rgba(225, 6, 0, 0.5));
            }
        }

        .f1-logo-header:hover {
            transform: scale(1.1) rotate(5deg);
            filter: drop-shadow(0 0 20px rgba(225, 6, 0, 1));
        }

        .logo-container h1 {
            background: linear-gradient(135deg, #e10600, #ff4d4d);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            font-size: 28px;
            letter-spacing: 2px;
            animation: textGlow 2s infinite;
        }

        @keyframes textGlow {
            0% {
                text-shadow: 0 0 0px rgba(225, 6, 0, 0);
            }
            50% {
                text-shadow: 0 0 10px rgba(225, 6, 0, 0.5);
            }
            100% {
                text-shadow: 0 0 0px rgba(225, 6, 0, 0);
            }
        }

        .score-circle {
            display: inline-block;
            margin-top: 10px;
            animation: scaleIn 0.5s ease-out 0.3s both;
        }

        @keyframes scaleIn {
            from {
                transform: scale(0);
                opacity: 0;
            }
            to {
                transform: scale(1);
                opacity: 1;
            }
        }

        .score-percent {
            font-size: 64px;
            font-weight: bold;
            background: linear-gradient(135deg, lime, darkgreen);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .score-details {
            color: #ddd;
            font-size: 18px;
            margin-top: 10px;
        }

        /* Eredmény lista */
        .results-details {
            background: rgba(41, 36, 36, 0.95);
            backdrop-filter: blur(10px);
            padding: 25px;
            border-radius: 16px;
            margin-bottom: 30px;
            max-height: 500px;
            overflow-y: auto;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .results-details:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.3);
        }

        .results-details h2 {
            color: #e10600;
            margin-bottom: 20px;
            font-size: 20px;
            border-bottom: 2px solid #e10600;
            padding-bottom: 10px;
        }

        /* Scrollbar stílus */
        .results-details::-webkit-scrollbar {
            width: 8px;
        }

        .results-details::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
        }

        .results-details::-webkit-scrollbar-thumb {
            background: #e10600;
            border-radius: 10px;
        }

        .results-details::-webkit-scrollbar-thumb:hover {
            background: #ff4d4d;
        }

        .result-item {
            background: rgba(255, 255, 255, 0.05);
            padding: 15px;
            margin-bottom: 12px;
            border-radius: 12px;
            border-left: 4px solid;
            transition: all 0.3s ease;
            animation: slideInRight 0.5s ease-out;
            animation-fill-mode: both;
        }

        @keyframes slideInRight {
            from {
                opacity: 0;
                transform: translateX(-20px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        /* Animációs késleltetések */
        <?php foreach ($results['results'] as $index => $result): ?>
        .result-item:nth-child(<?php echo $index + 1; ?>) {
            animation-delay: <?php echo $index * 0.05; ?>s;
        }
        <?php endforeach; ?>

        .result-item:hover {
            transform: translateX(5px);
            background: rgba(255, 255, 255, 0.1);
        }

        .result-item.correct {
            border-left-color: #28a745;
        }

        .result-item.incorrect {
            border-left-color: #dc3545;
        }

        .result-number {
            font-size: 14px;
            color: #888;
            margin-bottom: 8px;
        }

        .result-track-name {
            color: #ddd;
            margin-bottom: 5px;
            font-size: 15px;
        }

        .result-track-name strong {
            color: #28a745;
        }

        .result-user-answer {
            color: #ddd;
            margin-bottom: 8px;
            font-size: 15px;
        }

        .result-user-answer strong {
            color: #ffc107;
        }

        .result-status {
            font-size: 14px;
            font-weight: bold;
        }

        .result-item.correct .result-status {
            color: #28a745;
        }

        .result-item.incorrect .result-status {
            color: #dc3545;
        }

        /* Gombok */
        .results-actions {
            display: flex;
            gap: 20px;
            justify-content: center;
            flex-wrap: wrap;
        }

        .btn {
            display: inline-block;
            padding: 12px 30px;
            font-size: 16px;
            font-weight: 600;
            border: none;
            border-radius: 50px;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background: linear-gradient(135deg, #2c50f0, #5a7eff);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(44, 80, 240, 0.4);
        }

        .btn-secondary {
            background: linear-gradient(135deg, #e10600, #ff4d4d);
            color: white;
        }

        .btn-secondary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(225, 6, 0, 0.4);
        }

        /* Reszponzív */
        @media (max-width: 768px) {
            .container {
                padding: 15px;
            }
            
            .logo-container h1 {
                font-size: 22px;
            }
            
            .f1-logo-header {
                width: 50px;
            }
            
            .score-percent {
                font-size: 48px;
            }
            
            .results-header, .results-details {
                padding: 20px;
            }
            
            .btn {
                padding: 10px 25px;
                font-size: 14px;
            }
        }

        @media (max-width: 480px) {
            .logo-container {
                flex-direction: column;
                gap: 10px;
            }
            
            .logo-container h1 {
                font-size: 18px;
            }
            
            .f1-logo-header {
                width: 45px;
            }
            
            .score-percent {
                font-size: 36px;
            }
            
            .score-details {
                font-size: 14px;
            }
            
            .result-track-name, .result-user-answer {
                font-size: 13px;
            }
            
            .results-actions {
                gap: 15px;
            }
            
            .btn {
                padding: 8px 20px;
                font-size: 13px;
            }
        }
    </style>
</head>

<body>
    <!-- Piros buborékok -->
    <ul class="bg-bubbles">
        <li></li>
        <li></li>
        <li></li>
        <li></li>
        <li></li>
        <li></li>
        <li></li>
        <li></li>
        <li></li>
        <li></li>
    </ul>

    <div class="container">
        <div class="results-header">
            <div class="logo-container">
                <img src="../backend/uploads/f1.png" alt="F1 Logo" class="f1-logo-header" draggable="false">
                <h1>EREDMÉNYEK</h1>
            </div>
            <div class="score-circle">
                <div class="score-percent"><?php echo $results['score_percent']; ?>%</div>
                <div class="score-details">
                    <?php echo $results['correct_count']; ?> / <?php echo $results['total_questions']; ?> helyes
                </div>
            </div>
        </div>

        <div class="results-details">
            <h2>Válaszok részletei</h2>
            <?php foreach ($results['results'] as $index => $result): ?>
                <div class="result-item <?php echo $result['is_correct'] ? 'correct' : 'incorrect'; ?>">
                    <div class="result-number">Kérdés <?php echo $index + 1; ?></div>
                    <div class="result-track-name">
                        <strong>Helyes válasz:</strong> <?php echo htmlspecialchars($result['correct_name']); ?>
                    </div>
                    <div class="result-user-answer">
                        <strong>Te válaszod:</strong> <?php echo htmlspecialchars($result['user_answer'] ?: '(nincs válasz)'); ?>
                    </div>
                    <div class="result-status">
                        <?php echo $result['is_correct'] ? 'Helyes' : 'Helytelen'; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="results-actions">
            <a href="index.php" class="btn btn-primary">Vissza a főoldalra</a>
            <a href="quiz.php" class="btn btn-secondary">Kitöltés újra</a>
        </div>
    </div>
</body>

</html>
