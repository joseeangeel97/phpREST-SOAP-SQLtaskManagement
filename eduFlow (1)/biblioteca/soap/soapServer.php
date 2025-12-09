<?php
/**
 * SERVIDOR SOAP EDUFLOW
 * Servicio web para gestión de tareas de profesores
 * 
 * @author Sistema Eduflow
 * @version 1.0
 */

class EduflowSoapServer {
    
    private $rutaXML = "/opt/lampp/htdocs/eduFlow/assets/eduFlow.xml";
    private $rutaPortada = "/opt/lampp/htdocs/eduFlow/assets/img/portada.jpg";
    
    /**
     * Constructor - Valida que existan los archivos necesarios
     */
    public function __construct() {
        if (!file_exists($this->rutaXML)) {
            throw new SoapFault("Server", "Archivo XML no encontrado en: " . $this->rutaXML);
        }
        if (!file_exists($this->rutaPortada)) {
            throw new SoapFault("Server", "Archivo portada_1.png no encontrado en: " . $this->rutaPortada);
        }
    }
    //Normalizar caracteres
    private function normalizar($str) {
    // Pasar entidades tipo &#xF3; a caracteres reales
    $str = html_entity_decode($str, ENT_QUOTES, 'UTF-8');

    // Quitar espacios laterales
    $str = trim($str);

    // Sustituir acentos y ñ
    $buscar  = ['á','é','í','ó','ú','Á','É','Í','Ó','Ú','ñ','Ñ'];
    $reempl  = ['a','e','i','o','u','a','e','i','o','u','n','n'];
    $str = str_replace($buscar, $reempl, $str);

    // Pasar a minúsculas
    $str = mb_strtolower($str, 'UTF-8');

    return $str;
}


    
    /**
     * MÉTODO 1: Listar todas las tareas
     * 
     * @return array Lista de tareas con todos sus detalles
     * 
     * Ejemplo de respuesta:
     * [
     *   {
     *     "id": "1",
     *     "titulo": "Ejercicio de matemáticas",
     *     "descripcion": "Resolver ecuaciones",
     *     "fechaEntrega": "2025-12-01",
     *     "asignatura": "Matemáticas",
     *     "urgencia": "alta",
     *     "profesor": "Juan Pérez",
     *     "archivoAsociado": "portada_1.png"
     *   }
     * ]
     */
    public function listarTodasLasTareas($user) {
        try {
            $xml = simplexml_load_file($this->rutaXML);
            
            if ($xml === false) {
                throw new SoapFault("Server", "Error al cargar el archivo XML");
            }
            
            $tareas = $xml->xpath("//tareas/tarea");
            
            if (!$tareas) {
                return []; // No hay tareas, devolver array vacío
            }
            
            $listaTareas = [];
          
            
            foreach ($tareas as $tarea) {
                if(hash_equals($this->normalizar((string)$user),$this->normalizar((string)$tarea->profesor))){
                $listaTareas[] = [
                    'id' => (string)$tarea['id'],
                    'titulo' => (string)$tarea->titulo,
                    'descripcion' => (string)$tarea->descripcion,
                    'fechaEntrega' => (string)$tarea->fecha_entrega,
                    'asignatura' => (string)$tarea->asignatura,
                    'urgencia' => (string)$tarea->urgente,
                    'profesor' => (string)$tarea->profesor,
                    'archivoAsociado' => (string)$tarea->imagen
                ];
            }
            }
            
            return [
                'success' => true,
                'mensaje' => 'Tareas obtenidas correctamente',
                'cantidad' => count($listaTareas),
                'tareas' => $listaTareas
            ];
            
        } catch (Exception $e) {
            throw new SoapFault("Server", "Error al listar tareas: " . $e->getMessage());
        }
    }
    
