<?php
include_once __DIR__ . "/crud.php";
class Tareas{

//Subir tarea
public static function uploadTask($nombreTarea,$asignatura,$nombreProfesor,$nombreAlumno,$archivo){
 $nombreTarea=trim($nombreTarea);
 $asignatura=trim($asignatura);
 $nombreProfesor=trim($nombreProfesor);
 
 $nombreAlumno=trim($nombreAlumno);
 $archivo=trim($archivo);

  if ($nombreTarea === '' || $asignatura === '' || $nombreProfesor === '' || $nombreAlumno === '' || $archivo==='') {
            throw new InvalidArgumentException('Faltan datos obligatorios para crear la tarea.');
        }
        
 $sql = "INSERT INTO tareas  (nombre_tarea, asignatura, nombre_profesor, nombre_alumno,archivo_entrega)
                VALUES (?, ?, ?, ?, ?)";
                 return Crud::insertData($sql,$nombreTarea,$asignatura,$nombreProfesor,$nombreAlumno,$archivo);
}

//Subir comentario
public static function uploadComment($comentario, $idTarea) {
    $conexion = Database::connectionDB();
    $sql = "UPDATE tareas SET mensaje_profesor = ? WHERE id_tarea = ?";
    $statement = $conexion->prepare($sql);
    $statement->bind_param("si", $comentario, $idTarea);
    return $statement->execute();
}



}



?>