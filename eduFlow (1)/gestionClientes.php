<?php session_start(); ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/gestionTareas.css">
    <title>Gestión Clientes</title>
</head>
<body>
    <?php
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    
    include "cabecera.html";
    require_once __DIR__ . '/db/database.php';
require_once __DIR__ . '/db/users.php';
require_once __DIR__ . '/db/crud.php';
    // Formateador de fecha en español
    $fmt = new IntlDateFormatter('es_ES', IntlDateFormatter::FULL, IntlDateFormatter::SHORT);
    $today = ucfirst($fmt->format(new DateTime()));
    ?>

    <div id="saludo">
        <!-- Muestra la hora de inicio de sesión y el nombre del usuario -->
        <p id="hora">Inicio de sesión: <?php echo $today; ?></p>
        <p>Bienvenido a eduFlow <?php echo htmlspecialchars($_SESSION['user']); ?></p>
        <a href="index.php"><button class="boton-saludo" style='margin-top: 2.5rem; height=2rem;'>Panel de Control</button></a>
         <a href="crearUsuario.php"><button class="boton-saludo"style='margin-top: 2.5rem; height =2rem;' >Crear usuario</button></a>
    </div>
<h2>Usuarios</h2>
    <?php
    // Consulta a realizar para que solo aparezca las tareas del profesor en activo
    $sql = "SELECT *
            FROM usuarios
            ";
    $resultado = Crud::getData($sql);

    // Lectura del array tras la query e impresión dinámica en pantalla de los datos
    if ($resultado && count($resultado) > 0) {
        foreach ($resultado as $fila) {
            echo "
            <section id='encuadre'>
                <section id='bloque'>
                    <p>
                        <strong>Id:</strong> " . htmlspecialchars($fila['id_usuario'] ?? "Sin información", ENT_QUOTES) . "<br>
                        <strong>Usuario:</strong> " . htmlspecialchars($fila['nombre_usuario'] ?? "Sin información", ENT_QUOTES) . "<br>
                        <strong>Nombre:</strong> " . htmlspecialchars($fila['nombre_completo'] ?? "Sin información", ENT_QUOTES) . "<br>
                        <strong>Perfil:</strong> " . htmlspecialchars($fila['perfil'] ?? "Sin información", ENT_QUOTES) . "<br>
                        <strong>Fecha de alta:</strong> " . htmlspecialchars($fila['fecha_alta'] ?? "Sin archivo", ENT_QUOTES) . "<br>
                        
                    </p>
                    
                    <form method='POST'>
                        <input type='hidden' name='id_usuario' value='" . htmlspecialchars($fila['id_usuario'], ENT_QUOTES) . "'>
                        <input type='text' name='comentario' placeholder='Hacer observación' required>
                        <button type='submit' name='gestionTarea' value='comentar'>Comentar</button>
                    </form>

                    <form method='POST' style='margin-top: 1rem;'>
                        <input type='hidden' name='id_usuario' value='" . htmlspecialchars($fila['id_usuario'], ENT_QUOTES) . "'>
                        <button type='submit' name='gestionCliente' value='eliminar' class='boton-eliminar' onclick='return confirm(\"¿Estás seguro de que deseas eliminar este usuario?\");'>Eliminar</button>
                    </form>
                </section>
            </section>
            ";
        }
    } else {
        echo "<p>No hay tareas registradas.</p>";
    }
// Manejo del formulario para eliminar usuario
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['gestionCliente'])) {
    try {
        if ($_POST['gestionCliente'] === 'eliminar') {
            // 1. Coger el ID desde el formulario
            $idUsuario = isset($_POST['id_usuario']) ? (int) $_POST['id_usuario'] : 0;

            // 2. Si no hay ID válido, ni lo intentes
            if ($idUsuario <= 0) {
                echo "<script>alert('ID de usuario no válido');</script>";
            } else {
                // 3. Llamar al método que borra
                $filas = Usuario::deleteUserById($idUsuario);

                if ($filas > 0) {
                    echo "<script>alert('Usuario eliminado correctamente'); window.location.href='gestionClientes.php';</script>";
                } else {
                    echo "<script>alert('No se eliminó ningún usuario. Revisa el ID o la consulta SQL.');</script>";
                }
            }
        }
    } catch (Exception $e) {
        $mensaje_error = "Error: " . $e->getMessage();
        echo "<script>alert('$mensaje_error');</script>";
    }
}




    

    Database::closeConnection(); 
    ?>
</body>
</html>