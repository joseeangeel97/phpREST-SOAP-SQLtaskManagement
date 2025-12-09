<?php
// Configurar codificación 
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type");

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Establecer codificación interna de PHP
mb_internal_encoding('UTF-8');

try {
    $rutaXML = "/opt/lampp/htdocs/eduFlow/assets/eduFlow.xml"; 
    
    if(!file_exists($rutaXML)){
        //Respuesta del http
        http_response_code(404);
        //Codifica a json
        echo json_encode([
            "error" => "Archivo XML no encontrado",
            "code" => 404
        ], JSON_UNESCAPED_UNICODE); // Importante para tildes
        exit;
    }
    
    $xmlFile = simplexml_load_file($rutaXML);
    $clases = $xmlFile->xpath("//clases/clase");
    
    if(!$clases){
        http_response_code(500);
        echo json_encode([
            "error" => "No se pudieron cargar las clases del XML",
            "code" => 500
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }
    
    // Procesar datos
    $horario = [];
    $asignaturas = [];
    
    foreach ($clases as $clase) {
        //Guardar los datos del xml en la variables, hora asignatura y profesor
        $asignatura = (string)$clase->asignatura;
        $hora = (string)$clase->hora;
        $profesor = (string)$clase->profesor;
        //Creamos el json para horario
        $horario[] = [
            "asignatura" => $asignatura,
            "hora" => $hora,
            "profesor" => $profesor
        ];
        
        // Agrupar asignaturas por profesor
        if(!isset($asignaturas[$profesor])){
            $asignaturas[$profesor] = [];
        }
        $asignaturas[$profesor][] = [
            "asignatura" => $asignatura,
            "hora" => $hora
        ];
    }
    
    // Obtener el método HTTP
    $method = $_SERVER["REQUEST_METHOD"];
    
    if($method !== "GET"){
        http_response_code(405);
        echo json_encode([
            "error" => "Método no permitido. Solo se acepta GET",
            "code" => 405
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }
    
    // Obtener el endpoint solicitado
    $endpoint = isset($_GET['endpoint']) ? $_GET['endpoint'] : '';
    //Según endpoint se muestran unos u otros datos
    switch($endpoint){
        case "horario":
            //  Consultar el horario general
            echo json_encode([
                "success" => true,
                "data" => $horario,
                "total" => count($horario)
            ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
            break;
            
        case "asignaturas":
            //  Buscar asignaturas por profesor
            if(isset($_GET['profesor'])){
                $profesorBuscado = $_GET['profesor'];
                
                if(isset($asignaturas[$profesorBuscado])){
                    echo json_encode([
                        "success" => true,
                        "profesor" => $profesorBuscado,
                        "data" => $asignaturas[$profesorBuscado],
                        "total" => count($asignaturas[$profesorBuscado])
                    ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
                } else {
                    http_response_code(404);
                    echo json_encode([
                        "success" => false,
                        "error" => "No se encontraron asignaturas para el profesor: " . $profesorBuscado,
                        "code" => 404
                    ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
                }
            } else {
                // Listar todos los profesores y sus asignaturas
                echo json_encode([
                    "success" => true,
                    "data" => $asignaturas,
                    "total_profesores" => count($asignaturas)
                ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
            }
            break;
            
        case "profesores":
            // Endpoint adicional: listar profesores
            echo json_encode([
                "success" => true,
                "data" => array_keys($asignaturas),
                "total" => count($asignaturas)
            ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
            break;
            
        default:
            // Documentación de la API
            echo json_encode([
                "api" => "EduFlow REST API",
                "version" => "1.0",
                "descripción" => "API REST para consultar horarios y asignaturas",
                "endpoints" => [
                    [
                        "ruta" => "?endpoint=horario",
                        "método" => "GET",
                        "descripción" => "Obtener el horario general completo",
                        "ejemplo" => "restServer.php?endpoint=horario"
                    ],
                    [
                        "ruta" => "?endpoint=asignaturas",
                        "método" => "GET",
                        "descripción" => "Obtener todas las asignaturas agrupadas por profesor",
                        "ejemplo" => "restServer.php?endpoint=asignaturas"
                    ],
                    [
                        "ruta" => "?endpoint=asignaturas&profesor={nombre}",
                        "método" => "GET",
                        "descripción" => "Buscar asignaturas de un profesor específico",
                        "parámetros" => ["profesor" => "Nombre del profesor"],
                        "ejemplo" => "restServer.php?endpoint=asignaturas&profesor=Juan%20Pérez"
                    ],
                    [
                        "ruta" => "?endpoint=profesores",
                        "método" => "GET",
                        "descripción" => "Listar todos los profesores disponibles",
                        "ejemplo" => "restServer.php?endpoint=profesores"
                    ]
                ]
            ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
            break;
    }
    //Control de errores
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "error" => "Error interno del servidor: " . $e->getMessage(),
        "code" => 500
    ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
}
?>