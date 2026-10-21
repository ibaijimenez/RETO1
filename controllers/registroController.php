<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/AergibideModel.php'; 

class registrarController extends BaseController {

    public function index() {
        require_once __DIR__ . '/../views/registrar.php';
    }

    public function registrar() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $nombre       = trim($_POST['nombre'] ?? '');
            $apellidos    = trim($_POST['apellidos'] ?? '');
            $correo       = trim($_POST['correo'] ?? '');
            $contrasena   = trim($_POST['contrasena'] ?? '');
            $confirmar    = trim($_POST['confirmar'] ?? '');
            $departamento = $_POST['departamento'] ?? '';

            if ($contrasena !== $confirmar) {//comprobar contraeñas iguales
                $error = "Las contraseñas no coinciden";
                require __DIR__ . '/../views/registrar.php';
                exit;
            }

            //para nombre y apellido
            if (!preg_match("/^[A-Za-zÁÉÍÓÚáéíóúÑñ\s]+$/", $nombre) || !preg_match("/^[A-Za-zÁÉÍÓÚáéíóúÑñ\s]+$/", $apellidos)) {
                $error = "El nombre o los apellidos contienen caracteres no válidos";
                require __DIR__ . '/../views/registrar.php';
                exit;
            }

            $datos = [
                'nombre'       => $nombre,
                'apellidos'    => $apellidos,
                'correo'       => $correo,
                'contraseña'   => $contrasena,
                'departamento' => $departamento
            ];

            //guardar bd
            $exito = AergibideModel::create($datos);

            if ($exito) {
                header('Location: index.php?controller=loginController&action=index');
                exit;
            } else {
                $error = "No se pudo registrar el usuario en la base de datos";
                require __DIR__ . '/../views/registrar.php';
                exit;
            }
        }
    }
}