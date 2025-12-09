<?php
session_start();
//cogemos los datos del formulario para mostrarlos
$newUser   = $_POST['user'] ?? '';
$newPerfil = $_POST['perfil'] ?? '';
$newPass   = $_POST['password'] ?? '';
$newNickname   = $_POST['nickname'] ?? '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Usuario creado</title>
    <link rel="stylesheet" href="css/saludo.css">
    <script>
        // Redirige después de 5 segundos
        setTimeout(function() {
            window.location.href = "index.php";
        }, 5000);
    </script>
</head>
<body>
<?php include "cabecera.html"; ?>

<div id="saludo1">
    <h2>Hola, el usuario <?php // htmlspecialchars para evitar XSS
    echo htmlspecialchars($newUser); ?> Ha sido creado satisfactoriamente 😊</h2>
    <p>Perfil: <?php echo htmlspecialchars($newPerfil); ?></p>
    <p>
        Nickname: 
        <?php
 
        echo htmlspecialchars($newNickname);
        
        ?>
        👏
    </p>        
    <p>Será redirigido al inicio en 5 segundos...</p>
  
</div>
</body>
</html>
