<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resultado del Registro</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../estilos/resultado.css"> 
</head>
<body>
    <div class="resultado-container">
        <?php
        if (isset($_GET['status'])) {
            if ($_GET['status'] == 'success') {
                echo "<h1>Registro exitoso</h1>";
                echo "<p>El estudiante ha sido registrado correctamente.</p>";
                echo '<img src="../img/success.gif" alt="Registro exitoso" class="resultado-img">';
            } elseif ($_GET['status'] == 'error') {
                echo "<h1>Error en el registro</h1>";
                echo "<p>Hubo un problema al registrar al estudiante. Por favor, intenta nuevamente.</p>";
            } else {
                echo "<h1>Error desconocido</h1>";
                echo "<p>Por favor, vuelve a intentar.</p>";
            }
        } else {
            echo "<h1>Error de acceso</h1>";
            echo "<p>No se pudo determinar el estado del registro.</p>";
        }
        ?>
        <br>
        <input type="button" value="Regresar a la tabla" onclick="window.location.href='../index.html'">
    </div>
</body>
</html>