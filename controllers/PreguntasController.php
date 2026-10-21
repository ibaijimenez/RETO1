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

        // Los temas que se pueden elegir en los desplegables
        $temasDisponibles = AergibideModel::getTemas();

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
        $elegidos    = $_POST['temas'] ?? [];

        $temasDisponibles = AergibideModel::getTemas();

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

        // 2. De lo elegido, quedarnos solo con temas que existen y sin repetir
        $idsExistentes = array_column($temasDisponibles, 'id');
        $seleccionados = [];
        foreach ($elegidos as $id) {
            $id = (int) $id;
            if (in_array($id, $idsExistentes) && !in_array($id, $seleccionados)) {
                $seleccionados[] = $id;
            }
        }
        $seleccionados = array_slice($seleccionados, 0, 3);

        // 3. Guardar (si no hay errores)
        if (empty($errores)) {
            try {
                AergibideModel::crearPregunta((int) $_SESSION['usuario_id'], $titulo, $descripcion, $seleccionados);

                // Todo bien: volver a la lista (la nueva sale la primera)
                header('Location: index.php?controller=PreguntasController&accion=preguntas');
                exit;
            } catch (Throwable $e) {
                $errores[] = 'No se ha podido guardar la pregunta. Inténtalo de nuevo.';
                // Para depurar, quita las // de la línea de abajo:
                // $errores[] = $e->getMessage();
            }
        }

        // 4. Si hay errores, volver a mostrar el formulario con lo que ya había escrito
        require __DIR__ . '/../views/nuevaPregunta.php';
    }

    // Detalle de una pregunta con sus respuestas
    public function detalle() {
        $this->comprobarSesion();

        $id       = (int) ($_GET['id'] ?? 0);
        $pregunta = AergibideModel::getPregunta($id, (int) $_SESSION['usuario_id']);

        // Si no existe, volvemos a la lista
        if ($pregunta === null) {
            header('Location: index.php?controller=PreguntasController&accion=preguntas');
            exit;
        }

        $respuestas = AergibideModel::getRespuestas($id, (int) $_SESSION['usuario_id']);

        require __DIR__ . '/../views/detallePregunta.php';
    }

    // Recibe el formulario de respuesta, valida, guarda y vuelve al detalle
    public function publicarRespuesta() {
        $this->comprobarSesion();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?controller=PreguntasController&accion=preguntas');
            exit;
        }

        $id        = (int) ($_POST['pregunta_id'] ?? 0);
        $contenido = trim($_POST['contenido'] ?? '');

        $pregunta = AergibideModel::getPregunta($id, (int) $_SESSION['usuario_id']);
        if ($pregunta === null) {
            header('Location: index.php?controller=PreguntasController&accion=preguntas');
            exit;
        }

        $errores = [];
        if ($contenido === '') {
            $errores[] = 'La respuesta no puede estar vacía.';
        }

        if (empty($errores)) {
            try {
                AergibideModel::crearRespuesta($id, (int) $_SESSION['usuario_id'], $contenido);
                header('Location: index.php?controller=PreguntasController&accion=detalle&id=' . $id . '#respuestas');
                exit;
            } catch (Throwable $e) {
                $errores[] = 'No se ha podido guardar la respuesta. Inténtalo de nuevo.';
                // Para depurar, quita las // de la línea de abajo:
                // $errores[] = $e->getMessage();
            }
        }

        // Con errores: volver a mostrar el detalle conservando lo escrito
        $respuestas = AergibideModel::getRespuestas($id, (int) $_SESSION['usuario_id']);
        require __DIR__ . '/../views/detallePregunta.php';
    }

    // Like / guardado / respuesta favorita. Lo llama JavaScript (fetch) y responde en JSON.
    public function alternar() {
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

        $tipo = $_POST['tipo'] ?? '';
        $id   = (int) ($_POST['id'] ?? 0);

        try {
            $resultado = AergibideModel::alternar($tipo, $id, (int) $_SESSION['usuario_id']);
            echo json_encode($resultado);
        } catch (InvalidArgumentException $e) {
            http_response_code(400);
            echo json_encode(['error' => 'Petición no válida']);
        } catch (Throwable $e) {
            http_response_code(500);
            echo json_encode(['error' => 'No se ha podido guardar']);
            // Para depurar: echo json_encode(['error' => $e->getMessage()]);
        }
        exit;
    }
}