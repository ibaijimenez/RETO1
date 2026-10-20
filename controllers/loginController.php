<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/AergibideModel.php'; 

class loginController extends BaseController {

    public function index() {
        require_once __DIR__ . '/../views/login.php';
    }

    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $usuario = $_POST['usuario'] ?? null;
            $contrasena = $_POST['contrasena'] ?? null;

            $user = AergibideModel::comprobarCredenciales($usuario, $contrasena);

            if ($user) {
                session_start();
                $_SESSION['usuario'] = $user;
                header('Location: views/main.php');
                exit();
            } else {
                $error = "Usuario o contraseña incorrectos";
                // Limpiamos todo el HTML que el Router haya dibujado antes
                if (ob_get_length()) {
                    ob_clean();
                }
                require __DIR__ . '/../views/login.php';
                exit();
            }
        }
    }
}