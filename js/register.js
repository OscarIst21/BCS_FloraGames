// Esperar a que el DOM esté completamente cargado
document.addEventListener('DOMContentLoaded', function() {
    // Configurar los toggles de contraseña
    setupPasswordToggle('togglePassword', 'reg_password');
    setupPasswordToggle('toggleConfirmPassword', 'reg_confirm_password');
    
    // Configurar el evento de submit del formulario
    const registrationForm = document.getElementById('registrationForm');
    if (registrationForm) {
        registrationForm.addEventListener('submit', function(e) {
            if (!validateForm()) {
                e.preventDefault(); // Prevenir envío si la validación falla
            }
        });
    }

    // Validación en tiempo real
    const emailInput = document.getElementById('reg_email');
    const birthdateInput = document.getElementById('reg_birthdate');
    const passwordInput = document.getElementById('reg_password');
    const confirmPasswordInput = document.getElementById('reg_confirm_password');

    if (emailInput) emailInput.addEventListener('blur', validateEmail);
    if (birthdateInput) birthdateInput.addEventListener('blur', validateBirthdate);
    if (passwordInput) passwordInput.addEventListener('input', validatePassword);
    if (confirmPasswordInput) confirmPasswordInput.addEventListener('input', validateConfirmPassword);
});

// Función para alternar entre mostrar/ocultar contraseña
function setupPasswordToggle(toggleId, passwordId) {
    const toggle = document.getElementById(toggleId);
    if (!toggle) return;

    const icon = toggle.querySelector('i');
    const password = document.getElementById(passwordId);
    if (!password) return;

    toggle.addEventListener('click', function() {
        // Alternar entre tipo password y text
        const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
        password.setAttribute('type', type);
        
        // Cambiar icono entre ojo abierto y cerrado
        icon.classList.toggle('fa-eye');
        icon.classList.toggle('fa-eye-slash');
    });
}

function validateForm() {
    let isValid = true;
    
    // Validar nombre
    const name = document.getElementById('reg_name')?.value.trim();
    if (!name || name.length < 2) {
        showError('reg_name', 'name-error', 'El nombre debe tener al menos 2 caracteres');
        isValid = false;
    } else {
        clearError('reg_name', 'name-error');
    }
    
    // Validar email
    if (!validateEmail()) {
        isValid = false;
    }
    
    // Validar fecha de nacimiento
    if (!validateBirthdate()) {
        isValid = false;
    }
    
    // Validar contraseña
    if (!validatePassword()) {
        isValid = false;
    }
    
    // Validar confirmación de contraseña
    if (!validateConfirmPassword()) {
        isValid = false;
    }
    
    return isValid;
}

function validateEmail() {
    const email = document.getElementById('reg_email')?.value.trim();
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    
    if (!email || !emailRegex.test(email)) {
        showError('reg_email', 'email-error', 'Por favor, ingresa un correo electrónico válido');
        return false;
    } else {
        clearError('reg_email', 'email-error');
        return true;
    }
}

function validateBirthdate() {
    const birthdate = document.getElementById('reg_birthdate')?.value;
    if (!birthdate) {
        showError('reg_birthdate', 'birthdate-error', 'Por favor, ingresa tu fecha de nacimiento');
        return false;
    }
    
    const birthDate = new Date(birthdate);
    const today = new Date();
    const age = today.getFullYear() - birthDate.getFullYear();
    const monthDiff = today.getMonth() - birthDate.getMonth();
    
    if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthDate.getDate())) {
        age--;
    }
    
    if (birthDate > today || age < 5) {
        showError('reg_birthdate', 'birthdate-error', 'Debes tener al menos 5 años para registrarte');
        return false;
    } else {
        clearError('reg_birthdate', 'birthdate-error');
        return true;
    }
}

function validatePassword() {
    const password = document.getElementById('reg_password')?.value;
    const passwordError = document.getElementById('password-error');
    
    if (!password || password.length < 6) {
        showError('reg_password', 'password-error', 'La contraseña debe tener al menos 6 caracteres');
        return false;
    } else {
        clearError('reg_password', 'password-error');
        return true;
    }
}

function validateConfirmPassword() {
    const password = document.getElementById('reg_password')?.value;
    const confirmPassword = document.getElementById('reg_confirm_password')?.value;
    
    if (!password || !confirmPassword || password !== confirmPassword) {
        showError('reg_confirm_password', 'confirm-password-error', 'Las contraseñas no coinciden');
        return false;
    } else if (confirmPassword.length === 0) {
        showError('reg_confirm_password', 'confirm-password-error', 'Por favor, confirma tu contraseña');
        return false;
    } else {
        clearError('reg_confirm_password', 'confirm-password-error');
        return true;
    }
}

function showError(inputId, errorElementId, message) {
    const input = document.getElementById(inputId);
    const errorElement = document.getElementById(errorElementId);
    
    if (input && errorElement) {
        input.classList.add('error-input');
        errorElement.textContent = message;
        errorElement.style.display = 'block';
    }
}

function clearError(inputId, errorElementId) {
    const input = document.getElementById(inputId);
    const errorElement = document.getElementById(errorElementId);
    
    if (input && errorElement) {
        input.classList.remove('error-input');
        errorElement.textContent = '';
        errorElement.style.display = 'none';
    }
}