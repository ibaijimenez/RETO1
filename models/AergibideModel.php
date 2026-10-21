<?php
require_once __DIR__ . '/Database.php';

class AergibideModel {
    
    public static function getAll() {
        
    }
    
    public static function getById($id) {
        
    }
    
    public static function create($datos) {
        $db = Database::getConnection();

        $sql = "INSERT INTO usuarios (nombre, apellidos, correo, passwd, departamento) 
            VALUES (:nombre, :apellidos, :correo, :contrasena, :departamento)";

        $stmt = $db->prepare($sql);

        return $stmt->execute([
            ':nombre'       => $datos['nombre'],
            ':apellidos'    => $datos['apellidos'],
            ':correo'       => $datos['correo'],
            ':contrasena'   => $datos['contraseña'], 
            ':departamento' => $datos['departamento']
        ]);
        
    }
    
    public static function deleteById($id) {
        
    }
    
    public static function deleteAll() {
        
    }
}