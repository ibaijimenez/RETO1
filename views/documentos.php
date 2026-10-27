<?php
$pagina_activa = 'documentos';
$base = 'index.php?controller=DocumentosController&accion=documentos';

// Vienen del controller: $documentos, $conteos, $categorias, $categoria
// Solo si el formulario volvió con errores: $errores, $titulo, $descripcion, $categoriaElegida
$errores          = $errores          ?? [];
$titulo           = $titulo           ?? '';
$descripcion      = $descripcion      ?? '';
$categoriaElegida = $categoriaElegida ?? '';

// Color, texto e icono de cada categoría
$infoCat = [
    'Informes técnicos'    => ['clase' => 'catVerde',    'texto' => 'Estudios y análisis de ingeniería.',          'icono' => 'documento'],
    'Manuales'             => ['clase' => 'catAzul',     'texto' => 'Manuales de uso y mantenimiento.',            'icono' => 'libro'],
    'Ensayos y resultados' => ['clase' => 'catMorado',   'texto' => 'Mediciones, pruebas y resultados.',           'icono' => 'grafico'],
    'Planos y diseños'     => ['clase' => 'catAmarillo', 'texto' => 'Planos, modelos y archivos CAD.',             'icono' => 'cubo'],
];

$iconos = [
    'documento' => '<svg viewBox="0 0 24 24" width="30" height="30" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M7 3h7l5 5v13H7z"/><path d="M14 3v5h5"/><path d="M10 13h6M10 17h6"/></svg>',
    'libro'     => '<svg viewBox="0 0 24 24" width="30" height="30" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 6c-2-1.5-5-2-8-2v14c3 0 6 .5 8 2 2-1.5 5-2 8-2V4c-3 0-6 .5-8 2z"/><path d="M12 6v14"/></svg>',
    'grafico'   => '<svg viewBox="0 0 24 24" width="30" height="30" fill="currentColor"><rect x="4" y="12" width="4" height="8" rx="1"/><rect x="10" y="8" width="4" height="12" rx="1"/><rect x="16" y="4" width="4" height="16" rx="1"/></svg>',
    'cubo'      => '<svg viewBox="0 0 24 24" width="30" height="30" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3l8 4.5v9L12 21l-8-4.5v-9z"/><path d="M4 7.5l8 4.5 8-4.5M12 12v9"/></svg>',
];

// Tipo de archivo a partir de la extensión: [texto de la columna, texto del icono, clase de color]
if (!function_exists('tipoArchivo')) {
    function tipoArchivo(string $nombre): array
    {
        $ext = strtolower(pathinfo($nombre, PATHINFO_EXTENSION));
        switch ($ext) {
            case 'pdf':  return ['PDF',   'PDF', 'archPdf'];
            case 'xls':
            case 'xlsx': return ['EXCEL', 'XLS', 'archExcel'];
            case 'doc':
            case 'docx': return ['DOCX',  'DOC', 'archDoc'];
            case 'png':
            case 'jpg':
            case 'jpeg': return ['IMAGEN', 'IMG', 'archImg'];
            default:     return [strtoupper($ext) ?: 'ARCHIVO', 'ARCH', 'archOtro'];
        }
    }
}

require __DIR__ . '/layout/head.php';
require __DIR__ . '/layout/sidebar.php';
?>