    /**
     * MÉTODO 2: Añadir nueva tarea
     * 
     * @param string $titulo Título de la tarea (requerido)
     * @param string $descripcion Descripción detallada (requerido)
     * @param string $fechaEntrega Fecha en formato YYYY-MM-DD (requerido)
     * @param string $asignatura Nombre de la asignatura (requerido)
     * @param string $urgencia Nivel: 'baja', 'media', 'alta' (requerido)
     * @param string $profesor Nombre del profesor (requerido)
     * 
     * @return array Resultado de la operación
     * 
     * Ejemplo de uso:
     * $cliente->anadirTarea(
     *     "Trabajo final",
     *     "Proyecto de investigación sobre IA",
     *     "2025-12-15",
     *     "Informática",
     *     "alta",
     *     "María García"
     * );
     */
    public function anadirTarea($titulo, $descripcion, $fechaEntrega, $asignatura, $urgencia, $profesor,$arch) {
        try {
            // Validaciones
            if (empty($titulo) || empty($descripcion) || empty($fechaEntrega) || 
                empty($asignatura) || empty($urgencia) || empty($profesor)) {
                throw new SoapFault("Client", "Todos los campos son obligatorios");
            }
            
            // Validar formato de fecha
            $fecha = DateTime::createFromFormat('Y-m-d', $fechaEntrega);
            if (!$fecha || $fecha->format('Y-m-d') !== $fechaEntrega) {
                throw new SoapFault("Client", "Formato de fecha inválido. Use YYYY-MM-DD");
            }
            
            // Validar urgencia
            $urgenciasValidas = ['baja', 'media', 'alta'];
            if (!in_array(strtolower($urgencia), $urgenciasValidas)) {
                throw new SoapFault("Client", "Urgencia debe ser: baja, media o alta");
            }
            
            // Cargar XML
            $xml = simplexml_load_file($this->rutaXML);
            
            if ($xml === false) {
                throw new SoapFault("Server", "Error al cargar el archivo XML");
            }
            
          // Obtener el último ID
$tareas = $xml->xpath("//tareas/tarea");
$ultimoId = 0;

if ($tareas) {
    foreach ($tareas as $tarea) {
        $id = isset($tarea['id']) ? (int)$tarea['id'] : 0;
        if ($id > $ultimoId) {
            $ultimoId = $id;
        }
    }
}

$nuevoId = $ultimoId + 1;

// Crear nueva tarea
$tareasNode = $xml->xpath("//tareas");

if (!$tareasNode) {
    throw new SoapFault("Server", "No se encontró el nodo <tareas> en el XML");
}
//Crea un nuevo nodo tarea, se le añade un atributo y los hijos del nodo
$nuevaTarea = $tareasNode[0]->addChild('tarea');
$nuevaTarea->addAttribute('id', $nuevoId);

$nuevaTarea->addChild('titulo', htmlspecialchars($titulo));
$nuevaTarea->addChild('descripcion', htmlspecialchars($descripcion));
$nuevaTarea->addChild('fecha_entrega', $fechaEntrega);
$nuevaTarea->addChild('asignatura', htmlspecialchars($asignatura));
$nuevaTarea->addChild('urgente', strtolower($urgencia));
$nuevaTarea->addChild('profesor', htmlspecialchars($profesor));
$nuevaTarea->addChild('imagen', htmlspecialchars($arch));

// Guardar
$dom = new DOMDocument('1.0', 'UTF-8');
$dom->preserveWhiteSpace = false;
$dom->formatOutput = true;
$dom->loadXML($xml->asXML());

if (!is_writable($this->rutaXML)) {
    throw new SoapFault("Server", "El archivo no tiene permisos de escritura: " . $this->rutaXML);
}

if ($dom->save($this->rutaXML) === false) {
    throw new SoapFault("Server", "Error al guardar el archivo XML");
}
            
            return [
                'success' => true,
                'mensaje' => 'Tarea añadida correctamente',
                'idTarea' => $nuevoId,
                
                'tarea' => [
                    "tarea['id']" => $nuevoId,
                    'titulo' => $titulo,
                    'descripcion' => $descripcion,
                    'fecha_entrega' => $fechaEntrega,
                    'asignatura' => $asignatura,
                    'urgente' => strtolower($urgencia),
                    'profesor' => $profesor,
                    
                ]
            ];
            
        } catch (SoapFault $sf) {
            throw $sf;
        } catch (Exception $e) {
            throw new SoapFault("Server", "Error al añadir tarea: " . $e->getMessage());
        }
    }
    
