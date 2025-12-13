<?php
require 'conexion.php'; 

$sql = "SELECT MONTH(fecha_nacimiento) as mes, COUNT(*) as cantidad 
        FROM registro_estudiantes 
        GROUP BY MONTH(fecha_nacimiento) 
        ORDER BY mes";
$result = $conn->query($sql);

$meses = [
    1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
    5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
    9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
];

$labels = [];
$data = [];

// Inicializar todos los meses con 0
for ($i = 1; $i <= 12; $i++) {
    $labels[] = $meses[$i];
    $data[] = 0;
}

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $mesNumero = (int)$row['mes'];
        if ($mesNumero >= 1 && $mesNumero <= 12) {
            $data[$mesNumero - 1] = (int)$row['cantidad'];
        }
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
