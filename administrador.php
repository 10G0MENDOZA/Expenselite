<?php
session_start();

// Verificar si el usuario está logueado como administrador
if (!isset($_SESSION['admin'])) {
    header("Location: login_administrador.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administrador - Expenselite</title>
    <link rel="stylesheet" href="css/administrador.css">
    <link rel="icon" href="img/Logo_Avance.jpg">
    <!-- Enlace a Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<body>
    <div class="container">
        <aside class="sidebar">
            <div class="logo">Expenselite</div>
            <ul class="menu">
                <li><a href="administrador.php"><i class="fas fa-home"></i> Inicio</a></li>
                <li><a href="estadisticas_admin.php"><i class="fas fa-chart-line"></i> Estadísticas</a></li>
                <li><a href="consultas_admin.php"><i class="fas fa-file-invoice"></i> Consultar Facturas</a></li>
                <li><a href="#"><i class="fas fa-cogs"></i> Configuración</a></li>
                <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Cerrar Sesión</a></li>
            </ul>
        </aside>
        <main class="content">
            <header>
                <h1><i class="fas fa-user-shield"></i> Bienvenido, Administrador</h1>
            </header>
            <section class="charts">
                <div class="chart"><i class="fas fa-calendar-day"></i> Gráfico de Gastos por Día</div>
                <div class="chart"><i class="fas fa-box"></i> Productos con Más Gastos</div>
                <div class="chart"><i class="fas fa-receipt"></i> Facturas Recientes</div>
            </section>
        </main>
    </div>
</body>
</html>
