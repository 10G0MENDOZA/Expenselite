<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

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

// Obtener el último registro de caja
$sql = "SELECT saldo_actual, saldo_gastado FROM caja_menor ORDER BY id DESC LIMIT 1";
$result = $conn->query($sql);

// Definir valores iniciales
$saldo_actual = 0;
$saldo_gastado = 0;

if ($result->num_rows > 0) {
  $row = $result->fetch_assoc();
  $saldo_actual = (float) $row['saldo_actual'];
  $saldo_gastado = (float) $row['saldo_gastado'];
}

// Calcular el saldo disponible
$saldo_disponible = number_format($saldo_actual - $saldo_gastado, 2, '.', '');

// Guardar en la sesión
$_SESSION['saldo_actual'] = number_format($saldo_actual, 2, '.', '');
$_SESSION['saldo_gastado'] = number_format($saldo_gastado, 2, '.', '');
$_SESSION['saldo_disponible'] = $saldo_disponible;

$message = "";

// Procesar compras si se envía el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $producto = isset($_POST['producto']) ? $conn->real_escape_string($_POST['producto']) : '';

  $precio_input = isset($_POST['precio']) ? $_POST['precio'] : '0';
  $precio_input = str_replace('.', '', $precio_input);
  $precio_input = str_replace(',', '.', $precio_input);
  $precio = number_format((float) $precio_input, 2, '.', '');

  $descripcion = isset($_POST['descripcion']) ? $conn->real_escape_string($_POST['descripcion']) : '';
  $area = isset($_POST['area']) ? $conn->real_escape_string($_POST['area']) : '';

  // Verificar si la compra ya existe
  $sql_check = "SELECT * FROM compras WHERE producto = '$producto' AND descripcion = '$descripcion' AND area = '$area' LIMIT 1";
  $check_result = $conn->query($sql_check);

  if ($check_result->num_rows > 0) {
    $message = "";
  } else {
    $fecha = date("Y-m-d H:i:s");

    $sql = "INSERT INTO compras (producto, precio, descripcion, area, fecha) 
                VALUES ('$producto', $precio, '$descripcion', '$area', '$fecha')";

    if ($conn->query($sql) === TRUE) {
      $ultimo_id = $conn->insert_id;

      // Manejar la carga del archivo
      $archivo_subido = "";
      if (isset($_FILES['archivo']) && $_FILES['archivo']['error'] == 0) {
        $archivo_tipo = $_FILES['archivo']['type'];
        $archivo_tmp = $_FILES['archivo']['tmp_name'];
        $archivo_nombre = $_FILES['archivo']['name'];
        $ext = strtolower(pathinfo($archivo_nombre, PATHINFO_EXTENSION));

        $directorio_destino = 'img/facturas/';

        if (!is_dir($directorio_destino)) {
          mkdir($directorio_destino, 0777, true);
        }

        if (($ext == 'pdf' || strpos($archivo_tipo, 'image') !== false)) {
          $archivo_destino = $directorio_destino . $ultimo_id . '_' . $archivo_nombre;

          if (move_uploaded_file($archivo_tmp, $archivo_destino)) {
            $sql_update = "UPDATE compras SET factura = '$archivo_destino' WHERE id = $ultimo_id";
            $conn->query($sql_update);

            $archivo_subido = $archivo_destino;
          } else {
            $message = "Error al cargar el archivo.";
          }
        } else {
          $message = "Solo se permiten archivos PDF o imágenes.";
        }
      }

      if (empty($archivo_subido)) {
        $message = "Compra registrada exitosamente, pero sin archivo.";
      } else {
        $message = "Compra registrada exitosamente con el archivo.";
      }

      // Actualizar saldo_gastado y saldo_disponible
      $saldo_gastado += $precio;
      $saldo_disponible = number_format($saldo_actual - $saldo_gastado, 2, '.', '');

      // Asegurar formato antes de actualizar
      $saldo_gastado = number_format($saldo_gastado, 2, '.', '');

      $sql_last_id = "SELECT id FROM caja_menor ORDER BY id DESC LIMIT 1";
      $result_last_id = $conn->query($sql_last_id);
      $row_last_id = $result_last_id->fetch_assoc();
      $last_id = $row_last_id['id'];

      $sql_update = "UPDATE caja_menor SET saldo_gastado = $saldo_gastado, saldo_disponible = $saldo_disponible WHERE id = $last_id";
      $conn->query($sql_update);

      $_SESSION['saldo_gastado'] = $saldo_gastado;
      $_SESSION['saldo_disponible'] = $saldo_disponible;
    } else {
      $message = "Error al registrar la compra: " . $conn->error;
    }
  }
}

