function mostrarFormularioCompra() {
    document.getElementById("modalCompra").style.display = "block";
}

function cerrarFormularioCompra() {
    document.getElementById("modalCompra").style.display = "none";
}

const montoInput = document.getElementById('monto');

// Formato de número mientras se escribe
montoInput.addEventListener('input', function (e) {
    // Remover separadores existentes para procesar el valor puro
    let value = e.target.value.replace(/,/g, '').replace(/\./g, '');
    
    // Evitar valores no numéricos
    if (!isNaN(value) && value.trim() !== '') {
        // Formatear con separadores de miles
        const formattedValue = parseFloat(value).toLocaleString('es-CO');
        e.target.value = formattedValue;
    } else {
        // Si no es válido, limpiar el valor
        e.target.value = '';
    }
});

// Al salir del campo, asegurarse de que tenga decimales solo si se ingresaron
montoInput.addEventListener('blur', function (e) {
    // Remover las comas (miles) y los puntos (decimales) para procesar el valor
    let value = e.target.value.replace(/,/g, '').replace(/\./g, '');

    if (value !== '') {
        // Verificar si hay decimales ingresados
        const numberValue = parseFloat(value);
        const hasDecimals = value.includes('.');

        if (hasDecimals) {
            // Si hay decimales, formatear con separadores de miles y dos decimales
            e.target.value = numberValue.toLocaleString('es-CO', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        } else {
            // Si no hay decimales, formatear solo con los separadores de miles
            e.target.value = numberValue.toLocaleString('es-CO');
        }
    }
});
