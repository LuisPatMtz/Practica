<?php 
require 'check_session.php';
require 'conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre_materia = $_POST['nombre_materia'] ?? '';
    $usuario_id = $_SESSION['user_id'];
    
    if (!empty($nombre_materia)) {
        $stmt = $conn->prepare("INSERT INTO materias (nombre_materia, usuario_id) VALUES (?, ?)");
        $stmt->bind_param("si", $nombre_materia, $usuario_id);
        
        if ($stmt->execute()) {
            header("Location: mis_materias.php?success=1");
            exit();
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Materia</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../estilos/registro.css">
    <style>
        .page-header {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            background: rgba(0, 0, 0, 0.95);
            padding: 12px 20px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
            z-index: 1000;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .page-header nav {
            display: flex;
            gap: 15px;
            align-items: center;
            flex-wrap: wrap;
            justify-content: center;
        }
        .page-header nav a {
            color: white;
            text-decoration: none;
            padding: 8px 16px;
            border-radius: 20px;
            transition: all 0.3s ease;
            font-size: 0.9rem;
            white-space: nowrap;
        }
        .page-header nav a:hover { background: rgba(76, 175, 80, 0.2); }
        .page-header nav a.btn-home {
            background: #4CAF50;
            font-weight: 500;
        }
        .page-header nav a.btn-home:hover { background: #45a049; }
        body { padding-top: 70px !important; }
        @media (max-width: 768px) {
            .page-header nav { gap: 8px; }
            .page-header nav a { padding: 6px 12px; font-size: 0.85rem; }
        }
    </style>
</head>
<body>
    <div class="page-header">
        <nav>
            <a href="../registro/registrar.html">Registrar</a>
            <a href="../tabla/tabla.html">Tabla</a>
            <a href="../graficas/graficas.html">Gráficas</a>
            <a href="../materias/mis_materias.php">Materias</a>
            <a href="../index.php" class="btn-home">← Volver al inicio</a>
        </nav>
    </div>
    <div class="registro-container">
        <h1>Crear Nueva Materia</h1>
        
        <form method="POST">
            <label for="nombre_materia">Nombre de la Materia:</label>
            <input type="text" id="nombre_materia" name="nombre_materia" required>
            
            <center>
                <input type="submit" value="Crear Materia">
                <a href="../index.php" style="display:inline-block; margin-top:10px; color:white; text-decoration:none;">← Volver</a>
            </center>
        </form>
    </div>
</body>
</html>
