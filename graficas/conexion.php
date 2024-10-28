<?php
$servername = "localhost";
$username = "root"; 
$password = "ac2c5f034b"; 
$dbname = "registro_estudiantes"; 

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}
?>