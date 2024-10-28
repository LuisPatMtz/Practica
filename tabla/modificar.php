<?php
// Incluir archivo de conexión
include 'conexion.php';

// Verificar si la solicitud es POST (para modificar los datos)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $matricula = $_POST['matricula'];
    $nombre = $_POST['nombre'];
    $grupo = $_POST['grupo'];
    $telefono = $_POST['telefono'];
    $correo = $_POST['correo'];
    $fecha_nacimiento = $_POST['fecha_nacimiento'];
    $genero = $_POST['genero'];

    // Actualizar los datos en la base de datos
    $sql = "UPDATE registro_estudiantes 
            SET nombre = '$nombre', grupo = '$grupo', telefono = '$telefono', correo_electronico = '$correo', fecha_nacimiento = '$fecha_nacimiento', genero = '$genero'
            WHERE matricula = '$matricula'";

    if ($conn->query($sql) === TRUE) {
    
        if ($conn->query($sql) === TRUE) {
            header("Location: resultado.php?status=success&action=modificar"); // Hubo exito :]
            exit(); 
        } else {
            header("Location: resultado.php?status=error&action=modificar"); // Error :c
            exit();
        }
    } else {
        echo "Error actualizando el registro: " . $conn->error;
    }

    // Cerrar la conexión
    $conn->close();
}
?>
