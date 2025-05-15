<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link rel="stylesheet" href="loginSignUp.css">
    <title>Login Page</title>
</head>
<body>

<div class="container"  id="container">
    <div class="form-container sign-up">
        <form>
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
            <label for="txtName"></label><input type="text" placeholder="Name" id="txtName">
            <label for="txtEmail"></label><input type="email" placeholder="Email" id="txtEmail">
            <label for="txtPassword"></label><input type="password" placeholder="password" id="txtPassword">
            <label for="txtConfirmPass"></label><input type="password" placeholder="Confirm your password" id="txtConfirmPass">
            <button>Sign Up</button>
        </form>
    </div>

    <div class="form-container sign-in">
        <form>
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
            <label for="txtEmailSignIn"></label><input type="email" placeholder="Email" id="txtEmailSignIn">
            <label for="txtPasswordSignIn"></label><input type="password" placeholder="password" id="txtPasswordSignIn">
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


<script src="script.js"></script>
</body>
</html>