<?php
session_start();

?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Crear Usuario</title>
  <link rel="stylesheet" href="css/crearUsuario.css">
</head>
<body>
  <?php include "cabecera.html"; ?>

  <!-- Formulario de creación de usuario -->
  <form action="confirmacion.php" method="post" id="formulario">
      <h2>Confirmacion usuario</h2>
      <ul>
          <li id="primerLi">
              <label for="user">Usuario:</label>
              <input type="text" name="user" id="user" required>
          </li>
          <li id="segundoLi">
              <label for="perfil">Perfil:</label>
              <select name="perfil" id="perfil" required>
                  <option value="" disabled selected>-- Seleccione perfil --</option>
                  <option value="estudiante">Alumno</option>
                  <option value="profesor">Profesor</option>
                  <option value="admin">Administrativo</option>
              </select>
          </li>
          <li>
              <label for="password">Contraseña:</label>
              <input type="password" name="password" id="password" minlength="6" maxlength="6" required>
          </li>
          <li id="tercerLi">
              <label for="nickname">Nickname:</label>
              <input type="text" name="nickname" id="nickname" required>
          </li>
          <li>
              <input type="submit" value="Confirmar" id="boton">
          </li>
      </ul>
  </form>
</body>
</html>
