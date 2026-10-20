<?php
require_once __DIR__ . '/Database.php';

class AergibideModel {
    
    public static function getAll() {
        
    }
    
    public static function getById($id) {
        
    }
    
    public static function create($datos) {
        
    }
    
    public static function deleteById($id) {
        
    }
    
    public static function deleteAll() {
        
    }

    public static function comprobarCredenciales($usuario, $contrasena) {
        $datos = [
            "usuario" => $usuario,
            "contrasena" => $contrasena
        ];
        $db = Database::getConnection();
        $stmt=$db -> prepare("SELECT * FROM usuarios where  nombre=:usuario AND passwd=:contrasena ");
        $stmt->execute($datos); 
        return $stmt->fetch(); 
     }
}