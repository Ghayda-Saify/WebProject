<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

global $con;
error_reporting(E_ALL);
ini_set('display_errors', 1);

$con = new mysqli("localhost", "root", "", "alandalus");
if ($con->connect_error) {
    die("Connection failed: " . $con->connect_error);
}

session_start();
require('../connection.php');
require('../functions.php');
require __DIR__ . '/../vendor/autoload.php';

$msg = '';
$msgType = 'error';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['txtEmail'])) {
    $email = trim($_POST['txtEmail']);
    if (empty($email)) {
        $msg = "Email address is required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $msg = "Invalid email format.";
    } else {
        try {
            $db = $con;
            $checkStmt = $db->prepare("SELECT * FROM users WHERE email = ?");
            if (!$checkStmt) {
                die("Prepare failed: " . $db->error);
            }
            $checkStmt->bind_param("s", $email);
            $checkStmt->execute();
            $result = $checkStmt->get_result();

            if ($result->num_rows > 0) {
                $token = bin2hex(random_bytes(50));
                $expiry = time() + 3600; // 1 hour from now

                // Remove old tokens
                $deleteOldTokensStmt = $db->prepare("DELETE FROM password_resets WHERE email = ?");
                $deleteOldTokensStmt->bind_param("s", $email);
                $deleteOldTokensStmt->execute();
                $deleteOldTokensStmt->close();

                // Insert new token
                $insertTokenStmt = $db->prepare("INSERT INTO password_resets (email, token, expiry) VALUES (?, ?, ?)");
                $insertTokenStmt->bind_param("ssi", $email, $token, $expiry);
                $insertTokenStmt->execute();
                $insertTokenStmt->close();

                // Send email with PHPMailer
                $mail = new PHPMailer(true);
                try {
               //     $mail->SMTPDebug = SMTP::DEBUG_SERVER; // Enable verbose debug output
                    $mail->isSMTP();
                    $mail->Host = 'smtp.gmail.com';
                    $mail->SMTPAuth = true;
                    $mail->Username = 'talaalhendiuni4@gmail.com';
                    $mail->Password = 'vweh fdau pmoz etiz';
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                    $mail->Port = 587;
                    $mail->CharSet = 'UTF-8';

                    $mail->setFrom('talaalhendiuni4@gmail.com', 'Alandalus Design');
                    $mail->addAddress($email);

                    $resetLink = "http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . "/reset_password.php?token=" . $token;
                    $mail->isHTML(true);
                    $mail->Subject = 'Password Reset Request';
                    $mail->Body = "<p>Hello,</p><p>Click the link below to reset your password. This link will expire in 1 hour.</p><p><a href='{$resetLink}'>Reset Password</a></p><p>If you did not request a password reset, please ignore this email.</p>";
                    $mail->AltBody = "Hello,\n\nClick the link below to reset your password. This link will expire in 1 hour.\n{$resetLink}\nIf you did not request a password reset, please ignore this email.";

                    $mail->send();
                    $msg = "A password reset link has been sent to your email address. Please check your inbox (and spam folder).";
                    $msgType = 'success';
                } catch (Exception $e) {
                    $msg = "Failed to send email. Error: " . $mail->ErrorInfo;
                }
            } else {
                $msg = "If this email exists in our system, a password reset link will be sent.";
            }
            $checkStmt->close();
        } catch (Exception $e) {
            $msg = "An error occurred: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <link rel="stylesheet" href="loginSignUp.css">
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background-color: #f0f2f5;
            font-family: 'Cairo', sans-serif;
        }
        .form-container {
            background-color: #fff;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            width: 100%;
            max-width: 400px;
            text-align: center;
        }
        .form-container h1 {
            margin-bottom: 20px;
            color: #122c6f;
        }
        .form-container input[type="email"] {
            width: calc(100% - 20px);
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .form-container button {
            background-color: #122c6f;
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s;
        }
        .form-container button:hover {
            background-color: #0e235c;
        }
        .message {
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 4px;
            font-family: 'Cairo', sans-serif;
        }
        .error-message {
            color: #f13b1c;
            background-color: #ffe5e5;
        }
        .success-message {
            color: #28a745;
            background-color: #e8f5e9;
        }
        .back-link {
            display: block;
            margin-top: 20px;
            color: #122c6f;
            text-decoration: none;
        }
        .back-link:hover {
            text-decoration: underline;
        }
        .homepage-navbar {
            background: #6695ed; /* or your homepage color */
            color: #fff;
            /* Add any other styles you want for the homepage navbar */
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h1>Forgot Your Password?</h1>
        <p>Enter your email address and we will send you a link to reset your password.</p>
        <?php if (!empty($msg)): ?>
            <div class="message <?php echo $msgType === 'success' ? 'success-message' : 'error-message'; ?>">
                <?php echo htmlspecialchars($msg); ?>
            </div>
        <?php endif; ?>
        <form method="POST" action="forgot_password.php">
            <label for="txtEmail" style="display:none;">Email</label>
            <input type="email" placeholder="Enter your email address" name="txtEmail" id="txtEmail" required>
            <button type="submit">Send Reset Link</button>
        </form>
        <a href="sign.php" class="back-link">Back to Sign In</a>
    </div>
</body>
</html>