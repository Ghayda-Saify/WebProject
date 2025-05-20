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
        <form method="POST" action="validation.php" >
            <h1>Create Account</h1>
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
            <label for="txtEmail"></label><input type="email" placeholder="Email" name="txtName" id="txtEmail">
            <label for="txtPassword"></label><input type="password" placeholder="password" name="txtPassword" id="txtPassword">
            <label for="txtConfirmPass"></label><input type="password" placeholder="Confirm your password" name="txtConfirmPass" id="txtConfirmPass">
            <button>Sign Up</button>
        </form>
    </div>

    <div class="form-container sign-in">
        <form method="POST" action="validation.php">
            <h1>Sign in</h1>
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
            <label for="txtPasswordSignIn"></label><input type="password" placeholder="password" name="txtPasswordSignIn" id="txtPasswordSignIn">
            <a href="#">Forget Your Password?</a>
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
<!--<header>-->
<!--    <a href="#" class="logo text-primary font-['Pacifico'] text-2xl">Alandalus Design</a>-->
<!---->
<!---->
<!--</header>-->
<script src="script.js"></script>
</body>
</html>