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
    if (isset($_POST['txtName']) && isset($_POST['txtEmail']) && isset($_POST['txtPassword']) && isset($_POST['txtConfirmPass'])) {
        $name = trim($_POST['txtName']);
        $email = trim($_POST['txtEmail']);
        $password = $_POST['txtPassword'];
        $confirmPass = $_POST['txtConfirmPass'];

        if (empty($name) || empty($email) || empty($password) || empty($confirmPass)) {
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
                    $insertStmt = $db->prepare("INSERT INTO users (name, email, password, type) VALUES (?, ?, ?, 'user')");
                    $insertStmt->bind_param("sss", $id,$name, $email, $sha1Password);
                    if ($insertStmt->execute()) {
                        echo json_encode(["success" => true]);
                        $_SESSION['user_id'] = $id;
                        $_SESSION['user_email'] = $email;
                        $_SESSION['user_type'] = 'user';
                        header("Location: ../HomePage/index.php");

                        exit();
                    } else {
                        $msg = "Registration failed. Please try again.";
                        echo json_encode(["success" => false, "error" => $con->error]);
                    }
                    $insertStmt->close();
                }
                $checkStmt->close();
                $db->close();
            } catch (Exception $e) {
                $msg = "An error occurred: " . $e->getMessage();
                echo json_encode(["success" => false, "error" => $conn->error]);
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
            $stmt = $db->prepare("SELECT * FROM users WHERE email = ? AND password = ?");
            $stmt->bind_param("ss", $userEmail, $sha1Password);
            $stmt->execute();
            $res = $stmt->get_result();

            if ($res->num_rows > 0) {
                $userData = $res->fetch_assoc();
                $_SESSION['user'] = $userData;

                // Optional: JSON success response
                echo json_encode(["success" => true]);

                // Redirect to home page
                header("Location: ../HomePage/index.php");
                exit();
            } else {
                $msg = "Incorrect email or password.";
                echo json_encode(["success" => false, "error" => $msg]);
            }

            $stmt->close();
            $db->close();
        } catch (Exception $e) {
            $msg = "An error occurred: " . $e->getMessage();
            echo json_encode(["success" => false, "error" => $msg]);
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
            <?php if (!empty($msg) && (isset($_POST['txtName']) || $togglePanel === 'signin')): ?>
                <div class="error-message show"><?php echo htmlspecialchars($msg); ?></div>
            <?php endif; ?>
            <?php if (!empty($success_msg) && isset($_POST['txtName'])): ?>
                <div class="success-message show"><?php echo htmlspecialchars($success_msg); ?></div>
            <?php endif; ?>
            <div class="social-icons">
                <a href="#" class="icon">
                    <i class="fa-brands fa-google-plus-g"></i>
                </a>
                <a href="#" class="icon">
                    <i class="fa-brands fa-facebook-f"></i>
                </a>
            </div>
            <span>or use your email to register</span>
            <label for="txtName"></label><input type="text" placeholder="Name" name="txtName" id="txtName">
            <label for="txtEmail"></label><input type="email" placeholder="Email" name="txtEmail" id="txtEmail">
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
            <?php if (!empty($msg) && (isset($_POST['txtEmailSignIn']) || $togglePanel === 'signup')): ?>
                <div class="error-message show"><?php echo htmlspecialchars($msg); ?></div>
            <?php endif; ?>
            <?php if (!empty($success_msg) && isset($_POST['txtEmailSignIn'])): ?>
                <div class="success-message show"><?php echo htmlspecialchars($success_msg); ?></div>
            <?php endif; ?>
            <div class="social-icons">
                <a href="#" class="icon">
                    <i class="fa-brands fa-google-plus-g"></i>
                </a>
                <a href="#" class="icon">
                    <i class="fa-brands fa-facebook-f"></i>
                </a>
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

<style>
    .error-message, .success-message {
        display: none; /* Hide messages by default */
    }
    .error-message.show, .success-message.show {
        display: block; /* Show only when has content */
    }
    .error-message {
        color: #f13b1c;
        background-color: #ffe5e5;
        padding: 10px;
        border-radius: 8px;
        margin: 10px 0;
        text-align: center;
        font-family: 'Cairo', sans-serif;
    }
    .success-message {
        color: #28a745;
        background-color: #e8f5e9;
        padding: 10px;
        border-radius: 8px;
        margin: 10px 0;
        text-align: center;
        font-family: 'Cairo', sans-serif;
    }
    .password-wrapper {
        position: relative;
        display: flex;
        align-items: center;
    }
    .password-wrapper input[type="password"] {
        width: 100%;
        padding-right: 40px;
    }
    .toggle-password {
        position: absolute;
        right: 12px;
        top: 50%;
        transform: translateY(-50%);
        cursor: pointer;
        color: #888;
        font-size: 1.2rem;
        z-index: 2;
        transition: color 0.2s;
    }
    .toggle-password:hover {
        color: #122c6f;
    }
    /* Add hover effect for Forget Password link */
    .form-container a {
        transition: color 0.3s ease;
    }
    .form-container a:hover {
        color:rgb(67, 115, 238);
    }
</style>
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