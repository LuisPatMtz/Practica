<?php 
require 'check_session.php';
require 'conexion.php';

$materia_id = $_GET['materia_id'] ?? 0;

// Obtener información de la materia
$stmt = $conn->prepare("SELECT nombre_materia FROM materias WHERE id = ? AND usuario_id = ?");
$stmt->bind_param("ii", $materia_id, $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
$materia = $result->fetch_assoc();

if (!$materia) {
    header("Location: mis_materias.php");
    exit();
}

// Obtener grupos asociados
$stmt = $conn->prepare("SELECT id, grupo, num_unidades FROM materia_grupos WHERE materia_id = ?");
$stmt->bind_param("i", $materia_id);
$stmt->execute();
$grupos = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Grupos de la Materia</title>
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
    <h1>Grupos de: <?php echo htmlspecialchars($materia['nombre_materia']); ?></h1>
    
    <div class="container">
        <table>
            <thead>
                <tr>
                    <th>Grupo</th>
                    <th>Unidades</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php while($grupo = $grupos->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($grupo['grupo']); ?></td>
                    <td><?php echo $grupo['num_unidades']; ?></td>
                    <td>
                        <a href="alumnos_grupo.php?mg_id=<?php echo $grupo['id']; ?>" style="color:#4CAF50;">Ver Alumnos</a> | 
                        <a href="calificaciones_inline.php?mg_id=<?php echo $grupo['id']; ?>" style="color:#2196F3;">Calificaciones</a>
                    </td>
                </tr>
                <?php endwhile; ?>
                <?php if($grupos->num_rows == 0): ?>
                <tr>
                    <td colspan="3" style="text-align:center;">No hay grupos asociados</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
        <br>
        <center>
            <a href="mis_materias.php" style="color:white; background:#4CAF50; padding:10px 20px; border-radius:20px; text-decoration:none; display:inline-block;">← Volver</a>
        </center>
    </div>
</body>
</html>
<?php $conn->close(); ?>
