// Función para formatear con puntos cada vez que el usuario escribe
document.getElementById('nuevo_ingreso').addEventListener('input', function (e) {
let valor = e.target.value;

// Quitar puntos actuales y todo lo que no sea número
valor = valor.replace(/\./g, '').replace(/\D/g, '');

// Si hay valor, formatea con puntos
if (valor !== '') {
valor = parseInt(valor).toLocaleString('de-DE'); // Formato de miles con puntos estilo europeo
}

e.target.value = valor;
});

// Antes de enviar, eliminar puntos para enviar el número limpio
function desformatearNumero() {
let input = document.getElementById('nuevo_ingreso');
input.value = input.value.replace(/\./g, '');
return true;
}