<?php 
require 'check_session.php';
require 'conexion.php';

$usuario_id = $_SESSION['user_id'];

// Obtener materias con grupos del usuario
$stmt = $conn->prepare("
    SELECT mg.id, m.nombre_materia, mg.grupo, mg.num_unidades
    FROM materia_grupos mg
    JOIN materias m ON mg.materia_id = m.id
    WHERE m.usuario_id = ?
    ORDER BY m.nombre_materia, mg.grupo
");
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Capturar Calificaciones</title>
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
        .calif-cell {
            cursor: pointer;
            padding: 8px;
            transition: background-color 0.3s;
        }
        .calif-cell:hover {
            background-color: rgba(76, 175, 80, 0.2);
        }
        .calif-na {
            color: #ff5252;
            font-weight: 500;
        }
        .calif-input {
            width: 80px;
            padding: 6px;
            border: 2px solid #4CAF50;
            border-radius: 5px;
            text-align: center;
            font-size: 1rem;
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
    <h1>Capturar Calificaciones</h1>
    
    <div class="container">
        <table>
            <thead>
                <tr>
                    <th>Materia</th>
                    <th>Grupo</th>
                    <th>Unidades</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody>
                <?php while($mg = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($mg['nombre_materia']); ?></td>
                    <td><?php echo htmlspecialchars($mg['grupo']); ?></td>
                    <td><?php echo $mg['num_unidades']; ?></td>
                    <td>
                        <a href="calificaciones_inline.php?mg_id=<?php echo $mg['id']; ?>" style="color:#4CAF50;">Ver Tabla</a>
                    </td>
                </tr>
                <?php endwhile; ?>
                <?php if($result->num_rows == 0): ?>
                <tr>
                    <td colspan="4" style="text-align:center;">No tienes materias con grupos asignados</td>
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
<?php $conn->close(); ?>
