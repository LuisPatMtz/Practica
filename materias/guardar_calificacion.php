<?php
session_start();
require 'conexion.php';

header('Content-Type: application/json');

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit();
}

$mg_id = $_POST['mg_id'] ?? 0;
$matricula = $_POST['matricula'] ?? '';
$unidad = $_POST['unidad'] ?? 0;
$calificacion = $_POST['calificacion'] ?? null;

if (empty($mg_id) || empty($matricula) || empty($unidad) || $calificacion === null) {
    echo json_encode(['success' => false, 'message' => 'Datos incompletos']);
    exit();
}

// Verificar que el usuario sea dueño de esta materia
$stmt = $conn->prepare("
    SELECT mg.id 
    FROM materia_grupos mg
    JOIN materias m ON mg.materia_id = m.id
    WHERE mg.id = ? AND m.usuario_id = ?
");
$stmt->bind_param("ii", $mg_id, $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit();
}

// Verificar si ya existe la calificación
$stmt = $conn->prepare("SELECT id FROM calificaciones WHERE materia_grupo_id = ? AND matricula = ? AND unidad = ?");
$stmt->bind_param("isi", $mg_id, $matricula, $unidad);
$stmt->execute();
$existe = $stmt->get_result()->fetch_assoc();

if ($existe) {
    // Actualizar
    $stmt = $conn->prepare("UPDATE calificaciones SET calificacion = ? WHERE id = ?");
    $stmt->bind_param("di", $calificacion, $existe['id']);
    $success = $stmt->execute();
} else {
    // Insertar
    $stmt = $conn->prepare("INSERT INTO calificaciones (materia_grupo_id, matricula, unidad, calificacion) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isid", $mg_id, $matricula, $unidad, $calificacion);
    $success = $stmt->execute();
}

if ($success) {
    echo json_encode(['success' => true, 'message' => 'Calificación guardada']);
} else {
    echo json_encode(['success' => false, 'message' => 'Error al guardar']);
}

$stmt->close();
$conn->close();
?>
