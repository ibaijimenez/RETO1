<?php
// Requerimos el modelo para poder hacer la consulta directa desde la vista
require_once __DIR__ . '/../models/AergibideModel.php';

$pagina_activa  = 'inicio';
$usuarioId      = (int)($_SESSION['usuario_id'] ?? 0);
$nombre_usuario = $_SESSION['nombre_usuario'] ?? '';

// 1. Consultamos las preguntas reales a la BD usando la función existente en AergibideModel
$preguntasBD = AergibideModel::getPreguntas($usuarioId, 'todas', '', 'likes');

// 2. Mapeamos los campos para que encajen exactamente con las variables de tu HTML
$preguntas = array_map(function($p) {
    return [
        'id'        => $p['id'],
        'titulo'    => $p['titulo'],
        'etiquetas' => !empty($p['temas']) ? explode('||', $p['temas']) : [],
        'autor'     => $p['autor'],
        'likes'     => (int)$p['likes'],
        'fecha'     => date('d/m/Y', strtotime($p['fecha_creacion'])),
        'me_gusta'  => (bool)$p['me_gusta'],
        'guardada'  => (bool)$p['guardada'],
    ];
}, $preguntasBD);

$usuarios = ['Ana Torres', 'Pablo López', 'Elena Ruiz'];
$img = 'img/main/'; 

require __DIR__ . '/layout/head.php';
require __DIR__ . '/layout/sidebar.php';
?>

<main class="inicio">

    <!-- Banner con el logo -->
    <img class="bannerLogo" src="img/main/logoticoAiregibide.png" alt="Aergibide SL">

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

    <!-- Banner de visión -->
    <img class="bannerVision" src="img/main/vision.png" alt="Nuestra visión de ingeniería: construyendo el futuro aeroespacial con precisión, colaboración y un enfoque en la calidad">

    <!-- Preguntas y usuarios -->
    <div class="inicioColumnas">

        <section class="columnaPreguntas">
            <h2 class="tituloSeccion">PREGUNTAS DESTACADAS</h2>

            <?php foreach ($preguntas as $p): ?>
                <span class="etiquetaTipo">PREGUNTA</span>

                <article class="tarjetaPregunta" data-id="<?= $p['id']; ?>">

                    <div class="tarjetaCabecera">
                        <h3><?= htmlspecialchars($p['titulo']); ?></h3>

                        <button type="button" class="btnToggle btnGuardar" data-tipo="guardar"
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
                                    aria-pressed="<?= $p['me_gusta'] ? 'true' : 'false'; ?>" aria-label="Me gusta">
                                <img src="<?= $img . ($p['me_gusta'] ? 'coraConRelleno.png' : 'coraSinRelleno.png'); ?>" alt="">
                            </button>
                            <span><span class="numLikes"><?= (int)$p['likes']; ?></span> likes</span>
                        </div>

                        <div class="datoPie">
                            <img class="iconoCalendario" src="<?= $img; ?>calendario 1.png" alt="">
                            <span><?= htmlspecialchars($p['fecha']); ?></span>
                        </div>
                    </div>

                </article>
            <?php endforeach; ?>
        </section>

        <section class="columnaUsuarios">
            <h2 class="tituloSeccion">USUARIOS</h2>

            <?php foreach ($usuarios as $nombre): ?>
                <div class="usuarioDestacado">
                    <img src="<?= $img; ?>iconoPerfil.png" alt="">
                    <span><?= htmlspecialchars($nombre); ?></span>
                </div>
            <?php endforeach; ?>
        </section>
    </div>

</main>

<script src="JavaScript/main.js"></script>
</body>
</html>