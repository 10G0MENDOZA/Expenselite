document.addEventListener("DOMContentLoaded", function () {
        let errorMsg = document.getElementById("error-msg");
        if (errorMsg) {
            errorMsg.innerText = "⚠️ Usuario o contraseña incorrecta"; // Cambia el mensaje aquí
            errorMsg.style.display = "block"; // Muestra el mensaje

            setTimeout(() => {
                errorMsg.style.display = "none"; // Oculta después de 3 segundos
            }, 3000);
        }
    });