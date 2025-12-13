<?php
require 'check_session.php';
require 'conexion.php';

$usuario_id = $_SESSION['user_id'];
$archivo_id = $_GET['id'] ?? 0;

// Obtener información del archivo
$stmt = $conn->prepare("SELECT ruta_archivo FROM archivos_usuario WHERE id = ? AND usuario_id = ?");
$stmt->bind_param("ii", $archivo_id, $usuario_id);
$stmt->execute();
$result = $stmt->get_result();
$archivo = $result->fetch_assoc();

if($archivo) {
    $ruta = $archivo['ruta_archivo'];
    
    // Eliminar archivo físico
    if(file_exists($ruta)) {
        unlink($ruta);
    }
    
    // Eliminar registro de la BD
    $stmt = $conn->prepare("DELETE FROM archivos_usuario WHERE id = ? AND usuario_id = ?");
    $stmt->bind_param("ii", $archivo_id, $usuario_id);
    $stmt->execute();
    
    header("Location: mis_archivos.php?success=delete");
} else {
    header("Location: mis_archivos.php?error=Archivo no encontrado");
}

$conn->close();
?>
