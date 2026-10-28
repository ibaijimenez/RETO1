<?php
// Requerimos el modelo para poder hacer la consulta directa desde la vista
require_once __DIR__ . '/../models/AergibideModel.php';

$pagina_activa  = 'inicio';
$usuarioId      = (int) ($_SESSION['usuario_id'] ?? 0);
$nombre_usuario = $_SESSION['nombre'] ?? '';

// 1. Las 5 preguntas con más likes
$preguntasBD = AergibideModel::getPreguntas($usuarioId, 'todas', '', 'likes');
$preguntasBD = array_slice($preguntasBD, 0, 5);

// 2. Adaptamos los campos para la vista
$preguntas = array_map(function ($p) {
    return [
        'id'        => $p['id'],
        'titulo'    => $p['titulo'],
        'etiquetas' => !empty($p['temas']) ? explode('||', $p['temas']) : [],
        'autor'     => $p['autor'],
        'likes'     => (int) $p['likes'],
        'fecha'     => date('d/m/Y', strtotime($p['fecha_creacion'])),
        'me_gusta'  => (bool) $p['me_gusta'],
        'guardada'  => (bool) $p['guardada'],
    ];
}, $preguntasBD);

$img = 'img/main/';

require __DIR__ . '/layout/head.php';
require __DIR__ . '/layout/sidebar.php';
?>

<main class="inicio">
    <!-- Banner de bienvenida -->
    <section class="bannerHola">
        <div class="bannerHolaTexto">
            <h2><span class="manoSaludo">👋</span> ¡Hola<?= $nombre_usuario !== '' ? ', ' . htmlspecialchars($nombre_usuario) : ''; ?>!</h2>
            <p class="holaPrimera">Bienvenido a tu panel de AERGIBIDE SL</p>
            <p>
                Aquí puedes consultar todas las opciones y
                mantenerte al día con tus preguntas, documentos
                y novedades
            </p>
        </div>
    </section>

    <!-- Preguntas destacadas -->
    <section class="columnaPreguntas">
        <h2 class="tituloSeccion">PREGUNTAS DESTACADAS</h2>

        <?php if (empty($preguntas)): ?>
            <p class="sinResultados">Todavía no hay preguntas.</p>
        <?php endif; ?>

        <?php foreach ($preguntas as $p): ?>
            <?php $urlDetalle = 'index.php?controller=PreguntasController&accion=detalle&id=' . (int) $p['id']; ?>

            <article class="tarjetaPregunta">

                <div class="tarjetaCabecera">
                    <h3><a href="<?= $urlDetalle; ?>"><?= htmlspecialchars($p['titulo']); ?></a></h3>

                    <button type="button" class="btnToggle btnGuardar" data-tipo="guardar"
                            data-id="<?= (int) $p['id']; ?>"
                            aria-pressed="<?= $p['guardada'] ? 'true' : 'false'; ?>" aria-label="Guardar pregunta">
                        <img src="<?= $img . ($p['guardada'] ? 'guardadoConRelleno.png' : 'guardadoSinRelleno.png'); ?>" alt="">
                    </button>
                </div>

                <div class="etiquetas">
                    <?php foreach ($p['etiquetas'] as $etiqueta): ?>
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
                                data-id="<?= (int) $p['id']; ?>"
                                aria-pressed="<?= $p['me_gusta'] ? 'true' : 'false'; ?>" aria-label="Me gusta">
                            <img src="<?= $img . ($p['me_gusta'] ? 'coraConRelleno.png' : 'coraSinRelleno.png'); ?>" alt="">
                        </button>
                        <span><span class="numLikes"><?= (int) $p['likes']; ?></span> likes</span>
                    </div>

                    <div class="datoPie">
                        <img class="iconoCalendario" src="<?= $img; ?>calendario 1.png" alt="">
                        <span><?= htmlspecialchars($p['fecha']); ?></span>
                    </div>
                </div>

            </article>
        <?php endforeach; ?>

        <p class="verMas">
            <a href="index.php?controller=PreguntasController&accion=preguntas">Ver todas las preguntas &rsaquo;</a>
        </p>
    </section>

    <?php require __DIR__ . '/layout/footer.php'; ?>
</main>

<script src="JavaScript/main.js"></script>
</body>
</html>