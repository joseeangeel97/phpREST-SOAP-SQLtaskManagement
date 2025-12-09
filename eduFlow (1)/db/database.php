<?php
class Database {
    private static $conexion = null;

    public static function connectionDB() {
        if (self::$conexion === null) {
            $config = parse_ini_file(__DIR__ . "/../config.ini", false, INI_SCANNER_TYPED);
            if ($config === false) {
                throw new Exception("No se pudo leer config.ini");
            }

            mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
            self::$conexion = new mysqli(
                $config['host'],
                $config['user'],
                $config['password'],
                $config['dbname']
            );
            self::$conexion->set_charset($config['charset'] ?? "utf8mb4");
        }
       
        return self::$conexion;
    }

    public static function closeConnection() {
        if (self::$conexion !== null) {
            self::$conexion->close();
            self::$conexion = null;
        }
    }
}
