<?php
require_once __DIR__ . '/BaseController.php';

class UsuarioController extends BaseController {

    public function perfil() {
        $this->comprobarSesion();
        require __DIR__ . '/../views/Usuario.php';
    }
}