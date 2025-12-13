<?php
require 'check_session.php';
require 'conexion.php';

$usuario_id = $_SESSION['user_id'];
$archivo_id = $_GET['id'] ?? 0;

// Obtener información del archivo
$stmt = $conn->prepare("SELECT nombre_archivo, ruta_archivo, tipo_mime, tamanio_bytes FROM archivos_usuario WHERE id = ? AND usuario_id = ?");
$stmt->bind_param("ii", $archivo_id, $usuario_id);
$stmt->execute();
$result = $stmt->get_result();
$archivo = $result->fetch_assoc();

if (!$archivo) {
    header("Location: mis_archivos.php?error=Archivo no encontrado");
    exit();
}

// Determinar si el archivo es visualizable
$es_pdf = strpos($archivo['tipo_mime'], 'pdf') !== false;
$es_imagen = strpos($archivo['tipo_mime'], 'image') !== false;
$es_video = strpos($archivo['tipo_mime'], 'video') !== false;
$es_audio = strpos($archivo['tipo_mime'], 'audio') !== false;
$es_texto = strpos($archivo['tipo_mime'], 'text') !== false;

$es_visualizable = $es_pdf || $es_imagen || $es_video || $es_audio || $es_texto;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($archivo['nombre_archivo']); ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        .viewer-container {
            max-width: 1400px;
            margin: 0 auto;
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        }
        .viewer-header {
            background: rgba(0,0,0,0.95);
            padding: 20px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
        }
        .file-info {
            color: white;
        }
        .file-info h1 {
            font-size: 1.3rem;
            margin-bottom: 5px;
        }
        .file-info .meta {
            font-size: 0.85rem;
            color: #aaa;
        }
        .viewer-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }
        .btn {
            padding: 10px 20px;
            border-radius: 25px;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.9rem;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        .btn-download {
            background: #4CAF50;
            color: white;
        }
        .btn-download:hover {
            background: #45a049;
            transform: translateY(-2px);
        }
        .btn-back {
            background: #666;
            color: white;
        }
        .btn-back:hover {
            background: #555;
            transform: translateY(-2px);
        }
        .viewer-content {
            padding: 0;
            min-height: 70vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f5f5f5;
        }
        .pdf-viewer, .image-viewer, .video-viewer, .audio-viewer {
            width: 100%;
            height: 80vh;
            border: none;
        }
        .image-viewer {
            object-fit: contain;
            max-width: 100%;
            max-height: 80vh;
            display: block;
            margin: 0 auto;
            background: #000;
        }
        .video-viewer, .audio-viewer {
            display: block;
            margin: 20px auto;
            max-width: 100%;
        }
        .no-preview {
            text-align: center;
            padding: 60px 20px;
            color: #666;
        }
        .no-preview .icon {
            font-size: 5rem;
            margin-bottom: 20px;
        }
        .no-preview h2 {
            margin-bottom: 15px;
            color: #333;
        }
        .no-preview p {
            margin-bottom: 25px;
        }
        @media (max-width: 768px) {
            .viewer-header {
                flex-direction: column;
                align-items: flex-start;
            }
            .file-info h1 {
                font-size: 1.1rem;
            }
            .viewer-actions {
                width: 100%;
                justify-content: flex-start;
            }
        }
    </style>
</head>
<body>
    <div class="viewer-container">
        <div class="viewer-header">
            <div class="file-info">
                <h1><?php echo htmlspecialchars($archivo['nombre_archivo']); ?></h1>
                <div class="meta">
                    <?php echo htmlspecialchars($archivo['tipo_mime']); ?> • 
                    <?php 
                        $bytes = $archivo['tamanio_bytes'];
                        if($bytes < 1024) echo $bytes . ' B';
                        elseif($bytes < 1048576) echo round($bytes/1024, 2) . ' KB';
                        elseif($bytes < 1073741824) echo round($bytes/1048576, 2) . ' MB';
                        else echo round($bytes/1073741824, 2) . ' GB';
                    ?>
                </div>
            </div>
            <div class="viewer-actions">
                <a href="descargar_archivo.php?id=<?php echo $archivo_id; ?>" class="btn btn-download">
                    Descargar
                </a>
                <a href="mis_archivos.php" class="btn btn-back">
                    Volver
                </a>
            </div>
        </div>
        
        <div class="viewer-content">
            <?php if($es_pdf): ?>
                <iframe src="<?php echo $archivo['ruta_archivo']; ?>" class="pdf-viewer"></iframe>
            <?php elseif($es_imagen): ?>
                <img src="<?php echo $archivo['ruta_archivo']; ?>" alt="<?php echo htmlspecialchars($archivo['nombre_archivo']); ?>" class="image-viewer">
            <?php elseif($es_video): ?>
                <video controls class="video-viewer">
                    <source src="<?php echo $archivo['ruta_archivo']; ?>" type="<?php echo $archivo['tipo_mime']; ?>">
                    Tu navegador no soporta la reproducción de video.
                </video>
            <?php elseif($es_audio): ?>
                <audio controls class="audio-viewer">
                    <source src="<?php echo $archivo['ruta_archivo']; ?>" type="<?php echo $archivo['tipo_mime']; ?>">
                    Tu navegador no soporta la reproducción de audio.
                </audio>
            <?php else: ?>
                <div class="no-preview">
                    <div class="icon">[ARCHIVO]</div>
                    <h2>Vista previa no disponible</h2>
                    <p>Este tipo de archivo no se puede visualizar en el navegador.</p>
                    <a href="descargar_archivo.php?id=<?php echo $archivo_id; ?>" class="btn btn-download">
                        Descargar Archivo
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
<?php $conn->close(); ?>
