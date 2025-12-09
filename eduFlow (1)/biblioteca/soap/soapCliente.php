<?php
session_start();
include "eduFlow/css/cabecera.css";

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

/**
 * CLIENTE SOAP EDUFLOW - INTERFAZ WEB
 * Interfaz web para gestión de tareas educativas
 * 
 * @author Sistema Eduflow
 * @version 1.0
 */

// Configuración del cliente SOAP
$options = [
    'location' => 'http://localhost/eduFlow/biblioteca/soap/soapServer.php',
    'uri' => 'http://localhost/eduFlow/biblioteca/soap/soapServer.php',
    'trace' => 1,
    'exceptions' => true,
    'encoding' => 'UTF-8',
    'soap_version' => SOAP_1_2
];

// Variables para resultados
$mensaje = '';
$error = '';
$tareas = [];
$mostrarResultados = false;
$accionActual = '';

// Procesar formularios
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $cliente = new SoapClient(null, $options);
        
        // Acción: Listar todas las tareas
        if (isset($_POST['accion']) && $_POST['accion'] === 'listar') {
            $resultado = $cliente->listarTodasLasTareas($_SESSION['user']);
            
            if ($resultado['success']) {
                $mensaje = $resultado['mensaje'] . " - Total: " . $resultado['cantidad'];
                $tareas = $resultado['tareas'];
                $mostrarResultados = true;
            } else {
                $error = $resultado['mensaje'];
            }
        }
        
        // Acción: Añadir nueva tarea
        if (isset($_POST['accion']) && $_POST['accion'] === 'anadir') {
            $nuevaTarea = $cliente->anadirTarea(
                $_POST['titulo'],
                $_POST['descripcion'],
                $_POST['fechaEntrega'],
                $_POST['asignatura'],
                $_POST['urgencia'],
                $_POST['profesor'],
                $_POST['archivoAsociado']
            );
            
            if ($nuevaTarea['success']) {
                $mensaje = $nuevaTarea['mensaje'] . " (ID asignado: " . $nuevaTarea['idTarea'] . ")";
            } else {
                $error = "Error al añadir la tarea";
            }
        }
        
        // Acción: Eliminar tareas por asignatura
        if (isset($_POST['accion']) && $_POST['accion'] === 'eliminar') {
            $eliminacion = $cliente->eliminarTareasPorAsignatura($_POST['asignatura_eliminar']);
            
            if ($eliminacion['success']) {
                $mensaje = $eliminacion['mensaje'] . " - Tareas eliminadas: " . $eliminacion['tareasEliminadas'];
            } else {
                $error = $eliminacion['mensaje'];
            }
        }
        
    } catch (SoapFault $fault) {
        $error = "Error SOAP: " . $fault->faultstring;
    } catch (Exception $e) {
        $error = "Error: " . $e->getMessage();
    }
}

// Determinar qué vista mostrar
if (isset($_GET['vista'])) {
    $accionActual = $_GET['vista'];
}

// Obtener fecha y hora actual
date_default_timezone_set('Europe/Madrid');
$fechaHora = date('d/m/Y H:i:s');
$saludo = "Bienvenido,".$_SESSION['user'];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EDUFLOW - Gestión de Tareas</title>
    <link rel="stylesheet" href="soapCliente.css">