<main class="inicio documentos">

    <div class="cabeceraPreguntas">
        <div>
            <h1>Mis documentos</h1>
            <p class="subtituloPreguntas">Consulta y comparte documentación técnica con el equipo de ingeniería.</p>
        </div>
        <button type="button" class="btnNuevaPregunta" data-abrir-modal>+ Subir documento</button>
    </div>

    <!-- Categorías -->
    <h2 class="tituloDocs">Categorías</h2>

    <div class="gridCategorias">
        <?php foreach ($infoCat as $nombreCat => $c): ?>
            <?php $total = (int) ($conteos[$nombreCat] ?? 0); ?>
            <a class="tarjetaCategoria <?= $c['clase']; ?> <?= $categoria === $nombreCat ? 'catActiva' : ''; ?>"
               href="<?= $base; ?>&categoria=<?= urlencode($nombreCat); ?>">
                <span class="iconoCategoria"><?= $iconos[$c['icono']]; ?></span>
                <h3><?= htmlspecialchars($nombreCat); ?></h3>
                <p><?= htmlspecialchars($c['texto']); ?></p>
                <span class="totalCategoria">
                    <?= $total; ?> <?= $total === 1 ? 'documento' : 'documentos'; ?>
                    <span class="flechaCategoria">&rsaquo;</span>
                </span>
            </a>
        <?php endforeach; ?>
    </div>

    <!-- Tabla de documentos -->
    <div class="cabeceraTabla">
        <h2 class="tituloDocs">
            <?= $categoria === '' ? 'Documentos recientes' : htmlspecialchars($categoria); ?>
        </h2>
        <?php if ($categoria !== ''): ?>
            <a class="verTodos" href="<?= $base; ?>">Ver todos</a>
        <?php endif; ?>
    </div>

    <?php if (empty($documentos)): ?>
        <p class="sinResultados">No hay documentos en esta sección.</p>
    <?php else: ?>
        <div class="tablaWrap">
            <table class="tablaDocs">
                <thead>
                    <tr>
                        <th>Nombre de documento</th>
                        <th>Categoría</th>
                        <th>Tipo</th>
                        <th>Autor</th>
                        <th>Fecha</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($documentos as $d): ?>
                        <?php
                            [$tipoTexto, $tipoIcono, $tipoClase] = tipoArchivo($d['nombre_archivo']);
                            $claseCat = $infoCat[$d['categoria']]['clase'] ?? 'catVerde';
                        ?>
                        <tr>
                            <td>
                                <div class="celdaNombre">
                                    <span class="iconoArchivo <?= $tipoClase; ?>"><?= $tipoIcono; ?></span>
                                    <div>
                                        <strong><?= htmlspecialchars($d['titulo']); ?></strong>
                                        <?php if (!empty($d['descripcion'])): ?>
                                            <span class="descDoc"><?= htmlspecialchars($d['descripcion']); ?></span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </td>
                            <td><span class="pillCategoria <?= $claseCat; ?>"><?= htmlspecialchars($d['categoria']); ?></span></td>
                            <td class="textoGris"><?= htmlspecialchars($tipoTexto); ?></td>
                            <td>
                                <div class="celdaAutor">
                                    <img src="img/main/iconoPerfil.png" alt="">
                                    <span><?= htmlspecialchars($d['autor']); ?></span>
                                </div>
                            </td>
                            <td class="textoGris"><?= date('d/m/Y', strtotime($d['fecha_subida'])); ?></td>
                            <td>
                                <a class="btnDescargarIcono" title="Descargar"
                                   href="index.php?controller=DocumentosController&accion=descargar&id=<?= (int) $d['id']; ?>">
                                    <svg viewBox="0 0 24 24" width="26" height="26" aria-hidden="true">
                                        <path d="M12 4v11M7 11l5 5 5-5M5 20h14" fill="none" stroke="#6B7280"
                                              stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>


    <!-- Ventana "Subir nuevo documento" -->
    <dialog id="modalSubir" class="modalSubir" <?= !empty($errores) ? 'data-abrir="1"' : ''; ?>>

        <button type="button" class="cerrarModal" data-cerrar-modal aria-label="Cerrar">&times;</button>

        <h2>Subir nuevo documento</h2>

        <form method="POST" enctype="multipart/form-data"
              action="index.php?controller=DocumentosController&accion=subirDocumento">

            <?php if (!empty($errores)): ?>
                <div class="erroresForm">
                    <?php foreach ($errores as $error): ?>
                        <p><?= htmlspecialchars($error); ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <label>Área de Carga de Archivos</label>
            <div class="zonaCarga">
                <input type="file" id="archivo" name="archivo" required
                       accept=".pdf,.doc,.docx,.xls,.xlsx">
                <svg viewBox="0 0 64 64" width="84" height="84" aria-hidden="true">
                    <path d="M16 6h22l12 12v36a2 2 0 0 1-2 2H16a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2z"
                          fill="none" stroke="#6B7280" stroke-width="3" stroke-linejoin="round"/>
                    <path d="M38 6v12h12M22 28h20M22 36h14" fill="none" stroke="#6B7280"
                          stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
                    <circle cx="44" cy="46" r="11" fill="#6B7280"/>
                    <path d="M44 52V40M39 45l5-5 5 5" fill="none" stroke="white"
                          stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <p>Arrastre su archivo aquí o haga clic para seleccionar</p>
                <p>Tamaño máximo: 50MB. Tipos permitidos: PDF, DOCX, XLSX</p>
                <p class="archivoElegido" id="archivoElegido"></p>
            </div>

            <label for="titulo">Nombre del Documento</label>
            <input type="text" id="titulo" name="titulo" maxlength="200" required
                   placeholder="Ingrese el nombre..." value="<?= htmlspecialchars($titulo); ?>">

            <label for="descripcion">Descripción</label>
            <input type="text" id="descripcion" name="descripcion"
                   placeholder="Detalle el contenido del documento..." value="<?= htmlspecialchars($descripcion); ?>">

            <div class="filaDos">
                <div>
                    <label for="categoria">Categoría</label>
                    <select id="categoria" name="categoria" required>
                        <option value="">Elige una categoría</option>
                        <?php foreach ($categorias as $cat): ?>
                            <option value="<?= htmlspecialchars($cat); ?>" <?= $categoriaElegida === $cat ? 'selected' : ''; ?>>
                                <?= htmlspecialchars($cat); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label for="tipo">Tipo</label>
                    <input type="text" id="tipo" value="" placeholder="Según el archivo" disabled>
                </div>
            </div>

            <div class="filaDos">
                <div>
                    <label for="autor">Autor</label>
                    <input type="text" id="autor" value="<?= htmlspecialchars($_SESSION['nombre'] ?? ''); ?>" disabled>
                </div>
                <div>
                    <label for="fecha">Fecha</label>
                    <input type="text" id="fecha" value="<?= date('d/m/Y'); ?>" disabled>
                </div>
            </div>

            <div class="botonesModal">
                <button type="button" class="btnCancelarModal" data-cerrar-modal>Cancelar</button>
                <button type="submit" class="btnSubirModal">Subir Documento</button>
            </div>

        </form>
    </dialog>

</main>

<script src="JavaScript/documentos.js"></script>
</body>
</html>