<?php
$pagina_activa = 'documentos';

// Valores por si el formulario vuelve con errores (así no se pierde lo escrito)
$errores           = $errores           ?? [];
$titulo            = $titulo            ?? '';
$descripcion       = $descripcion       ?? '';
$categoriaElegida  = $categoriaElegida  ?? '';
$categorias        = $categorias        ?? [];

$rutaDocumentos = 'index.php?controller=DocumentosController&accion=documentos';

require __DIR__ . '/layout/head.php';
require __DIR__ . '/layout/sidebar.php';
?>

<main class="inicio nuevaPregunta">

    <!-- Migas de pan -->
    <nav class="migas">
        <a href="<?= $rutaDocumentos; ?>">Documentos</a>
        <span>/</span>
        <span>Subir documento</span>
    </nav>

    <h1>Subir documento</h1>
    <p class="subtituloPreguntas">
        Comparte un documento técnico con el resto del equipo.
    </p>

    <form class="formPregunta" method="POST" enctype="multipart/form-data"
          action="index.php?controller=DocumentosController&accion=subirDocumento">

        <?php if (!empty($errores)): ?>
            <div class="erroresForm">
                <?php foreach ($errores as $error): ?>
                    <p><?= htmlspecialchars($error); ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <label for="titulo">Título del documento <span class="obligatorio">*</span></label>
        <input type="text" id="titulo" name="titulo" maxlength="200" required
               placeholder="Ej: Informe de ensayos de fatiga"
               value="<?= htmlspecialchars($titulo); ?>">

        <label for="categoria">Categoría <span class="obligatorio">*</span></label>
        <select id="categoria" name="categoria" class="selectCategoria" required>
            <option value="">Elige una categoría</option>
            <?php foreach ($categorias as $cat): ?>
                <option value="<?= htmlspecialchars($cat); ?>" <?= $categoriaElegida === $cat ? 'selected' : ''; ?>>
                    <?= htmlspecialchars($cat); ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label for="descripcion">Descripción</label>
        <textarea id="descripcion" name="descripcion"
                  placeholder="Explica de qué trata el documento (opcional)"><?= htmlspecialchars($descripcion); ?></textarea>

        <label for="archivo">Archivo <span class="obligatorio">*</span></label>
        <div class="cajaArchivo">
            <input type="file" id="archivo" name="archivo" required
                   accept=".pdf,.doc,.docx,.xls,.xlsx,.png,.jpg,.jpeg,.txt">
            <p>PDF, imágenes, documentos, excels... (máximo 10 MB)</p>
        </div>

        <div class="pieForm">
            <p class="avisoForm">Los campos marcados con <span class="obligatorio">*</span> son obligatorios.</p>

            <div class="botonesForm">
                <a href="<?= $rutaDocumentos; ?>" class="btnCancelar">Cancelar</a>
                <button type="submit" class="btnPublicar">
                    <svg viewBox="0 0 24 24" width="26" height="26" aria-hidden="true">
                        <path d="M21 3L3 10.5l6.5 2.5L12 20l2.5-6.5L21 3zM9.5 13L21 3"
                              fill="none" stroke="white" stroke-width="1.6" stroke-linejoin="round" stroke-linecap="round"/>
                    </svg>
                    Subir documento
                </button>
            </div>
        </div>

    </form>

</main>

</body>
</html>