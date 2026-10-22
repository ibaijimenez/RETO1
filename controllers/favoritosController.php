<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/AergibideModel.php';

class FavoritosController extends BaseController {

    public function favoritos() {
        if (!isset($_SESSION['usuario_id'])) {
            $this->redirect('index.php');
        }

        $todas = AergibideModel::getPreguntas((int) $_SESSION['usuario_id'], 'todas', '', 'recientes');
        $preguntas = array_filter($todas, function ($p) {
            return !empty($p['me_gusta']);
        });

        require __DIR__ . '/../views/favoritos.php';
    }
}