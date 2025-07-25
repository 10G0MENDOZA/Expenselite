
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Registro de Caja Menor</title>
  <link rel="stylesheet" href="css/registro_caja.css">
  <link rel="icon" href="img/Logo_Avance.jpg">
</head>

<body>
  <div class="formulario">
    <h1>Registro de Caja Menor</h1>
    <form action="procesar_caja.php" method="POST">
      <div class="form-group">
        <label for="fecha">Fecha:</label>
        <input type="date" id="fecha" name="fecha" required>
      </div>
      <div class="form-group">
        <label for="monto">Monto Inicial:</label>
        <input type="text" id="monto" name="monto" required>
      </div>
      <input type="submit" value="Registrar">
    </form>
  </div>

  <script src="js/formato_monto_caja.js"></script>
</body>

</html>