<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/AergibideModel.php';

class registroController extends BaseController {

    public function index() {
        require_once __DIR__ . '/../views/registrar.php';
    }

    public function registrar() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?controller=registroController&accion=index');
            exit;
        }

        $nombre     = trim($_POST['nombre'] ?? '');
        $apellidos  = trim($_POST['apellidos'] ?? '');
        $correo     = trim($_POST['correo'] ?? '');
        $contrasena = trim($_POST['contrasena'] ?? '');
        $confirmar  = trim($_POST['confirmar'] ?? '');

        if ($contrasena !== $confirmar) {
            $error = "Las contraseñas no coinciden";
            require __DIR__ . '/../views/registrar.php';
            exit;
        }

        if (!preg_match("/^[A-Za-zÁÉÍÓÚáéíóúÑñ\s]+$/", $nombre) || !preg_match("/^[A-Za-zÁÉÍÓÚáéíóúÑñ\s]+$/", $apellidos)) {
            $error = "El nombre o los apellidos contienen caracteres no válidos";
            require __DIR__ . '/../views/registrar.php';
            exit;
        }

        $datos = [
            'nombre'     => $nombre,
            'apellidos'  => $apellidos,
            'correo'     => $correo,
            'contrasena' => $contrasena
        ];

        try {
            AergibideModel::create($datos);
            header('Location: index.php?controller=loginController&accion=index');
            exit;
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                $error = "Ese correo ya está registrado";
            } else {
                $error = "Error: " . $e->getMessage();
            }
            require __DIR__ . '/../views/registrar.php';
            exit;
        }
    }
}