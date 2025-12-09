<?php
include_once __DIR__ . "/crud.php";
class Usuario{

    // Orden correcto: usuario, password plano, perfil, nombre completo
    public static function createUser($nombreUsuario, $passwordPlain, $perfil, $nombreCompleto) {
        $nombreUsuario  = trim($nombreUsuario);
        $passwordPlain  = (string) $passwordPlain;
        $perfil         = trim($perfil);
        $nombreCompleto = trim($nombreCompleto);

        if ($nombreUsuario === '' || $passwordPlain === '' || $perfil === '' || $nombreCompleto === '') {
            throw new InvalidArgumentException('Faltan datos obligatorios para crear el usuario.');
        }

        $hash = hash('sha256',$passwordPlain);
        if ($hash === false) {
            throw new RuntimeException('No se pudo generar el hash de la contraseña.');
        }

        $sql = "INSERT INTO usuarios (nombre_usuario, contrasena_hash, nombre_completo, perfil)
                VALUES (?, ?, ?, ?)";

        return Crud::insertData($sql, $nombreUsuario, $hash, $nombreCompleto, $perfil);
    }

    //Cambiar contraseña
    public static function updatePasswordForUser(string $nombreUsuario, string $passwordPlain): int
{
    $nombreUsuario = trim($nombreUsuario);
    $passwordPlain = (string)$passwordPlain;

    if ($nombreUsuario === '' || $passwordPlain === '') {
        throw new InvalidArgumentException('Usuario o contraseña vacíos.');
    }

    // Genera el hash
    $hash = hash('sha256',$passwordPlain);
    if ($hash === false) {
        throw new RuntimeException('No se pudo generar el hash de la contraseña.');
    }

    // SQL: actualiza la columna contrasena_hash para el usuario dado
    $sql = "UPDATE usuarios SET contrasena_hash = ? WHERE nombre_usuario = ?";

    // Llama a tu función updateData (devuelve filas afectadas)
    $filas = Crud::updateData($sql, $hash, $nombreUsuario);

    return $filas;
}

public static function deleteUserById($id) {
    $sql = "DELETE FROM usuarios WHERE id_usuario = ?";
    return Crud::deleteData($sql, $id);
}

}

?>