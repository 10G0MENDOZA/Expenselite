<?php
session_start();

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '/home2/avanceap/Credencial_Global/config.php';

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT);
if ($conn->connect_error) {
    die("❌ Error de conexión: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (!empty($_POST['usuario']) && !empty($_POST['password'])) {
        $usuario = trim($_POST['usuario']);
        $contrasena = trim($_POST['password']);

        // Puedes guardar mensajes de depuración en sesión si quieres verlos luego en una vista
        // $_SESSION['debug'][] = "Usuario recibido: $usuario";
        // $_SESSION['debug'][] = "Contraseña recibida: $contrasena";

        $stmt = $conn->prepare("SELECT * FROM datos WHERE usuario = ? LIMIT 1");
        if (!$stmt) {
            die("Error preparando la consulta: " . $conn->error);
        }

        $stmt->bind_param("s", $usuario);
        $stmt->execute();
        $resultado = $stmt->get_result();

        if ($resultado && $resultado->num_rows > 0) {
            $fila = $resultado->fetch_assoc();
            $hash = $fila['contrasena'];

            // $_SESSION['debug'][] = "Hash obtenido de BD: $hash";

            if (password_verify($contrasena, $hash)) {
                // $_SESSION['debug'][] = "✅ Contraseña verificada correctamente";

                $_SESSION['usuario'] = $fila['usuario'];

                $resCaja = $conn->query("SELECT saldo_actual FROM caja_menor WHERE estado = 'ABIERTA' ORDER BY id DESC LIMIT 1");

                if ($resCaja && $resCaja->num_rows > 0) {
                    $filaCaja = $resCaja->fetch_assoc();
                    $saldo = floatval($filaCaja['saldo_actual']);

                    if ($saldo > 0) {
                        header("Location: menu_principal.php");
                        exit;
                    } else {
                        header("Location: registro_caja.php");
                        exit;
                    }
                } else {
                    // No hay caja abierta
                    header("Location: registro_caja.php");
                    exit;
                }
            } else {
                $_SESSION['error'] = "❌ Usuario o contraseña incorrecta.";
                header("Location: index.php");
                exit;
            }
        } else {
            $_SESSION['error'] = "❌ Usuario o contraseña incorrecta.";
            header("Location: index.php");
            exit;
        }

        $stmt->close();
    } else {
        $_SESSION['error'] = "⚠️ Por favor, completa todos los campos.";
        header("Location: index.php");
        exit;
    }
} else {
    $_SESSION['error'] = "⚠️ Método de solicitud no permitido.";
    header("Location: index.php");
    exit;
}

$conn->close();
