<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/AergibideModel.php';

class UsuarioController extends BaseController {

    public function perfil() {
        $this->comprobarSesion();
        $uid = (int) $_SESSION['usuario_id'];

        $datosUsuario = AergibideModel::getUsuario($uid) ?? [];

        // Aviso de la contraseña (se muestra una sola vez)
        $mensaje = $_SESSION['perfil_msg'] ?? null;
        unset($_SESSION['perfil_msg']);

        $mis_preguntas = AergibideModel::getPreguntas($uid, 'mias');

        $preguntas_guardadas = array_values(array_filter(
            AergibideModel::getPreguntas($uid, 'todas'),
            fn($p) => !empty($p['guardada'])
        ));

        require __DIR__ . '/../views/Usuario.php';
    }

    public function cambiarPassword() {
        $this->comprobarSesion();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?controller=UsuarioController&accion=perfil');
            exit;
        }

        $actual    = $_POST['actual'] ?? '';
        $nueva     = $_POST['nueva'] ?? '';
        $confirmar = $_POST['confirmar'] ?? '';

        if ($actual === '' || $nueva === '' || $confirmar === '') {
            $msg = ['tipo' => 'error', 'texto' => 'Rellena todos los campos.'];
        } elseif ($nueva !== $confirmar) {
            $msg = ['tipo' => 'error', 'texto' => 'Las contraseñas nuevas no coinciden.'];
            //mb_strlen cuenta bytes y cada letra es un byte
        } elseif (mb_strlen($nueva) < 6) {
            $msg = ['tipo' => 'error', 'texto' => 'La nueva contraseña debe tener al menos 6 caracteres.'];
        } elseif ($nueva === $actual) {
            $msg = ['tipo' => 'error', 'texto' => 'La nueva contraseña debe ser distinta de la actual.'];
        } elseif (!AergibideModel::cambiarPassword((int) $_SESSION['usuario_id'], $actual, $nueva)) {
            $msg = ['tipo' => 'error', 'texto' => 'La contraseña actual no es correcta.'];
        } else {
            $msg = ['tipo' => 'ok', 'texto' => 'Contraseña actualizada correctamente.'];
        }

        $_SESSION['perfil_msg'] = $msg;
        header('Location: index.php?controller=UsuarioController&accion=perfil');
        exit;
    }
}