<?php 
require 'check_session.php';
require 'conexion.php';

$mg_id = $_GET['mg_id'] ?? 0;

// Obtener información del grupo y materia
$stmt = $conn->prepare("
    SELECT mg.grupo, mg.num_unidades, m.nombre_materia, mg.materia_id
    FROM materia_grupos mg
    JOIN materias m ON mg.materia_id = m.id
    WHERE mg.id = ? AND m.usuario_id = ?
");
$stmt->bind_param("ii", $mg_id, $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
$info = $result->fetch_assoc();

if (!$info) {
    header("Location: mis_materias.php");
    exit();
}

// Obtener alumnos del grupo
$stmt = $conn->prepare("SELECT matricula, nombre, correo_electronico FROM registro_estudiantes WHERE grupo = ? ORDER BY nombre");
$stmt->bind_param("s", $info['grupo']);
$stmt->execute();
$alumnos = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alumnos del Grupo</title>
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
    <h1><?php echo htmlspecialchars($info['nombre_materia']); ?> - <?php echo htmlspecialchars($info['grupo']); ?></h1>
    <p style="text-align:center; color:white; margin-bottom:20px;">Unidades: <?php echo $info['num_unidades']; ?></p>
    
    <div class="container">
        <table>
            <thead>
                <tr>
                    <th>Matrícula</th>
                    <th>Nombre</th>
                    <th>Correo</th>
                </tr>
            </thead>
            <tbody>
                <?php while($alumno = $alumnos->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($alumno['matricula']); ?></td>
                    <td><?php echo htmlspecialchars($alumno['nombre']); ?></td>
                    <td><?php echo htmlspecialchars($alumno['correo_electronico']); ?></td>
                </tr>
                <?php endwhile; ?>
                <?php if($alumnos->num_rows == 0): ?>
                <tr>
                    <td colspan="3" style="text-align:center;">No hay alumnos en este grupo</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
        <br>
        <center>
            <a href="ver_grupos.php?materia_id=<?php echo $info['materia_id']; ?>" style="color:white; background:#4CAF50; padding:10px 20px; border-radius:20px; text-decoration:none; display:inline-block;">← Volver</a>
        </center>
    </div>
</body>
</html>
<?php $conn->close(); ?>