    /**
     * MÉTODO 3: Eliminar tareas por asignatura
     * 
     * @param string $asignatura Nombre de la asignatura cuyas tareas se eliminarán
     * 
     * @return array Resultado de la operación con cantidad de tareas eliminadas
     * 
     * Ejemplo de uso:
     * $cliente->eliminarTareasPorAsignatura("Matemáticas");
     */
    public function eliminarTareasPorAsignatura($asignatura) {
        try {
            if (empty($asignatura)) {
                throw new SoapFault("Client", "El nombre de la asignatura es obligatorio");
            }
            
            // Cargar XML
            $xml = simplexml_load_file($this->rutaXML);
           
            if ($xml === false) {
                throw new SoapFault("Server", "Error al cargar el archivo XML");
            }
            
            // Buscar tareas de la asignatura
            $tareas = $xml->xpath("//tareas/tarea[asignatura='" . htmlspecialchars($asignatura) . "']");
            
            if (!$tareas || count($tareas) === 0) {
                return [
                    'success' => false,
                    'mensaje' => 'No se encontraron tareas para la asignatura: ' . $asignatura,
                    'tareasEliminadas' => 0
                ];
            }
            
            $tareasEliminadas = [];
            $contador = 0;
            
            // Eliminar cada tarea encontrada
            foreach ($tareas as $tarea) {
                $tareasEliminadas[] = [
                    'id' => (string)$tarea->id,
                    'titulo' => (string)$tarea->titulo
                ];
                
                // Usar DOM para eliminar correctamente
                $dom = dom_import_simplexml($tarea);
                $dom->parentNode->removeChild($dom);
                $contador++;
            }
            
            // Guardar XML
            $dom = new DOMDocument('1.0', 'UTF-8');
            $dom->preserveWhiteSpace = false;
            $dom->formatOutput = true;
            $dom->loadXML($xml->asXML());
            
            if (!$dom->save($this->rutaXML)) {
                throw new SoapFault("Server", "Error al guardar el archivo XML después de eliminar");
            }
            
            return [
                'success' => true,
                'mensaje' => 'Tareas eliminadas correctamente',
                'asignatura' => $asignatura,
                'tareasEliminadas' => $contador,
                'detalles' => $tareasEliminadas
            ];
            
        } catch (SoapFault $sf) {
            throw $sf;
        } catch (Exception $e) {
            throw new SoapFault("Server", "Error al eliminar tareas: " . $e->getMessage());
        }
    }
    
    /**
     * MÉTODO 4: Obtener información del servicio
     * 
     * @return array Información sobre el estado del servicio
     */
    public function obtenerInfoServicio() {
        return [
            'servicio' => 'Eduflow SOAP Server',
            'version' => '1.0',
            'estado' => 'activo',
            'metodos' => [
                'listarTodasLasTareas',
                'anadirTarea',
                'eliminarTareasPorAsignatura',
                'obtenerInfoServicio'
            ],
            'archivoXML' => $this->rutaXML,
            'archivoPortada' => $this->rutaPortada,
            'xmlExiste' => file_exists($this->rutaXML),
            'portadaExiste' => file_exists($this->rutaPortada)
        ];
    }
}

// =======================
// INICIALIZACIÓN DEL SERVIDOR SOAP
// =======================

try {
    // Configuración del servidor SOAP en modo non-WSDL
    $options = [
        'uri' => 'http://localhost/eduFlow/biblioteca/soap/soapServer.php',
        'encoding' => 'UTF-8',
        'soap_version' => SOAP_1_2
    ];
    
    // Crear servidor SOAP
    $server = new SoapServer(null, $options);
    
    // Registrar la clase del servicio
    $server->setClass('EduflowSoapServer');
    
    // Configurar manejo de errores
    $server->setPersistence(SOAP_PERSISTENCE_SESSION);
    
    // Procesar peticiones SOAP
    $server->handle();
    
} catch (SoapFault $sf) {
    // Registrar error en log
    error_log("SOAP Fault: " . $sf->getMessage());
    
    // Devolver error SOAP
    echo '<?xml version="1.0" encoding="UTF-8"?>';
    echo '<SOAP-ENV:Envelope xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/">';
    echo '<SOAP-ENV:Body>';
    echo '<SOAP-ENV:Fault>';
    echo '<faultcode>' . htmlspecialchars($sf->faultcode) . '</faultcode>';
    echo '<faultstring>' . htmlspecialchars($sf->faultstring) . '</faultstring>';
    echo '</SOAP-ENV:Fault>';
    echo '</SOAP-ENV:Body>';
    echo '</SOAP-ENV:Envelope>';
    
} catch (Exception $e) {
    error_log("Error del servidor: " . $e->getMessage());
    http_response_code(500);
    echo "Error interno del servidor";
}
?>