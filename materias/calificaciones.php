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
    header("Location: capturar_calificaciones.php");
    exit();
}

// Procesar envío de calificaciones
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $unidad = $_POST['unidad'] ?? 0;
    $calificaciones = $_POST['calificacion'] ?? [];
    
    foreach ($calificaciones as $matricula => $calificacion) {
        if ($calificacion !== '') {
            // Verificar si ya existe
            $stmt = $conn->prepare("SELECT id FROM calificaciones WHERE materia_grupo_id = ? AND matricula = ? AND unidad = ?");
            $stmt->bind_param("isi", $mg_id, $matricula, $unidad);
            $stmt->execute();
            $existe = $stmt->get_result()->fetch_assoc();
            
            if ($existe) {
                // Actualizar
                $stmt = $conn->prepare("UPDATE calificaciones SET calificacion = ? WHERE id = ?");
                $stmt->bind_param("di", $calificacion, $existe['id']);
                $stmt->execute();
            } else {
                // Insertar
                $stmt = $conn->prepare("INSERT INTO calificaciones (materia_grupo_id, matricula, unidad, calificacion) VALUES (?, ?, ?, ?)");
                $stmt->bind_param("isid", $mg_id, $matricula, $unidad, $calificacion);
                $stmt->execute();
            }
        }
    }
    $success = true;
}

// Obtener unidad a mostrar
$unidad_actual = $_GET['unidad'] ?? 1;

// Obtener alumnos del grupo
$stmt = $conn->prepare("SELECT matricula, nombre FROM registro_estudiantes WHERE grupo = ? ORDER BY nombre");
$stmt->bind_param("s", $info['grupo']);
$stmt->execute();
$alumnos = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calificaciones</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../estilos/tabla.css">
</head>
<body>
    <h1><?php echo htmlspecialchars($info['nombre_materia']); ?> - <?php echo htmlspecialchars($info['grupo']); ?></h1>
    
    <?php if(isset($success)): ?>
        <div style="background:rgba(76,175,80,0.3); color:#4CAF50; padding:10px; border-radius:8px; text-align:center; margin:20px auto; max-width:600px;">
            Calificaciones guardadas exitosamente
        </div>
    <?php endif; ?>
    
    <div style="text-align:center; margin:20px;">
        <label style="color:white; margin-right:10px;">Seleccionar Unidad:</label>
        <?php for($i = 1; $i <= $info['num_unidades']; $i++): ?>
            <a href="?mg_id=<?php echo $mg_id; ?>&unidad=<?php echo $i; ?>" 
               style="display:inline-block; padding:8px 15px; margin:5px; background:<?php echo ($i == $unidad_actual) ? '#4CAF50' : 'rgba(76,175,80,0.3)'; ?>; color:white; border-radius:8px; text-decoration:none;">
                Unidad <?php echo $i; ?>
            </a>
        <?php endfor; ?>
    </div>
    
    <div class="container">
        <form method="POST">
            <input type="hidden" name="unidad" value="<?php echo $unidad_actual; ?>">
            <table>
                <thead>
                    <tr>
                        <th>Matrícula</th>
                        <th>Nombre</th>
                        <th>Calificación Unidad <?php echo $unidad_actual; ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $alumnos->data_seek(0);
                    while($alumno = $alumnos->fetch_assoc()): 
                        // Obtener calificación existente
                        $stmt = $conn->prepare("SELECT calificacion FROM calificaciones WHERE materia_grupo_id = ? AND matricula = ? AND unidad = ?");
                        $stmt->bind_param("isi", $mg_id, $alumno['matricula'], $unidad_actual);
                        $stmt->execute();
                        $calif_result = $stmt->get_result();
                        $calif = $calif_result->fetch_assoc();
                        $calificacion_actual = $calif ? $calif['calificacion'] : '';
                    ?>
                    <tr>
                        <td><?php echo htmlspecialchars($alumno['matricula']); ?></td>
                        <td><?php echo htmlspecialchars($alumno['nombre']); ?></td>
                        <td>
                            <input type="number" 
                                   name="calificacion[<?php echo $alumno['matricula']; ?>]" 
                                   value="<?php echo $calificacion_actual; ?>"
                                   min="0" max="100" step="0.1"
                                   style="width:100px; padding:8px; border-radius:5px; border:none;">
                        </td>
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
                <input type="submit" value="Guardar Calificaciones" style="padding:12px 30px; background:#4CAF50; color:white; border:none; border-radius:20px; font-size:1rem; cursor:pointer; margin-right:10px;">
                <a href="capturar_calificaciones.php" style="color:white; background:#666; padding:12px 30px; border-radius:20px; text-decoration:none; display:inline-block;">← Volver</a>
            </center>
        </form>
    </div>
</body>
</html>
<?php $conn->close(); ?>
