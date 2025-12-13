<?php
require 'conexion.php'; 

$sql = "SELECT genero, COUNT(*) as cantidad FROM registro_estudiantes GROUP BY genero";
$result = $conn->query($sql);

$labels = [];
$data = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $genero = ucfirst($row['genero']);
        $labels[] = $genero;
        $data[] = (int)$row['cantidad'];
    }
}

$response = [
    'labels' => $labels,
    'data' => $data,
];

header('Content-Type: application/json');
echo json_encode($response);

$conn->close();
?>
