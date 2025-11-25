<?php
$servername = "localhost";
$username = "root"; 
$password = "ac5f0sad3s4b"; 
$dbname = "registro_estudiantes"; 

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}
?>
