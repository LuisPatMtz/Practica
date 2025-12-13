<?php
require 'check_session.php';
require 'conexion.php';

$usuario_id = $_SESSION['user_id'];
$archivo_id = $_GET['id'] ?? 0;

// Obtener información del archivo
$stmt = $conn->prepare("SELECT nombre_archivo, ruta_archivo, tipo_mime FROM archivos_usuario WHERE id = ? AND usuario_id = ?");
$stmt->bind_param("ii", $archivo_id, $usuario_id);
$stmt->execute();
$result = $stmt->get_result();
$archivo = $result->fetch_assoc();

if($archivo && file_exists($archivo['ruta_archivo'])) {
    // Headers para descarga
    header('Content-Description: File Transfer');
    header('Content-Type: ' . $archivo['tipo_mime']);
    header('Content-Disposition: attachment; filename="' . $archivo['nombre_archivo'] . '"');
    header('Content-Length: ' . filesize($archivo['ruta_archivo']));
    header('Pragma: public');
    
    // Limpiar buffer y enviar archivo
    ob_clean();
    flush();
    readfile($archivo['ruta_archivo']);
    exit();
} else {
    header("Location: mis_archivos.php?error=Archivo no encontrado");
}

$conn->close();
?>
