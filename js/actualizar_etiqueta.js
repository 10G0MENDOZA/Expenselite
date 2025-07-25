function actualizarEtiqueta() {
    var tipoDocumento = document.getElementById("tipo_documento").value;
    var labelArchivo = document.getElementById("labelArchivo");

    if (tipoDocumento === "factura") {
        labelArchivo.textContent = "Adjuntar Factura (PDF o Imagen):";
    } else {
        labelArchivo.textContent = "Adjuntar Recibo de Caja Menor (PDF o Imagen):";
    }
}