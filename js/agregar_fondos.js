function mostrarFormulario() {
    document.getElementById("abrir-caja-btn").style.display = "none";
    document.getElementById("formulario-saldo").style.display = "block";
}

document.getElementById("saldoForm").addEventListener("submit", function(event) {
    event.preventDefault();

    let nuevoIngreso = document.getElementById("nuevo_ingreso").value;

    fetch("actualizar_saldo.php", {
        method: "POST",
        body: new URLSearchParams({ "nuevo_ingreso": nuevoIngreso }),
        headers: { "Content-Type": "application/x-www-form-urlencoded" }
    })
    .then(response => response.json())
    .then(data => {
        document.getElementById("saldoDisponible").textContent = data.nuevo_saldo;
        document.getElementById("formulario-saldo").style.display = "none";
        document.getElementById("abrir-caja-btn").style.display = "block";
    })
    .catch(error => console.error("Error:", error));
});
