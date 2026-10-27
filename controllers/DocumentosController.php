<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/AergibideModel.php';

class DocumentosController extends BaseController {

    // Categorías permitidas (tienen que ser iguales que en el ENUM de la tabla documentos)
    private $categorias = ['Informes técnicos', 'Manuales', 'Ensayos y resultados', 'Planos y diseños'];

    // Extensiones permitidas y tamaño máximo (50 MB, como en el diseño)
    private $extensionesPermitidas = ['pdf', 'doc', 'docx', 'xls', 'xlsx'];
    private $tamanoMaximo = 52428800;

    // Página de documentos: categorías + documentos recientes
    public function documentos() {
        $this->comprobarSesion();
        $this->mostrarLista();
    }

    // Prepara los datos y carga la vista (la usan documentos() y subirDocumento() si hay errores)
    private function mostrarLista($errores = [], $titulo = '', $descripcion = '', $categoriaElegida = '') {
        $categorias = $this->categorias;

        // Categoría por la que se está filtrando (si hay)
        $categoria = $_GET['categoria'] ?? '';
        if (!in_array($categoria, $categorias, true)) {
            $categoria = '';
        }

        // Sin filtro: solo los 10 más recientes. Con filtro: todos los de esa categoría.
        $documentos = AergibideModel::getDocumentos($categoria, $categoria === '' ? 10 : null);
        $conteos    = AergibideModel::getConteoCategorias();

        require __DIR__ . '/../views/documentos.php';
    }

    // Recibe el formulario del modal, valida, guarda el archivo y vuelve a Documentos
    public function subirDocumento() {
        $this->comprobarSesion();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?controller=DocumentosController&accion=documentos');
            exit;
        }

        $titulo           = trim($_POST['titulo'] ?? '');
        $descripcion      = trim($_POST['descripcion'] ?? '');
        $categoriaElegida = $_POST['categoria'] ?? '';
        $archivo          = $_FILES['archivo'] ?? null;
        $extension        = '';

        // 1. Validar los textos
        $errores = [];
        if ($titulo === '') {
            $errores[] = 'El nombre del documento es obligatorio.';
        } elseif (mb_strlen($titulo) > 200) {
            $errores[] = 'El nombre no puede tener más de 200 caracteres.';
        }
        if (!in_array($categoriaElegida, $this->categorias, true)) {
            $errores[] = 'Elige una categoría.';
        }

        // 2. Validar el archivo
        if (!$archivo || $archivo['error'] === UPLOAD_ERR_NO_FILE) {
            $errores[] = 'Selecciona un archivo.';
        } elseif ($archivo['error'] === UPLOAD_ERR_INI_SIZE || $archivo['error'] === UPLOAD_ERR_FORM_SIZE) {
            $errores[] = 'El archivo es demasiado grande.';
        } elseif ($archivo['error'] !== UPLOAD_ERR_OK) {
            $errores[] = 'No se ha podido subir el archivo.';
        } elseif ($archivo['size'] > $this->tamanoMaximo) {
            $errores[] = 'El archivo no puede pesar más de 50 MB.';
        } else {
            $extension = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));
            if (!in_array($extension, $this->extensionesPermitidas)) {
                $errores[] = 'Tipo de archivo no permitido. Puedes subir: PDF, DOC, DOCX, XLS o XLSX.';
            }
        }

        // 3. Guardar (si no hay errores)
        if (empty($errores)) {
            $carpeta = __DIR__ . '/../uploads/docs/';
            if (!is_dir($carpeta)) {
                mkdir($carpeta, 0775, true);
            }

            // Nombre propio y aleatorio, así nadie puede subir un archivo con un nombre peligroso
            $nombreGuardado = 'doc_' . bin2hex(random_bytes(8)) . '.' . $extension;
            $destino        = $carpeta . $nombreGuardado;

            if (move_uploaded_file($archivo['tmp_name'], $destino)) {
                $finfo = new finfo(FILEINFO_MIME_TYPE);
                $mime  = $finfo->file($destino) ?: 'application/octet-stream';

                try {
                    AergibideModel::crearDocumento(
                        (int) $_SESSION['usuario_id'],
                        $categoriaElegida,
                        $titulo,
                        $descripcion,
                        mb_substr(basename($archivo['name']), 0, 255),
                        'uploads/docs/' . $nombreGuardado,
                        $mime
                    );

                    header('Location: index.php?controller=DocumentosController&accion=documentos');
                    exit;
                } catch (Throwable $e) {
                    unlink($destino);   // si falla la base de datos, no dejamos el archivo huérfano
                    $errores[] = 'No se ha podido guardar el documento. Inténtalo de nuevo.';
                    // Para depurar, quita las // de la línea de abajo:
                    // $errores[] = $e->getMessage();
                }
            } else {
                $errores[] = 'No se ha podido guardar el archivo en el servidor.';
            }
        }

        // 4. Si hay errores, volver a mostrar la página con el modal abierto y lo que ya había escrito
        $this->mostrarLista($errores, $titulo, $descripcion, $categoriaElegida);
    }

    // Descarga un documento
    public function descargar() {
        $this->comprobarSesion();

        $id        = (int) ($_GET['id'] ?? 0);
        $documento = AergibideModel::getDocumento($id);

        if ($documento === null) {
            http_response_code(404);
            echo 'Documento no encontrado.';
            exit;
        }

        $ruta = __DIR__ . '/../' . ltrim($documento['ruta_almacenamiento'], '/\\');

        if (!is_file($ruta)) {
            http_response_code(404);
            echo 'El archivo de este documento no está disponible.';
            exit;
        }

        $nombre = str_replace('"', '', basename($documento['nombre_archivo']));

        header('Content-Type: ' . $documento['tipo_mime']);
        header('Content-Disposition: attachment; filename="' . $nombre . '"');
        header('Content-Length: ' . filesize($ruta));
        readfile($ruta);
        exit;
    }
}