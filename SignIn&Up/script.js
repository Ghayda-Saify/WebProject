const container = document.getElementById("container");
const registerBtn = document.getElementById("register");
const loginBtn = document.getElementById("login");

// Add sound effect for panel switch
const switchSound = new Audio('https://cdn.pixabay.com/audio/2022/07/26/audio_124bfae5b2.mp3'); // royalty-free pop sound

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

// Also play sound if panel changes programmatically
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
