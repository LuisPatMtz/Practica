<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Incluir archivo de conexión
include 'conexion.php';

// Verifica si se ha pasado la matrícula como parámetro GET
if (isset($_GET['matricula'])) {
    $matricula = $_GET['matricula'];

    // Consulta para obtener los datos del estudiante basados en la matrícula
    $sql = "SELECT matricula, nombre, grupo, telefono, correo_electronico, fecha_nacimiento, genero FROM registro_estudiantes WHERE matricula = '$matricula'";
    $result = $conn->query($sql);

    // Si se encuentra un registro con la matrícula proporcionada
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        // Verifica si las claves existen en el resultado y asigna los valores correspondientes
        $nombre = isset($row['nombre']) ? $row['nombre'] : null;
        $grupo = isset($row['grupo']) ? $row['grupo'] : null;
        $telefono = isset($row['telefono']) ? $row['telefono'] : null;
        $correo = isset($row['correo_electronico']) ? $row['correo_electronico'] : null;
        $fecha_nacimiento = isset($row['fecha_nacimiento']) ? $row['fecha_nacimiento'] : null;
        $genero = isset($row['genero']) ? $row['genero'] : null;

        // Devolver los datos en formato JSON para que puedan ser usados en la página de modificación
        echo json_encode([
            'success' => true,
            'matricula' => $row['matricula'],
            'nombre' => $nombre,
            'grupo' => $grupo,
            'telefono' => $telefono,
            'correo' => $correo,
            'fecha_nacimiento' => $fecha_nacimiento,
            'genero' => $genero
        ]);
    } else {
        // Si no se encuentra el estudiante con esa matrícula
        echo json_encode(['success' => false, 'message' => 'Estudiante no encontrado.']);
    }
} else {
    // Si no se ha proporcionado una matrícula en el parámetro GET
    echo json_encode(['success' => false, 'message' => 'No se proporcionó la matrícula.']);
}

// Cierra la conexión a la base de datos
$conn->close();
?>