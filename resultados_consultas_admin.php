<?php
session_start();

// Permitir el acceso si está logueado como admin o como usuario
if (!isset($_SESSION['admin']) && !isset($_SESSION['usuario'])) {
  header("Location: login_administrador.php"); // Redirige al login general
  exit();
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Resultados de Compras</title>
  <link rel="stylesheet" href="css/resultados_consultas.css"> <!-- Referencia al CSS externo -->
  <link rel="icon" href="img/Logo_Avance.jpg">
</head>

<body>

  <header class="header">
    <h1>Resultados de Compras</h1>
  </header>

  <main>
    <?php


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



    // Inicializar variables para los filtros
    $area = isset($_POST['area']) ? $_POST['area'] : '';
    $fecha = isset($_POST['fecha']) ? $_POST['fecha'] : '';

    // Construir la consulta SQL
    $sql = "SELECT id, producto, precio, descripcion, area, fecha FROM compras WHERE 1=1";

    // Aplicar filtros si están definidos
    if (!empty($area)) {
      $sql .= " AND area = '" . $conn->real_escape_string($area) . "'";
    }
    if (!empty($fecha)) {
      $sql .= " AND DATE(fecha) = '" . $conn->real_escape_string($fecha) . "'";
    }

    // Ejecutar la consulta
    $result = $conn->query($sql);

    echo "<div class='container'>";
    if ($result && $result->num_rows > 0) {
      echo "<table class='styled-table'>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Producto</th>
                        <th>Precio</th>
                        <th>Descripción</th>
                        <th>Archivo</th>
                        <th>Área</th>
                        <th>Fecha</th>
                    </tr>
                </thead>
                <tbody>";

      while ($row = $result->fetch_assoc()) {
        $id = $row['id'];
        $filePath = ""; // Inicializar variable de archivo
        $fileDir = $_SERVER['DOCUMENT_ROOT'] . "/img/facturas/";

        // Buscar archivos asociados con el ID
        $pattern = $fileDir . $id . "_*";
        $matchingFiles = glob($pattern);

        $fileDisplay = "<span>Sin archivo</span>";

        if (!empty($matchingFiles)) {
          $fileName = basename($matchingFiles[0]);
          $filePath = "/img/facturas/" . $fileName;

          // Mostrar solo el enlace de descarga
          $fileDisplay = "<a href='$filePath' download>Descargar archivo</a>";
        }

        echo "<tr>
                    <td>{$row['id']}</td>
                    <td>{$row['producto']}</td>
                    <td>{$row['precio']}</td>
                    <td>{$row['descripcion']}</td>
                    <td>$fileDisplay</td>
                    <td>{$row['area']}</td>
                    <td>{$row['fecha']}</td>
                  </tr>";
      }

      echo "</tbody></table>";
    } else {
      echo "<p class='no-results'>No se encontraron resultados para los filtros seleccionados.</p>";
    }
    echo "</div>";

    // Cerrar la conexión
    $conn->close();
    ?>

    <!-- Botón Volver -->
    <div class="volver-btn-container">
      <a href="administrador.php" class="volver-btn">Volver al Menú Principal</a>
    </div>
  </main>

</body>

</html>