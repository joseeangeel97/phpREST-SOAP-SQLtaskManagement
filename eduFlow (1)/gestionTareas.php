<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/gestionTareas.css">
    <title>Gestion Tareas</title>
</head>
<body>
    <?php
    error_reporting(E_ALL);
ini_set('display_errors', 1);
    
include "cabecera.html";
require_once __DIR__ . '/db/crud.php';

// Formateador de fecha en español
$fmt = new IntlDateFormatter('es_ES', IntlDateFormatter::FULL, IntlDateFormatter::SHORT);
$today = ucfirst($fmt->format(new DateTime()));
?>
<div id="saludo">
<!-- Muestra la hora de inicio de sesión y el nombre del usuario -->
        <p id="hora">Inicio de sesión: <?php echo $today; ?></p>
        <p>Bienvenido a eduFlow <?php echo htmlspecialchars($_SESSION['user']); ?></p>
           <a href="/eduFlow/profesor.php"> <button class="boton-saludo">Volver</button></a>
</div>

<?php
//Consulta a realizar para que solo aparezca las tareas del profesor en activo
 $sql = "SELECT  id_tarea, nombre_tarea, asignatura, nombre_alumno,fecha_entrega,archivo_entrega,mensaje_profesor
            FROM tareas
            WHERE nombre_profesor = ?";
$resultado=Crud::getData($sql,htmlspecialchars($_SESSION['user']));
//Lectura del array tras la query e impresion dinamica en pantalla de los datos
 if ($resultado && count($resultado) > 0) {
    foreach ($resultado as $fila) {
        echo "
        <section id='encuadre'>
            <section id='bloque'>
                
                    <p>
                        Tarea: " . htmlspecialchars($fila['nombre_tarea'] ?? "nada que ver aquí", ENT_QUOTES) . "<br>
                        Asignatura: " . htmlspecialchars($fila['asignatura'] ?? "nada que ver aquí", ENT_QUOTES) . "<br>
                        Alumno: " . htmlspecialchars($fila['nombre_alumno'] ?? "nada que ver aquí", ENT_QUOTES) . "<br>
                        Entrega: " . htmlspecialchars($fila['fecha_entrega'] ?? "nada que ver aquí", ENT_QUOTES) . "<br>
                        Archivo: " . htmlspecialchars($fila['archivo_entrega'] ?? "nada que ver aquí", ENT_QUOTES) . "<br>
                        Mensaje: " . htmlspecialchars($fila['mensaje_profesor'] ?? "nada que ver aquí", ENT_QUOTES) . "<br>
                    </p>
                    <form method='POST'>
                            <input type='hidden' name='id_tarea' value='" . htmlspecialchars($fila['id_tarea'], ENT_QUOTES) . "'>
                            <input type='text' name='comentario' value='' placeholder='Hacer observacion'>
                            <button type='submit' name='gestionTarea' value='1'>Comentar</button>
                        </form>
                
            </section>
        </section>
        ";
    }
} else {
    echo "<p>No hay tareas registradas.</p>";
}

//Manejo del formulario para subir el comentario a la base de datos
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['gestionTarea'])) {
    include_once __DIR__ . "/db/tareas.php";
    //Bloque para manejo de errores
    try {
        $comentario = trim($_POST['comentario']); // trim elimina espacios en blanco al principio y final de la cadena
        $idTarea = (int) $_POST['id_tarea']; // id de la tarea a actualizar

        $resultado = Tareas::uploadComment($comentario, $idTarea);

        
    } catch (Exception $e) {
        $mensaje_error = "Error: " . $e->getMessage();
    }
}




 Database::closeConnection(); ?>
</body>
</html>