<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/AergibideModel.php';

class UsuarioController extends BaseController {

    public function perfil() {
        $this->comprobarSesion();

        $datosUsuario = AergibideModel::getUsuario((int) $_SESSION['usuario_id']) ?? [];

        $mis_preguntas = AergibideModel::getPreguntas($_SESSION['usuario_id'], 'mias');

        
        $todas = AergibideModel::getPreguntas($_SESSION['usuario_id'], 'todas');
        $preguntas_guardadas = [];
        foreach ($todas as $pregunta) {
            if ($pregunta['guardada']) {
                $preguntas_guardadas[] = $pregunta;
            }
        }

        require __DIR__ . '/../views/Usuario.php';
    }
}
