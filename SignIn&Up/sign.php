<?php
session_start();
global $con;
require ('../connection.php');
require ('../functions.php');

$msg = '';
$success_msg = '';
$togglePanel = '';

// Only process messages if form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle Sign Up
    if (isset($_POST['txtName']) && isset($_POST['txtEmail']) && isset($_POST['txtPassword']) && isset($_POST['txtConfirmPass']) && isset($_POST['txtPhone'])) {
        $name = trim($_POST['txtName']);
        $email = trim($_POST['txtEmail']);
        $password = $_POST['txtPassword'];
        $confirmPass = $_POST['txtConfirmPass'];
        $phone = trim($_POST['txtPhone']);

        if (empty($name) || empty($email) || empty($password) || empty($confirmPass) || empty($phone)) {
            $msg = "All fields are required";
        } elseif ($password !== $confirmPass) {
            $msg = "Passwords do not match";
        } elseif (strlen($password) < 6) {
            $msg = "Password must be at least 6 characters long";
        } else {
            try {
                $db = $con;
                // Check if email already exists in users table
                $checkStmt = $db->prepare("SELECT * FROM users WHERE email = ?");
                $checkStmt->bind_param("s", $email);
                $checkStmt->execute();
                $result = $checkStmt->get_result();

                if ($result->num_rows > 0) {
                    $msg = "You already have an account. Please sign in.";
                    $togglePanel = 'signin';
                } else {
                    $sha1Password = sha1($password);
                    $insertStmt = $db->prepare("INSERT INTO users (name, email, password, phone_number, type) VALUES (?, ?, ?, ?, 'user')");
                    $insertStmt->bind_param("ssss", $name, $email, $sha1Password, $phone);
                    if ($insertStmt->execute()) {
                        $_SESSION['user_email'] = $email;
                        $_SESSION['user_type'] = 'user';
                        header("Location: ../HomePage/index.php");
                        exit();
                    } else {
                        $msg = "Registration failed. Please try again.";
                    }
                    $insertStmt->close();
                }
                $checkStmt->close();
                $db->close();
            } catch (Exception $e) {
                $msg = "An error occurred: " . $e->getMessage();
            }
        }
    }

    // Handle Sign In
    if (isset($_POST['txtEmailSignIn']) && isset($_POST['txtPasswordSignIn'])) {
        $userEmail = $_POST['txtEmailSignIn'];
        $userPassword = $_POST['txtPasswordSignIn'];
        $sha1Password = sha1($userPassword);
        try {
            $db = $con;
            $checkStmt = $db->prepare("SELECT * FROM users WHERE email = ?");
            $checkStmt->bind_param("s", $userEmail);
            $checkStmt->execute();
            $emailResult = $checkStmt->get_result();
            if ($emailResult->num_rows === 0) {
                $msg = "Account not found.";
                $togglePanel = 'signup';
            } else {
                $stmt = $db->prepare("SELECT * FROM users WHERE email = ? AND password = ?");
                $stmt->bind_param("ss", $userEmail, $sha1Password);
                $stmt->execute();
                $res = $stmt->get_result();
                if ($res->num_rows > 0) {
                    $_SESSION['user_email'] = $userEmail;
                    $_SESSION['user_type'] = $emailResult->fetch_assoc()['type'];
                    header("Location: ../HomePage/index.php");
                    exit();
                } else {
                    $msg = "Incorrect password.";
                }
                $stmt->close();
            }
            $checkStmt->close();
            $db->close();
        } catch (Exception $e) {
            $msg = "An error occurred: " . $e->getMessage();
        }
    }

    // Get messages from session only after form submission
    if (isset($_SESSION['error_msg'])) {
        $msg = $_SESSION['error_msg'];
        unset($_SESSION['error_msg']);
    }
    if (isset($_SESSION['success_msg'])) {
        $success_msg = $_SESSION['success_msg'];
        unset($_SESSION['success_msg']);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link rel="stylesheet" href="loginSignUp.css">
    <link
            rel="stylesheet"
            href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css"/>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;700&family=Tajawal:wght@400;700&display=swap" rel="stylesheet">
    <audio id="panel-sound" src="/sounds/Fast_Whoosh.mp3" preload="auto"></audio>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: { primary: "#122c6f", secondary: "#f13b1c" },
                    borderRadius: {
                        none: "0px",
                        sm: "4px",
                        DEFAULT: "8px",
                        md: "12px",
                        lg: "16px",
                        xl: "20px",
                        "2xl": "24px",
                        "3xl": "32px",
                        full: "9999px",
                        button: "8px",
                    },
                },
            },
        };
    </script>
    <title>Login Page</title>
