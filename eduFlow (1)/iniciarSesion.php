<?php 

// La cookie de sesión solo dura hasta que se cierre el navegador
// y es segura y no accesible vía JavaScript (HTTP only)
session_set_cookie_params([
    'lifetime' => 0,       // 0 = hasta cerrar navegador
    'path' => '/',
    'secure' => true,     
    'httponly' => true,
    'samesite' => 'Lax'
]);
session_start(); 
error_reporting(E_ALL);
ini_set('display_errors', 1);
//Incluimos el archivo crud
include_once __DIR__."/db/crud.php";


        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user     = trim($_POST['user']) ?? '';
    $password = $_POST['password'] ?? '';
    

    //  Traer la fila del usuario por nombre
    $sql = "SELECT nombre_usuario, contrasena_hash, perfil
            FROM usuarios
            WHERE nombre_usuario = ?";
    $resultado = Crud::getData($sql, $user);  
  
   
    //  Validar existencia + contraseña con hash
    if (hash_equals($user,trim($resultado[0]['nombre_usuario'])) && hash_equals( $resultado[0]['contrasena_hash'],hash('sha256',$password))) {

        $_SESSION['perfil']       = $resultado[0]['perfil'];
        $_SESSION['logueado']   = true;
        $_SESSION['login_time'] = time();
        $_SESSION['user']= $resultado[0]['nombre_usuario'];

        //  Redirigir por perfil
        switch ($resultado[0]['perfil']) {
            case 'admin':
                header('Location: index.php');       exit;
            case 'estudiante':
                header('Location: estudiante.php');  exit;
            case 'profesor':
                header('Location: profesor.php');    exit;
            default:
                header('Location: iniciarSesion.php?error=perfil'); exit;
        }
    } else {
        // Usuario no existe o contraseña incorrecta
        header('Location: iniciarSesion.php?error=credenciales');
        exit;
    }
}


       
$mensaje = $mensaje ?? "<p id='mensaje'>Por favor, inicie sesión</p>";
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión</title>
    <link rel="stylesheet" href="css/iniciarSesion.css">
</head>
<body>
    <?php include "cabecera.html"; ?>

    <?php echo $mensaje; ?>

    <!-- Formulario de inicio de sesión -->
    <form action="iniciarSesion.php" method="post" id="formulario">
        <h2>Iniciar Sesion</h2>
        <ul>
            <li id="primerLi">
                <label for="user">Usuario:</label>
                <input type="text" name="user" id="user"
                       value="<?php echo isset($_SESSION['user']) ? htmlspecialchars($_SESSION['user']) : ''; ?>">
            </li>
            <li>
                <label for="password">Contraseña:</label>
                <input type="password" name="password" id="password">
            </li>
            <li>
                <input type="submit" value="Iniciar sesion" id="boton">
            </li> 
        </ul>
    </form>
</body>
<?php
Database::closeConnection();

?>
</html>
