<?php
$pagina_activa = 'favoritos';
$img    = 'img/main/';
$imgDet = 'img/preguntas/';
$base   = 'index.php?controller=FavoritosController&accion=favoritos';

$corazon = '<img class="iconoOff" src="' . $imgDet . 'coraSinRelleno.png" alt="">'
         . '<img class="iconoOn"  src="' . $imgDet . 'coraConRelleno.png" alt="">';

require __DIR__ . '/layout/head.php';
require __DIR__ . '/layout/sidebar.php';
?>
<main class="inicio favoritos">

    <div class="cabeceraFavoritos">
        <div class="iconoFavoritos">
            <img src="img/favoritos/corazon_favoritos.png" alt="">
        </div>
        <div>
            <h1>Favoritos</h1>
            <p class="subtituloPreguntas">Aquí tienes las preguntas y respuestas que te han gustado.</p>
        </div>
    </div>

    <form method="GET" action="index.php">
        <input type="hidden" name="controller" value="FavoritosController">
        <input type="hidden" name="accion" value="favoritos">
        <input type="hidden" name="filtro" value="<?= htmlspecialchars($filtro); ?>">

        <div class="filtros">
            <div class="filtrosBotones">
                <a href="<?= $base; ?>&filtro=todas&orden=<?= $orden; ?>"
                   class="pill <?= $filtro === 'todas' ? 'pillActiva' : ''; ?>">TODAS (<?= $total; ?>)</a>
                <a href="<?= $base; ?>&filtro=preguntas&orden=<?= $orden; ?>"
                   class="pill <?= $filtro === 'preguntas' ? 'pillActiva' : ''; ?>">Preguntas (<?= $totalPreguntas; ?>)</a>
                <a href="<?= $base; ?>&filtro=respuestas&orden=<?= $orden; ?>"
                   class="pill <?= $filtro === 'respuestas' ? 'pillActiva' : ''; ?>">Respuestas (<?= $totalRespuestas; ?>)</a>
            </div>

            <select name="orden" class="selectOrden" onchange="this.form.submit()">
                <option value="recientes" <?= $orden === 'recientes' ? 'selected' : ''; ?>>Más recientes</option>
                <option value="likes"     <?= $orden === 'likes'     ? 'selected' : ''; ?>>Más likes</option>
            </select>
        </div>
    </form>

    <?php if (empty($preguntas) && empty($respuestas)): ?>
        <p class="sinResultados">Todavía no tienes favoritos en esta sección.</p>
    <?php endif; ?>

    <?php foreach ($preguntas as $p): ?>
        <?php $urlDetalle = 'index.php?controller=PreguntasController&accion=detalle&id=' . (int)$p['id']; ?>
        <span class="etiquetaTipo">PREGUNTA</span>
        <article class="tarjetaPregunta tarjetaFavorito">
            <div class="tarjetaCabecera">
                <h3><a href="<?= $urlDetalle; ?>"><?= htmlspecialchars($p['titulo']); ?></a></h3>
                <button type="button" class="btnToggle btnGuardar" data-tipo="guardar"
                        data-id="<?= (int)$p['id']; ?>"
                        aria-pressed="<?= !empty($p['guardada']) ? 'true' : 'false'; ?>" aria-label="Guardar pregunta">
                    <img src="<?= $img . (!empty($p['guardada']) ? 'guardadoConRelleno.png' : 'guardadoSinRelleno.png'); ?>" alt="">
                </button>
            </div>

            <div class="etiquetas">
                <?php foreach (array_filter(explode('||', $p['temas'] ?? '')) as $etiqueta): ?>
                    <span class="etiqueta"><?= htmlspecialchars($etiqueta); ?></span>
                <?php endforeach; ?>
            </div>

            <div class="tarjetaPie">
                <div class="datoPie">
                    <img class="avatarMini" src="<?= $img; ?>iconoPerfil.png" alt="">
                    <span><?= htmlspecialchars($p['autor']); ?></span>
                </div>
                <div class="datoPie">
                    <button type="button" class="btnToggle btnLike" data-tipo="like"
                            data-id="<?= (int)$p['id']; ?>"
                            aria-pressed="<?= !empty($p['me_gusta']) ? 'true' : 'false'; ?>" aria-label="Me gusta">
                        <img src="<?= $img . (!empty($p['me_gusta']) ? 'coraConRelleno.png' : 'coraSinRelleno.png'); ?>" alt="">
                    </button>
                    <span><span class="numLikes"><?= (int)$p['likes']; ?></span> likes</span>
                </div>
                <div class="datoPie">
                    <img class="iconoCalendario" src="<?= $img; ?>calendario 1.png" alt="">
                    <span><?= date('d/m/Y', strtotime($p['fecha_creacion'])); ?></span>
                </div>
            </div>
        </article>
    <?php endforeach; ?>

    <?php foreach ($respuestas as $r): ?>
        <?php $urlResp = 'index.php?controller=PreguntasController&accion=detalle&id=' . (int)$r['pregunta_id'] . '#respuestas'; ?>
        <span class="etiquetaTipo">RESPUESTA</span>
        <article class="tarjetaPregunta tarjetaFavorito">
            <p class="respuestaEn">
                En la pregunta: <a href="<?= $urlResp; ?>"><?= htmlspecialchars($r['pregunta_titulo']); ?></a>
            </p>

            <p class="respuestaFavTexto"><?= nl2br(htmlspecialchars($r['contenido'])); ?></p>

            <div class="tarjetaPie">
                <div class="datoPie">
                    <img class="avatarMini" src="<?= $img; ?>iconoPerfil.png" alt="">
                    <span><?= htmlspecialchars($r['autor']); ?></span>
                </div>
                <div class="datoPie">
                    <button type="button" class="btnAccion btnCorazon" data-tipo="respuesta_like"
                            data-id="<?= (int)$r['id']; ?>" aria-pressed="true"
                            aria-label="Me gusta la respuesta"><?= $corazon; ?></button>
                    <span><span class="numLikes"><?= (int)$r['likes']; ?></span> likes</span>
                </div>
                <div class="datoPie">
                    <img class="iconoCalendario" src="<?= $img; ?>calendario 1.png" alt="">
                    <span><?= date('d/m/Y', strtotime($r['fecha_creacion'])); ?></span>
                </div>
            </div>
        </article>
    <?php endforeach; ?>

</main>

<script src="JavaScript/main.js"></script>
<script src="JavaScript/detalle.js"></script>
</body>
</html>