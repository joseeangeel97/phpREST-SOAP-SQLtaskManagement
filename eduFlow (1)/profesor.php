<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profesor</title>
    <link rel="stylesheet" href="css/profesor.css">
</head>
<body>
    <?php include "cabecera.html"; 
    // Bloquea acceso si no hay login válido
if (
  // No está logueado o no es profesor
    !isset($_SESSION['logueado']) ||
    $_SESSION['logueado'] !== true ||
    $_SESSION['perfil'] !== 'profesor'
) {
  // Redirige a la página de inicio de sesión
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
$fmt = new IntlDateFormatter('es_ES', IntlDateFormatter::FULL, IntlDateFormatter::SHORT);
$today = ucfirst($fmt->format(new DateTime()));
    
    ?>

   <div id="saludo">
      <!-- Muestra la hora de inicio de sesión y el nombre del usuario -->
        <p id="hora">Inicio de sesión: <?php echo $today; ?></p>
        <p>Bienvenido a eduFlow <?php echo htmlspecialchars($_SESSION['user']); ?></p>
        <a href="biblioteca/soap/soapCliente.php"><button style='margin-top: 2.5rem;'>Gestion Personal</button></a>
</div>
   

<section id ="encuadre">
          <article id="flex"> <button>Calendario</button> <a href="gestionTareas.php"><button>Gestión de tareas</button></a></article>    
           
             <?php 
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
         } }else {
          //En caso de fichero no encontrado
        echo "<h1>Fichero no encontrado en: " . $rutaXML . "</h1>";
    }?>
</body>
</html>