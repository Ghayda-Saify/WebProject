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
                $msg = "Sorry, this email is not signed up with us.";
                $msgType = 'error';
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
    <link rel="stylesheet" href="forgot_password.css">
    <link rel="stylesheet" href="loginSignUp.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


</head>
<body>
<div class="form-container">
    <h1>Forgot Your Password?</h1>
    <p>Enter your email address and we will send you a link to reset your password.</p>
    <form method="POST" action="forgot_password.php" id="forgot-form">
        <label for="txtEmail" style="display:none;">Email</label>
        <input type="email" placeholder="Enter your email address" name="txtEmail" id="txtEmail" required>
        <button type="submit">Send Reset Link</button>
    </form>
    <a href="sign.php" class="back-link">Back to Sign In</a>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // If PHP set a message, show it with SweetAlert
        <?php if (!empty($msg)): ?>
        Swal.fire({
            icon: '<?php echo $msgType === 'success' ? 'success' : 'error'; ?>',
            title: '<?php echo $msgType === 'success' ? 'Email Sent!' : 'Error'; ?>',
            html: '<?php echo addslashes($msg); ?>',
            confirmButtonText: 'OK'
        });
        <?php endif; ?>

        document.querySelectorAll('.toggle-password').forEach(function(span) {
            span.addEventListener('click', function(e) {
                e.preventDefault();
                const targetId = this.getAttribute('data-target');
                const input = document.getElementById(targetId);
                const icon = this.querySelector('i');
                if (input && icon) {
                    if (input.type === 'password') {
                        input.type = 'text';
                        icon.classList.remove('fa-eye-slash');
                        icon.classList.add('fa-eye');
                    } else {
                        input.type = 'password';
                        icon.classList.remove('fa-eye');
                        icon.classList.add('fa-eye-slash');
                    }
                }
            });
        });
    });
</script>
</body>
</html>