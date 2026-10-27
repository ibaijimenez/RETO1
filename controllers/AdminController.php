<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/AergibideModel.php';

class AdminController extends BaseController {

    private function soloAdmin() {
        if (($_SESSION['rol'] ?? '') !== 'admin') {
            header('Location: index.php?controller=AergibideController&accion=inicio');
            exit;
        }
    }

    public function usuarios() {
        $this->soloAdmin();
        $usuarios = AergibideModel::getUsuarios();
        require __DIR__ . '/../views/admin/usuarios.php';
    }

    public function preguntasUsuario() {
        $this->soloAdmin();
        $id = (int) ($_GET['id'] ?? 0);

        $usuarioVisto = AergibideModel::getUsuario($id);
        if (!$usuarioVisto) {
            header('Location: index.php?controller=AdminController&accion=usuarios');
            exit;
        }

        $preguntas  = AergibideModel::getPreguntas($id, 'mias');
        $respuestas = AergibideModel::getRespuestasPorUsuario($id);
        require __DIR__ . '/../views/admin/preguntasUsuario.php';
    }

    public function crearUsuario() {
        $this->soloAdmin();
        $error = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nombre = trim($_POST['nombre'] ?? '');
            $email  = trim($_POST['email'] ?? '');
            $passwd = $_POST['passwd'] ?? '';
            $rol    = $_POST['rol'] ?? 'user';

            if ($nombre === '' || $email === '' || $passwd === '') {
                $error = 'Rellena todos los campos';
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $error = 'El email no es válido';
            } elseif (!in_array($rol, ['admin', 'user'], true)) {
                $error = 'Rol no válido';
            } else {
                try {
                    // Sin password_hash: tu login compara en texto plano
                    AergibideModel::crearUsuario($nombre, $email, $passwd, $rol);
                    header('Location: index.php?controller=AdminController&accion=usuarios');
                    exit;
                } catch (PDOException $e) {
                    $error = ($e->getCode() == 23000)
                        ? 'Ese email o nombre ya está registrado'
                        : 'Error al crear el usuario';
                }
            }
        }
        require __DIR__ . '/../views/admin/crearUsuario.php';
    }

    public function eliminarPregunta() {
        $this->soloAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?controller=AdminController&accion=usuarios');
            exit;
        }

        $id = (int) ($_POST['id'] ?? 0);
        $autorId = AergibideModel::eliminarPregunta($id);

        if ($autorId) {
            header('Location: index.php?controller=AdminController&accion=preguntasUsuario&id=' . $autorId);
        } else {
            header('Location: index.php?controller=AdminController&accion=usuarios');
        }
        exit;
    }

    public function eliminarRespuesta() {
        $this->soloAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?controller=AdminController&accion=usuarios');
            exit;
        }

        $id = (int) ($_POST['id'] ?? 0);
        $autorId = AergibideModel::eliminarRespuesta($id);

        if ($autorId) {
            header('Location: index.php?controller=AdminController&accion=preguntasUsuario&id=' . $autorId);
        } else {
            header('Location: index.php?controller=AdminController&accion=usuarios');
        }
        exit;
    }
}