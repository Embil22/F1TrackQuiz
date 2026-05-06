<?php
// frontend/index.php
require_once '../backend/config.php';
redirectIfNotLoggedIn();
$stats = getUserStats($pdo, $_SESSION['user_id']);

// Részletes statisztikák lekérése a diagramhoz
$stmt = $pdo->prepare("SELECT score_percent, completed_at FROM quiz_attempts WHERE user_id = ? ORDER BY completed_at ASC");
$stmt->execute([$_SESSION['user_id']]);
$attempts_data = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Kategóriákba sorolás a kördiagramhoz
$excellent = 0; // 90-100%
$good = 0;      // 70-89%
$average = 0;   // 50-69%
$poor = 0;      // 30-49%
$bad = 0;       // 0-29%

foreach ($attempts_data as $attempt) {
    $score = $attempt['score_percent'];
    if ($score >= 90) {
        $excellent++;
    } elseif ($score >= 70) {
        $good++;
    } elseif ($score >= 50) {
        $average++;
    } elseif ($score >= 30) {
        $poor++;
    } else {
        $bad++;
    }
}

// Legjobb és legrosszabb eredmény
$best = 0;
$worst = 100;
foreach ($attempts_data as $attempt) {
    if ($attempt['score_percent'] > $best) $best = $attempt['score_percent'];
    if ($attempt['score_percent'] < $worst) $worst = $attempt['score_percent'];
}
if ($stats['attempts'] == 0) {
    $best = 0;
    $worst = 0;
}
?>

