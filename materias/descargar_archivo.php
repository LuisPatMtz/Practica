<?php 
require 'check_session.php';
require 'conexion.php';

$archivo_id = $_GET['id'] ?? 0;

// Verificar que el archivo pertenece a una materia del usuario
$stmt = $conn->prepare("
    SELECT a.*, m.usuario_id 
    FROM archivos_materia a
    JOIN materias m ON a.materia_id = m.id
    WHERE a.id = ?
");
$stmt->bind_param("i", $archivo_id);
$stmt->execute();
$result = $stmt->get_result();
$archivo = $result->fetch_assoc();

if (!$archivo || $archivo['usuario_id'] != $_SESSION['user_id']) {
    die("Acceso denegado");
}

// Verificar que el archivo existe
if (!file_exists($archivo['ruta_archivo'])) {
    die("Archivo no encontrado");
}

// Enviar archivo para descarga
header('Content-Description: File Transfer');
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="' . basename($archivo['nombre_original']) . '"');
header('Content-Length: ' . filesize($archivo['ruta_archivo']));
header('Cache-Control: must-revalidate');
header('Pragma: public');

readfile($archivo['ruta_archivo']);
exit();
?>
