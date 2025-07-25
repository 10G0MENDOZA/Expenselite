<?php
session_start();
?>

<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Administrador</title>
  <link rel="stylesheet" href="css/style.css">
  <link rel="icon" href="img/Logo_Avance.jpg">
</head>
<body>
  <div class="formulario">
    <div class="titulo">
      <h1>Administrador</h1>
      <img src="img/Logo_Avance.jpg" alt="Logo Avance Legal">
    </div>

    <?php if (isset($_SESSION['error'])): ?>
      <div class="error" id="error-msg"><?php echo $_SESSION['error']; ?></div>
      <?php unset($_SESSION['error']); // Eliminar el mensaje después de mostrarlo ?>
    <?php endif; ?>

    <form action="procesar_admin.php" method="post">
      <div class="username">
        <input type="text" name="usuario" required>
        <label>Nombre de usuario</label>
      </div>
      <div class="username">
        <input type="password" name="contrasena" required>
        <label>Contraseña</label>
      </div>
      <div class="recordar">¿Olvidaste tu cuenta?</div>
      <input type="submit" value="Iniciar">
      <div class="registrarse">
        Quiero hacer el <a href="#">Registro</a>
      </div>
    </form>
  </div>
  <script src="js/admin.js"></script>

</body>

</html>