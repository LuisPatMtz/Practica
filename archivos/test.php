<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "Probando conexión...<br>";

require 'check_session.php';
echo "Session check OK<br>";

require 'conexion.php';
echo "Conexión OK<br>";

$usuario_id = $_SESSION['user_id'];
echo "Usuario ID: " . $usuario_id . "<br>";

$stmt = $conn->prepare("SELECT COUNT(*) as total FROM archivos_usuario WHERE usuario_id = ?");
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

echo "Total de archivos: " . $row['total'] . "<br>";
echo "Todo funciona correctamente!";

$conn->close();
?>
