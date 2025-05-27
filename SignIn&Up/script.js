document.addEventListener('DOMContentLoaded', function() {
    // Toggle password visibility
    document.querySelectorAll('.toggle-password').forEach(icon => {
        const targetId = icon.getAttribute('data-target');
        const input = document.getElementById(targetId);
        if (input) {
            icon.innerHTML = '<i class="fa fa-eye-slash"></i>';
            input.type = 'password';
            icon.addEventListener('click', function() {
                if (input.type === 'password') {
                    input.type = 'text';
                    this.innerHTML = '<i class="fa fa-eye"></i>';
                } else {
                    input.type = 'password';
                    this.innerHTML = '<i class="fa fa-eye-slash"></i>';
                }
            });
        }
    });

    // Rest of your existing code (container toggle, sound, etc.)
    const container = document.getElementById("container");
    const registerBtn = document.getElementById("register");
    const loginBtn = document.getElementById("login");



    // Add sound effect for panel switch
    const switchSound = new Audio('../sounds/fastwhoosh.mp3');

    function playSwitchSound() {
        switchSound.currentTime = 0;
        switchSound.play();
    }

    registerBtn.addEventListener("click", (e) => {
        container.classList.add("active");
        playSwitchSound();
    });

    loginBtn.addEventListener("click", (e) => {
        container.classList.remove("active");
        playSwitchSound();
    });

    // Mutation Observer for class changes
    const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            if (mutation.attributeName === 'class') {
                playSwitchSound();
            }
        });
    });
    observer.observe(container, { attributes: true });

    // Password confirmation check for sign-up form
    const signUpForm = document.querySelector('.form-container.sign-up form');
    if (signUpForm) {
        signUpForm.addEventListener('submit', function(e) {
            const password = document.getElementById('txtPassword').value;
            const confirmPass = document.getElementById('txtConfirmPass').value;
            if (password !== confirmPass) {
                alert('Passwords do not match!');
                e.preventDefault();
            }
        });
    }
});

