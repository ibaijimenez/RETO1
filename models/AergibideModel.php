<?php
require_once __DIR__ . '/Database.php';

class AergibideModel {
    
    public static function getAll() {
        
    }
    
    public static function getById($id) {
        
    }
    
    // registrar usuario nuevo
    public static function create($datos) {
        $db = Database::getConnection();

        $stmt = $db->prepare(
            "INSERT INTO usuarios (nombre, apellidos, correo, passwd, departamento)
             VALUES (:nombre, :apellidos, :correo, :passwd, :departamento)"
        );

        return $stmt->execute([
            ':nombre'       => $datos['nombre'],
            ':apellidos'    => $datos['apellidos'],
            ':correo'       => $datos['correo'],
            ':contrasena'   => password_hash($datos['contrasena'], PASSWORD_DEFAULT), 
            ':departamento' => $datos['departamento']
        ]);
        
    }

    // comprobar si existe correo ya registrado
    public static function existeCorreo($correo) {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT 1 FROM usuarios WHERE correo = c");
        $stmt->execute(['c' => $correo]);
        return (bool) $stmt->fetch();
    }

    
    
    public static function deleteById($id) {
        
    }
    
    public static function deleteAll() {
        
    }
}