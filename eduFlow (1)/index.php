<?php
session_start();
include "eduFlow/css/cabecera.css";

// Bloquea acceso si no hay login válido
if (
  // No está logueado o no es admin
    !isset($_SESSION['logueado']) ||
    $_SESSION['logueado'] !== true ||
    $_SESSION['perfil'] !== 'admin'
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

// Formateador de fecha en español
$fmt = new IntlDateFormatter('es_ES', IntlDateFormatter::FULL, IntlDateFormatter::SHORT);
$today = ucfirst($fmt->format(new DateTime()));
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión</title>
    <link rel="stylesheet" href="css/index.css">
</head>
<body>
  
    <?php // Incluimos la cabecera
    include "cabecera.html"; ?>
<div id="saludo">
<!-- Muestra la hora de inicio de sesión y el nombre del usuario -->
        <p id="hora">Inicio de sesión: <?php echo $today; ?></p>
        <p>Bienvenido a eduFlow <?php echo htmlspecialchars($_SESSION['user']); ?></p>
        
</div>
    <div id="panel">
      
        
          <h3>ERP eduFlow</h3>
          <section>
            <a href="gestionClientes.php"><button id="boton1"><svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-abacus"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 3v18" /><path d="M19 21v-18" /><path d="M5 7h14" /><path d="M5 15h14" /><path d="M8 13v4" /><path d="M11 13v4" /><path d="M16 13v4" /><path d="M14 5v4" /><path d="M11 5v4" /><path d="M8 5v4" /><path d="M3 21h18" /></svg>Gestion usuario</button></a><!--
            <a href=""><button id="boton2"><svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="currentColor"  class="icon icon-tabler icons-tabler-filled icon-tabler-aerial-lift"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M19.876 2.008a1 1 0 1 1 .248 1.984l-7.124 .891v2.117h4.2a1 1 0 0 1 .688 .274l.087 .093c2.79 3.417 2.717 9.963 -.226 13.295a1 1 0 0 1 -.749 .338h-10.106a1 1 0 0 1 -.763 -.353c-2.86 -3.373 -2.86 -9.92 0 -13.294a1 1 0 0 1 .763 -.353h4.106v-1.867l-6.876 .86a1 1 0 0 1 -1.095 -.754l-.021 -.115a1 1 0 0 1 .868 -1.116l7.996 -1l.011 -.001l.008 -.001zm-8.876 6.992h-3.617l-.051 .072c-.718 1.042 -1.149 2.41 -1.292 3.844l-.008 .084h4.968zm5.698 0h-3.698v4h4.979l-.005 -.072c-.123 -1.436 -.533 -2.811 -1.232 -3.864z" /></svg>Gestion de calendario</button></a>
            <a href=""><button id="boton3"><svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="currentColor"  class="icon icon-tabler icons-tabler-filled icon-tabler-bowl-chopsticks"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M20 10a2 2 0 0 1 2 2v.5c0 1.694 -2.247 5.49 -3.983 6.983l-.017 .013v.504a2 2 0 0 1 -1.85 1.995l-.15 .005h-8a2 2 0 0 1 -2 -2v-.496l-.065 -.053c-1.76 -1.496 -3.794 -4.965 -3.928 -6.77l-.007 -.181v-.5a2 2 0 0 1 2 -2z" /><path d="M18.929 6.003a1 1 0 1 1 .142 1.994l-14 1a1 1 0 1 1 -.142 -1.994z" /><path d="M18.79 1.022a1 1 0 1 1 .42 1.956l-14 3a1 1 0 1 1 -.42 -1.956z" /></svg>Gestion de tareas</button></a>-->
            <a href="biblioteca/rest/restCliente.php"><button id="boton2">Coordinación</button></a>
            </section>
        </div>
    </div>
</body>
<?php Database::closeConnection(); ?>
</html>
