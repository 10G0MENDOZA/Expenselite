<?php
// Mostrar errores para depuraci��n
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Incluir credenciales de la base de datos
require_once '/home2/avanceap/Credencial_Global/config.php';

// Crear conexi��n segura
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT);
if ($conn->connect_error) {
    die("�7�4 Error de conexi��n: " . $conn->connect_error);
}

session_start();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $usuario = trim($_POST["usuario"]);
    $contrasena = trim($_POST["contrasena"]);

    if (empty($usuario) || empty($contrasena)) {
        $_SESSION['error'] = "�7�2�1�5 Por favor, completa todos los campos.";
        header("Location: login_administrador.php");
        exit();
    }

    // Buscar la contrase�0�9a hasheada en la base de datos
    $stmt = $conn->prepare("SELECT contrasena FROM administradores WHERE usuario = ?");
    $stmt->bind_param("s", $usuario);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($hash);
        $stmt->fetch();

        // Verificar la contrase�0�9a con hash
        if (password_verify($contrasena, $hash)) {
            $_SESSION["admin"] = $usuario;
            header("Location: administrador.php");
            exit();
        } else {
            $_SESSION['error'] = "�7�4 Usuario o contrase�0�9a incorrectos.";
        }
    } else {
        $_SESSION['error'] = "�7�4 Usuario o contrase�0�9a incorrectos.";
    }

    $stmt->close();
    $conn->close();

    // Redirigir de nuevo al login con el error
    header("Location: login_administrador.php");
    exit();
} else {
    $_SESSION['error'] = "�7�2�1�5 Acceso no permitido.";
    header("Location: login_administrador.php");
    exit();
}

