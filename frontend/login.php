<?php
require_once '../backend/config.php';

$error = '';

// Ha be van jelentkezve, átirányítás
if (isLoggedIn()) {
    header('Location: index.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    
    $stmt = $pdo->prepare("SELECT id, username, password_hash FROM users WHERE username = ? OR email = ?");
    $stmt->execute([$username, $username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user && password_verify($password, $user['password_hash'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        header('Location: index.php');
        exit();
    } else {
        $error = 'Helytelen email/felhasználónév vagy jelszó!';
    }
}
?>

<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
    <title>Bejelentkezés - F1 Quiz</title>
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
        }

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
            animation: fadeIn 0.8s ease-out;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .form-container {
            max-width: 450px;
            width: 100%;
            background: rgba(41, 36, 36, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 16px;
            padding: 40px 35px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .form-container:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.3);
        }
        
        .f1-logo {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .f1-logo img {
            max-width: 180px;
            height: auto;
            display: block;
            margin: 0 auto;
            transition: filter 0.3s ease;
        }
        
        .f1-logo img:hover {
            filter: drop-shadow(0 0 10px rgba(225, 6, 0, 0.5));
        }
        
        .f1-logo h1 {
            font-size: 24px;
            margin-top: 15px;
            font-weight: bold;
            letter-spacing: 2px;
            background: linear-gradient(135deg, #e10600, #ff4d4d);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .form-group {
            margin-bottom: 25px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #ddd;
            font-size: 14px;
        }
        
        .form-group input {
            width: 100%;
            padding: 14px 16px;
            border: 1px solid #555;
            border-radius: 8px;
            font-size: 16px;
            background: rgba(255, 255, 255, 0.1);
            color: white;
            transition: all 0.3s ease;
        }
        
        .form-group input:focus {
            outline: none;
            border-color: #e10600;
            box-shadow: 0 0 0 2px rgba(225, 6, 0, 0.2);
        }
        
        /* Google stílusú checkbox - javított pipa pozíció */
        .checkbox-container {
            display: flex;
            align-items: center;
            margin: 20px 0 25px 0;
            cursor: pointer;
            user-select: none;
        }
        
        .checkbox-container input {
            position: absolute;
            opacity: 0;
            cursor: pointer;
            height: 0;
            width: 0;
        }
        
        .checkmark {
            position: relative;
            display: inline-block;
            width: 20px;
            height: 20px;
            background-color: rgba(255, 255, 255, 0.1);
            border: 1px solid #777;
            border-radius: 4px;
            margin-right: 12px;
            transition: all 0.2s ease;
            flex-shrink: 0;
        }
        
        .checkbox-container:hover input ~ .checkmark {
            background-color: rgba(255, 255, 255, 0.2);
            border-color: #e10600;
        }
        
        .checkbox-container input:checked ~ .checkmark {
            background-color: #e10600;
            border-color: #e10600;
        }
        
        .checkmark:after {
            content: "";
            position: absolute;
            display: none;
            left: 50%;
            top: 45%;
            transform: translate(-50%, -55%) rotate(45deg);
            width: 5px;
            height: 10px;
            border: solid white;
            border-width: 0 2px 2px 0;
        }
        
        .checkbox-container input:checked ~ .checkmark:after {
            display: block;
        }
        
        .checkbox-label {
            color: #ddd;
            font-size: 14px;
            cursor: pointer;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #e10600, #ff4d4d);
            color: white;
            width: 100%;
            padding: 14px;
            font-size: 16px;
            font-weight: 600;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 10px;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(225, 6, 0, 0.4);
        }
        
        .btn-primary:active {
            transform: translateY(0);
        }
        
        .login-link {
            text-align: center;
            margin-top: 25px;
            color: #aaa;
            font-size: 14px;
        }
        
        .login-link a {
            color: #e10600;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s ease;
        }
        
        .login-link a:hover {
            color: #ff4d4d;
            text-decoration: underline;
        }
        
        .error-message {
            background: rgba(220, 53, 69, 0.2);
            border: 1px solid #dc3545;
            color: #ff6b6b;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
            font-size: 14px;
            animation: shake 0.5s ease;
        }
        
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            75% { transform: translateX(5px); }
        }
        
        /* Reszponzív beállítások */
        @media (max-width: 768px) {
            .form-container {
                padding: 30px 25px;
            }
            
            .f1-logo img {
                max-width: 140px;
            }
            
            .f1-logo h1 {
                font-size: 20px;
            }
            
            .form-group input {
                padding: 12px 14px;
                font-size: 16px;
            }
            
            .btn-primary {
                padding: 12px;
            }
        }
        
        @media (max-width: 480px) {
            .form-container {
                padding: 25px 20px;
            }
            
            .f1-logo img {
                max-width: 120px;
            }
            
            .f1-logo h1 {
                font-size: 18px;
            }
        }
    </style>
</head>
<body>
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
        <div class="form-container">
            <div class="f1-logo">
                <img src="../backend/uploads/f1.png" alt="F1 Logo" draggable="false">
                <h1 style="background: linear-gradient(to bottom, #e10600, white); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">F1 TRACK QUIZ</h1>
            </div>
            
            <?php if ($error): ?>
                <div class="error-message"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <form method="POST" action="" id="loginForm">
                <div class="form-group">
                    <label for="username">Email vagy felhasználónév</label>
                    <input type="text" id="username" name="username" required autocomplete="off">
                </div>
                
                <div class="form-group">
                    <label for="password">Jelszó</label>
                    <input type="password" id="password" name="password" required>
                </div>
                
                <label class="checkbox-container">
                    <input type="checkbox" id="showPassword" onclick="togglePassword()">
                    <span class="checkmark"></span>
                    <span class="checkbox-label">Jelszó megjelenítése</span>
                </label>
                
                <button type="submit" class="btn-primary">Bejelentkezés</button>
            </form>
            
            <p class="login-link">Nincs fiókod? <a href="register.php">Fiók létrehozása</a></p>
        </div>
    </div>
    
    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const checkbox = document.getElementById('showPassword');
            
            if (checkbox.checked) {
                passwordInput.type = 'text';
            } else {
                passwordInput.type = 'password';
            }
        }
        
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            const username = document.getElementById('username').value.trim();
            const password = document.getElementById('password').value;
            
            if (!username || !password) {
                e.preventDefault();
                alert('Kérlek töltsd ki az összes mezőt!');
            }
        });
        
        document.getElementById('password').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                document.getElementById('loginForm').submit();
            }
        });
    </script>
</body>
</html>
