<?php session_start(); ?>
<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Inicio de Sesión</title>
  <link rel="stylesheet" href="css/style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <link rel="icon" href="img/Logo_Avance.jpg">
</head>

<body>
  <div class="formulario">
    <div class="titulo">
      <h1>Inicio de Sesión</h1>
      <img src="img/Logo_Avance.jpg" alt="Logo Avance Legal">
    </div>

    <?php if (isset($_SESSION['error'])): ?>
      <div class="error" id="error-msg"><?= htmlspecialchars($_SESSION['error']) ?></div>
      <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <form action="procesar_datos.php" method="post">
      <div class="username">
        <div class="i"><i class="fas fa-user"></i></div>
        <input type="text" name="usuario" required>
        <label>Nombre de usuario</label>
      </div>

      <div class="username">
        <div class="i"><i class="fas fa-lock"></i></div>
        <input type="password" name="password" id="password" required>
        <span class="toggle-password" onclick="togglePassword()">
          <i class="fas fa-eye" id="eye-icon"></i>
        </span>
        <label>Contraseña</label>
      </div>

      <div class="recordar">¿Olvidaste tu cuenta?</div>
      <input type="submit" value="Iniciar">
      <div class="registrarse">
        Quiero hacer el <a href="#">Registro</a>
      </div>
    </form>
  </div>
  <script src="js/index.js"></script>
</body>

</html>