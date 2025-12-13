<?php 
require 'check_session.php';
require 'conexion.php';

$usuario_id = $_SESSION['user_id'];

// Obtener archivos del usuario
$stmt = $conn->prepare("SELECT id, nombre_archivo, tipo_mime, tamanio_bytes, fecha_subida FROM archivos_usuario WHERE usuario_id = ? ORDER BY fecha_subida DESC");
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Archivos</title>
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
        
        .upload-section {
            background: rgba(0, 0, 0, 0.8);
            padding: 25px;
            border-radius: 15px;
            margin: 20px auto;
            max-width: 800px;
            text-align: center;
        }
        .upload-section h2 {
            color: white;
            margin-bottom: 20px;
        }
        .file-input-wrapper {
            position: relative;
            display: inline-block;
            margin-bottom: 15px;
        }
        .file-input-wrapper input[type="file"] {
            position: absolute;
            opacity: 0;
            width: 100%;
            height: 100%;
            cursor: pointer;
        }
        .file-input-label {
            display: inline-block;
            background: #4CAF50;
            color: white;
            padding: 12px 30px;
            border-radius: 25px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s;
        }
        .file-input-label:hover {
            background: #45a049;
            transform: translateY(-2px);
        }
        .file-name-display {
            color: white;
            margin-top: 10px;
            font-size: 0.9rem;
        }
        .upload-btn {
            background: #2196F3;
            color: white;
            padding: 12px 40px;
            border: none;
            border-radius: 25px;
            cursor: pointer;
            font-size: 1rem;
            font-weight: 600;
            margin-top: 15px;
            transition: all 0.3s;
        }
        .upload-btn:hover {
            background: #1976D2;
            transform: translateY(-2px);
        }
        .upload-btn:disabled {
            background: #666;
            cursor: not-allowed;
            transform: none;
        }
        
        .file-icon {
            font-size: 1.5rem;
            margin-right: 10px;
        }
        .file-actions {
            display: flex;
            gap: 10px;
            justify-content: center;
        }
        .btn-action {
            padding: 6px 12px;
            border-radius: 15px;
            text-decoration: none;
            font-size: 0.85rem;
            transition: all 0.3s;
            display: inline-block;
        }
        .btn-download {
            background: #4CAF50;
            color: white;
        }
        .btn-download:hover {
            background: #45a049;
        }
        .btn-rename {
            background: #2196F3;
            color: white;
        }
        .btn-rename:hover {
            background: #1976D2;
        }
        .btn-delete {
            background: #f44336;
            color: white;
        }
        .btn-delete:hover {
            background: #da190b;
        }
        .success-message {
            background: rgba(76,175,80,0.3);
            color: #4CAF50;
            padding: 15px;
            border-radius: 10px;
            margin: 20px auto;
            max-width: 600px;
            text-align: center;
        }
        .error-message {
            background: rgba(244,67,54,0.3);
            color: #f44336;
            padding: 15px;
            border-radius: 10px;
            margin: 20px auto;
            max-width: 600px;
            text-align: center;
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
            <a href="../archivos/mis_archivos.php">Mis Archivos</a>
            <a href="../index.php" class="btn-home">← Volver al inicio</a>
        </nav>
    </div>
    
    <h1>Mis Archivos</h1>
    
    <?php if(isset($_GET['success'])): ?>
        <div class="success-message">
            <?php 
                if($_GET['success'] == 'upload') echo 'Archivo subido exitosamente';
                if($_GET['success'] == 'delete') echo 'Archivo eliminado exitosamente';
                if($_GET['success'] == 'rename') echo 'Archivo renombrado exitosamente';
            ?>
        </div>
    <?php endif; ?>
    
    <?php if(isset($_GET['error'])): ?>
        <div class="error-message">
            <?php echo htmlspecialchars($_GET['error']); ?>
        </div>
    <?php endif; ?>
    
    <!-- Sección de Subir Archivo -->
    <div class="upload-section">
        <h2>Subir Nuevo Archivo</h2>
        <form action="subir_archivo.php" method="POST" enctype="multipart/form-data" id="uploadForm">
            <div class="file-input-wrapper">
                <label class="file-input-label">
                    Seleccionar Archivo
                    <input type="file" name="archivo" id="fileInput" required onchange="mostrarNombreArchivo()">
                </label>
            </div>
            <div class="file-name-display" id="fileName">Ningún archivo seleccionado</div>
            <br>
            <button type="submit" class="upload-btn" id="uploadBtn" disabled>Subir Archivo</button>
        </form>
    </div>
    
    <!-- Lista de Archivos -->
    <div class="container">
        <table>
            <thead>
                <tr>
                    <th>Archivo</th>
                    <th>Tipo</th>
                    <th>Tamaño</th>
                    <th>Fecha de Subida</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php while($archivo = $result->fetch_assoc()): ?>
                <tr>
                    <td>
                        <?php 
                            // Icono según tipo de archivo
                            $tipo = $archivo['tipo_mime'];
                            $icono = '';
                            if(strpos($tipo, 'pdf') !== false) $icono = '[PDF]';
                            elseif(strpos($tipo, 'image') !== false) $icono = '[IMG]';
                            elseif(strpos($tipo, 'video') !== false) $icono = '[VIDEO]';
                            elseif(strpos($tipo, 'audio') !== false) $icono = '[AUDIO]';
                            elseif(strpos($tipo, 'zip') !== false || strpos($tipo, 'rar') !== false) $icono = '[ZIP]';
                            elseif(strpos($tipo, 'word') !== false || strpos($tipo, 'document') !== false) $icono = '[DOC]';
                            elseif(strpos($tipo, 'excel') !== false || strpos($tipo, 'sheet') !== false) $icono = '[XLS]';
                            elseif(strpos($tipo, 'powerpoint') !== false || strpos($tipo, 'presentation') !== false) $icono = '[PPT]';
                            
                            echo $icono . ' ' . htmlspecialchars($archivo['nombre_archivo']); 
                        ?>
                    </td>
                    <td>
                        <?php 
                            // Mostrar tipo de archivo de forma amigable
                            $tipo = $archivo['tipo_mime'];
                            $tipo_display = '';
                            if(strpos($tipo, 'pdf') !== false) $tipo_display = 'PDF';
                            elseif(strpos($tipo, 'image/jpeg') !== false || strpos($tipo, 'image/jpg') !== false) $tipo_display = 'JPEG';
                            elseif(strpos($tipo, 'image/png') !== false) $tipo_display = 'PNG';
                            elseif(strpos($tipo, 'image/gif') !== false) $tipo_display = 'GIF';
                            elseif(strpos($tipo, 'image/') !== false) $tipo_display = 'Imagen';
                            elseif(strpos($tipo, 'video/mp4') !== false) $tipo_display = 'MP4';
                            elseif(strpos($tipo, 'video/') !== false) $tipo_display = 'Video';
                            elseif(strpos($tipo, 'audio/mpeg') !== false) $tipo_display = 'MP3';
                            elseif(strpos($tipo, 'audio/') !== false) $tipo_display = 'Audio';
                            elseif(strpos($tipo, 'zip') !== false) $tipo_display = 'ZIP';
                            elseif(strpos($tipo, 'rar') !== false) $tipo_display = 'RAR';
                            elseif(strpos($tipo, 'word') !== false || strpos($tipo, 'document') !== false) $tipo_display = 'Word';
                            elseif(strpos($tipo, 'excel') !== false || strpos($tipo, 'sheet') !== false) $tipo_display = 'Excel';
                            elseif(strpos($tipo, 'powerpoint') !== false || strpos($tipo, 'presentation') !== false) $tipo_display = 'PowerPoint';
                            elseif(strpos($tipo, 'text/plain') !== false) $tipo_display = 'Texto';
                            else $tipo_display = strtoupper(pathinfo($archivo['nombre_archivo'], PATHINFO_EXTENSION));
                            
                            echo htmlspecialchars($tipo_display); 
                        ?>
                    </td>
                    <td>
                        <?php 
                            $bytes = $archivo['tamanio_bytes'];
                            if($bytes < 1024) echo $bytes . ' B';
                            elseif($bytes < 1048576) echo round($bytes/1024, 2) . ' KB';
                            elseif($bytes < 1073741824) echo round($bytes/1048576, 2) . ' MB';
                            else echo round($bytes/1073741824, 2) . ' GB';
                        ?>
                    </td>
                    <td><?php echo date('d/m/Y H:i', strtotime($archivo['fecha_subida'])); ?></td>
                    <td>
                        <div class="file-actions">
                            <a href="ver_archivo.php?id=<?php echo $archivo['id']; ?>" class="btn-action btn-download">Ver</a>
                            <a href="#" onclick="renombrarArchivo(<?php echo $archivo['id']; ?>, '<?php echo htmlspecialchars($archivo['nombre_archivo']); ?>')" class="btn-action btn-rename">Renombrar</a>
                            <a href="eliminar_archivo.php?id=<?php echo $archivo['id']; ?>" onclick="return confirm('¿Estás seguro de eliminar este archivo?')" class="btn-action btn-delete">Eliminar</a>
                        </div>
                    </td>
                </tr>
                <?php endwhile; ?>
                <?php if($result->num_rows == 0): ?>
                <tr>
                    <td colspan="5" style="text-align:center; padding:40px; color:white;">
                        No tienes archivos aún. ¡Sube tu primer archivo!
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    
    <script>
        function mostrarNombreArchivo() {
            const input = document.getElementById('fileInput');
            const fileNameDisplay = document.getElementById('fileName');
            const uploadBtn = document.getElementById('uploadBtn');
            
            if(input.files.length > 0) {
                const file = input.files[0];
                const sizeMB = (file.size / 1048576).toFixed(2);
                fileNameDisplay.textContent = `📎 ${file.name} (${sizeMB} MB)`;
                uploadBtn.disabled = false;
            } else {
                fileNameDisplay.textContent = 'Ningún archivo seleccionado';
                uploadBtn.disabled = true;
            }
        }
        
        function renombrarArchivo(id, nombreActual) {
            const nuevoNombre = prompt('Nuevo nombre del archivo:', nombreActual);
            if(nuevoNombre && nuevoNombre.trim() !== '') {
                window.location.href = `renombrar_archivo.php?id=${id}&nombre=${encodeURIComponent(nuevoNombre.trim())}`;
            }
        }
    </script>
</body>
</html>
<?php $conn->close(); ?>
