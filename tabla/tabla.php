<?php
require 'conexion.php'; 

$sql = "SELECT matricula, nombre, grupo, telefono, correo_electronico FROM registro_estudiantes";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['matricula']) . "</td>";
        echo "<td>" . htmlspecialchars($row['nombre']) . "</td>";
        echo "<td>" . htmlspecialchars($row['grupo']) . "</td>";
        echo "<td>" . htmlspecialchars($row['telefono']) . "</td>";
        echo "<td>" . htmlspecialchars($row['correo_electronico']) . "</td>";
        echo "<td class='table-actions'>
                <a href='modificar.html?matricula=" . urlencode($row['matricula']) . "' class='btn'>Modificar</a>
                <a href='eliminar.html?matricula=" . urlencode($row['matricula']) . "' class='btn'>Eliminar</a>
              </td>";
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='6'>No hay registros</td></tr>";
}

$conn->close();
?>