</head>
<body>
    <h1>EDUFLOW</h1>
    
    <div id="saludo">
        <p><?php echo $saludo; ?></p>
        <p id="hora"><?php echo $fechaHora; ?></p>
         <a href="/eduFlow/profesor.php"> <button class="boton-saludo">Volver</button></a>
    </div>

    <div class="container">
        
        <?php if ($mensaje): ?>
            <div class="mensaje-exito">
                ✓ <?php echo htmlspecialchars($mensaje); ?>
            </div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="mensaje-error">
                ✗ <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <?php if (empty($accionActual)): ?>
            <!-- PANEL PRINCIPAL -->
            <div id="panel">
                <h2>PANEL DE GESTIÓN</h2>
                <div class="botones-grupo">
                    <form method="GET" style="display: inline;">
                        <button type="submit" name="vista" value="listar" class="boton-principal">
                            📋 LISTAR TAREAS
                        </button>
                    </form>
                    
                    <form method="GET" style="display: inline;">
                        <button type="submit" name="vista" value="anadir" class="boton-principal">
                            ➕ AÑADIR TAREA
                        </button>
                    </form>
                    
                    <form method="GET" style="display: inline;">
                        <button type="submit" name="vista" value="eliminar" class="boton-principal">
                            🗑️ ELIMINAR TAREAS
                        </button>
                    </form>
                </div>
            </div>

        <?php elseif ($accionActual === 'listar'): ?>
            <!-- VISTA: LISTAR TAREAS -->
            <form method="POST" class="formulario">
                <h2>LISTAR TODAS LAS TAREAS</h2>
                <input type="hidden" name="accion" value="listar">
                <button type="submit" class="boton-submit">🔍 CONSULTAR TAREAS</button>
            </form>
            
            <?php if ($mostrarResultados && !empty($tareas)): ?>
                <div id="resultados">
                    <h2>📚 TAREAS REGISTRADAS</h2>
                    <p class="contador-tareas">Total de tareas: <?php echo count($tareas); ?></p>
                    
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Título</th>
                                <th>Asignatura</th>
                                <th>Fecha Entrega</th>
                                <th>Urgencia</th>
                                <th>Profesor</th>
                                <th>Archivo</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($tareas as $tarea): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($tarea['id']); ?></td>
                                    <td><strong><?php echo htmlspecialchars($tarea['titulo']); ?></strong></td>
                                    <td><?php echo htmlspecialchars($tarea['asignatura']); ?></td>
                                    <td><?php echo htmlspecialchars($tarea['fechaEntrega']); ?></td>
                                    <td>
                                        <span class="urgencia-<?php echo strtolower($tarea['urgencia']); ?>">
                                            <?php echo strtoupper($tarea['urgencia']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo htmlspecialchars($tarea['profesor']); ?></td>
                                    <td><?php echo htmlspecialchars($tarea['archivoAsociado']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php elseif ($mostrarResultados && empty($tareas)): ?>
                <div class="info-box">
                    <p>📭 No hay tareas registradas en el sistema</p>
                </div>
            <?php endif; ?>
            
            <div style="text-align: center;">
                <a href="?"><button type="button" class="boton-volver">⬅️ VOLVER AL MENÚ</button></a>
            </div>

        <?php elseif ($accionActual === 'anadir'): ?>
            <!-- VISTA: AÑADIR TAREA -->
            <form method="POST" class="formulario">
                <h2>AÑADIR NUEVA TAREA</h2>
                <input type="hidden" name="accion" value="anadir">
                
                <div class="form-grupo">
                    <label for="titulo">Título de la tarea *</label>
                    <input type="text" id="titulo" name="titulo" required>
                </div>
                
                <div class="form-grupo">
                    <label for="descripcion">Descripción *</label>
                    <textarea id="descripcion" name="descripcion" required></textarea>
                </div>
                
                <div class="form-grupo">
                    <label for="fechaEntrega">Fecha de entrega *</label>
                    <input type="date" id="fechaEntrega" name="fechaEntrega" required>
                </div>

                <div class="form-grupo">
                    <label for="archivoAsociado">Archivo *</label>
                    <input type="text" id="archivoAsociado" name="archivoAsociado" required
                    placeholder="portada_1.png">
                </div>
                
                <div class="form-grupo">
                    <label for="asignatura">Asignatura *</label>
                    <input type="text" id="asignatura" name="asignatura" required 
                           placeholder="Ej: Matemáticas, Lengua, Ciencias...">
                </div>
                
                <div class="form-grupo">
                    <label for="urgencia">Nivel de urgencia *</label>
                    <select id="urgencia" name="urgencia" required>
                        <option value="">Seleccione...</option>
                        <option value="alta">🔴 Alta</option>
                        <option value="media">🟡 Media</option>
                        <option value="baja">🟢 Baja</option>
                    </select>
                </div>
                
                <div class="form-grupo">
                    <label for="profesor">Nombre del profesor *</label>
                    <input type="text" id="profesor" name="profesor" required>
                </div>
                
                <button type="submit" class="boton-submit">💾 GUARDAR TAREA</button>
            </form>
            
            <div style="text-align: center;">
                <a href="?"><button type="button" class="boton-volver">⬅️ VOLVER AL MENÚ</button></a>
            </div>

        <?php elseif ($accionActual === 'eliminar'): ?>
            <!-- VISTA: ELIMINAR TAREAS -->
            <form method="POST" class="formulario">
                <h2>ELIMINAR TAREAS POR ASIGNATURA</h2>
                <input type="hidden" name="accion" value="eliminar">
                
                <div class="form-grupo">
                    <label for="asignatura_eliminar">Nombre de la asignatura *</label>
                    <input type="text" id="asignatura_eliminar" name="asignatura_eliminar" required
                           placeholder="Ej: Matemáticas">
                </div>
                
                <div class="info-box">
                    <p>⚠️ <strong>ADVERTENCIA:</strong> Esta acción eliminará TODAS las tareas de la asignatura especificada.</p>
                </div>
                
                <button type="submit" class="boton-submit" 
                        onclick="return confirm('¿Está seguro de eliminar todas las tareas de esta asignatura?');">
                    🗑️ ELIMINAR TAREAS
                </button>
            </form>
            
            <div style="text-align: center;">
                <a href="?"><button type="button" class="boton-volver">⬅️ VOLVER AL MENÚ</button></a>
            </div>

        <?php endif; ?>

    </div>

</body>
</html>