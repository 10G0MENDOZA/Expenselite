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


// Registrar la compra en la base de datos
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $producto = $conn->real_escape_string($_POST['producto']);
    $precio_input = $_POST['precio'];
    $descripcion = $conn->real_escape_string($_POST['descripcion']);
    $area = $conn->real_escape_string($_POST['area']);
    $foto = $_FILES['foto']['name'];

    // Limpiar el campo de precio como hiciste en el otro archivo
    $precio_clean = str_replace('.', '', $precio_input); // Eliminar puntos de miles
    $precio_clean = str_replace(',', '.', $precio_clean); // Reemplazar coma decimal
    $precio = number_format((float)$precio_clean, 2, '.', ''); // Convertir a float con 2 decimales

    // Subir la foto de la factura
    $target_dir = "uploads/";
    $target_file = $target_dir . basename($_FILES["foto"]["name"]);
    move_uploaded_file($_FILES["foto"]["tmp_name"], $target_file);

    // Insertar la compra en la base de datos
    $sql = "INSERT INTO compras (producto, precio, descripcion, foto, area, estado) 
            VALUES ('$producto', $precio, '$descripcion', '$foto', '$area', 'gastado')";

    if ($conn->query($sql) === TRUE) {
        // Si la compra se registró correctamente, actualizar los saldos
        $sql_gastado = "SELECT SUM(precio) AS total_gastado FROM compras WHERE estado = 'gastado'";
        $result_gastado = $conn->query($sql_gastado);
        $row_gastado = $result_gastado->fetch_assoc();
        $total_gastado = $row_gastado['total_gastado'] ?? 0;

        // Obtener el saldo actual más reciente
        $sql_saldo = "SELECT saldo_actual FROM caja_menor ORDER BY id DESC LIMIT 1";
        $result_saldo = $conn->query($sql_saldo);
        $row_saldo = $result_saldo->fetch_assoc();
        $saldo_actual = $row_saldo['saldo_actual'] ?? 0;

        // Calcular el saldo disponible
        $saldo_disponible = $saldo_actual - $total_gastado;

        // Guardar los valores en la sesión
        $_SESSION['saldo_actual'] = number_format($saldo_actual, 2, '.', '');
        $_SESSION['total_gastado'] = number_format($total_gastado, 2, '.', '');
        $_SESSION['saldo_disponible'] = number_format($saldo_disponible, 2, '.', '');

        header('Location: menu_principal.php');
    } else {
        echo "Error al registrar la compra: " . $conn->error;
    }
}

$conn->close();
?>

