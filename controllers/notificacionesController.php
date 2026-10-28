<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/AergibideModel.php';

class notificacionesController extends BaseController {

    public function notificaciones() {
        if (!isset($_SESSION['usuario_id'])) {
            header('Location: index.php');
            exit;
        }
        $uid = (int) $_SESSION['usuario_id'];

        $notificaciones = AergibideModel::getNotificaciones($uid);
        AergibideModel::marcarLeidas($uid);

        require __DIR__ . '/../views/notificaciones.php';
    }
}