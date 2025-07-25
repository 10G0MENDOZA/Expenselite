function mostrarFormularioAgregarDineroCaja() {
    var formulario = document.getElementById('formulario-saldo');
    if (formulario.style.display === 'none' || formulario.style.display === '') {
        formulario.style.display = 'block';
    } else {
        formulario.style.display = 'none';
    }
}
