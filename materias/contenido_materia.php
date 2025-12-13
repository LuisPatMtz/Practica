<?php 
require 'check_session.php';
require 'conexion.php';

$materia_id = $_GET['materia_id'] ?? 0;

// Verificar que la materia pertenece al usuario
$stmt = $conn->prepare("SELECT id, nombre_materia FROM materias WHERE id = ? AND usuario_id = ?");
$stmt->bind_param("ii", $materia_id, $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
$materia = $result->fetch_assoc();

if (!$materia) {
    header("Location: mis_materias.php");
    exit();
}

// Procesar subida de archivo
$mensaje = '';
$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES['archivo'])) {
    $descripcion = $_POST['descripcion'] ?? '';
    $archivo = $_FILES['archivo'];
    
    // Validar archivo
    if ($archivo['error'] === UPLOAD_ERR_OK) {
        $permitidos = ['pdf', 'doc', 'docx', 'ppt', 'pptx', 'xls', 'xlsx', 'txt', 'jpg', 'jpeg', 'png', 'zip', 'rar'];
        $extension = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));
        $tamano_max = 50 * 1024 * 1024; // 50MB
        
        if (!in_array($extension, $permitidos)) {
            $error = "Tipo de archivo no permitido. Permitidos: PDF, DOC, DOCX, PPT, PPTX, XLS, XLSX, TXT, JPG, PNG, ZIP, RAR";
        } elseif ($archivo['size'] > $tamano_max) {
            $error = "El archivo es demasiado grande. Máximo 50MB";
        } else {
            // Crear directorio si no existe
            $directorio = "../uploads/materias/" . $materia_id . "/";
            if (!file_exists($directorio)) {
                mkdir($directorio, 0777, true);
            }
            
            // Generar nombre único
            $nombre_unico = uniqid() . '_' . time() . '.' . $extension;
            $ruta_completa = $directorio . $nombre_unico;
            
            if (move_uploaded_file($archivo['tmp_name'], $ruta_completa)) {
                // Guardar en base de datos
                $stmt = $conn->prepare("INSERT INTO archivos_materia (materia_id, nombre_archivo, nombre_original, ruta_archivo, tipo_archivo, tamano_bytes, descripcion) VALUES (?, ?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("issssds", $materia_id, $nombre_unico, $archivo['name'], $ruta_completa, $archivo['type'], $archivo['size'], $descripcion);
                
                if ($stmt->execute()) {
                    $mensaje = "Archivo subido exitosamente";
                } else {
                    $error = "Error al guardar en base de datos";
                    unlink($ruta_completa);
                }
                $stmt->close();
            } else {
                $error = "Error al subir el archivo";
            }
        }
    } else {
        $error = "Error en la carga del archivo";
    }
}

