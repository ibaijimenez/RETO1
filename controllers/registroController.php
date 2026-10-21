<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/AergibideModel.php';

class registroController extends BaseController {

    public function index() {
        require __DIR__ . '/../views/registrar.php';
    }

    public function registrar() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('index.php?controller=registroController&accion=index');
        }

        $nombre       = trim($_POST['nombre'] ?? '');
        $apellidos    = trim($_POST['apellidos'] ?? '');
        $correo       = trim($_POST['correo'] ?? '');
        $contrasena   = $_POST['contrasena'] ?? '';
        $confirmar    = $_POST['confirmar'] ?? '';
        $departamento = $_POST['departamento'] ?? '';

        
        $soloLetras = '/^[A-Za-zÁÉÍÓÚÜáéíóúüÑñ\s]+$/u';
        $error = '';

        if (!preg_match($soloLetras, $nombre) || !preg_match($soloLetras, $apellidos)) {
            $error = 'El nombre y los apellidos solo pueden contener letras.';
        } elseif (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
            $error = 'El correo electrónico no es válido.';
        } elseif ($contrasena !== $confirmar) {
            $error = 'Las contraseñas no coinciden.';
        } elseif (!in_array($departamento, ['ingenieria', 'calidad', 'produccion', 'administracion'], true)) {
            $error = 'Selecciona un departamento válido.';
        } elseif (AergibideModel::existeCorreo($correo)) {
            $error = 'Ya existe una cuenta con ese correo.';
        }

        // todo bien se gurada
        if ($error === '') {
            try {
                AergibideModel::create([
                    'nombre'       => $nombre,
                    'apellidos'    => $apellidos,
                    'correo'       => $correo,
                    'contrasena'   => $contrasena,
                    'departamento' => $departamento,
                ]);
                $this->redirect('index.php?controller=loginController&accion=index');
            } catch (Throwable $e) {
                $error = 'No se pudo registrar el usuario en la base de datos.';
            }
        }

        require __DIR__ . '/../views/registrar.php';
    }
}
