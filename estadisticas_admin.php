<?php
session_start();

if (!isset($_SESSION['admin']) && !isset($_SESSION['usuario'])) {
  header("Location: index.php");
  exit();
}

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '/home2/avanceap/Credencial_Global/config.php';

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT);
if ($conn->connect_error) {
  die("Error de conexión: " . $conn->connect_error);
}

$labels = [];
$totalGasto = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $area = $_POST['area'];
  $producto = isset($_POST['producto']) ? $_POST['producto'] : '';

  $sqlQuery = "SELECT SUM(precio) AS total_gasto, area, producto FROM compras WHERE 1";

  if (!empty($area)) {
    $sqlQuery .= " AND area = ?";
  }

  if (!empty($producto)) {
    $sqlQuery .= " AND producto = ?";
  }

  $sqlQuery .= " GROUP BY area, producto";

  $sql = $conn->prepare($sqlQuery);

  if ($sql === false) {
    die("Error al preparar la consulta: " . $conn->error);
  }

  if (!empty($area) && !empty($producto)) {
    $sql->bind_param('ss', $area, $producto);
  } elseif (!empty($area)) {
    $sql->bind_param('s', $area);
  } elseif (!empty($producto)) {
    $sql->bind_param('s', $producto);
  }

  $sql->execute();
  $result = $sql->get_result();

  if ($result->num_rows > 0) {
    while ($data = $result->fetch_assoc()) {
      $labels[] = ucfirst($data['producto']);
      $totalGasto[] = $data['total_gasto'];
    }
  } else {
    echo "<p>No hay datos disponibles para la selección.</p>";
  }

  $sql->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="es-ES">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Estadísticas de Compras</title>
  <link rel="stylesheet" href="css/estadisticas.css">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <link rel="icon" href="img/Logo_Avance.jpg">
</head>

<body>
  <h1>Estadísticas de Compras</h1>

  <form action="estadisticas.php" method="POST">
    <div class="form-group">
      <label for="area">Selecciona el Área:</label>
      <select id="area" name="area" required>
        <option value="">Selecciona Todas las Áreas</option>
        <option value="comercial">Comercial</option>
        <option value="administrativa">Administrativa</option>
        <option value="TI">TI</option>
        <option value="gerencia">Gerencia</option>
      </select>
    </div>
    <div class="boton-volver">
      <a href="menu_principal.php" class="btn">Volver al Menú Principal</a>
    </div>
    <input type="submit" value="Ver Estadísticas">
  </form>

  <div id="estadisticas">
    <?php
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($labels)) {
      echo "<h2>Estadísticas de Compras</h2>";
      foreach ($labels as $key => $label) {
        echo "<p>Producto: " . ucfirst($label) . "</p>";
        echo "<p>Total Gastado: $" . number_format($totalGasto[$key], 2) . "</p>";
      }
    }
    ?>
  </div>

  <div class="graficas-container">
    <h2>Gráfico de Gastos</h2>
    <canvas id="graficaCompras"></canvas>
  </div>

  <!-- Script que genera la gráfica con datos PHP -->
  <script>
    const labels = <?= json_encode($labels ?? []); ?>;
    const gastosData = <?= json_encode($totalGasto ?? []); ?>;

    const datos = {
      labels: labels,
      datasets: [{
        label: 'Total Gastado en Compras',
        backgroundColor: 'rgba(75, 192, 192, 0.5)',
        borderColor: 'rgba(75, 192, 192, 1)',
        borderWidth: 1,
        data: gastosData
      }]
    };

    const opciones = {
      responsive: true,
      scales: {
        y: {
          beginAtZero: true,
          ticks: {
            callback: function(value) {
              return '$' + value;
            }
          }
        }
      }
    };

    const ctx = document.getElementById('graficaCompras').getContext('2d');
    const grafica = new Chart(ctx, {
      type: 'bar',
      data: datos,
      options: opciones
    });
  </script>
</body>

</html>
