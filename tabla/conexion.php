<?php
$servername = "localhost";
$username = "captus"; 
$password = "captus"; 
$dbname = "cactus"; 

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}
?>
