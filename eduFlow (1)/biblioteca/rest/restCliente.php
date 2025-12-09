<?php
session_start();
include "eduFlow/css/cabecera.css";
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
// URL del servidor REST
$API_URL = "http://localhost/eduFlow/biblioteca/rest/restServer.php";

// Función para hacer peticiones al servidor
function llamarAPI($endpoint, $params = []) {
    global $API_URL;
    
    $url = $API_URL . "?endpoint=" . $endpoint;
    
    foreach ($params as $key => $value) {
        $url .= "&" . $key . "=" . urlencode($value);
    }
    
    $response = @file_get_contents($url);
    
    if ($response === false) {
        return ["success" => false, "error" => "No se pudo conectar con el servidor"];
    }
    
    return json_decode($response, true);
}

// Procesar acciones del cliente
$accion = isset($_GET['accion']) ? $_GET['accion'] : 'inicio';
$datos = null;
$error = null;

switch ($accion) {
    case 'horario':
        $datos = llamarAPI('horario');
        break;
        
    case 'profesores':
        $datos = llamarAPI('profesores');
        break;
        
    case 'buscar':
        if (isset($_GET['profesor'])) {
            $datos = llamarAPI('asignaturas', ['profesor' => $_GET['profesor']]);
        } else {
            $error = "Debe seleccionar un profesor";
        }
        break;
        
    case 'inicio':
    default:
        $datos = llamarAPI('horario');
        break;
}

// Obtener lista de profesores para el selector
$profesores = llamarAPI('profesores');
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EduFlow - Cliente REST</title>
    <link rel="stylesheet" href="rest.css">
</head>
<body><h1>eduFlow</h1>
    <div id="saludo">
        <h3>🎓 EduFlow</h3>
        <p id="hora">Sistema de Coordinación, a su disposicion <?php echo htmlspecialchars($_SESSION['user']);?></p>
        <a href="/eduFlow/index.php" id="anchor-enlace"> <button class='boton-saludo'>Volver</button></a>
    </div>
    
    <div id="panel">
        
        <section>
            <a href="?accion=horario">
                <button id="boton1">📅 Horario General</button>
            </a>
            
            <a href="?accion=profesores">
                <button id="boton2">👨‍🏫 Profesores</button>
            </a>
            
            <a href="#buscar">
                <button id="boton3">🔍 Buscar</button>
            </a>
        </section>
    </div>
    
    <!-- Formulario de búsqueda por profesor -->
    <section id="buscar">
        <h2>🔍 Buscar Asignaturas por Profesor</h2>
        <form method="GET" action="">
            <input type="hidden" name="accion" value="buscar">
            <label for="profesor">Selecciona un profesor:</label>
            <select name="profesor" id="profesor" required>
                <option value="">-- Selecciona un profesor --</option>
                <?php
                if ($profesores && $profesores['success']) {
                    foreach ($profesores['data'] as $prof) {
                        $selected = (isset($_GET['profesor']) && $_GET['profesor'] == $prof) ? 'selected' : '';
                        echo "<option value=\"$prof\" $selected>$prof</option>";
                    }
                }
                ?>
            </select>
            <button type="submit">Buscar</button>
        </form>
    </section>
    
    <!-- Área de resultados -->
    <section id="resultados">
        <?php if ($error): ?>
            <div class="error">
                <h3>❌ Error</h3>
                <p><?php echo htmlspecialchars($error); ?></p>
            </div>
        <?php endif; ?>
        
        <?php if ($datos): ?>
            <?php if (isset($datos['success']) && !$datos['success']): ?>
                <div class="error">
                    <h3>❌ Error</h3>
                    <p><?php echo htmlspecialchars($datos['error']); ?></p>
                </div>
            <?php else: ?>
                
                <!-- Mostrar Horario General -->
                <?php if ($accion == 'horario' || $accion == 'inicio'): ?>
                    <h2>📅 Horario General</h2>
                    <p><strong>Total de clases:</strong> <?php echo $datos['total']; ?></p>
                    <table>
                        <thead>
                            <tr>
                                <th>Asignatura</th>
                                <th>Horario</th>
                                <th>Profesor</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($datos['data'] as $clase): ?>
                                <tr>
                                    <td><strong><?php echo htmlspecialchars($clase['asignatura']); ?></strong></td>
                                    <td><?php echo htmlspecialchars($clase['hora']); ?></td>
                                    <td><?php echo htmlspecialchars($clase['profesor']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
                
                <!-- Mostrar Lista de Profesores -->
                <?php if ($accion == 'profesores'): ?>
                    <h2>👨‍🏫 Lista de Profesores</h2>
                    <p><strong>Total de profesores:</strong> <?php echo $datos['total']; ?></p>
                    <ul>
                        <?php foreach ($datos['data'] as $prof): ?>
                            <li>
                                <a href="?accion=buscar&profesor=<?php echo urlencode($prof); ?>">
                                    <?php echo htmlspecialchars($prof); ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
                
                <!-- Mostrar Asignaturas por Profesor -->
                <?php if ($accion == 'buscar' && isset($datos['profesor'])): ?>
                    <h2>📚 Asignaturas de <?php echo htmlspecialchars($datos['profesor']); ?></h2>
                    <p><strong>Total de asignaturas:</strong> <?php echo $datos['total']; ?></p>
                    <table>
                        <thead>
                            <tr>
                                <th>Asignatura</th>
                                <th>Horario</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($datos['data'] as $asignatura): ?>
                                <tr>
                                    <td><strong><?php echo htmlspecialchars($asignatura['asignatura']); ?></strong></td>
                                    <td><?php echo htmlspecialchars($asignatura['hora']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
                
            <?php endif; ?>
        <?php endif; ?>
    </section>
</body>
</html>


