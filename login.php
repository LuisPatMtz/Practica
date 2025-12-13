<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    // Conectar a la base de datos
    $servername = "localhost";
    $db_username = "captus";
    $db_password = "captus";
    $dbname = "cactus";
    
    $conn = new mysqli($servername, $db_username, $db_password, $dbname);
    
    if ($conn->connect_error) {
        die("Conexión fallida: " . $conn->connect_error);
    }
    
    // Buscar usuario en la base de datos
    $stmt = $conn->prepare("SELECT id, username, password FROM usuarios WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        
        // Verificar contraseña (sin encriptar)
        if ($password === $user['password']) {
            // Login exitoso
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['logged_in'] = true;
            
            header("Location: index.php");
            exit();
        }
    }
    
    // Login fallido
    $stmt->close();
    $conn->close();
    header("Location: login.html?error=1");
    exit();
}
?>