// Obtener archivos de la materia
$stmt = $conn->prepare("SELECT * FROM archivos_materia WHERE materia_id = ? ORDER BY fecha_subida DESC");
$stmt->bind_param("i", $materia_id);
$stmt->execute();
$archivos = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contenido de la Materia</title>
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
        .upload-form {
            background: rgba(0,0,0,0.8);
            padding: 20px;
            border-radius: 15px;
            margin: 20px auto;
            max-width: 600px;
        }
        .upload-form input[type="file"],
        .upload-form input[type="text"],
        .upload-form textarea {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border-radius: 8px;
            border: 2px solid #4CAF50;
            background: rgba(255,255,255,0.9);
            font-family: 'Poppins', sans-serif;
        }
        .upload-form textarea {
            min-height: 60px;
            resize: vertical;
        }
        .btn-subir {
            background: #4CAF50;
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 20px;
            cursor: pointer;
            font-size: 1rem;
            font-weight: 600;
            transition: all 0.3s;
        }
        .btn-subir:hover {
            background: #45a049;
            transform: translateY(-2px);
        }
        .mensaje {
            background: rgba(76, 175, 80, 0.3);
            color: #4CAF50;
            padding: 12px;
            border-radius: 8px;
            margin: 15px auto;
            text-align: center;
            max-width: 600px;
        }
        .error {
            background: rgba(244, 67, 54, 0.3);
            color: #f44336;
        }
        .file-icon {
            font-size: 1.5rem;
            margin-right: 10px;
        }
        .file-size {
            color: #aaa;
            font-size: 0.85rem;
        }
        .file-actions {
            white-space: nowrap;
        }
        .file-actions a {
            margin: 0 5px;
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

    <h1>Contenido: <?php echo htmlspecialchars($materia['nombre_materia']); ?></h1>

    <?php if($mensaje): ?>
        <div class="mensaje"><?php echo $mensaje; ?></div>
    <?php endif; ?>
    
    <?php if($error): ?>
        <div class="mensaje error"><?php echo $error; ?></div>
    <?php endif; ?>

    <div class="upload-form">
        <h2 style="color:white; text-align:center; margin-bottom:15px;">📤 Subir Archivo</h2>
        <form method="POST" enctype="multipart/form-data">
            <input type="file" name="archivo" required accept=".pdf,.doc,.docx,.ppt,.pptx,.xls,.xlsx,.txt,.jpg,.jpeg,.png,.zip,.rar">
            <input type="text" name="descripcion" placeholder="Descripción (opcional)" maxlength="255">
            <center>
                <button type="submit" class="btn-subir">Subir Archivo</button>
            </center>
        </form>
        <p style="color:#aaa; text-align:center; font-size:0.85rem; margin-top:10px;">
            Tipos permitidos: PDF, DOC, DOCX, PPT, PPTX, XLS, XLSX, TXT, JPG, PNG, ZIP, RAR (máx. 50MB)
        </p>
    </div>

    <div class="container">
        <h2 style="color:white; text-align:center; margin-bottom:20px;">📁 Archivos Subidos</h2>
        <table>
            <thead>
                <tr>
                    <th>Archivo</th>
                    <th>Descripción</th>
                    <th>Tamaño</th>
                    <th>Fecha</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php while($archivo = $archivos->fetch_assoc()): 
                    $extension = strtolower(pathinfo($archivo['nombre_original'], PATHINFO_EXTENSION));
                    $icono = '📄';
                    if (in_array($extension, ['pdf'])) $icono = '📕';
                    elseif (in_array($extension, ['doc', 'docx'])) $icono = '📘';
                    elseif (in_array($extension, ['ppt', 'pptx'])) $icono = '📙';
                    elseif (in_array($extension, ['xls', 'xlsx'])) $icono = '📗';
                    elseif (in_array($extension, ['jpg', 'jpeg', 'png'])) $icono = '🖼️';
                    elseif (in_array($extension, ['zip', 'rar'])) $icono = '🗜️';
                    
                    $tamano_mb = round($archivo['tamano_bytes'] / 1048576, 2);
                    $tamano_kb = round($archivo['tamano_bytes'] / 1024, 2);
                    $tamano = $tamano_mb > 0 ? $tamano_mb . ' MB' : $tamano_kb . ' KB';
                ?>
                <tr>
                    <td>
                        <span class="file-icon"><?php echo $icono; ?></span>
                        <?php echo htmlspecialchars($archivo['nombre_original']); ?>
                    </td>
                    <td><?php echo htmlspecialchars($archivo['descripcion'] ?: '-'); ?></td>
                    <td class="file-size"><?php echo $tamano; ?></td>
                    <td><?php echo date('d/m/Y H:i', strtotime($archivo['fecha_subida'])); ?></td>
                    <td class="file-actions">
                        <a href="descargar_archivo.php?id=<?php echo $archivo['id']; ?>" style="color:#4CAF50;">Descargar</a> |
                        <a href="eliminar_archivo.php?id=<?php echo $archivo['id']; ?>&materia_id=<?php echo $materia_id; ?>" 
                           onclick="return confirm('¿Eliminar este archivo?')" style="color:#f44336;">Eliminar</a>
                    </td>
                </tr>
                <?php endwhile; ?>
                <?php if($archivos->num_rows == 0): ?>
                <tr>
                    <td colspan="5" style="text-align:center;">No hay archivos subidos</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
        <br>
        <center>
            <a href="mis_materias.php" style="color:white; background:#4CAF50; padding:10px 20px; border-radius:20px; text-decoration:none; display:inline-block;">← Volver a Mis Materias</a>
        </center>
    </div>
</body>
</html>
<?php $conn->close(); ?>
