// Función para manejar el envío del formulario sin recargar la página
function registrarCompraAJAX() {
    const formData = new FormData(document.querySelector('#formCompra'));
    
    // Enviar los datos del formulario a través de AJAX
    fetch('menu_principal.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.text())
    .then(data => {
        // Aquí puedes actualizar los saldos si es necesario o hacer cualquier otra acción
        console.log(data); // Imprime la respuesta para verificar

        // Cerrar el modal
        cerrarFormularioCompra();
    })
    .catch(error => console.error('Error al registrar la compra:', error));
}

// Función para mostrar el formulario de compra
function mostrarFormularioCompra() {
    document.getElementById('modalCompra').style.display = 'block';
}

// Función para cerrar el formulario de compra
function cerrarFormularioCompra() {
    document.getElementById('modalCompra').style.display = 'none';
}
