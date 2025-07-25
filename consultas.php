<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Consultas - Avance Legal</title>
  <link rel="stylesheet" href="css/consultas.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  <link rel="icon" href="img/Logo_Avance.jpg">
</head>

<body>
  <header class="header">
    <h1 class="logo"><a href="menu_principal.php">Expenselite</a></h1>
  </header>

  <main class="main-content">
    <section class="consulta-form">
      <h2>Consultas de compras realizadas</h2>
      <form action="resultados_consultas.php" method="POST">
        <div class="form-group">
          <label for="area">Área:</label>
          <select id="area" name="area">
            <option value="">Selecciona Todas las Áreas</option>
            <option value="comercial">Comercial</option>
            <option value="administrativa">Administrativa</option>
            <option value="TI">TI</option>
            <option value="gerencia">Gerencia</option>
          </select>
        </div>
        <div class="form-group">
          <label for="fecha">Fecha:</label>
          <input type="date" id="fecha" name="fecha">
        </div>
        <div class="form-group">
          <button type="submit" class="btn"><i class="fas fa-search"></i> Consultar</button>
        </div>
      </form>
    </section>


    </div>
    </section>
  </main>
</body>

</html>