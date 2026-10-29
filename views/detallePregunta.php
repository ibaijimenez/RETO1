<?php
$pagina_activa = 'preguntas';

// Valores por si el formulario de respuesta vuelve con errores
$errores       = $errores       ?? [];
$contenido     = $contenido     ?? '';
$respuestas    = $respuestas    ?? [];
$temasPregunta = $temasPregunta ?? [];
$esMia         = $esMia         ?? false;

$rutaPreguntas = 'index.php?controller=PreguntasController&accion=preguntas';

// "hace 2 días", "hace 3 h"...
if (!function_exists('haceTiempo')) {
    function haceTiempo(string $fecha): string
    {
        $seg = time() - strtotime($fecha);
        if ($seg < 60)    return 'hace un momento';
        if ($seg < 3600)  return 'hace ' . floor($seg / 60) . ' min';
        if ($seg < 86400) return 'hace ' . floor($seg / 3600) . ' h';
        $dias = (int) floor($seg / 86400);
        return 'hace ' . $dias . ($dias === 1 ? ' día' : ' días');
    }
}

// Iconos: tus imágenes de img/preguntas/
$rutaImg = 'img/preguntas/';
$avatarSvg = '<img src="' . $rutaImg . 'iconoPerfil.png" alt="">';
// Cada botón lleva dos imágenes (sin relleno / con relleno); el CSS enseña la que toca según aria-pressed
$corazonSvg = '<img class="iconoOff" src="' . $rutaImg . 'coraSinRelleno.png" alt="">'
            . '<img class="iconoOn"  src="' . $rutaImg . 'coraConRelleno.png" alt="">';
$marcadorSvg = '<img class="iconoOff" src="' . $rutaImg . 'guardadoSinRelleno.png" alt="">'
             . '<img class="iconoOn"  src="' . $rutaImg . 'guardadoConRelleno.png" alt="">';

require __DIR__ . '/layout/head.php';
require __DIR__ . '/layout/sidebar.php';
?>

<main class="inicio detallePregunta">

    <!-- Migas de pan -->
    <nav class="migas">
        <a href="<?= $rutaPreguntas; ?>">Preguntas</a>
        <span>/</span>
        <span>Detalle pregunta</span>
    </nav>

    <!-- Título y botón de editar (solo si la pregunta es tuya) -->
    <div class="cabeceraDetalle">
        <h1><?= htmlspecialchars($pregunta['titulo']); ?></h1>

        <?php if ($esMia): ?>
            <a class="btnEditar"
               href="index.php?controller=PreguntasController&accion=editarPregunta&id=<?= (int) $pregunta['id']; ?>">
                <svg viewBox="0 0 24 24" width="22" height="22" aria-hidden="true">
                    <path d="M4 20h4L19 9l-4-4L4 16v4zM13.5 6.5l4 4" fill="none" stroke="currentColor"
                          stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                Editar pregunta
            </a>
        <?php endif; ?>
    </div>

    <!-- Temas de la pregunta -->
    <?php if (!empty($temasPregunta)): ?>
        <div class="etiquetas etiquetasDetalle">
            <?php foreach ($temasPregunta as $tema): ?>
                <span class="etiqueta"><?= htmlspecialchars($tema['nombre']); ?></span>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <!-- Autor, likes, fecha y guardar -->
    <div class="metaDetalle">
        <span class="metaItem">
            <span class="avatarDetalle"><?= $avatarSvg; ?></span>
            <?= htmlspecialchars($pregunta['autor']); ?>
        </span>

        <span class="metaItem">
            <button type="button" class="btnAccion btnCorazon" data-tipo="pregunta_like"
                    data-id="<?= (int) $pregunta['id']; ?>"
                    aria-pressed="<?= !empty($pregunta['me_gusta']) ? 'true' : 'false'; ?>"
                    aria-label="Me gusta"><?= $corazonSvg; ?></button>
            <span><span class="numLikes"><?= (int) $pregunta['likes']; ?></span> likes</span>
        </span>

        <span class="metaItem">
            <img class="iconoCalendario" src="<?= $rutaImg; ?>calendario 1.png" alt="">
            Publicado el <?= date('d/m/Y', strtotime($pregunta['fecha_creacion'])); ?>
        </span>

        <button type="button" class="btnAccion btnGuardarTexto" data-tipo="pregunta_guardar"
                data-id="<?= (int) $pregunta['id']; ?>"
                aria-pressed="<?= !empty($pregunta['guardada']) ? 'true' : 'false'; ?>">
            <?= $marcadorSvg; ?>
            <span class="textoAccion"
                  data-on="Guardada en favoritos"
                  data-off="Guardar en favoritos"><?= !empty($pregunta['guardada']) ? 'Guardada en favoritos' : 'Guardar en favoritos'; ?></span>
        </button>
    </div>

    <!-- Descripción -->
    <section class="cajaDescripcion">
        <h2>Descripción del problema</h2>
        <p><?= nl2br(htmlspecialchars($pregunta['descripcion'])); ?></p>
    </section>

    <!-- Respuestas -->
    <h2 class="tituloDetalle" id="respuestas">Respuestas (<?= count($respuestas); ?>)</h2>

    <?php if (empty($respuestas)): ?>
        <p class="sinResultados">Todavía no hay respuestas. ¡Sé el primero en responder!</p>
    <?php else: ?>
        <?php foreach ($respuestas as $r): ?>
            <article class="tarjetaRespuesta">
                <div class="respuestaCabecera">
                    <span class="avatarDetalle avatarGrande"><?= $avatarSvg; ?></span>
                    <strong><?= htmlspecialchars($r['autor']); ?></strong>
                    <span class="respuestaFecha"><?= haceTiempo($r['fecha_creacion']); ?></span>
                </div>

                <p class="respuestaTexto"><?= nl2br(htmlspecialchars($r['contenido'])); ?></p>

                <div class="respuestaPie">
                    <span class="metaItem">
                        <button type="button" class="btnAccion btnCorazon" data-tipo="respuesta_like"
                                data-id="<?= (int) $r['id']; ?>"
                                aria-pressed="<?= !empty($r['me_gusta']) ? 'true' : 'false'; ?>"
                                aria-label="Me gusta la respuesta"><?= $corazonSvg; ?></button>
                        <span><span class="numLikes"><?= (int) $r['likes']; ?></span> likes</span>
                    </span>
                </div>
            </article>
        <?php endforeach; ?>
    <?php endif; ?>

    <!-- Escribir respuesta -->
    <h2 class="tituloDetalle">Escribe tu respuesta</h2>

    <form class="formRespuesta" method="POST"
          action="index.php?controller=PreguntasController&accion=publicarRespuesta">

        <?php if (!empty($errores)): ?>
            <div class="erroresForm">
                <?php foreach ($errores as $error): ?>
                    <p><?= htmlspecialchars($error); ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <input type="hidden" name="pregunta_id" value="<?= (int) $pregunta['id']; ?>">

        <textarea name="contenido" required
                  placeholder="Comparte tu propuesta o solución..."><?= htmlspecialchars($contenido); ?></textarea>

        <button type="submit" class="btnPublicar">
            <svg viewBox="0 0 24 24" width="26" height="26" aria-hidden="true">
                <path d="M21 3L3 10.5l6.5 2.5L12 20l2.5-6.5L21 3zM9.5 13L21 3"
                      fill="none" stroke="white" stroke-width="1.6" stroke-linejoin="round" stroke-linecap="round"/>
            </svg>
            Publicar respuesta
        </button>
    </form>

</main>

<script src="JavaScript/detalle.js"></script>
</body>
</html>