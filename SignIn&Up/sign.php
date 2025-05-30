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
            $togglePanel = 'signup';
        } elseif (strlen($password) < 6) {
            $msg = "Password must be at least 6 characters long";
            $togglePanel = 'signup';
        } elseif ($password !== $confirmPass) {
            $msg = "Passwords do not match";
            $togglePanel = 'signup';
        }else {
            try {
                $db = $con;
                // Check if email already exists in users table
                $checkStmt = $db->prepare("SELECT * FROM users WHERE email = ?");
                $checkStmt->bind_param("s", $email);
                $checkStmt->execute();
                $result = $checkStmt->get_result();

                if ($result->num_rows > 0) {
                    $_SESSION['error_msg'] = "You already have an account. Please sign in.";
                    $_SESSION['toggle_panel'] = 'signin';
                    header("Location: sign.php");
                    exit();
                } else {
                    $sha1Password = sha1($password);
                    $insertStmt = $db->prepare("INSERT INTO users (name, email, password, phone_number, type) VALUES (?, ?, ?, ?, 'user')");
                    $insertStmt->bind_param("ssss",$name, $email, $sha1Password, $phone);
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
                echo json_encode(["success" => false, "error" => $conn->error]);
            }
        }
    }
    // Handle Sign In
    if (isset($_POST['txtEmailSignIn']) && isset($_POST['txtPasswordSignIn'])) {
        $userEmail = $_POST['txtEmailSignIn'];
        $userPassword = $_POST['txtPasswordSignIn'];

        if (empty($userEmail) || empty($userPassword)) {
            $msg = "Please fill in all fields";
        }
        else{
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

                    if ($userData['type'] === 'admin') {
                        header("Location: ../AdminDashboard/index.php"); // عدلي المسار حسب ما عندك
                    } else {
                        header("Location: ../HomePage/index.php");
                    }
                    exit();
                }
                else {
                    $msg = "Incorrect email or password.";

                }
                $stmt->close();
                $db->close();
            } catch (Exception $e) {
                $msg = "An error occurred: " . $e->getMessage();

            }
        }
    }

    // Get messages from session only after form submission
    if (isset($_SESSION['error_msg'])) {
        $msg = $_SESSION['error_msg'];
        $togglePanel = $_SESSION['toggle_panel'] ?? '';
        unset($_SESSION['error_msg'], $_SESSION['toggle_panel']);
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
    <script src="https://unpkg.com/sweetalert2@11"></script>

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
            <?php if (!empty($msg) && (isset($_POST['txtName']) || $togglePanel === 'signin')): ?>
                <div class="error-message show"><?php echo htmlspecialchars($msg); ?></div>
            <?php endif; ?>
            <?php if (!empty($success_msg) && isset($_POST['txtName'])): ?>
                <div class="success-message show"><?php echo htmlspecialchars($success_msg); ?></div>
            <?php endif; ?>
            <h1>Create Account</h1>
            <div class="social-icons">
            </div>
            <span class="brief2" >Your Journey Starts Here</span>
            <label for="txtName"></label><input type="text" placeholder="Name" name="txtName" id="txtName" >
            <label for="txtEmail"></label><input type="email" placeholder="Email" name="txtEmail" id="txtEmail">
            <label for="txtPhone"></label><input type="tel" placeholder="Phone Number" name="txtPhone" id="txtPhone">
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
            <?php if (!empty($msg)): ?>
                <div class="error-message <?= isset($_POST['txtName']) ? 'show' : '' ?>">
                    <?= htmlspecialchars($msg) ?>
                </div>
            <?php endif; ?>
            <?php if (!empty($success_msg) && isset($_POST['txtEmailSignIn'])): ?>
                <div class="success-message show"><?php echo htmlspecialchars($success_msg); ?></div>
            <?php endif; ?>
            <div class="social-icons">
                <!-- Social sign in/up buttons removed -->
            </div>
            <span class="brief">Welcome Back with Ease</span>
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
                <button class="hidden sound" id="login">Sign In</button>
            </div>
            <div class="toggle-panel toggle-right">
                <h1>Hello, New Friend!</h1>
                <p>Register with your personal details to use all site features</p>
                <button class="hidden sound" id="register">Sign Up</button>
            </div>
        </div>
    </div>
</div>

<audio id="panel-sound" src="../sounds/fastwhoosh.mp3" preload="auto"></audio>


<script>
    document.addEventListener('DOMContentLoaded', function () {
        const container = document.getElementById('container');
        const signupForm = document.querySelector('.sign-up form');
        const signinForm = document.querySelector('.sign-in form');
        const toast = document.getElementById("toast");

        // Handle togglePanel alerts and animation
        <?php if (!empty($togglePanel)): ?>
        <?php if ($togglePanel === 'signup'): ?>
        container.classList.add('right-panel-active');
        Swal.fire({
            icon: 'error',
            title: 'Signup Failed',
            text: <?php echo json_encode($msg); ?>,
            confirmButtonText: 'OK'
        });
        <?php elseif ($togglePanel === 'signin'): ?>
        Swal.fire({
            icon: 'info',
            title: 'Account Exists',
            text: <?php echo json_encode($msg); ?>,
            confirmButtonText: 'OK'
        }).then(() => {
            container.classList.remove('right-panel-active');
        });
        <?php endif; ?>

        <?php endif; ?>

        // Form validation function
        function setupValidation(form, actionText) {
            form.addEventListener('submit', function (e) {
                e.preventDefault(); // Stop form from submitting

                let isEmpty = false;
                let firstEmptyInput = null;

                const inputs = form.querySelectorAll('input');

                // Remove previous highlights
                inputs.forEach(input => input.classList.remove('highlight'));

                inputs.forEach(input => {
                    if (!input.value.trim()) {
                        isEmpty = true;
                        input.classList.add('highlight'); // Highlight empty fields
                        if (!firstEmptyInput) firstEmptyInput = input;
                    }
                });

                if (isEmpty) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Oops!',
                        text: `Please fill in all fields to ${actionText}.`,
                        confirmButtonText: 'OK'
                    }).then(() => {
                        if (firstEmptyInput) {
                            firstEmptyInput.focus();
                        }
                    });
                } else {
                    this.submit(); // Submit form if valid
                }
            });
        }

        if (signupForm) {
            setupValidation(signupForm, 'sign up');
        }

        if (signinForm) {
            setupValidation(signinForm, 'sign in');
        }

    });
</script>
<script>
    <?php if (!empty($_SESSION['error_msg']) && $_SESSION['toggle_panel'] === 'signin'): ?>
    Swal.fire({
        icon: 'info',
        title: 'Already Registered',
        text: '<?= $_SESSION['error_msg'] ?>',
        confirmButtonText: 'Go to Sign In'
    }).then(() => {
        const container = document.getElementById('container');
        if (container) {
            container.classList.add('right-panel-active');
        }
    });
    <?php unset($_SESSION['error_msg'], $_SESSION['toggle_panel']); ?>
    <?php endif; ?>
</script>

<script src="script.js"></script>


<script>
    document.addEventListener('DOMContentLoaded', function() {
        <?php if (!empty($msg)): ?>
        Swal.fire({
            icon: 'info',
            title: 'Info',
            text: <?= json_encode($msg) ?>,
            confirmButtonText: 'OK',
            width: 400,        // تعيين العرض حسب طلبك
            heightAuto: false, // منع تغير الارتفاع تلقائياً
            padding: '1.5em',
            customClass: {
                popup: 'custom-swal-popup'
            }
        });
        <?php endif; ?>
    });
</script>

</body>
</html>