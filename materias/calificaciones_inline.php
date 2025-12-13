<?php 
require 'check_session.php';
require 'conexion.php';

$mg_id = $_GET['mg_id'] ?? 0;

// Obtener información del grupo y materia
$stmt = $conn->prepare("
    SELECT mg.grupo, mg.num_unidades, m.nombre_materia, mg.materia_id
    FROM materia_grupos mg
    JOIN materias m ON mg.materia_id = m.id
    WHERE mg.id = ? AND m.usuario_id = ?
");
$stmt->bind_param("ii", $mg_id, $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
$info = $result->fetch_assoc();

if (!$info) {
    header("Location: mis_materias.php");
    exit();
}

// Obtener alumnos del grupo
$stmt = $conn->prepare("SELECT matricula, nombre FROM registro_estudiantes WHERE grupo = ? ORDER BY nombre");
$stmt->bind_param("s", $info['grupo']);
$stmt->execute();
$alumnos = $stmt->get_result();

// Obtener todas las calificaciones de este grupo
$stmt = $conn->prepare("SELECT matricula, unidad, calificacion FROM calificaciones WHERE materia_grupo_id = ?");
$stmt->bind_param("i", $mg_id);
$stmt->execute();
$califs_result = $stmt->get_result();

$calificaciones = [];
while($c = $califs_result->fetch_assoc()) {
    $calificaciones[$c['matricula']][$c['unidad']] = $c['calificacion'];
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calificaciones</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../estilos/tabla.css">
    <style>
        .page-header {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            background: rgba(0, 0, 0, 0.95);
            padding: 12px 20px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
            z-index: 1000;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .page-header nav {
            display: flex;
            gap: 15px;
            align-items: center;
            flex-wrap: wrap;
            justify-content: center;
        }
        .page-header nav a {
            color: white;
            text-decoration: none;
            padding: 8px 16px;
            border-radius: 20px;
            transition: all 0.3s ease;
            font-size: 0.9rem;
            white-space: nowrap;
        }
        .page-header nav a:hover { background: rgba(76, 175, 80, 0.2); }
        .page-header nav a.btn-home {
            background: #4CAF50;
            font-weight: 500;
        }
        .page-header nav a.btn-home:hover { background: #45a049; }
        body { padding-top: 70px !important; }
        @media (max-width: 768px) {
            .page-header nav { gap: 8px; }
            .page-header nav a { padding: 6px 12px; font-size: 0.85rem; }
        }
        .calif-cell {
            cursor: pointer;
            padding: 8px;
            transition: background-color 0.3s;
            text-align: center;
        }
        .calif-cell:hover {
            background-color: rgba(76, 175, 80, 0.2);
        }
        .calif-na {
            color: #ff5252;
            font-weight: 500;
        }
        .calif-input {
            width: 80px;
            padding: 6px;
            border: 2px solid #4CAF50;
            border-radius: 5px;
            text-align: center;
            font-size: 1rem;
            background: white;
            color: #333;
        }
        #mensaje {
            display: none;
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 15px 25px;
            background: rgba(76, 175, 80, 0.9);
            color: white;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.3);
            z-index: 1000;
        }
    </style>
</head>
<body>
    <div id="mensaje"></div>
    
    <h1><?php echo htmlspecialchars($info['nombre_materia']); ?> - <?php echo htmlspecialchars($info['grupo']); ?></h1>
    
    <div class="container">
        <table>
            <thead>
                <tr>
                    <th>Matrícula</th>
                    <th>Nombre</th>
                    <?php for($i = 1; $i <= $info['num_unidades']; $i++): ?>
                        <th>U<?php echo $i; ?></th>
                    <?php endfor; ?>
                </tr>
            </thead>
            <tbody>
                <?php while($alumno = $alumnos->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($alumno['matricula']); ?></td>
                    <td><?php echo htmlspecialchars($alumno['nombre']); ?></td>
                    <?php for($unidad = 1; $unidad <= $info['num_unidades']; $unidad++): 
                        $calif = $calificaciones[$alumno['matricula']][$unidad] ?? null;
                    ?>
                        <td class="calif-cell" 
                            data-matricula="<?php echo $alumno['matricula']; ?>" 
                            data-unidad="<?php echo $unidad; ?>"
                            data-calif="<?php echo $calif ?? ''; ?>"
                            ondblclick="editarCalificacion(this)">
                            <span class="calif-display <?php echo $calif === null ? 'calif-na' : ''; ?>">
                                <?php echo $calif !== null ? number_format($calif, 1) : 'NA'; ?>
                            </span>
                        </td>
                    <?php endfor; ?>
                </tr>
                <?php endwhile; ?>
                <?php if($alumnos->num_rows == 0): ?>
                <tr>
                    <td colspan="<?php echo 2 + $info['num_unidades']; ?>" style="text-align:center;">No hay alumnos en este grupo</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
        <br>
        <center>
            <a href="ver_grupos.php?materia_id=<?php echo $info['materia_id']; ?>" style="color:white; background:#4CAF50; padding:12px 30px; border-radius:20px; text-decoration:none; display:inline-block;">← Volver a Grupos</a>
        </center>
    </div>

    <script>
        let editandoCelda = null;

        function editarCalificacion(celda) {
            if (editandoCelda) return;
            
            editandoCelda = celda;
            const califActual = celda.dataset.calif;
            const display = celda.querySelector('.calif-display');
            
            const input = document.createElement('input');
            input.type = 'number';
            input.className = 'calif-input';
            input.value = califActual;
            input.min = '0';
            input.max = '100';
            input.step = '0.1';
            
            celda.innerHTML = '';
            celda.appendChild(input);
            input.focus();
            input.select();
            
            input.onblur = () => guardarCalificacion(celda, input.value);
            input.onkeydown = (e) => {
                if (e.key === 'Enter') {
                    guardarCalificacion(celda, input.value);
                } else if (e.key === 'Escape') {
                    cancelarEdicion(celda, califActual);
                }
            };
        }

        function cancelarEdicion(celda, valorAnterior) {
            const span = document.createElement('span');
            span.className = 'calif-display' + (valorAnterior ? '' : ' calif-na');
            span.textContent = valorAnterior ? parseFloat(valorAnterior).toFixed(1) : 'NA';
            celda.innerHTML = '';
            celda.appendChild(span);
            editandoCelda = null;
        }

        function guardarCalificacion(celda, valor) {
            const matricula = celda.dataset.matricula;
            const unidad = celda.dataset.unidad;
            const mgId = <?php echo $mg_id; ?>;
            
            if (valor === '' || valor === null) {
                cancelarEdicion(celda, celda.dataset.calif);
                return;
            }
            
            const formData = new FormData();
            formData.append('mg_id', mgId);
            formData.append('matricula', matricula);
            formData.append('unidad', unidad);
            formData.append('calificacion', valor);
            
            fetch('guardar_calificacion.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    celda.dataset.calif = valor;
                    const span = document.createElement('span');
                    span.className = 'calif-display';
                    span.textContent = parseFloat(valor).toFixed(1);
                    celda.innerHTML = '';
                    celda.appendChild(span);
                    
                    mostrarMensaje('Calificación guardada exitosamente');
                } else {
                    alert('Error al guardar: ' + data.message);
                    cancelarEdicion(celda, celda.dataset.calif);
                }
                editandoCelda = null;
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al guardar la calificación');
                cancelarEdicion(celda, celda.dataset.calif);
                editandoCelda = null;
            });
        }

        function mostrarMensaje(texto) {
            const mensaje = document.getElementById('mensaje');
            mensaje.textContent = texto;
            mensaje.style.display = 'block';
            setTimeout(() => {
                mensaje.style.display = 'none';
            }, 3000);
        }
    </script>
</body>
</html>
<?php $conn->close(); ?>
