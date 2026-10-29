<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/AergibideModel.php';

class PreguntasController extends BaseController {

    // Lista de preguntas con buscador, orden y filtro por temas
    public function preguntas() {
        $this->comprobarSesion();

        $filtro   = ($_GET['filtro'] ?? 'todas') === 'mias' ? 'mias' : 'todas';
        $busqueda = trim($_GET['q'] ?? '');
        $orden    = $_GET['orden'] ?? 'recientes';

        // Temas marcados: solo números, sin repetir ni ceros
        $temasSel = array_values(array_unique(array_filter(array_map('intval', (array) ($_GET['temas'] ?? [])))));

        // Todos los temas, para pintar las casillas
        $temasDisponibles = AergibideModel::getTemas();

        $preguntas = AergibideModel::getPreguntas((int) $_SESSION['usuario_id'], $filtro, $busqueda, $orden, $temasSel);

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

        // Validar y quedarnos solo con los temas que existen
        [$errores, $seleccionados] = $this->validarPregunta($titulo, $descripcion, $elegidos, $temasDisponibles);

        // Guardar (si no hay errores)
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

        // Si hay errores, volver a mostrar el formulario con lo que ya había escrito
        require __DIR__ . '/../views/nuevaPregunta.php';
    }

    // Detalle de una pregunta con sus temas y sus respuestas
    public function detalle() {
        $this->comprobarSesion();

        $this->mostrarDetalle((int) ($_GET['id'] ?? 0));
    }

    // Muestra el formulario para editar una pregunta (solo si es tuya)
    public function editarPregunta() {
        $this->comprobarSesion();

        $id       = (int) ($_GET['id'] ?? 0);
        $pregunta = $this->cargarPreguntaPropia($id);

        // Valores actuales de la pregunta
        $titulo           = $pregunta['titulo'];
        $descripcion      = $pregunta['descripcion'];
        $seleccionados    = array_map(function ($t) { return (int) $t['id']; }, AergibideModel::getTemasDePregunta($id));
        $temasDisponibles = AergibideModel::getTemas();
        $errores          = [];

        require __DIR__ . '/../views/editarPregunta.php';
    }

    // Recibe el formulario de edición, valida, guarda y vuelve al detalle
    public function actualizarPregunta() {
        $this->comprobarSesion();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?controller=PreguntasController&accion=preguntas');
            exit;
        }

        $id       = (int) ($_POST['pregunta_id'] ?? 0);
        $pregunta = $this->cargarPreguntaPropia($id);   // si no es tuya, te saca de aquí

        $titulo      = trim($_POST['titulo'] ?? '');
        $descripcion = trim($_POST['descripcion'] ?? '');
        $elegidos    = $_POST['temas'] ?? [];

        $temasDisponibles = AergibideModel::getTemas();

        [$errores, $seleccionados] = $this->validarPregunta($titulo, $descripcion, $elegidos, $temasDisponibles);

        if (empty($errores)) {
            try {
                $guardada = AergibideModel::actualizarPregunta($id, (int) $_SESSION['usuario_id'], $titulo, $descripcion, $seleccionados);

                if ($guardada) {
                    header('Location: index.php?controller=PreguntasController&accion=detalle&id=' . $id);
                    exit;
                }
                $errores[] = 'No puedes editar esta pregunta.';
            } catch (Throwable $e) {
                $errores[] = 'No se ha podido guardar los cambios. Inténtalo de nuevo.';
                // Para depurar, quita las // de la línea de abajo:
                // $errores[] = $e->getMessage();
            }
        }

        // Con errores: volver al formulario conservando lo escrito
        require __DIR__ . '/../views/editarPregunta.php';
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
        $this->mostrarDetalle($id, $errores, $contenido);
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

    // ==================================================================
    // Funciones de apoyo (no son páginas, solo las usan las de arriba)
    // ==================================================================

    // Prepara todo lo que necesita la página de detalle y la muestra
    private function mostrarDetalle(int $id, array $errores = [], string $contenido = '') {
        $usuarioId = (int) $_SESSION['usuario_id'];

        $pregunta = AergibideModel::getPregunta($id, $usuarioId);

        // Si no existe, volvemos a la lista
        if ($pregunta === null) {
            header('Location: index.php?controller=PreguntasController&accion=preguntas');
            exit;
        }

        $respuestas    = AergibideModel::getRespuestas($id, $usuarioId);
        $temasPregunta = AergibideModel::getTemasDePregunta($id);
        $esMia         = ((int) $pregunta['usuario_id'] === $usuarioId);

        require __DIR__ . '/../views/detallePregunta.php';
    }

    // Devuelve la pregunta si es del usuario. Si no existe o es de otro, redirige y corta.
    private function cargarPreguntaPropia(int $id): array {
        $usuarioId = (int) $_SESSION['usuario_id'];
        $pregunta  = AergibideModel::getPregunta($id, $usuarioId);

        if ($pregunta === null) {
            header('Location: index.php?controller=PreguntasController&accion=preguntas');
            exit;
        }
        if ((int) $pregunta['usuario_id'] !== $usuarioId) {
            header('Location: index.php?controller=PreguntasController&accion=detalle&id=' . $id);
            exit;
        }
        return $pregunta;
    }

    // Valida título y descripción y filtra los temas elegidos.
    // Devuelve [lista de errores, lista de ids de temas válidos (máximo 3)]
    private function validarPregunta(string $titulo, string $descripcion, array $elegidos, array $temasDisponibles): array {
        $errores = [];

        if ($titulo === '') {
            $errores[] = 'El título es obligatorio.';
        } elseif (mb_strlen($titulo) > 200) {
            $errores[] = 'El título no puede tener más de 200 caracteres.';
        }
        if ($descripcion === '') {
            $errores[] = 'La descripción es obligatoria.';
        }

        // Solo temas que existen y sin repetir
        $idsExistentes = array_column($temasDisponibles, 'id');
        $seleccionados = [];
        foreach ($elegidos as $id) {
            $id = (int) $id;
            if (in_array($id, $idsExistentes) && !in_array($id, $seleccionados)) {
                $seleccionados[] = $id;
            }
        }
        $seleccionados = array_slice($seleccionados, 0, 3);

        return [$errores, $seleccionados];
    }
}