<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/AergibideModel.php'; 

class loginController extends BaseController {

    public function index() {
        require_once __DIR__ . '/../views/login.php';
    }

    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $user = $_POST['usuario'] ?? '';
            $pass = $_POST['contrasena'] ?? '';

            $usuario = AergibideModel::comprobarCredenciales($user, $pass);

            if ($usuario) {
                $_SESSION['usuario_id'] = $usuario->id;
                $_SESSION['nombre']     = $usuario->nombre;
                header('Location: index.php?controller=AergibideController&accion=inicio');
                exit;
            }

            $error = "Usuario o contraseña incorrectos";
            require __DIR__ . '/../views/login.php';
            exit;
        }
    }
    public function logout() {
        session_destroy();
        header('Location: index.php');
        exit;
    }
}