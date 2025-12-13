<?php 
require 'check_session.php';
require 'conexion.php';

$archivo_id = $_GET['id'] ?? 0;
$materia_id = $_GET['materia_id'] ?? 0;

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

// Eliminar archivo físico
if (file_exists($archivo['ruta_archivo'])) {
    unlink($archivo['ruta_archivo']);
}

// Eliminar registro de base de datos
$stmt = $conn->prepare("DELETE FROM archivos_materia WHERE id = ?");
$stmt->bind_param("i", $archivo_id);
$stmt->execute();

header("Location: contenido_materia.php?materia_id=" . $materia_id . "&deleted=1");
exit();
?>
