<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


// Incluir credenciales de la base de datos
require_once '/home2/avanceap/Credencial_Global/config.php';

// Crear conexión segura
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT);

// Verificar conexión
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);

}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nuevo_ingreso'])) {
    // Limpieza adicional en PHP por seguridad
    $nuevo_ingreso = $_POST['nuevo_ingreso'];
    $nuevo_ingreso = str_replace('.', '', $nuevo_ingreso); // Eliminar separador de miles
    $nuevo_ingreso = str_replace(',', '.', $nuevo_ingreso); // Cambiar coma por punto decimal
    $nuevo_ingreso = floatval($nuevo_ingreso);

    if ($nuevo_ingreso > 0) {
        // Obtener 迆ltimo registro de caja
        $sql = "SELECT id, saldo_actual, saldo_gastado FROM caja_menor ORDER BY id DESC LIMIT 1";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $id = $row['id'];
            $saldo_actual = $row['saldo_actual'] + $nuevo_ingreso;
            $saldo_disponible = $saldo_actual - $row['saldo_gastado'];

            // Actualizar caja
            $stmt_update = $conn->prepare("UPDATE caja_menor SET saldo_actual = ?, saldo_disponible = ? WHERE id = ?");
            $stmt_update->bind_param("ddi", $saldo_actual, $saldo_disponible, $id);
            $stmt_update->execute();
            $stmt_update->close();

            // Actualizar sesi車n
            $_SESSION['saldo_actual'] = $saldo_actual;
            $_SESSION['saldo_disponible'] = $saldo_disponible;
        } else {
            // Crear un nuevo registro si no hay ninguno
            $stmt_insert = $conn->prepare("INSERT INTO caja_menor (saldo_actual, saldo_gastado, saldo_disponible) VALUES (?, 0, ?)");
            $stmt_insert->bind_param("dd", $nuevo_ingreso, $nuevo_ingreso);
            $stmt_insert->execute();
            $stmt_insert->close();

            $_SESSION['saldo_actual'] = $nuevo_ingreso;
            $_SESSION['saldo_gastado'] = 0;
            $_SESSION['saldo_disponible'] = $nuevo_ingreso;
        }
    }
}

$conn->close();

// Redirigir de vuelta al dashboard
header("Location: menu_principal.php");
exit;
?>