</head>
<body>

<div class="container"  id="container">
    <div class="form-container sign-up">
        <form method="POST" action="sign.php">
            <h1>Create Account</h1>
            <div class="social-icons">
                <!-- Social sign in/up buttons removed -->
            </div>
            <span>or use your email to register</span>
            <label for="txtName"></label><input type="text" placeholder="Name" name="txtName" id="txtName">
            <label for="txtEmail"></label><input type="email" placeholder="Email" name="txtEmail" id="txtEmail">
            <label for="txtPhone"></label><input type="tel" placeholder="Phone Number" name="txtPhone" id="txtPhone" required>
            <div class="password-wrapper">
                <input type="password" placeholder="Password" name="txtPassword" id="txtPassword">
                <span class="toggle-password" data-target="txtPassword"><i class="fa fa-eye"></i></span>
            </div>
            <div class="password-wrapper">
                <input type="password" placeholder="Confirm your password" name="txtConfirmPass" id="txtConfirmPass">
                <span class="toggle-password" data-target="txtConfirmPass"><i class="fa fa-eye"></i></span>
            </div>
            <button>Sign Up</button>
        </form>
    </div>

    <div class="form-container sign-in">
        <form method="POST" action="sign.php">
            <h1>Sign in</h1>
            <div class="social-icons">
                <!-- Social sign in/up buttons removed -->
            </div>
            <span>or use your email & password</span>
            <label for="txtEmailSignIn"></label><input type="email" placeholder="Email" name="txtEmailSignIn" id="txtEmailSignIn">
            <div class="password-wrapper">
                <input type="password" placeholder="Password" name="txtPasswordSignIn" id="txtPasswordSignIn">
                <span class="toggle-password" data-target="txtPasswordSignIn"><i class="fa fa-eye"></i></span>
            </div>
            <a href="forgot_password.php">Forget Your Password?</a>
            <button>Sign In</button>
        </form>
    </div>
    <div class="toggle-container">
        <div class="toggle">
            <div class="toggle-panel toggle-left">
                <h1>Welcome Back!</h1>
                <p>Enter your personal details to use all site features</p>
                <button class="hidden" id="login">Sign In</button>
            </div>
            <div class="toggle-panel toggle-right">
                <h1>Hello, New Friend!</h1>
                <p>Register with your personal details to use all site features</p>
                <button class="hidden" id="register">Sign Up</button>
            </div>
        </div>
    </div>
</div>


<script src="script.js"></script>
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

    function playPanelSound() {
        const audio = document.getElementById('panel-sound');
        if (audio) {
            audio.currentTime = 0;
            audio.play();
        }
    }

    // Add sound to panel switch buttons
    const loginBtn = document.getElementById('login');
    const registerBtn = document.getElementById('register');
    if (loginBtn) loginBtn.addEventListener('click', playPanelSound);
    if (registerBtn) registerBtn.addEventListener('click', playPanelSound);

    // Optionally, play sound on successful sign in/up
    // document.querySelectorAll('form').forEach(form => {
    //     form.addEventListener('submit', playPanelSound);
    // });
</script>
<?php if (!empty($togglePanel)): ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            <?php if ($togglePanel === 'signup'): ?>
            document.getElementById('container').classList.add('right-panel-active');
            alert('Account not found. Please sign up.');
            <?php elseif ($togglePanel === 'signin'): ?>
            document.getElementById('container').classList.remove('right-panel-active');
            alert('You already have an account. Please sign in.');
            <?php endif; ?>
        });
    </script>
<?php endif; ?>

</body>
</html>