<?php
$pagina_activa = 'preguntas';

// Vienen del controller: $pregunta, $titulo, $descripcion, $seleccionados, $temasDisponibles, $errores
$errores          = $errores          ?? [];
$titulo           = $titulo           ?? '';
$descripcion      = $descripcion      ?? '';
$seleccionados    = $seleccionados    ?? [];
$temasDisponibles = $temasDisponibles ?? [];

$rutaPreguntas = 'index.php?controller=PreguntasController&accion=preguntas';
$rutaDetalle   = 'index.php?controller=PreguntasController&accion=detalle&id=' . (int) $pregunta['id'];

require __DIR__ . '/layout/head.php';
require __DIR__ . '/layout/sidebar.php';
?>

<main class="inicio nuevaPregunta">

    <!-- Migas de pan -->
    <nav class="migas">
        <a href="<?= $rutaPreguntas; ?>">Preguntas</a>
        <span>/</span>
        <a href="<?= $rutaDetalle; ?>">Detalle pregunta</a>
        <span>/</span>
        <span>Editar</span>
    </nav>

    <h1>Editar pregunta</h1>
    <p class="subtituloPreguntas">
        Cambia el título, la descripción o las etiquetas de tu pregunta.
    </p>

    <form class="formPregunta" method="POST"
          action="index.php?controller=PreguntasController&accion=actualizarPregunta">

        <?php if (!empty($errores)): ?>
            <div class="erroresForm">
                <?php foreach ($errores as $error): ?>
                    <p><?= htmlspecialchars($error); ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <input type="hidden" name="pregunta_id" value="<?= (int) $pregunta['id']; ?>">

        <label for="titulo">Título de la pregunta <span class="obligatorio">*</span></label>
        <input type="text" id="titulo" name="titulo" maxlength="200" required
               value="<?= htmlspecialchars($titulo); ?>">

        <label for="descripcion">Descripción del problema <span class="obligatorio">*</span></label>
        <textarea id="descripcion" name="descripcion" required><?= htmlspecialchars($descripcion); ?></textarea>

        <label>Etiquetas</label>
        <div class="filaTemas">
            <?php for ($i = 0; $i < 3; $i++): ?>
                <select name="temas[]" class="selectTema">
                    <option value="">Elige una etiqueta</option>
                    <?php foreach ($temasDisponibles as $t): ?>
                        <option value="<?= (int) $t['id']; ?>"
                            <?= (isset($seleccionados[$i]) && (int) $seleccionados[$i] === (int) $t['id']) ? 'selected' : ''; ?>>
                            <?= htmlspecialchars($t['nombre']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            <?php endfor; ?>
        </div>
        <p class="ayudaForm">Puedes elegir hasta 3 etiquetas.</p>

        <div class="pieForm">
            <p class="avisoForm">Los campos marcados con <span class="obligatorio">*</span> son obligatorios.</p>

            <div class="botonesForm">
                <a href="<?= $rutaDetalle; ?>" class="btnCancelar">Cancelar</a>
                <button type="submit" class="btnPublicar">Guardar cambios</button>
            </div>
        </div>

    </form>

</main>

</body>
</html>