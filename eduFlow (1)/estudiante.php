<?php 
session_start(); 
include_once __DIR__ . "/db/database.php";
//Mostrar posibles errores
error_reporting(E_ALL);
ini_set('display_errors', 1);
// Bloquea acceso si no hay login válido
if (
    !isset($_SESSION['logueado']) ||
    $_SESSION['logueado'] !== true ||
    $_SESSION['perfil'] !== 'estudiante'
) {
    header("Location: iniciarSesion.php");
    exit;
}

// Caduca la sesión a los 30 minutos
if (isset($_SESSION['login_time']) && (time() - $_SESSION['login_time'] > 1800)) {
    session_unset();
    session_destroy();
    header("Location: iniciarSesion.php?expirada=1");
    exit;
}

// Procesar entrega de tarea
$mensaje_exito = null;
$mensaje_error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['entregar_tarea'])) {
    include_once __DIR__ . "/db/tareas.php";
    // manejo de errores
    try {
        //Prueba primero a realizar la funcion
        $resultado = Tareas::uploadTask(
            $_POST['titulo'],
            $_POST['asignatura'],
            $_POST['profesor'],
            $_SESSION['user'],
            $_POST['imagen']
        );
        
        if ($resultado) {
            $mensaje_exito = "Tarea entregada correctamente";
        } else {
            $mensaje_error = "Error al entregar la tarea";
        }
    } catch (Exception $e) {
        //Que no funciona el try se acciona el catch
        $mensaje_error = "Error: " . $e->getMessage();
    }
}

$fmt = new IntlDateFormatter('es_ES', IntlDateFormatter::FULL, IntlDateFormatter::SHORT);
$today = ucfirst($fmt->format(new DateTime()));
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Estudiante</title>
    <link rel="stylesheet" href="css/estudiante.css">
</head>
<body>
    <?php include "cabecera.html"; ?>

    <div id="saludo">
        <p id="hora">Inicio de sesión: <?php echo $today; ?></p>
        <p>Bienvenido a eduFlow <?php echo htmlspecialchars($_SESSION['user']); ?></p>
        
    </div>  

    <?php 
    //Mensaje a mostrar si hay un error o no
    if ($mensaje_exito !== null) {
        echo "<p style='
        font-family: Arial, sans-serif;color: crimson; text-align: center;'>" . htmlspecialchars($mensaje_exito) . "</p>";
    }
    if ($mensaje_error !== null) {
        echo "<p style='font-family: Arial, sans-serif;color: crimson; text-align: center;'>" . htmlspecialchars($mensaje_error) . "</p>";
    }
    
    // Ruta XML
    $rutaXML = "assets/eduFlow.xml"; 
    //Se comprueba que el archivo exista
    if(file_exists($rutaXML)){
        //Se carga el archivo y se lee la ruta del xml
        $xmlFile = simplexml_load_file($rutaXML);
        $clases = $xmlFile->xpath("//clases/clase");
        $tareas = $xmlFile->xpath("//tareas/tarea");
            
        // Mostrar todas las clases
        foreach($clases as $clase){
            echo "
            <section id='encuadre'>
            
                <section id='bloque'>
                  
                    <h4>".$clase->dia."</h4>
                    <article>
                        <p>Asignatura: ".$clase->asignatura."</p>
                        <ul>
                            <li>Profesor: ".$clase->profesor."</li>
                            <li>Día: ".$clase->dia."</li>
                            <li>Hora: ".$clase->hora."</li>
                            <li>Aula: ".$clase->aula."</li>
                        </ul>
                    </article>
                </section>
            </section>";
        }

        // Mostrar todas las tareas
        foreach($tareas as $tarea){
            $titulo = (string)$tarea->titulo;
            $asignatura = (string)$tarea->asignatura;
            $profesor = (string)$tarea->profesor;
            $imagen = (string)$tarea->imagen;
            //  Dsiplay del mensaje dinamico y formulario seguro y  oculto que se envia a la bd al clickar
            echo "
            <section id='encuadre'>
                <section id='bloque2'>
                    <h4>".$tarea->titulo."</h4>
                    <article>
                        <p>Fecha de entrega: ".$tarea->fecha_entrega."</p>
                        <ul>
                            <li>Título: ".$tarea->titulo."</li>
                            <li>Descripción: ".$tarea->descripcion."</li>
                            <li><strong>¿Urge? ".$tarea->urgente."</strong></li>
                            <li>Documento: <a href='assets/img/".$tarea->imagen."' target='_blank'>Ver archivo</a></li>
                        </ul>
                        <form method='POST'>
                            <input type='hidden' name='titulo' value='".htmlspecialchars($titulo, ENT_QUOTES)."'>
                            <input type='hidden' name='asignatura' value='".htmlspecialchars($asignatura, ENT_QUOTES)."'>
                            <input type='hidden' name='profesor' value='".htmlspecialchars($profesor, ENT_QUOTES)."'>
                            <input type='hidden' name='imagen' value='".htmlspecialchars($imagen, ENT_QUOTES)."'>
                            <button type='submit' name='entregar_tarea' value='1'>Entregar</button>
                        </form>
                    </article>
                </section>
            </section>";
        }
    } else {
        echo "<h1>Fichero no encontrado en: " . $rutaXML . "</h1>";
    }
    ?>
    <?php Database::closeConnection(); ?>

</body>
</html>