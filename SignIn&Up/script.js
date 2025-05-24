const container = document.getElementById("container");
const registerBtn = document.getElementById("register");
const loginBtn = document.getElementById("login");

registerBtn.addEventListener("click", (e) => {
    container.classList.add("active");
});

loginBtn.addEventListener("click", (e) => {
    container.classList.remove("active");
})

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
