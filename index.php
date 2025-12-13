<?php require 'check_session.php'; ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Estudiantes</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="estilos/index.css">
</head>
<body>
    <div class="user-info">
        <span>Bienvenido, <?php echo htmlspecialchars($_SESSION['username']); ?></span>
        <a href="logout.php" class="btn-logout">Cerrar sesión</a>
    </div>
    
    <h1>Sistema de gestion academica</h1>

    <div class="main-container">
        <!-- Sección Gestión de Estudiantes -->
        <div class="section">
            <h2 class="section-title">Gestión de estudiantes</h2>
            <ul class="menu-list">
                <li><a href="registro/registrar.html">Registrar estudiante</a></li>
                <li><a href="tabla/tabla.html">Ver tabla de registros</a></li>
                <li><a href="graficas/graficas.html">Ver graficas estadisticas</a></li>
            </ul>
        </div>

        <!-- Sección Gestión de Materias -->
        <div class="section">
            <h2 class="section-title">Gestion de materias</h2>
            <ul class="menu-list">
                <li><a href="materias/crear_materia.php">Crear nueva materia</a></li>
                <li><a href="materias/mis_materias.php">Mis materias</a></li>
                <li><a href="materias/capturar_calificaciones.php">Capturar calificaciones</a></li>
            </ul>
        </div>
        
        <!-- Sección Mis Archivos -->
        <div class="section">
            <h2 class="section-title">Mis Archivos</h2>
            <ul class="menu-list">
                <li><a href="archivos/mis_archivos.php">Gestionar archivos</a></li>
            </ul>
        </div>
    </div>
</body>
</html>
