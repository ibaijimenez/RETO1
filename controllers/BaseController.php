<?php

class BaseController {
    
    protected function render($view, $data = []) {
        // Extraer variables para la vista. Explicación:
        // La función extract() convierte un array asociativo en variables individuales.
        // Por ejemplo, si $data = ['titulo' => 'Hola', 'total' => 5],
        // entonces después de llamar a extract($data), tendremos dos variables:
        // $titulo y $total.
        extract($data);

        // Incluir vista
        require __DIR__ . "/../views/{$view}";
    }
    
    protected function redirect($url) {
        header("Location: {$url}");
        exit;
    }
}