<?php
// Script para crear la tabla de archivos
$servername = "localhost";
$username = "captus"; 
$password = "captus"; 
$dbname = "cactus"; 

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

$sql = "CREATE TABLE IF NOT EXISTS archivos_materia (
    id INT AUTO_INCREMENT PRIMARY KEY,
    materia_id INT NOT NULL,
    nombre_archivo VARCHAR(255) NOT NULL,
    nombre_original VARCHAR(255) NOT NULL,
    ruta_archivo VARCHAR(500) NOT NULL,
    tipo_archivo VARCHAR(100),
    tamano_bytes INT,
    descripcion TEXT,
    fecha_subida TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (materia_id) REFERENCES materias(id) ON DELETE CASCADE,
    INDEX idx_materia (materia_id)
)";

if ($conn->query($sql) === TRUE) {
    echo "Tabla 'archivos_materia' creada exitosamente o ya existe<br>";
} else {
    echo "Error al crear tabla: " . $conn->error . "<br>";
}

$conn->close();
echo "<br><a href='mis_materias.php'>← Volver a Mis Materias</a>";
?>
