<?php
session_start();
require 'conexion.php';

// Buscar si hay una caja abierta
$sql = "SELECT id, saldo_actual, fecha, estado FROM caja_menor WHERE estado = 'abierta' ORDER BY fecha DESC LIMIT 1";
$result = $conn->query($sql);
$row = $result->fetch_assoc();

if ($row) {
    // Guardar los datos en la sesión
    $_SESSION['saldo_disponible'] = $row['saldo_actual'];
    $_SESSION['caja_id'] = $row['id'];
    $_SESSION['fecha_caja'] = $row['fecha'];
    $_SESSION['caja_cerrada'] = false; // Si la caja estaba abierta, la mantenemos abierta
} else {
    $_SESSION['saldo_disponible'] = 0;
    $_SESSION['caja_cerrada'] = true; // Si no hay caja abierta, se requiere abrir una nueva
}

$conn->close();
?>