// Configurar paginación
$registros_por_pagina = 10;
$pagina_actual = isset($_GET['pagina']) ? intval($_GET['pagina']) : 1;
$offset = ($pagina_actual - 1) * $registros_por_pagina;

// Obtener compras con paginación
$sql_compras = "SELECT * FROM compras ORDER BY fecha DESC LIMIT $offset, $registros_por_pagina";
$result_compras = $conn->query($sql_compras);

// Obtener el número total de registros
$sql_total = "SELECT COUNT(*) as total FROM compras";
$total_result = $conn->query($sql_total);
$total_registros = $total_result->fetch_assoc()['total'];
$total_paginas = ceil($total_registros / $registros_por_pagina);

// Cerrar conexión
$conn->close();
?>

<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard - Expenselite</title>
  <link rel="stylesheet" href="css/menu_principal.css">
  <link rel="stylesheet" href="css/menu_principal2.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <link rel="icon" href="img/Logo_Avance.jpg">
</head>

<body>
  <div class="dashboard">
    <aside class="menu">
      <h1 class="logo">Expenselite</h1>
      <ul class="menu-list">
        <li><a href="menu_principal.php"><i class="fas fa-home"></i> Inicio</a></li>
        <li><a href="consultas.php"><i class="fas fa-search"></i> Consultas</a></li>
        <li><a href="estadisticas.php"><i class="fas fa-chart-line"></i> Estadísticas</a></li>
        <li><a href="logout_menu.php"><i class="fas fa-sign-out-alt"></i> Cerrar Sesión</a></li>
      </ul>
    </aside>

    <main class="main-content">
      <section class="content">
        <h2>Resumen de Caja Menor</h2>
        <div class="dashboard-summary">
          <div class="summary-box saldo-actual">
            <h3>Saldo Recibido</h3>
            <?php if ($_SESSION['saldo_disponible'] == 0): ?>
              <button id="abrir-caja-btn" type="button" onclick="mostrarFormularioAgregarDineroCaja()">Abrir
                Nuevamente Caja</button>
            <?php endif; ?>


            <p><?php echo number_format($_SESSION['saldo_actual'], 0, ',', '.'); ?></p>
          </div>
          <div class="summary-box saldo-gastado">
            <h3>Saldo Gastado</h3>
            <p><?php echo number_format($_SESSION['saldo_gastado'], 0, ',', '.'); ?></p>
          </div>
          <div class="summary-box saldo-disponible">
            <h3>Saldo Disponible</h3>
            <p><?php echo number_format($_SESSION['saldo_disponible'], 0, ',', '.'); ?></p>

            <?php
            if ($_SESSION['saldo_disponible'] <= 50000) {
              echo "<div id='mensaje-advertencia' style='color:white; font-weight:bold; margin-top: 10px;'>⚠️ ¡Atención! El saldo disponible es menor a \$50.000</div>";
            }
            ?>


          </div>
          <div class="actions">
            <h3>Acciones</h3>
            <button onclick="mostrarFormularioCompra()">Registrar Compra</button>
          </div>

          <! !-- Formulario oculto para agregar dinero -->
            <div id="formulario-saldo" class="formulario-agregar-saldo" style="display: none;">
              <form action="actualizar_saldo.php" method="POST">
                <label for="nuevo_ingreso">Añadir Fondos a la Caja</label>
                <input type="text" id="nuevo_ingreso" name="nuevo_ingreso" required placeholder="0">
                <button type="submit">Agregar Fondos</button>
              </form>
            </div>
            <?php if ($saldo_disponible <= 0): ?>
              <div class="cierre-caja">
                <form id="formCierreCaja" method="POST" onsubmit="return confirmarCierre()">
                  <!-- No es necesario que el usuario seleccione la fecha, será la actual -->
                  <input type="hidden" name="cerrar_caja" value="1">
                  <input type="hidden" name="fecha" value="<?php echo date('Y-m-d'); ?>">
                  <button type="submit">Cerrar Caja</button>
                </form>
              </div>
            <?php endif; ?>

            <div id="resultado"></div> <!-- Aquí se mostrará el resultado del cierre de caja -->
            <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>


        </div>
        <div class="compras-registradas">
          <h3>Movimientos Registrados</h3>
          <?php if ($message): ?>
            <p class="message"><?php echo htmlspecialchars($message); ?></p>
          <?php endif; ?>

          <?php if ($result_compras->num_rows > 0): ?>
            <table class="compras-table">
              <thead>
                <tr>
                  <th>Producto</th>
                  <th>Precio</th>
                  <th>Descripción O El Porque De La compra</th>
                  <th>Área</th>
                  <th>Fecha</th>
                  <th>Factura</th>
                </tr>
              </thead>
              <tbody>
                <?php while ($row = $result_compras->fetch_assoc()): ?>
                  <?php
                  $id = $row['id'];
                  $filePath = "";
                  $fileDir = $_SERVER['DOCUMENT_ROOT'] . "/img/facturas/";

                  // Buscar archivos asociados con el ID
                  $pattern = $fileDir . $id . "_*";
                  $matchingFiles = glob($pattern);

                  $fileDisplay = "<span>Sin archivo</span>";
                  if (!empty($matchingFiles)) {
                    $fileName = basename($matchingFiles[0]);
                    $filePath = "/img/facturas/" . $fileName;
                    $fileDisplay = "<a href='$filePath' download>Descargar factura</a>";
                  }
                  ?>
                  <tr>
                    <td><?php echo htmlspecialchars($row['producto']); ?></td>
                    <td><?php echo number_format($row['precio'], 2, '.', ','); ?></td>
                    <td><?php echo htmlspecialchars($row['descripcion']); ?></td>
                    <td><?php echo htmlspecialchars($row['area']); ?></td>
                    <td><?php echo date("d/m/Y H:i", strtotime($row['fecha'])); ?></td>
                    <td><?php echo $fileDisplay; ?></td>
                  </tr>
                <?php endwhile; ?>
              </tbody>
            </table>

            <!-- Controles de paginación -->
            <div class="pagination">
              <?php if ($pagina_actual > 1): ?>
                <a href="?pagina=<?php echo $pagina_actual - 1; ?>" class="prev">&lt; Anterior</a>
              <?php endif; ?>

              <?php if ($pagina_actual < $total_paginas): ?>
                <a href="?pagina=<?php echo $pagina_actual + 1; ?>" class="next">Siguiente &gt;</a>
              <?php endif; ?>
            </div>
          <?php else: ?>
            <p>No hay compras registradas.</p>
          <?php endif; ?>
        </div>
      </section>

      <!-- Modal de Registro de Compra -->
      <div id="modalCompra" class="modal" style="display: none;">
        <div class="modal-content">
          <span class="close" onclick="cerrarFormularioCompra()">&times;</span>
          <h2>Registrar Compra</h2>
          <form action="menu_principal.php" method="POST" id="formCompra" enctype="multipart/form-data">
            <div class="form-group">
              <label for="producto">Producto:</label>
              <input type="text" id="producto" name="producto" required>
            </div>
            <div class="form-group">
              <label for="precio">Precio:</label>
              <input type="text" id="precio" name="precio" required>
            </div>
            <div class="form-group">
              <label for="descripcion">Descripción:</label>
              <textarea id="descripcion" name="descripcion" required></textarea>
            </div>
            <div class="form-group">
              <label for="area">Área de la Compra:</label>
              <select id="area" name="area" required>
                <option value="">Selecciona un área</option>
                <option value="comercial">Comercial</option>
                <option value="administrativa">Administrativa</option>
                <option value="ti">TI</option>
                <option value="gerencia">Gerencia</option>
              </select>
            </div>
            <div class="form-group">
              <label for="tipo_documento" class="tooltip">Evidencia
                <span class="tooltip-text">Esta evidencia es para seleccionar si es una factura o un
                  recibo de caja menor.</span>
              </label>
              <select id="tipo_documento" name="tipo_documento" required onchange="actualizarEtiqueta()">
                <option value="factura">Factura</option>
                <option value="recibo_transporte">Recibo de caja menor</option>
              </select>
            </div>

            <div class="form-group">
              <label id="labelArchivo" for="archivo">Adjuntar Factura (PDF o Imagen):</label>
              <input type="file" id="archivo" name="archivo" accept=".pdf,image/*" required>
            </div>
            <input type="submit" value="Registrar Compra">
          </form>
        </div>
      </div>
  </div>
  <script src="js/formato_precio.js"></script>
  <script src="js/actualizar_etiqueta.js"></script>
  <script src="js/mostrar_formulario_agregar_dinero_caja.js"></script>
  <script src="js/agregar_fondos.js"></script>
  <script src="js/formatear_puntos.js"></script>
  <script src="js/limpiar.js"></script>
  <script src="js/cerrar_caja.js"></script>
</body>

</html>