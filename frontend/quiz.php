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
<html lang="hu">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
    <title>F1 Quiz - Kvíz</title>
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
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            animation: fadeIn 0.8s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Quiz header - üveghatás */
        .quiz-header {
            background: rgba(41, 36, 36, 0.95);
            backdrop-filter: blur(10px);
            padding: 25px 30px;
            border-radius: 16px;
            margin-bottom: 30px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .quiz-header:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.3);
        }

        /* Logó konténer a fejlécben */
        .quiz-header-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 20px;
            flex-wrap: wrap;
            gap: 15px;
        }

        .logo-container {
            display: flex;
            align-items: center;
            gap: 12px;
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
            width: 100px;
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
            font-size: 24px;
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

        .progress-bar-container {
            background: rgba(255, 255, 255, 0.2);
            border-radius: 10px;
            height: 10px;
            margin: 15px 0;
            overflow: hidden;
        }

        .progress-bar {
            background: linear-gradient(90deg, lime, darkgreen);
            height: 100%;
            width: 0%;
            transition: width 0.3s ease;
            border-radius: 10px;
        }

        .quiz-stats {
            display: flex;
            justify-content: space-between;
            color: #ddd;
            font-size: 14px;
        }

        /* Kérdés kártya - üveghatás */
        .question-card {
            background: rgba(41, 36, 36, 0.95);
            backdrop-filter: blur(10px);
            padding: 30px;
            border-radius: 16px;
            margin-bottom: 20px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .question-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.2);
        }

        .question-number {
            color: #e10600;
            font-size: 14px;
            margin-bottom: 20px;
            font-weight: bold;
        }

        .quiz-layout {
            display: flex;
            gap: 30px;
            flex-wrap: wrap;
        }

        .track-image-side {
            flex: 1;
            min-width: 300px;
            background: white;
            border-radius: 12px;
            padding: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .track-image-side img {
            width: 100%;
            max-height: 400px;
            object-fit: contain;
            border-radius: 8px;
            transition: transform 0.3s ease;
            background: white;
        }

        .track-image-side img:hover {
            transform: scale(1.02);
        }

        .options-side {
            flex: 1;
            min-width: 300px;
        }

        .options-side h3 {
            color: #ddd;
            margin-bottom: 20px;
            font-size: 20px;
        }

        .options-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
            margin-top: 10px;
        }

        .option-card {
            background: rgba(255, 255, 255, 0.1);
            border: 2px solid #555;
            border-radius: 12px;
            padding: 15px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
            color: #ddd;
            font-weight: 500;
        }

        .option-card:hover:not(.disabled) {
            background: linear-gradient(135deg, #e10600, #ff4d4d);
            border-color: #e10600;
            color: white;
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(225, 6, 0, 0.4);
        }

        .option-card.disabled {
            cursor: not-allowed;
            opacity: 0.6;
        }

        .option-card.correct-answer {
            background: #28a745;
            border-color: #28a745;
            color: white;
            box-shadow: 0 0 10px rgba(40, 167, 69, 0.5);
        }

        .option-card.wrong-answer {
            background: #dc3545;
            border-color: #dc3545;
            color: white;
            box-shadow: 0 0 10px rgba(220, 53, 69, 0.5);
        }

        .feedback-message {
            margin-top: 20px;
            padding: 12px;
            border-radius: 10px;
            text-align: center;
            font-weight: bold;
            animation: fadeInUp 0.5s ease;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .feedback-correct {
            background: rgba(40, 167, 69, 0.2);
            border: 1px solid #28a745;
            color: #28a745;
        }

        .feedback-wrong {
            background: rgba(220, 53, 69, 0.2);
            border: 1px solid #dc3545;
            color: #ff6b6b;
        }

        .next-indicator {
            text-align: center;
            margin-top: 20px;
            color: #ffc107;
            font-size: 14px;
            animation: pulse 1s infinite;
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }

        /* Vissza gomb */
        .btn-back {
            display: inline-block;
            background: linear-gradient(135deg, #2c50f0, #5a7eff);
            color: white;
            padding: 12px 25px;
            font-size: 16px;
            font-weight: 600;
            border: none;
            border-radius: 50px;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.3s ease;
            margin-top: 20px;
        }

        .btn-back:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(44, 80, 240, 0.4);
        }

        /* Beküldő gomb */
        .btn-submit {
            background: linear-gradient(135deg, #28a745, #34ce57);
            color: white;
            padding: 14px 35px;
            font-size: 18px;
            font-weight: 600;
            border: none;
            border-radius: 50px;
            cursor: pointer;
            transition: all 0.3s ease;
            width: 100%;
            margin-top: 20px;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(40, 167, 69, 0.5);
        }

        .quiz-complete {
            text-align: center;
            padding: 30px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 12px;
            margin-top: 20px;
        }

        .quiz-complete h2 {
            color: #28a745;
            margin-bottom: 15px;
        }

        .quiz-complete p {
            color: #ddd;
            margin-bottom: 20px;
        }

        /* Reszponzív */
        @media (max-width: 768px) {
            .container {
                padding: 15px;
            }
            
            .quiz-layout {
                flex-direction: column;
            }
            
            .options-grid {
                grid-template-columns: 1fr;
            }
            
            .logo-container h1 {
                font-size: 18px;
            }
            
            .f1-logo-header {
                width: 70px;
            }
            
            .question-card {
                padding: 20px;
            }
        }

        @media (max-width: 480px) {
            .quiz-header-top {
                flex-direction: column;
                text-align: center;
            }
            
            .logo-container h1 {
                font-size: 16px;
            }
            
            .f1-logo-header {
                width: 55px;
            }
            
            .option-card {
                padding: 12px;
                font-size: 14px;
            }
            
            .track-image-side {
                min-width: 250px;
            }
        }
    </style>
</head>

<body>
    <!-- Piros buborékok -->
    <ul class="bg-bubbles">
        <li></li><li></li><li></li><li></li><li></li>
        <li></li><li></li><li></li><li></li><li></li>
    </ul>

    <div class="container">
        <div class="quiz-header">
            <div class="quiz-header-top">
                <div class="logo-container">
                    <img src="../backend/uploads/f1.png" alt="F1 Logo" class="f1-logo-header" draggable="false">
                    <h1>TRACK QUIZ</h1>
                </div>
            </div>
            <div class="progress-bar-container">
                <div class="progress-bar" id="progressBar"></div>
            </div>
            <div class="quiz-stats">
                <span id="questionCounter">Kérdés 1 / 24</span>
                <span id="scoreCounter">Pontszám: 0 / 0</span>
            </div>
        </div>

        <form id="quizForm" method="POST" action="../backend/submit_quiz.php">
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
                                <img src="<?php echo htmlspecialchars($track['image_url']); ?>" alt="Pálya <?php echo $index + 1; ?>">
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
                                    Következő kérdés...
                                </div>
                            </div>
                        </div>

                        <input type="hidden" name="answers[<?php echo $track['id']; ?>]" id="answer_<?php echo $track['id']; ?>" value="">
                        <input type="hidden" name="track_ids[]" value="<?php echo $track['id']; ?>">
                    </div>
                <?php endforeach; ?>
            </div>
        </form>
        <a href="index.php" class="btn-back">Vissza a főoldalra</a>
    </div>

    <script src="script.js"></script>
</body>

</html>
