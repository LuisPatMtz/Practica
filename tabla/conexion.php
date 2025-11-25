<?php
$servername = "localhost";
$username = "root"; 
$password = "ac2cdsadf03s4b"; 
$dbname = "registro_estudiantes"; 

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}
?>
