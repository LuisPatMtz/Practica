<?php
require 'conexion.php'; 

$sql = "SELECT grupo, COUNT(*) as cantidad FROM registro_estudiantes GROUP BY grupo";
$result = $conn->query($sql);

$labels = [];
$data = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $labels[] = $row['grupo'];
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