<!DOCTYPE html>
<html lang="hu">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
    <title>F1 Quiz - Kezdőlap</title>
    <link rel="stylesheet" href="style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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

        /* Header - üveghatás */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: rgba(41, 36, 36, 0.95);
            backdrop-filter: blur(10px);
            padding: 20px 30px;
            border-radius: 16px;
            margin-bottom: 30px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .header:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.3);
        }

        /* Logó konténer */
        .logo-container {
            display: flex;
            align-items: center;
            gap: 15px;
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

        .user-info {
            display: flex;
            gap: 15px;
            align-items: center;
            color: #ddd;
        }

        .btn-logout {
            background: linear-gradient(135deg, #dc3545, #ff6b6b);
            color: white;
            padding: 8px 20px;
            border-radius: 8px;
            text-decoration: none;
            transition: all 0.3s ease;
            font-weight: 500;
        }

        .btn-logout:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(220, 53, 69, 0.4);
        }

        /* Statisztikai kártyák */
        .stats-container {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 30px;
            margin-bottom: 30px;
        }

        .stats-card {
            background: rgba(41, 36, 36, 0.95);
            backdrop-filter: blur(10px);
            padding: 25px;
            border-radius: 16px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .stats-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.3);
        }

        .stats-card h2 {
            margin-bottom: 20px;
            color: #e10600;
            border-bottom: 2px solid #e10600;
            padding-bottom: 10px;
            font-size: 20px;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
        }

        .stat-item {
            text-align: center;
            padding: 15px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            transition: all 0.3s ease;
        }

        .stat-item:hover {
            background: rgba(255, 255, 255, 0.15);
            transform: scale(1.02);
        }

        .stat-value {
            font-size: 36px;
            font-weight: bold;
        }

        .stat-label {
            color: #aaa;
            margin-top: 5px;
            font-size: 14px;
        }

        /* Diagram konténer */
        .chart-container {
            background: rgba(41, 36, 36, 0.95);
            backdrop-filter: blur(10px);
            padding: 25px;
            border-radius: 16px;
            margin-bottom: 30px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .chart-container:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.3);
        }

        .chart-container h2 {
            margin-bottom: 20px;
            color: #e10600;
            border-bottom: 2px solid #e10600;
            padding-bottom: 10px;
            font-size: 20px;
        }

        .chart-wrapper {
            display: flex;
            justify-content: center;
            align-items: center;
            flex-wrap: wrap;
            gap: 30px;
        }

        canvas {
            max-width: 300px;
            max-height: 300px;
            transition: transform 0.3s ease;
        }

        canvas:hover {
            transform: scale(1.02);
        }

        .chart-legend {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .legend-item {
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 14px;
            color: #ddd;
            padding: 8px 12px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .legend-item:hover {
            background: rgba(255, 255, 255, 0.1);
            transform: translateX(5px);
        }

        .legend-color {
            width: 20px;
            height: 20px;
            border-radius: 50%;
            transition: transform 0.3s ease;
        }

        .legend-item:hover .legend-color {
            transform: scale(1.1);
        }

        .legend-color.excellent { background: #28a745; box-shadow: 0 0 5px #28a745; }
        .legend-color.good { background: #17a2b8; box-shadow: 0 0 5px #17a2b8; }
        .legend-color.average { background: #ffc107; box-shadow: 0 0 5px #ffc107; }
        .legend-color.poor { background: #fd7e14; box-shadow: 0 0 5px #fd7e14; }
        .legend-color.bad { background: #dc3545; box-shadow: 0 0 5px #dc3545; }

        .no-data-message {
            text-align: center;
            padding: 40px;
            color: #aaa;
        }

        /* Kvíz info kártya */
        .quiz-info {
            background: rgba(41, 36, 36, 0.95);
            backdrop-filter: blur(10px);
            padding: 35px;
            border-radius: 16px;
            text-align: center;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .quiz-info:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.3);
        }

        .quiz-info h2 {
            color: #e10600;
            margin-bottom: 15px;
            font-size: 24px;
        }

        .quiz-info p {
            color: #aaa;
            margin-bottom: 25px;
            font-size: 16px;
        }

        .btn-start {
            display: inline-block;
            background: linear-gradient(135deg, #e10600, #ff4d4d);
            color: white;
            padding: 14px 35px;
            font-size: 18px;
            font-weight: 600;
            border: none;
            border-radius: 50px;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.3s ease;
            animation: pulse 2s infinite;
        }

        .btn-start:hover {
            transform: scale(1.05);
            box-shadow: 0 5px 25px rgba(225, 6, 0, 0.5);
        }

        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.02); }
            100% { transform: scale(1); }
        }

        .best-score {
            color: #28a745;
        }

        .worst-score {
            color: #dc3545;
        }

        .percentage-badge {
            font-size: 12px;
            color: #888;
            margin-left: 5px;
        }

        /* Reszponzív beállítások */
        @media (max-width: 768px) {
            .container {
                padding: 15px;
            }
            
            .stats-container {
                grid-template-columns: 1fr;
                gap: 20px;
            }
            
            .header {
                flex-direction: column;
                gap: 15px;
                text-align: center;
                padding: 20px;
            }
            
            .logo-container h1 {
                font-size: 20px;
            }
            
            .f1-logo-header {
                width: 75px;
            }
            
            .stats-card, .chart-container, .quiz-info {
                padding: 20px;
            }
            
            .stat-value {
                font-size: 28px;
            }
            
            .chart-wrapper {
                flex-direction: column;
            }
            
            .btn-start {
                padding: 12px 25px;
                font-size: 16px;
            }
        }

        @media (max-width: 480px) {
            .stats-grid {
                grid-template-columns: 1fr;
                gap: 15px;
            }
            
            .logo-container h1 {
                font-size: 16px;
            }
            
            .f1-logo-header {
                width: 75px;
            }
            
            .user-info span {
                font-size: 14px;
            }
            
            .btn-logout {
                padding: 6px 15px;
                font-size: 14px;
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
        <div class="header">
            <div class="logo-container">
                <img src="../backend/uploads/f1.png" alt="F1 Logo" class="f1-logo-header" draggable="false">
                <h1>TRACK QUIZ</h1>
            </div>
            <div class="user-info">
                <span>Üdvözlet, <?php echo htmlspecialchars($_SESSION['username']); ?>!</span>
                <a href="logout.php" class="btn-logout">Kijelentkezés</a>
            </div>
        </div>

        <div class="stats-container">
            <!-- Statisztikai kártyák -->
            <div class="stats-card">
                <h2>📊 Összesített statisztika</h2>
                <div class="stats-grid">
                    <div class="stat-item">
                        <div class="stat-value"><?php echo $stats['attempts']; ?></div>
                        <div class="stat-label">Összes kitöltés</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value <?php echo $stats['avg_score'] >= 50 ? 'best-score' : ($stats['avg_score'] <= 30 ? 'worst-score' : ''); ?>">
                            <?php echo $stats['avg_score']; ?>%
                        </div>
                        <div class="stat-label">Átlagos pontszám</div>
                    </div>
                </div>
            </div>

            <!-- Legjobb/legrosszabb eredmények -->
            <div class="stats-card">
                <h2>🎖️Eredmények</h2>
                <div class="stats-grid">
                    <div class="stat-item">
                        <div class="stat-value best-score"><?php echo $best; ?>%</div>
                        <div class="stat-label">🏅 Legjobb eredmény</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value worst-score"><?php echo $worst; ?>%</div>
                        <div class="stat-label">📉 Legrosszabb eredmény</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Kördiagram -->
        <div class="chart-container">
            <h2>📈 Teljesítmény megoszlás</h2>
            <?php if ($stats['attempts'] > 0): ?>
                <div class="chart-wrapper">
                    <canvas id="pieChart"></canvas>
                    <div class="chart-legend">
                        <div class="legend-item">
                            <div class="legend-color excellent"></div>
                            <span>Kiváló (90-100%) <span class="percentage-badge">(<?php echo $excellent; ?> db)</span></span>
                        </div>
                        <div class="legend-item">
                            <div class="legend-color good"></div>
                            <span>Jó (70-89%) <span class="percentage-badge">(<?php echo $good; ?> db)</span></span>
                        </div>
                        <div class="legend-item">
                            <div class="legend-color average"></div>
                            <span>Közepes (50-69%) <span class="percentage-badge">(<?php echo $average; ?> db)</span></span>
                        </div>
                        <div class="legend-item">
                            <div class="legend-color poor"></div>
                            <span>Gyenge (30-49%) <span class="percentage-badge">(<?php echo $poor; ?> db)</span></span>
                        </div>
                        <div class="legend-item">
                            <div class="legend-color bad"></div>
                            <span>Rossz (0-29%) <span class="percentage-badge">(<?php echo $bad; ?> db)</span></span>
                        </div>
                    </div>
                </div>
                <p style="text-align: center; margin-top: 20px; color: #888; font-size: 13px;">
                    * Összesen <?php echo $stats['attempts']; ?> kitöltés elemzése
                </p>
            <?php else: ?>
                <div class="no-data-message">
                    <p>Még nincs kitöltött kvízed!</p>
                    <p>Kezdj el játszani a "Kvíz indítása" gombbal, és itt megjelennek a statisztikáid.</p>
                </div>
            <?php endif; ?>
        </div>

        <div class="quiz-info">
            <h2>Teszteld tudásod!</h2>
            <p>Fel tudod ismerni mind a 24 Forma-1-es pályát a képek alapján?</p>
            <a href="quiz.php" class="btn-start">Kvíz indítása</a>
        </div>
    </div>

    <?php if ($stats['attempts'] > 0): ?>
        <script>
            const ctx = document.getElementById('pieChart').getContext('2d');

            const excellent = <?php echo $excellent; ?>;
            const good = <?php echo $good; ?>;
            const average = <?php echo $average; ?>;
            const poor = <?php echo $poor; ?>;
            const bad = <?php echo $bad; ?>;

            new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: ['Kiváló (90-100%)', 'Jó (70-89%)', 'Közepes (50-69%)', 'Gyenge (30-49%)', 'Rossz (0-29%)'],
                    datasets: [{
                        data: [excellent, good, average, poor, bad],
                        backgroundColor: ['#28a745', '#17a2b8', '#ffc107', '#fd7e14', '#dc3545'],
                        borderColor: 'rgba(41, 36, 36, 0.95)',
                        borderWidth: 2,
                        hoverOffset: 15,
                        hoverBorderWidth: 3
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const label = context.label || '';
                                    const value = context.raw || 0;
                                    const total = excellent + good + average + poor + bad;
                                    const percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                                    return `${label}: ${value} db (${percentage}%)`;
                                }
                            },
                            backgroundColor: 'rgba(0,0,0,0.8)',
                            titleColor: '#fff',
                            bodyColor: '#ddd',
                            borderColor: '#e10600',
                            borderWidth: 1
                        }
                    },
                    animation: {
                        animateScale: true,
                        animateRotate: true,
                        duration: 1000
                    }
                }
            });
        </script>
    <?php endif; ?>
</body>

</html>
