<?php

class Router {
    
    // Como el router no tiene estados, los métodos pueden ser estáticos.
    public static function dispatch() {
        // Determinar controlador (por defecto EmpleadoController)
        $controllerName = $_GET['controller'] ?? 'XxxxController';
        
        // Determinar acción (por defecto index)
        $action = $_GET['accion'] ?? 'index';
        
        // Cargar y ejecutar controlador
        self::loadController($controllerName, $action);
    }
    
    private static function loadController($controllerName, $action) {
        // Construir ruta del archivo del controlador
        $controllerFile = __DIR__ . "/controllers/{$controllerName}.php";
        require_once $controllerFile;
        
        // Instanciar controlador
        $controller = new $controllerName();

        // Ejecutar método
        $controller->$action();
    }
    

}