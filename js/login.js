
function toggleForm(form) {
    const loginForm = document.getElementById('loginForm');
    const registerForm = document.getElementById('registerForm');
    
    if (form === 'register') {
        loginForm.style.display = 'none';
        registerForm.style.display = 'block';
    } else {
        loginForm.style.display = 'block';
        registerForm.style.display = 'none';
    }
}

document.getElementById('showPassword').addEventListener('change', function() {
    const passwordInput = document.getElementById('password');
    passwordInput.type = this.checked ? 'text' : 'password';
});

document.getElementById('showRegPassword').addEventListener('change', function() {
    const passwordInput = document.getElementById('reg_password');
    passwordInput.type = this.checked ? 'text' : 'password';
});

document.getElementById('showRegConfirmPassword').addEventListener('change', function() {
    const passwordInput = document.getElementById('reg_confirm_password');
    passwordInput.type = this.checked ? 'text' : 'password';
});

function validatePasswords() {
    const password = document.getElementById('reg_password').value;
    const confirmPassword = document.getElementById('reg_confirm_password').value;

    if (password !== confirmPassword) {
        alert('Las contraseñas no coinciden');
        return false;
    }
    return true;
}


