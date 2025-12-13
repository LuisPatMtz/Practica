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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $grupo = $_POST['grupo'] ?? '';
    $num_unidades = $_POST['num_unidades'] ?? 0;
    
    if (!empty($grupo) && $num_unidades > 0) {
        $stmt = $conn->prepare("INSERT INTO materia_grupos (materia_id, grupo, num_unidades) VALUES (?, ?, ?)");
        $stmt->bind_param("isi", $materia_id, $grupo, $num_unidades);
        
        if ($stmt->execute()) {
            $success = true;
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
    <title>Agregar Grupos</title>
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
        <h1>Agregar Grupo a: <?php echo htmlspecialchars($materia['nombre_materia']); ?></h1>
        
        <?php if(isset($success)): ?>
            <div style="background:rgba(76,175,80,0.3); color:#4CAF50; padding:10px; border-radius:8px; text-align:center; margin-bottom:15px;">
                Grupo agregado exitosamente
            </div>
        <?php endif; ?>
        
        <form method="POST">
            <label for="grupo">Grupo:</label>
            <select id="grupo" name="grupo" required>
                <option value="">Selecciona un grupo</option>
                <option value="ISMA - 1">ISMA - 1</option>
                <option value="ISMA - 3">ISMA - 3</option>
                <option value="ISMA - 5">ISMA - 5</option>
                <option value="ISBV - 5">ISBV - 5</option>
                <option value="ISMA - 7">ISMA - 7</option>
                <option value="ISBV - 7">ISBV - 7</option>
            </select>
            
            <label for="num_unidades">Número de Unidades:</label>
            <input type="number" id="num_unidades" name="num_unidades" min="1" max="10" required>
            
            <center>
                <input type="submit" value="Agregar Grupo">
                <a href="mis_materias.php" style="display:inline-block; margin-top:10px; color:white; text-decoration:none;">← Volver</a>
            </center>
        </form>
    </div>
</body>
</html>
<?php $conn->close(); ?>
