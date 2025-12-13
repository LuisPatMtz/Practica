<?php 
require 'check_session.php';
require 'conexion.php';

$usuario_id = $_SESSION['user_id'];

// Obtener materias del usuario
$stmt = $conn->prepare("SELECT id, nombre_materia, fecha_creacion FROM materias WHERE usuario_id = ? ORDER BY fecha_creacion DESC");
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Materias</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../estilos/tabla.css">
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
    <h1>Mis Materias</h1>
    
    <?php if(isset($_GET['success'])): ?>
        <div style="background:rgba(76,175,80,0.3); color:#4CAF50; padding:10px; border-radius:8px; text-align:center; margin:20px auto; max-width:600px;">
            Materia creada exitosamente
        </div>
    <?php endif; ?>
    
    <div class="container">
        <table>
            <thead>
                <tr>
                    <th>Materia</th>
                    <th>Fecha de Creación</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php while($materia = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($materia['nombre_materia']); ?></td>
                    <td><?php echo date('d/m/Y', strtotime($materia['fecha_creacion'])); ?></td>
                    <td>
                        <a href="contenido_materia.php?materia_id=<?php echo $materia['id']; ?>" style="color:#FF9800;">📁 Contenido</a> | 
                        <a href="agregar_grupos.php?materia_id=<?php echo $materia['id']; ?>" style="color:#4CAF50;">Agregar Grupos</a> | 
                        <a href="ver_grupos.php?materia_id=<?php echo $materia['id']; ?>" style="color:#2196F3;">Ver Grupos</a>
                    </td>
                </tr>
                <?php endwhile; ?>
                <?php if($result->num_rows == 0): ?>
                <tr>
                    <td colspan="3" style="text-align:center;">No tienes materias creadas</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
        <br>
        <center>
            <a href="../index.php" style="color:white; background:#4CAF50; padding:10px 20px; border-radius:20px; text-decoration:none; display:inline-block;">← Volver al inicio</a>
        </center>
    </div>
</body>
</html>
<?php
$stmt->close();
$conn->close();
?>
