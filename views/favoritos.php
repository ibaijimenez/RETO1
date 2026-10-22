<?php
$pagina_activa = 'favoritos';
$img = 'img/main/';

// $preguntas llega desde FavoritosController (BD)
$total = count($preguntas);

require __DIR__ . '/layout/head.php';
require __DIR__ . '/layout/sidebar.php';
?>

<main class="inicio favoritos">

    <!-- Cabecera -->
    <div class="cabeceraFavoritos">
        <div class="iconoFavoritos">
            <img src="img/favoritos/corazon_favoritos.png" alt="">
        </div>
        <div>
            <h1>Favoritos</h1>
            <p class="subtituloPreguntas">Aquí tienes todas las preguntas que has guardado como favoritas.</p>
        </div>
    </div>

    <!-- Filtros -->
    <div class="filtros">
        <div class="filtrosBotones">
            <a href="#" class="pill pillActiva">TODAS (<?= $total; ?>)</a>
            <a href="#" class="pill">Preguntas (<?= $total; ?>)</a>
            <a href="#" class="pill">Respuestas (<?= $total; ?>)</a>
        </div>

        <select class="selectOrden">
            <option>Selecciona</option>
            <option>Más recientes</option>
            <option>Más likes</option>
        </select>
    </div>

    <!-- Preguntas favoritas (desde la BD) -->
    <?php if (empty($preguntas)): ?>
        <p class="sinResultados">Todavía no tienes preguntas favoritas.</p>
    <?php endif; ?>

    <?php foreach ($preguntas as $p): ?>
        <article class="tarjetaPregunta tarjetaFavorito">

            <div class="tarjetaCabecera">
                <h3><?= htmlspecialchars($p['titulo']); ?></h3>
                <button type="button" class="btnToggle btnGuardar" data-tipo="guardar"
                        data-id="<?= (int)$p['id']; ?>"
                        aria-pressed="true" aria-label="Guardar pregunta">
                    <img src="<?= $img; ?>guardadoConRelleno.png" alt="">
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

</main>

<script src="JavaScript/main.js"></script>
</body>
</html>