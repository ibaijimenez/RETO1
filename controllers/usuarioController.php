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

    // Borra una pregunta del propio usuario. Lo llama JavaScript (fetch) y responde en JSON.
    public function eliminarPregunta() {
        header('Content-Type: application/json; charset=utf-8');

        if (empty($_SESSION['usuario_id'])) {
            http_response_code(401);
            echo json_encode(['error' => 'Sesión no iniciada']);
            exit;
        }
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => 'Método no permitido']);
            exit;
        }

        $uid = (int) $_SESSION['usuario_id'];
        $id  = (int) ($_POST['id'] ?? 0);

        // Solo se puede borrar una pregunta que sea tuya
        $pregunta = AergibideModel::getPregunta($id, $uid);
        if ($pregunta === null || (int) $pregunta['usuario_id'] !== $uid) {
            http_response_code(403);
            echo json_encode(['error' => 'No puedes borrar esta pregunta']);
            exit;
        }

        try {
            AergibideModel::eliminarPregunta($id);
            echo json_encode(['ok' => true]);
        } catch (Throwable $e) {
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);   // temporal: para ver el fallo real
        }
        exit;
    }
}