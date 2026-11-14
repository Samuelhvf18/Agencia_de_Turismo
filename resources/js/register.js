document.addEventListener('DOMContentLoaded', function () {
            const form = document.getElementById('registerForm');
            const password = document.getElementById('password');
            const confirmation = document.getElementById('password_confirmation');
            const matchText = document.getElementById('matchText');
            const strengthText = document.getElementById('strengthText');
            const strengthBars = document.querySelectorAll('[data-strength-bar]');
            const registerButton = document.getElementById('registerButton');
            const registerIcon = document.getElementById('registerIcon');
            const registerText = document.getElementById('registerText');
document.querySelectorAll('[data-toggle-password]').forEach(function (button) {
                button.addEventListener('click', function () {
                    const input = document.getElementById(button.dataset.togglePassword);
                    const icon = button.querySelector('i');
                    const show = input.type === 'password';
                    input.type = show ? 'text' : 'password';
                    button.setAttribute('aria-pressed', String(show));
                    button.setAttribute(
                        'aria-label',
                        show ? 'Ocultar contraseña' : 'Mostrar contraseña'
                    );
                    icon.classList.toggle('fa-eye', !show);
                    icon.classList.toggle('fa-eye-slash', show);
                    icon.classList.toggle('fa-regular', !show);
                    icon.classList.toggle('fa-solid', show);
                });
            });
function checkPasswordStrength() {
                const value = password.value;
                let score = 0;
                if (value.length === 0) {
                    strengthText.textContent = 'Sin contraseña';
                    strengthBars.forEach(function (bar) {
                        bar.style.backgroundColor = '#D9D2C9';
                    });
                    return;
                }
                score = 1;
                if (value.length >= 8) score = 2;
                if (
                    value.length >= 10 &&
                    /[a-z]/.test(value) &&
                    /[A-Z]/.test(value)
                ) {
                    score = 3;
                }
                if (
                    value.length >= 12 &&
                    /\d/.test(value) &&
                    /[^A-Za-z0-9]/.test(value)
                ) {
                    score = 4;
                }
                const levels = ['', 'Débil', 'Básica', 'Buena', 'Muy buena'];
                const colors = [
                    '#D9D2C9',
                    '#C96F4A',
                    '#D9A441',
                    '#6B9B78',
                    '#1F5F8B'
                ];
                strengthBars.forEach(function (bar, index) {
                    bar.style.backgroundColor = index < score
                        ? colors[score]
                        : '#D9D2C9';
                });
                strengthText.textContent = levels[score];
            }
function checkPasswordMatch() {
                const value = confirmation.value;
                confirmation.setCustomValidity('');
                matchText.textContent = '';
                matchText.className = 'mt-2 text-xs';
                if (!value) return;
                if (password.value === value) {
                    matchText.textContent = '✓ Las contraseñas coinciden';
                    matchText.classList.add('font-medium', 'text-green-700');
                } else {
                    matchText.textContent = 'Las contraseñas no coinciden';
                    matchText.classList.add('font-medium', 'text-red-600');
                    confirmation.setCustomValidity(
                        'Las contraseñas no coinciden.'
                    );
                }
            }
            password.addEventListener('input', function () {
                checkPasswordStrength();
                checkPasswordMatch();
            });
            confirmation.addEventListener('input', checkPasswordMatch);
            form.addEventListener('submit', function (event) {
                checkPasswordMatch();
                if (!form.checkValidity()) {
                    event.preventDefault();
                    form.reportValidity();
                    return;
                }
                if (registerButton.disabled) {
                    event.preventDefault();
                    return;
                }
                registerButton.disabled = true;
                registerIcon.className = 'fa-solid fa-spinner fa-spin';
                registerText.textContent = 'Creando tu cuenta...';
            });
        });