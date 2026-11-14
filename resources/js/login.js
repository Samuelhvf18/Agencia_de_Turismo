document.addEventListener('DOMContentLoaded', function () {
const password = document.getElementById('password');
const togglePassword = document.getElementById('togglePassword');
const passwordIcon = togglePassword?.querySelector('i');
const loginForm = document.getElementById('loginForm');
const loginButton = document.getElementById('loginButton');
const loginIcon = document.getElementById('loginIcon');
const loginText = document.getElementById('loginText');
            if (password && togglePassword && passwordIcon) {
                togglePassword.addEventListener('click', function () {
                    const mostrar = password.type === 'password';
                    password.type = mostrar ? 'text' : 'password';
                    passwordIcon.classList.toggle('fa-eye', !mostrar);
                    passwordIcon.classList.toggle('fa-eye-slash', mostrar);
                    togglePassword.setAttribute('aria-pressed', String(mostrar));
                    togglePassword.setAttribute(
                        'aria-label',
                        mostrar ? 'Ocultar contraseña' : 'Mostrar contraseña'
                    );
                });
            }
            if (loginForm && loginButton) {
                loginForm.addEventListener('submit', function (event) {
                    if (!loginForm.checkValidity()) {
                        event.preventDefault();
                        loginForm.reportValidity();
                        return;
                    }
                    if (loginButton.disabled) {
                        event.preventDefault();
                        return;
                    }
                    loginButton.disabled = true;
                    if (loginIcon) {
                        loginIcon.classList.remove('fa-right-to-bracket');
                        loginIcon.classList.add('fa-spinner', 'fa-spin');
                    }
                    if (loginText) {
                        loginText.textContent = 'Iniciando sesión...';
                    }
                });
            }
        });