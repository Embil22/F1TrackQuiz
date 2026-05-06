<?php
require_once '../backend/config.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if ($password !== $confirm_password) {
        $error = 'Passwords do not match!';
    } else {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$username, $email]);

        if ($stmt->rowCount() > 0) {
            $error = 'Username or email already exists!';
        } else {
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (username, email, password_hash) VALUES (?, ?, ?)");

            if ($stmt->execute([$username, $email, $password_hash])) {
                $success = 'Registration successful! You can now login.';
            } else {
                $error = 'Registration failed!';
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - F1 Quiz</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            position: relative;
            overflow-x: hidden;
            min-height: 100vh;
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
            background: rgba(255, 255, 255, 0.15);
            bottom: -160px;
            animation: square 25s infinite;
            animation-timing-function: linear;
            border-radius: 50%;
            backdrop-filter: blur(5px);
        }

        .bg-bubbles li:nth-child(1) {
            left: 10%;
            width: 80px;
            height: 80px;
            animation-delay: 0s;
            background: red;
        }

        .bg-bubbles li:nth-child(2) {
            left: 20%;
            width: 40px;
            height: 40px;
            animation-delay: 2s;
            animation-duration: 17s;
            background: red;
        }

        .bg-bubbles li:nth-child(3) {
            left: 25%;
            width: 120px;
            height: 120px;
            animation-delay: 4s;
            background: red;
        }

        .bg-bubbles li:nth-child(4) {
            left: 40%;
            width: 60px;
            height: 60px;
            animation-delay: 0s;
            animation-duration: 22s;
            background: red;
        }

        .bg-bubbles li:nth-child(5) {
            left: 70%;
            width: 50px;
            height: 50px;
            animation-delay: 0s;
            background: red;
        }

        .bg-bubbles li:nth-child(6) {
            left: 80%;
            width: 110px;
            height: 110px;
            animation-delay: 3s;
            background: red;
        }

        .bg-bubbles li:nth-child(7) {
            left: 32%;
            width: 150px;
            height: 150px;
            animation-delay: 7s;
            background: red;
        }

        .bg-bubbles li:nth-child(8) {
            left: 55%;
            width: 45px;
            height: 45px;
            animation-delay: 15s;
            animation-duration: 40s;
            background: red;
        }

        .bg-bubbles li:nth-child(9) {
            left: 15%;
            width: 35px;
            height: 35px;
            animation-delay: 2s;
            animation-duration: 40s;
            background: red;
        }

        .bg-bubbles li:nth-child(10) {
            left: 90%;
            width: 140px;
            height: 140px;
            animation-delay: 11s;
            background: red;
        }

        @keyframes square {
            0% {
                transform: translateY(0) rotate(0deg);
                opacity: 1;
            }

            100% {
                transform: translateY(-1000px) rotate(720deg);
                opacity: 0;
            }
        }

        /* A container és form legyen előtérben */
        .container {
            position: relative;
            z-index: 1;
        }

        .form-container {
            background: #292929;
            backdrop-filter: blur(10px);
            position: relative;
            z-index: 2;
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
            <h1>Register for F1 Quiz</h1>

            <?php if ($error): ?>
                <div class="error-message"><?php echo $error; ?></div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="success-message"><?php echo $success; ?></div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="form-group">
                    <label for="username">Username:</label>
                    <input type="text" id="username" name="username" required>
                </div>

                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" required>
                </div>

                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" required>
                </div>

                <div class="form-group">
                    <label for="confirm_password">Confirm Password:</label>
                    <input type="password" id="confirm_password" name="confirm_password" required>
                </div>

                <button type="submit" class="btn btn-primary">Register</button>
            </form>

            <p class="login-link">Already have an account? <a href="login.php">Login here</a></p>
        </div>
    </div>
</body>

</html>
