<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/AergibideModel.php';

class PreguntasController extends BaseController {

    // Archivos adjuntos: extensiones permitidas, tamaño máximo por archivo y cuántos por pregunta/respuesta
    private $extensionesAdjuntos = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'png', 'jpg', 'jpeg', 'txt'];
    private $tamanoMaxAdjunto    = 10485760;   // 10 MB
    private $maxAdjuntos         = 5;

    // Lista de preguntas con buscador, orden y filtro por temas
    public function preguntas() {
        $this->comprobarSesion();

        $filtro   = ($_GET['filtro'] ?? 'todas') === 'mias' ? 'mias' : 'todas';
        $busqueda = trim($_GET['q'] ?? '');
        $orden    = $_GET['orden'] ?? 'recientes';

        // Temas marcados: solo números, sin repetir ni ceros
        $temasSel = array_values(array_unique(array_filter(array_map('intval', (array) ($_GET['temas'] ?? [])))));

        // Todos los temas, para pintar las casillas del filtro
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

        // Validar los archivos adjuntos (input name="archivos[]")
        [$errAdj, $adjuntos] = $this->validarAdjuntos('archivos');
        $errores = array_merge($errores, $errAdj);

        // Guardar (si no hay errores)
        if (empty($errores)) {
            try {
                $preguntaId = AergibideModel::crearPregunta((int) $_SESSION['usuario_id'], $titulo, $descripcion, $seleccionados);
                $this->guardarArchivosPregunta($adjuntos, $preguntaId);

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

    // Descarga un archivo de una respuesta (tabla adjuntos)
    public function descargarAdjunto() {
        $this->comprobarSesion();

        $adjunto = AergibideModel::getAdjunto((int) ($_GET['id'] ?? 0));
        if ($adjunto === null) {
            $this->noEncontrado();
        }
        $this->enviarArchivo(
            __DIR__ . '/../' . ltrim($adjunto['ruta_almacenamiento'], '/\\'),
            $adjunto['nombre_original']
        );
    }

    // Descarga un archivo de una pregunta (carpeta uploads/preguntas/<id>/)
    public function descargarArchivoPregunta() {
        $this->comprobarSesion();

        $id      = (int) ($_GET['id'] ?? 0);
        $archivo = basename($_GET['f'] ?? '');   // basename impide rutas tipo ../../
        if ($id <= 0 || $archivo === '') {
            $this->noEncontrado();
        }

        $pos    = strpos($archivo, '__');
        $nombre = $pos !== false ? substr($archivo, $pos + 2) : $archivo;
        $this->enviarArchivo($this->carpetaPregunta($id) . $archivo, $nombre);
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
        $adjuntosPregunta = $this->adjuntosDePregunta($id);

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

        // Archivos nuevos que se añaden a la pregunta (input name="archivos[]")
        [$errAdj, $adjuntos] = $this->validarAdjuntos('archivos');
        $errores = array_merge($errores, $errAdj);

        if (empty($errores)) {
            try {
                $guardada = AergibideModel::actualizarPregunta($id, (int) $_SESSION['usuario_id'], $titulo, $descripcion, $seleccionados);

                if ($guardada) {
                    $this->borrarArchivosPregunta($id, (array) ($_POST['borrar'] ?? []));
                    $this->guardarArchivosPregunta($adjuntos, $id);
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
        $adjuntosPregunta = $this->adjuntosDePregunta($id);
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

        // Archivos adjuntos de la respuesta (input name="archivos[]")
        [$errAdj, $adjuntos] = $this->validarAdjuntos('archivos');
        $errores = array_merge($errores, $errAdj);

        if (empty($errores)) {
            try {
                $respuestaId = AergibideModel::crearRespuesta($id, (int) $_SESSION['usuario_id'], $contenido);
                $this->guardarArchivosRespuesta($adjuntos, $respuestaId);

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

        $respuestas         = AergibideModel::getRespuestas($id, $usuarioId);
        $temasPregunta      = AergibideModel::getTemasDePregunta($id);
        $esMia              = ((int) $pregunta['usuario_id'] === $usuarioId);
        $adjuntosPregunta   = $this->adjuntosDePregunta($id);
        $adjuntosRespuestas = $this->adjuntosDeRespuestas($id);

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

    // ------------------------------------------------------------------
    // Archivos adjuntos
    // ------------------------------------------------------------------

    // Valida los archivos de un input múltiple. Devuelve [errores, archivos válidos]
    private function validarAdjuntos(string $campo): array {
        $errores = [];
        $validos = [];

        if (empty($_FILES[$campo]) || !is_array($_FILES[$campo]['name'])) {
            return [$errores, $validos];
        }
        $f = $_FILES[$campo];

        foreach ($f['name'] as $i => $nombreOriginal) {
            if ($f['error'][$i] === UPLOAD_ERR_NO_FILE) {
                continue;
            }
            $nombre = basename($nombreOriginal);

            if ($f['error'][$i] === UPLOAD_ERR_INI_SIZE || $f['error'][$i] === UPLOAD_ERR_FORM_SIZE) {
                $errores[] = "«$nombre» es demasiado grande (máximo 10 MB).";
                continue;
            }
            if ($f['error'][$i] !== UPLOAD_ERR_OK) {
                $errores[] = "No se ha podido subir «$nombre».";
                continue;
            }
            if ($f['size'][$i] > $this->tamanoMaxAdjunto) {
                $errores[] = "«$nombre» pesa más de 10 MB.";
                continue;
            }
            $ext = strtolower(pathinfo($nombre, PATHINFO_EXTENSION));
            if (!in_array($ext, $this->extensionesAdjuntos, true)) {
                $errores[] = "«$nombre»: tipo de archivo no permitido.";
                continue;
            }

            $validos[] = ['tmp' => $f['tmp_name'][$i], 'nombre' => $nombre, 'ext' => $ext, 'size' => (int) $f['size'][$i]];
        }

        if (count($validos) > $this->maxAdjuntos) {
            $errores[] = 'Puedes adjuntar como máximo ' . $this->maxAdjuntos . ' archivos.';
        }

        return [$errores, $validos];
    }

    private function noEncontrado() {
        http_response_code(404);
        echo 'Archivo no encontrado.';
        exit;
    }

    // Envía un archivo: las imágenes se ven en la página, el resto se descarga
    private function enviarArchivo(string $ruta, string $nombre) {
        if (!is_file($ruta)) {
            $this->noEncontrado();
        }
        $mime    = (new finfo(FILEINFO_MIME_TYPE))->file($ruta) ?: 'application/octet-stream';
        $enLinea = in_array($mime, ['image/png', 'image/jpeg'], true);
        $nombre  = str_replace(['"', "\r", "\n"], '', basename($nombre));

        header('Content-Type: ' . $mime);
        header('X-Content-Type-Options: nosniff');
        header('Content-Disposition: ' . ($enLinea ? 'inline' : 'attachment') . '; filename="' . $nombre . '"');
        header('Content-Length: ' . filesize($ruta));
        readfile($ruta);
        exit;
    }

    private function carpetaPregunta(int $id): string {
        return __DIR__ . '/../uploads/preguntas/' . $id . '/';
    }

    // Guarda los archivos de una pregunta en uploads/preguntas/<id>/
    // Nombre en disco: adj_<aleatorio>__<nombre original limpio>
    private function guardarArchivosPregunta(array $archivos, int $preguntaId): void {
        if (empty($archivos)) {
            return;
        }
        $carpeta = $this->carpetaPregunta($preguntaId);
        if (!is_dir($carpeta)) {
            mkdir($carpeta, 0775, true);
        }
        foreach ($archivos as $a) {
            $limpio = preg_replace('/[^A-Za-z0-9._-]/', '_', $a['nombre']);
            $limpio = mb_substr($limpio, -100);   // por si el nombre es larguísimo
            move_uploaded_file($a['tmp'], $carpeta . 'adj_' . bin2hex(random_bytes(6)) . '__' . $limpio);
        }
    }

    // Guarda los archivos de una respuesta en uploads/adjuntos/ y los registra en la tabla adjuntos
    private function guardarArchivosRespuesta(array $archivos, int $respuestaId): void {
        if (empty($archivos)) {
            return;
        }
        $carpeta = __DIR__ . '/../uploads/adjuntos/';
        if (!is_dir($carpeta)) {
            mkdir($carpeta, 0775, true);
        }
        $finfo = new finfo(FILEINFO_MIME_TYPE);

        foreach ($archivos as $a) {
            $guardado = 'adj_' . bin2hex(random_bytes(8)) . '.' . $a['ext'];
            $destino  = $carpeta . $guardado;

            if (!move_uploaded_file($a['tmp'], $destino)) {
                continue;
            }
            $mime = $finfo->file($destino) ?: 'application/octet-stream';

            try {
                AergibideModel::crearAdjuntoRespuesta(
                    $respuestaId, mb_substr($a['nombre'], 0, 255), 'uploads/adjuntos/' . $guardado, $mime
                );
            } catch (Throwable $e) {
                unlink($destino);
            }
        }
    }

    // Borra de la carpeta de la pregunta los archivos marcados (basename impide salirse de la carpeta)
    private function borrarArchivosPregunta(int $preguntaId, array $archivos): void {
        foreach ($archivos as $archivo) {
            $ruta = $this->carpetaPregunta($preguntaId) . basename((string) $archivo);
            if (is_file($ruta)) {
                unlink($ruta);
            }
        }
    }

    // Lista los archivos de una pregunta leyendo su carpeta
    private function adjuntosDePregunta(int $id): array {
        $lista = [];
        $finfo = new finfo(FILEINFO_MIME_TYPE);

        foreach (glob($this->carpetaPregunta($id) . '*') ?: [] as $ruta) {
            if (!is_file($ruta)) {
                continue;
            }
            $archivo = basename($ruta);
            $pos     = strpos($archivo, '__');
            $lista[] = [
                'archivo'         => $archivo,   // nombre en disco (lo usa el formulario de editar para borrar)
                'url'             => 'index.php?controller=PreguntasController&accion=descargarArchivoPregunta&id=' . $id . '&f=' . urlencode($archivo),
                'nombre_original' => $pos !== false ? substr($archivo, $pos + 2) : $archivo,
                'tipo_mime'       => $finfo->file($ruta) ?: 'application/octet-stream',
                'tamano'          => filesize($ruta),
            ];
        }
        return $lista;
    }

    // Archivos de las respuestas, agrupados por respuesta: [respuesta_id => [archivos]]
    private function adjuntosDeRespuestas(int $preguntaId): array {
        $agrupados = [];
        foreach (AergibideModel::getAdjuntosRespuestas($preguntaId) as $a) {
            $ruta = __DIR__ . '/../' . ltrim($a['ruta_almacenamiento'], '/\\');
            if (!is_file($ruta)) {
                continue;   // p. ej. los datos de ejemplo del script SQL no tienen archivo real
            }
            $agrupados[(int) $a['respuesta_id']][] = [
                'url'             => 'index.php?controller=PreguntasController&accion=descargarAdjunto&id=' . (int) $a['id'],
                'nombre_original' => $a['nombre_original'],
                'tipo_mime'       => $a['tipo_mime'],
                'tamano'          => filesize($ruta),
            ];
        }
        return $agrupados;
    }
}