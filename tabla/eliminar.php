<?php
// Incluir archivo de conexión
include 'conexion.php';

// Verificar si la solicitud es POST y si se ha proporcionado la matrícula
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirmar']) && $_POST['confirmar'] === 'si') {
    $matricula = $_POST['matricula'];

    // Consulta para eliminar el registro
    $sql = "DELETE FROM registro_estudiantes WHERE matricula = '$matricula'";

    if ($conn->query($sql) === TRUE) {
        // Redirigir a resultado.php con éxito
        header("Location: resultado.php?status=success&action=eliminar");
        exit();
    } else {
        // Redirigir a resultado.php con error
        header("Location: resultado.php?status=eliminar");
        exit();
    }
} else {
    // Redirigir a resultado.php con error si no se confirmó la acción o faltan datos
    header("Location: resultado.php?status=error");
    exit();
}

// Cerrar la conexión a la base de datos
$conn->close();
?>
