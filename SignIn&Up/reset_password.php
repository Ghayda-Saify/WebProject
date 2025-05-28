<?php
global $con;
session_start();
require('../connection.php');
require('../functions.php');

$msg = '';
$msgType = 'error';
$validToken = false;
$email = '';

// Check if token is provided and valid
if (isset($_GET['token'])) {
    $token = $_GET['token'];
    try {
        $db = $con;
        $stmt = $db->prepare("SELECT email FROM password_resets WHERE token = ? AND expiry > ?");
        $currentTime = time();
        $stmt->bind_param("si", $token, $currentTime);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $validToken = true;
            $email = $result->fetch_assoc()['email'];
        } else {
            $msg = "Invalid or expired reset link. Please request a new password reset.";
        }
        $stmt->close();
    } catch (Exception $e) {
        $msg = "An error occurred. Please try again later.";
    }
} else {
    $msg = "Invalid reset link. Please request a new password reset.";
}

// Handle password reset form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['new_password']) && isset($_POST['confirm_password'])) {
    $newPassword = $_POST['new_password'];
    $confirmPassword = $_POST['confirm_password'];

    if (empty($newPassword) || empty($confirmPassword)) {
        $msg = "All fields are required.";
    } elseif ($newPassword !== $confirmPassword) {
        $msg = "Passwords do not match.";
    } elseif (strlen($newPassword) < 6) {
        $msg = "Password must be at least 6 characters long.";
    } else {
        try {
            $db = $con;
            // Update password
            $sha1Password = sha1($newPassword);
            $updateStmt = $db->prepare("UPDATE users SET password = ? WHERE email = ?");
            $updateStmt->bind_param("ss", $sha1Password, $email);

            if ($updateStmt->execute()) {
                // Delete used token
                $deleteStmt = $db->prepare("DELETE FROM password_resets WHERE token = ?");
                $deleteStmt->bind_param("s", $token);
                $deleteStmt->execute();
                $deleteStmt->close();

                $msg = "Your password has been reset successfully. You can now sign in with your new password.";
                $msgType = 'success';
                $validToken = false; // Prevent further password changes
            } else {
                $msg = "Failed to update password. Please try again.";
            }
            $updateStmt->close();
        } catch (Exception $e) {
            $msg = "An error occurred. Please try again later.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <link rel="stylesheet" href="loginSignUp.css">
    <link rel="stylesheet" href="reset_password.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
</head>
<body>
<div class="form-container">
    <h1>Reset Your Password</h1>
    <?php if (!empty($msg)): ?>
        <div class="message <?php echo $msgType === 'success' ? 'success-message' : 'error-message'; ?>">
            <?php echo htmlspecialchars($msg); ?>
        </div>
    <?php endif; ?>

    <?php if ($validToken): ?>
        <form method="POST" action="reset_password.php?token=<?php echo htmlspecialchars($token); ?>">
            <div class="password-wrapper">
                <input type="password" placeholder="New Password" name="new_password" id="new_password" required>
                <span class="toggle-password" data-target="new_password"><i class="fa fa-eye"></i></span>
            </div>
            <div class="password-wrapper">
                <input type="password" placeholder="Confirm New Password" name="confirm_password" id="confirm_password" required>
                <span class="toggle-password" data-target="confirm_password"><i class="fa fa-eye"></i></span>
            </div>
            <button type="submit">Reset Password</button>
        </form>
    <?php endif; ?>
    <a href="sign.php" class="back-link">Back to Sign In</a>
</div>

<script>
    document.querySelectorAll('.toggle-password').forEach(icon => {
        icon.addEventListener('click', function() {
            const targetId = this.getAttribute('data-target');
            const input = document.getElementById(targetId);
            if (input) {
                if (input.type === 'password') {
                    input.type = 'text';
                    this.innerHTML = '<i class="fa fa-eye-slash"></i>';
                } else {
                    input.type = 'password';
                    this.innerHTML = '<i class="fa fa-eye"></i>';
                }
            }
        });
    });
</script>
</body>
</html>