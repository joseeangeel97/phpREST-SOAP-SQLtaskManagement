<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="css/confirmacion.css">
</head>
<body>

<?php
//Incluimos la cabecera y la base de datos     
    
include "cabecera.html";

include_once __DIR__."/db/users.php";
$conexion=Database::connectionDB();




//Recogemos los datos del formulario de crearUsuario.php
if (
    isset($_POST['user'], $_POST['perfil'], $_POST['password'], $_POST['nickname'])
) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);

    $user      = $_POST['user'];
    $perfil    = $_POST['perfil'];     
    $password  = $_POST['password'];
    $nickname  = $_POST['nickname'];    

    // Llama con el orden correcto: user, pass, perfil, nombre completo
    $insertId = Usuario::createUser($user, $password, $perfil, $nickname);

   
}

     

?>  <!--Formulario de confirmación, se envía saludo.php, mediante POST-->
<!--Mostramos por pantalla los datos introducidos por el user y por detras enviamos el value-->
     <form action="saludo.php" method="post" id="formulario">
        <h2>Confirmacion usuario </h2>
     <ul>
        <li id="primerLi">
          <label for="user">Usuario: <?php echo "$user"?> </label>
          <input type="hidden" name="user" id="user"  value="<?php echo "$user";?> "required>
        </li>
        <li id="segundoLi"> 
         <label for="perfil" > Perfil: <?php echo "$perfil"?> </label>
         <input type="hidden" name="perfil" id="perfil" value="<?php echo"$perfil";?> " required>
               
            
              
        
        </li>
        <li >
         <label for="password" >Contraseña:  <?php echo "$password"?></label>
         <input type="hidden" name="password" id="password" value="<?php echo"$password";?> "  minlength="6" maxlength="6" required>
        </li>
         <li id="tercerLi">
           
             <label for="nickname">Nickname: <?php echo "$nickname" ?> </label>
          <input type="hidden" name="nickname" id="nickname"  value="<?php echo "$nickname";?> "required>
            
            
            <input type="hidden" name="edad" id="edad" placeholder="Edad" value="<?php echo "$edad";?> "required>
        <li>
        <li>
         <input type="submit" value="Confirmar" id="boton" >
        </li> 
        <li>
         <a href="crearUsuario.php" id="boton" class="boton-saludo" > Corregir datos   </a>
        </li> 
     </ul>
    </form>
    
      <?php
      //Cerramos conexion
Database::closeConnection();
      ?>

</body>
</html>