<?php
$pagina_activa = 'preguntas';
$img = 'img/main/';


require __DIR__ . '/layout/head.php';
require __DIR__ . '/layout/sidebar.php';
?>

<main class="inicio preguntas">

    <div class="cabeceraPreguntas">
        <div>
            <h1>Preguntas</h1>
            <p class="subtituloPreguntas">Aquí puedes consultar todas las preguntas de la comunidad.</p>
        </div>
        <a href="index.php?controller=PreguntasController&accion=nuevaPregunta" class="btnNuevaPregunta">+ Nueva pregunta</a>
    </div>

    <form method="GET" action="index.php">
        <input type="hidden" name="controller" value="PreguntasController">
        <input type="hidden" name="accion" value="preguntas">
        <input type="hidden" name="filtro" value="<?= htmlspecialchars($filtro); ?>">

        <div class="buscador">
            <input type="search" name="q" value="<?= htmlspecialchars($busqueda); ?>"
                placeholder="Buscar preguntas,temas, usuarios....">
        </div>

        <div class="filtros">
            <div class="filtrosBotones">
                <a href="index.php?controller=PreguntasController&accion=preguntas&filtro=todas"
                class="pill <?= $filtro === 'todas' ? 'pillActiva' : ''; ?>">TODAS</a>
                <a href="index.php?controller=PreguntasController&accion=preguntas&filtro=mias"
                class="pill <?= $filtro === 'mias' ? 'pillActiva' : ''; ?>">MIS PREGUNTAS</a>
            </div>

            <select name="orden" class="selectOrden" onchange="this.form.submit()">
                <option value="recientes"  <?= $orden === 'recientes'  ? 'selected' : ''; ?>>Más recientes</option>
                <option value="likes"      <?= $orden === 'likes'      ? 'selected' : ''; ?>>Más likes</option>
                <option value="respuestas" <?= $orden === 'respuestas' ? 'selected' : ''; ?>>Más respuestas</option>
            </select>
        </div>
    </form>


    <?php if (empty($preguntas)): ?>
        <p class="sinResultados">Todavía no hay preguntas.</p>
    <?php endif; ?>

    <?php foreach ($preguntas as $p): ?>
        <div class="filaPregunta">

            <div class="cajaRespuestas">
                <img src="img/preguntas/preguntas_blanco.png" alt="">
                <span><?= (int)($p['respuestas'] ?? 0); ?> respuestas</span>
            </div>

            <article class="tarjetaPregunta">
                <div class="tarjetaCabecera">
                    <h3><?= htmlspecialchars($p['titulo']); ?></h3>
                    <button type="button" class="btnToggle btnGuardar" data-tipo="guardar"
                            data-id="<?= (int)$p['id']; ?>"
                            aria-pressed="<?= !empty($p['guardada']) ? 'true' : 'false'; ?>" aria-label="Guardar pregunta">
                        <img src="<?= $img . (!empty($p['guardada']) ? 'guardadoConRelleno.png' : 'guardadoSinRelleno.png'); ?>" alt="">
                    </button>
                </div>

            <div class="etiquetas">
                <?php foreach (array_filter(explode('||', $p['temas'] ?? '')) as $tema): ?>
                    <span class="etiqueta"><?= htmlspecialchars($tema); ?></span>
                <?php endforeach; ?>
            </div>
                <div class="tarjetaPie">
                    <div class="datoPie">
                        <img class="avatarMini" src="<?= $img; ?>iconoPerfil.png" alt="">
                        <span><?= htmlspecialchars($p['autor'] ?? ''); ?></span>
                    </div>
                    <div class="datoPie">
                        <button type="button" class="btnToggle btnLike" data-tipo="like"
                                data-id="<?= (int)$p['id']; ?>"
                                aria-pressed="<?= !empty($p['me_gusta']) ? 'true' : 'false'; ?>" aria-label="Me gusta">
                            <img src="<?= $img . (!empty($p['me_gusta']) ? 'coraConRelleno.png' : 'coraSinRelleno.png'); ?>" alt="">
                        </button>
                        <span><span class="numLikes"><?= (int)($p['likes'] ?? 0); ?></span> likes</span>
                    </div>
                    <div class="datoPie">
                        <img class="iconoCalendario" src="<?= $img; ?>calendario 1.png" alt="">
                        <span><?= date('d/m/Y', strtotime($p['fecha_creacion'])); ?></span>
                    </div>
                </div>
            </article>

        </div>
    <?php endforeach; ?>

</main>

<script src="JavaScript/main.js"></script>
</body>
</html>