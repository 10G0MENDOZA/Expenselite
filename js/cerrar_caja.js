$(document).ready(function () {
    // ==== 1. Manejar el cierre de caja ====
    $("#formCierreCaja").submit(function (event) {
        event.preventDefault(); // Previene recarga

        if (!confirm("¿Estás seguro de que deseas cerrar la caja?")) return;

        $("#btnCerrar").prop("disabled", true).text("Procesando...");

        $.ajax({
            url: 'cerrar_caja.php',
            type: 'POST',
            data: {
                cerrar_caja: '1'
            },
            success: function (response) {
                $("#resultado").html(response);
                $("#btnCerrar").prop("disabled", false).text("Cerrar Caja");
            },
            error: function () {
                alert("⚠️ Ocurrió un error al procesar el cierre de caja.");
                $("#btnCerrar").prop("disabled", false).text("Cerrar Caja");
            }
        });
    });

    // ==== 2. Ocultar mensaje de advertencia automáticamente ====
    setTimeout(function () {
        var mensaje = document.getElementById("mensaje-advertencia");
        if (mensaje) {
            mensaje.style.display = "none";
        }
    }, 3000);
});
