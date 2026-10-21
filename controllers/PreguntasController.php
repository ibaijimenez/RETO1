<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/AergibideModel.php';

class PreguntasController extends BaseController {

    // Lista de preguntas (antes estaba en AergibideController)
    public function preguntas() {
        $this->comprobarSesion();

        $filtro   = ($_GET['filtro'] ?? 'todas') === 'mias' ? 'mias' : 'todas';
        $busqueda = trim($_GET['q'] ?? '');
        $orden    = $_GET['orden'] ?? 'recientes';

        $preguntas = AergibideModel::getPreguntas((int) $_SESSION['usuario_id'], $filtro, $busqueda, $orden);

        require __DIR__ . '/../views/preguntas.php';
    }

    // Muestra el formulario vacío
    public function nuevaPregunta() {
        $this->comprobarSesion();
        require __DIR__ . '/../views/nuevaPregunta.php';
    }

    // Recibe el formulario, valida, guarda y vuelve a Preguntas
    public function publicarPregunta() {
        $this->comprobarSesion();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?controller=PreguntasController&accion=nuevaPregunta');
            exit;
        }

        $titulo      = trim($_POST['titulo'] ?? '');
        $descripcion = trim($_POST['descripcion'] ?? '');
        $etiquetas   = trim($_POST['etiquetas'] ?? '');

        // 1. Validar
        $errores = [];
        if ($titulo === '') {
            $errores[] = 'El título es obligatorio.';
        } elseif (mb_strlen($titulo) > 200) {
            $errores[] = 'El título no puede tener más de 200 caracteres.';
        }
        if ($descripcion === '') {
            $errores[] = 'La descripción es obligatoria.';
        }

        // 2. Preparar las etiquetas: separadas por comas, sin repetir, máximo 5
        $temas = [];
        foreach (explode(',', $etiquetas) as $t) {
            $t = mb_substr(trim($t), 0, 50);
            if ($t !== '' && !in_array(mb_strtolower($t), array_map('mb_strtolower', $temas))) {
                $temas[] = $t;
            }
        }
        $temas = array_slice($temas, 0, 5);

        // 3. Guardar (si no hay errores)
        if (empty($errores)) {
            try {
                AergibideModel::crearPregunta((int) $_SESSION['usuario_id'], $titulo, $descripcion, $temas);

                // Todo bien: volver a la lista (la nueva sale la primera)
                header('Location: index.php?controller=PreguntasController&accion=preguntas');
                exit;
            } catch (Throwable $e) {
                $errores[] = 'No se ha podido guardar la pregunta. Inténtalo de nuevo.';
            }
        }

        // 4. Si hay errores, volver a mostrar el formulario con lo que ya había escrito
        require __DIR__ . '/../views/nuevaPregunta.php';
    }
}