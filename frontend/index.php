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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>F1 Quiz - Kezdőlap</title>
    <link rel="stylesheet" href="style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .stats-container {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 30px;
            margin-bottom: 30px;
        }

        .stats-card {
            background: white;
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .stats-card h2 {
            margin-bottom: 20px;
            color: #333;
            border-bottom: 2px solid #667eea;
            padding-bottom: 10px;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
        }

        .stat-item {
            text-align: center;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 10px;
        }

        .stat-value {
            font-size: 36px;
            font-weight: bold;
        }

        .stat-label {
            color: #666;
            margin-top: 5px;
            font-size: 14px;
        }

        .chart-container {
            background: white;
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
        }

        .chart-container h2 {
            margin-bottom: 20px;
            color: #333;
            border-bottom: 2px solid #667eea;
            padding-bottom: 10px;
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
        }

        .chart-legend {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .legend-item {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 14px;
        }

        .legend-color {
            width: 20px;
            height: 20px;
            border-radius: 50%;
        }

        .legend-color.excellent {
            background: #28a745;
        }

        .legend-color.good {
            background: #17a2b8;
        }

        .legend-color.average {
            background: #ffc107;
        }

        .legend-color.poor {
            background: #fd7e14;
        }

        .legend-color.bad {
            background: #dc3545;
        }

        .no-data-message {
            text-align: center;
            padding: 40px;
            color: #999;
        }

        .quiz-info {
            background: white;
            padding: 30px;
            border-radius: 15px;
            text-align: center;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .quiz-info h2 {
            color: #333;
            margin-bottom: 15px;
        }

        .quiz-info p {
            color: #666;
            margin-bottom: 20px;
        }

        @media (max-width: 768px) {
            .stats-container {
                grid-template-columns: 1fr;
            }

            .chart-wrapper {
                flex-direction: column;
            }
        }

        .best-score {
            color: #28a745;
        }

        .worst-score {
            color: #dc3545;
        }

        .avg-score {
            color: #667eea;
        }

        .percentage-badge {
            font-size: 12px;
            color: #666;
            margin-left: 5px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>🏎️ F1 Track Quiz 🏁</h1>
            <div class="user-info">
                <span>Üdvözlet, <?php echo htmlspecialchars($_SESSION['username']); ?>!</span>
                <a href="../backend/logout.php" class="btn-logout">Kijelentkezés</a>
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
                <h2>🏆 Eredmények</h2>
                <div class="stats-grid">
                    <div class="stat-item">
                        <div class="stat-value best-score"><?php echo $best; ?>%</div>
                        <div class="stat-label">Legjobb eredmény</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value worst-score"><?php echo $worst; ?>%</div>
                        <div class="stat-label">Legrosszabb eredmény</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Kördiagram -->
        <div class="chart-container">
            <h2>📊 Teljesítmény megoszlás</h2>
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
                <p style="text-align: center; margin-top: 20px; color: #666; font-size: 14px;">
                    * Összesen <?php echo $stats['attempts']; ?> kitöltés elemzése
                </p>
            <?php else: ?>
                <div class="no-data-message">
                    <p>📭 Még nincs kitöltött kvízed!</p>
                    <p>Kezdj el játszani a "Start Quiz" gombbal, és itt megjelennek a statisztikáid.</p>
                </div>
            <?php endif; ?>
        </div>

        <div class="quiz-info">
            <h2>🧠 Teszteld tudásod!</h2>
            <p>Fel tudod ismerni mind a 24 Forma-1-es pályát a képek alapján?</p>
            <a href="quiz.php" class="btn btn-start">🚀 Kvíz indítása</a>
        </div>
    </div>

    <?php if ($stats['attempts'] > 0): ?>
        <script>
            const ctx = document.getElementById('pieChart').getContext('2d');

            // Adatok a PHP-ből
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
                        backgroundColor: [
                            '#28a745', // zöld - kiváló
                            '#17a2b8', // kék - jó
                            '#ffc107', // sárga - közepes
                            '#fd7e14', // narancs - gyenge
                            '#dc3545' // piros - rossz
                        ],
                        borderColor: '#fff',
                        borderWidth: 2,
                        hoverOffset: 10
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        legend: {
                            display: false,
                            position: 'bottom'
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const label = context.label || '';
                                    const value = context.raw || 0;
                                    const total = excellent + good + average + poor + bad;
                                    const percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                                    return `${label}: ${value} db (${percentage}%)`;
                                }
                            }
                        }
                    }
                }
            });
        </script>
    <?php endif; ?>
</body>

</html>
