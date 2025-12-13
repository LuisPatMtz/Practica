<?php
require 'check_session.php';
require 'conexion.php';

$usuario_id = $_SESSION['user_id'];
$archivo_id = $_GET['id'] ?? 0;
$nuevo_nombre = $_GET['nombre'] ?? '';

if(!empty($nuevo_nombre)) {
    $stmt = $conn->prepare("UPDATE archivos_usuario SET nombre_archivo = ? WHERE id = ? AND usuario_id = ?");
    $stmt->bind_param("sii", $nuevo_nombre, $archivo_id, $usuario_id);
    
    if($stmt->execute()) {
        header("Location: mis_archivos.php?success=rename");
    } else {
        header("Location: mis_archivos.php?error=Error al renombrar");
    }
    $stmt->close();
} else {
    header("Location: mis_archivos.php?error=Nombre inválido");
}

$conn->close();
?>
