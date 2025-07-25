  // Mostrar/ocultar mensaje de error
        document.addEventListener("DOMContentLoaded", function () {
            let errorMsg = document.getElementById("error-msg");
            if (errorMsg) {
                errorMsg.style.display = "block";
                setTimeout(() => {
                    errorMsg.style.display = "none";
                }, 2000);
            }
        });

        // Alternar visibilidad de la contraseña
        function togglePassword() {
            const passwordField = document.getElementById("password");
            const eyeIcon = document.getElementById("eye-icon");
            if (passwordField.type === "password") {
                passwordField.type = "text";
                eyeIcon.classList.remove("fa-eye");
                eyeIcon.classList.add("fa-eye-slash");
            } else {
                passwordField.type = "password";
                eyeIcon.classList.remove("fa-eye-slash");
                eyeIcon.classList.add("fa-eye");
            }
        }