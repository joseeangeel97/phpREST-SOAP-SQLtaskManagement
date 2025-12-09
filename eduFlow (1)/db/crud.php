<?php 
include_once __DIR__ . "/database.php";
 class Crud {

      // Función estática para aplicar bind_param dinámicamente
    private static function bindParams($stmt, $params) {
        if ($params && count($params) > 0) {
            // Crear el string de tipos (s = string, i = integer, d = double, b = blob)
            $types = '';
            $values = [];
            foreach ($params as $p) {
                $types .= is_int($p) ? 'i' : (is_float($p) ? 'd' : 's');
                $values[] = $p;
            }
            // Vincular parámetros usando referencias
            $stmt->bind_param($types, ...$values);
        }
    }
    public static function  getData($sql,...$param){
         $conexion = Database::connectionDB();
         $statement= $conexion->prepare($sql);
        
         self::bindParams($statement,$param);
          $statement->execute();
        $result = $statement->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
         
    }


public static function insertData($sql, ...$param) {
    $conexion = Database::connectionDB();
    $statement = $conexion->prepare($sql);

    if (!$statement) {
        die("Error al preparar la consulta: " . $conexion->error);
    }

    self::bindParams($statement, $param);

    if (!$statement->execute()) {
        die("Error al ejecutar la consulta: " . $statement->error);
    }

    // Devuelve el ID del último registro insertado
    return $conexion->insert_id;
}  

// UPDATE
public static function updateData($sql, ...$param) {
    $conexion = Database::connectionDB();
    $statement = $conexion->prepare($sql);

    if (!$statement) {
        die("Error al preparar la consulta: " . $conexion->error);
    }

    self::bindParams($statement, $param);

    if (!$statement->execute()) {
        die("Error al ejecutar la consulta: " . $statement->error);
    }

    // Devuelve cuántas filas fueron modificadas
    return $statement->affected_rows;
}


// DELETE
public static function deleteData($sql, ...$param) {
    $conexion = Database::connectionDB();
    $statement = $conexion->prepare($sql);

    if (!$statement) {
        die("Error al preparar la consulta: " . $conexion->error);
    }

    self::bindParams($statement, $param);

    if (!$statement->execute()) {
        die("Error al ejecutar la consulta: " . $statement->error);
    }

    // Devuelve cuántas filas fueron eliminadas
    return $statement->affected_rows;
}


 }


 


?>