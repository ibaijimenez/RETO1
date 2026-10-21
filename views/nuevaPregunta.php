<?php
$pagina_activa = 'preguntas';

// Valores por si el formulario vuelve con errores (así no se pierde lo escrito)
$errores          = $errores          ?? [];
$titulo           = $titulo           ?? '';
$descripcion      = $descripcion      ?? '';
$seleccionados    = $seleccionados    ?? [];
$temasDisponibles = $temasDisponibles ?? [];

$rutaPreguntas = 'index.php?controller=PreguntasController&accion=preguntas';

require __DIR__ . '/layout/head.php';
require __DIR__ . '/layout/sidebar.php';
?>

<main class="inicio nuevaPregunta">

    <!-- Migas de pan -->
    <nav class="migas">
        <a href="<?= $rutaPreguntas; ?>">Preguntas</a>
        <span>/</span>
        <span>Nueva pregunta</span>
    </nav>

    <h1>Nueva pregunta</h1>
    <p class="subtituloPreguntas">
        Comparte tu duda con el equipo de ingeniería y encuentra soluciones en comunidad.
    </p>

    <form class="formPregunta" method="POST"
          action="index.php?controller=PreguntasController&accion=publicarPregunta">

        <?php if (!empty($errores)): ?>
            <div class="erroresForm">
                <?php foreach ($errores as $error): ?>
                    <p><?= htmlspecialchars($error); ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <label for="titulo">Título de la pregunta <span class="obligatorio">*</span></label>
        <input type="text" id="titulo" name="titulo" maxlength="200" required
               placeholder="Ej: ¿Cómo reducir las vibraciones en una turbina?"
               value="<?= htmlspecialchars($titulo); ?>">

        <label for="descripcion">Descripción del problema <span class="obligatorio">*</span></label>
        <textarea id="descripcion" name="descripcion" required
                  placeholder="Explica tu duda, las pruebas realizadas y los resultados obtenidos...."><?= htmlspecialchars($descripcion); ?></textarea>

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
                <a href="<?= $rutaPreguntas; ?>" class="btnCancelar">Cancelar</a>
                <button type="submit" class="btnPublicar">
                    <svg viewBox="0 0 24 24" width="26" height="26" aria-hidden="true">
                        <path d="M21 3L3 10.5l6.5 2.5L12 20l2.5-6.5L21 3zM9.5 13L21 3"
                              fill="none" stroke="white" stroke-width="1.6" stroke-linejoin="round" stroke-linecap="round"/>
                    </svg>
                    Publicar pregunta
                </button>
            </div>
        </div>

    </form>

</main>

</body>
</html>