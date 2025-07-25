<?php
session_start();

// Mostrar errores para depuración
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Incluir credenciales de la base de datos
require_once '/home2/avanceap/Credencial_Global/config.php';

// Crear conexión segura
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT);

// Verificar conexión
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);

}


if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $fecha = $conn->real_escape_string($_POST['fecha']);
    $monto_input = $_POST['monto'];

    // 1. Remover puntos de miles
    $monto_input_clean = str_replace('.', '', $monto_input);

    // 2. Reemplazar la coma decimal por punto para base de datos
    $monto_input_clean = str_replace(',', '.', $monto_input_clean);

    // Convertir a número flotante con los decimales correctos
    $monto = number_format((float)$monto_input_clean, 2, '.', '');

    // Guardar la fecha en sesión
    $_SESSION['fecha_seleccionada'] = $fecha;

    // Insertar en la base de datos
    $sql = "INSERT INTO caja_menor (fecha, monto_inicial, saldo_actual) VALUES ('$fecha', $monto, $monto)";

    if ($conn->query($sql) === TRUE) {
        header("Location: menu_principal.php");
        exit;
    } else {
        echo "Error al registrar la caja menor: " . $conn->error;
    }
}

$conn->close();
?>

