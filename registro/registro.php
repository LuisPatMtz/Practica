<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require 'conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $_POST['nombre'] ?? '';
    $matricula = $_POST['matricula'] ?? '';
    $grupo = $_POST['grupo'] ?? '';
    $telefono = $_POST['telefono'] ?? '';
    $correo = $_POST['correo_electronico'] ?? '';
    $fecha_nacimiento = $_POST['fecha_nacimiento'] ?? '';
    $genero = $_POST['genero'] ?? '';

    // Validar matrícula
    if (preg_match("/^\d{9}$/", $matricula)) {
        // Usar prepared statements para evitar inyección SQL
        $stmt = $conn->prepare("INSERT INTO registro_estudiantes (nombre, matricula, grupo, telefono, correo_electronico, fecha_nacimiento, genero) VALUES (?, ?, ?, ?, ?, ?, ?)");
        
        if ($stmt) {
            $stmt->bind_param("sssssss", $nombre, $matricula, $grupo, $telefono, $correo, $fecha_nacimiento, $genero);
            
            if ($stmt->execute()) {
                header("Location: resultado.php?status=success");
                exit();
            } else {
                echo "Error al insertar: " . $stmt->error;
                header("Location: resultado.php?status=error");
                exit();
            }
            
            $stmt->close();
        } else {
            echo "Error en la preparación: " . $conn->error;
            header("Location: resultado.php?status=error");
            exit();
        }
    } else {
        header("Location: resultado.php?status=error&msg=matricula_invalida");
        exit();
    }

    $conn->close();
}
?>
