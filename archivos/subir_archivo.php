<?php
require 'check_session.php';
require 'conexion.php';

$usuario_id = $_SESSION['user_id'];

if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES['archivo'])) {
    $archivo = $_FILES['archivo'];
    
    // Verificar que no haya errores en la subida
    if($archivo['error'] !== UPLOAD_ERR_OK) {
        header("Location: mis_archivos.php?error=Error al subir el archivo");
        exit();
    }
    
    // Crear carpeta de uploads si no existe
    $carpeta_uploads = "../uploads/";
    if(!file_exists($carpeta_uploads)) {
        mkdir($carpeta_uploads, 0755, true);
    }
    
    // Generar nombre único para el archivo
    $extension = pathinfo($archivo['name'], PATHINFO_EXTENSION);
    $nombre_unico = uniqid() . '_' . time() . '.' . $extension;
    $ruta_destino = $carpeta_uploads . $nombre_unico;
    
    // Mover archivo a la carpeta de uploads
    if(move_uploaded_file($archivo['tmp_name'], $ruta_destino)) {
        // Guardar información en la base de datos
        $nombre_original = $archivo['name'];
        $tipo_mime = $archivo['type'];
        $tamanio = $archivo['size'];
        
        $stmt = $conn->prepare("INSERT INTO archivos_usuario (usuario_id, nombre_archivo, nombre_original, ruta_archivo, tipo_mime, tamanio_bytes) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("issssi", $usuario_id, $nombre_original, $nombre_original, $ruta_destino, $tipo_mime, $tamanio);
        
        if($stmt->execute()) {
            header("Location: mis_archivos.php?success=upload");
        } else {
            // Si falla la BD, eliminar el archivo
            unlink($ruta_destino);
            header("Location: mis_archivos.php?error=Error al guardar en la base de datos");
        }
        $stmt->close();
    } else {
        header("Location: mis_archivos.php?error=Error al mover el archivo");
    }
} else {
    header("Location: mis_archivos.php");
}

$conn->close();
?>
