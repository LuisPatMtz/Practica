<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resultado del Registro</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/estilos/resultado.css"> 
</head>
<body>
    <div class="resultado-container">
        <?php
        // Verificar si se han proporcionado los parámetros 'status' y 'action'
        if (isset($_GET['status']) && isset($_GET['action'])) {
            $action = $_GET['action'];  // Puede ser 'modificar' o 'eliminar'

            if ($_GET['status'] == 'success') {
                if ($action == 'modificar') {
                    echo "<h1>Modificación exitosa</h1>";
                    echo "<p>El estudiante ha sido modificado correctamente.</p>";
                } elseif ($action == 'eliminar') {
                    echo "<h1>Eliminación exitosa</h1>";
                    echo "<p>El registro del estudiante ha sido eliminado correctamente.</p>";
                }
                echo '<img src="/img/success.gif" alt="Operación exitosa" class="resultado-img">';
            } elseif ($_GET['status'] == 'error') {
                if ($action == 'modificar') {
                    echo "<h1>Error en la modificación</h1>";
                    echo "<p>Hubo un problema al modificar el registro. Por favor, intenta nuevamente.</p>";
                } elseif ($action == 'eliminar') {
                    echo "<h1>Error en la eliminación</h1>";
                    echo "<p>Hubo un problema al eliminar el registro. Por favor, intenta nuevamente.</p>";
                }
            } else {
                echo "<h1>Error desconocido</h1>";
                echo "<p>Por favor, vuelve a intentar.</p>";
            }
        } else {
            echo "<h1>Error de acceso</h1>";
            echo "<p>No se pudo determinar el estado de la operación.</p>";
        }
        ?>
        <br>
        <input type="button" value="Regresar a la tabla" onclick="window.location.href='/tabla/tabla.html'">
    </div>
</body>
</html>
