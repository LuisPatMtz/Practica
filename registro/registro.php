<?php
require 'conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $_POST['nombre'];
    $matricula = $_POST['matricula'];
    $grupo = $_POST['grupo'];
    $telefono = $_POST['telefono'];
    $correo = $_POST['correo_electronico'];
    $fecha_nacimiento = $_POST['fecha_nacimiento'];
    $genero = $_POST['genero'];

    $sql = "INSERT INTO registro_estudiantes (nombre, matricula, grupo, telefono, correo_electronico, fecha_nacimiento, genero) 
            VALUES ('$nombre', '$matricula', '$grupo', '$telefono', '$correo', '$fecha_nacimiento', '$genero')";

    if (preg_match("/^\d{9}$/", $matricula)) {
        $sql = "INSERT INTO registro_estudiantes (nombre, matricula, grupo, telefono, correo_electronico, fecha_nacimiento, genero) 
                VALUES ('$nombre', '$matricula', '$grupo', '$telefono', '$correo', '$fecha_nacimiento', '$genero')";

        if ($conn->query($sql) === TRUE) {
            header("Location: resultado.php?status=success"); // Hubo exito :]
            exit(); 
        } else {
            header("Location: resultado.php?status=error"); // Error :c
            exit();
        }
    } else {
        header("Location: resultado.php?status=error"); 
        exit();
    }

    $conn->close();
}
?>
