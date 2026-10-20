<?php

$pagina_activa  = 'inicio';
$nombre_usuario = '';

// Datos de ejemplo. Más adelante vendrán de la base de datos.
$preguntas = [
    [
        'titulo'  => '¿Cómo mejorar la precisión de los sensores de temperatura en ensayos de laboratorio?',
        'etiquetas' => ['Instrumentación', 'Sensores', 'Ensayos'],
        'autor'   => 'Elena Ruiz',
        'likes'   => 30,
        'fecha'   => '22/11/2026',
        'me_gusta' => true,
        'guardada' => true,
    ],
    [
        'titulo'  => '¿Cómo podemos reducir el peso de una estructura aeronáutica?',
        'etiquetas' => ['Materiales', 'Diseño estructural', 'Aeronáutica'],
        'autor'   => 'Ana Torres',
        'likes'   => 23,
        'fecha'   => '13/10/2026',
        'me_gusta' => false,
        'guardada' => false,
    ],
    [
        'titulo'  => '¿Alguien ha trabajado con el material Ti6Al4V en procesos de mecanizado?',
        'etiquetas' => ['Materiales', 'Mecanizado', 'Ti6Al4V'],
        'autor'   => 'Pablo López',
        'likes'   => 18,
        'fecha'   => '02/08/2026',
        'me_gusta' => false,
        'guardada' => false,
    ],
];

$usuarios = ['Ana Torres', 'Pablo López', 'Elena Ruiz'];

require __DIR__ . '/layout/head.php';
require __DIR__ . '/layout/sidebar.php';
?>

<main class="inicio">

    <!-- Banner con el logo -->
    <img class="bannerLogo" src="img/main/logoticoAiregibide.png" alt="Aergibide SL">

    <!-- Banner de bienvenida -->
    <section class="bannerHola">
        <div class="bannerHolaTexto">
            <h2><span class="manoSaludo">👋</span> ¡Hola<?= $nombre_usuario !== '' ? ', ' . htmlspecialchars($nombre_usuario) : ''; ?>!</h2>            <p class="holaPrimera">Bienvenido a tu panel de AERGIBIDE SL</p>
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

                <article class="tarjetaPregunta">

                    <div class="tarjetaCabecera">
                        <h3><?= htmlspecialchars($p['titulo']); ?></h3>

                        <!-- Guardar -->
                        <svg class="iconoGuardar" viewBox="0 0 24 24" width="26" height="26" aria-hidden="true">
                            <path d="M6 3h12v18l-6-4.5L6 21z"
                                  fill="<?= $p['guardada'] ? '#00A67E' : 'none'; ?>"
                                  stroke="<?= $p['guardada'] ? '#00A67E' : '#111820'; ?>"
                                  stroke-width="1.6" stroke-linejoin="round"/>
                        </svg>
                    </div>

                    <div class="etiquetas">
                        <?php foreach ($p['etiquetas'] as $etiqueta): ?>
                            <span class="etiqueta"><?= htmlspecialchars($etiqueta); ?></span>
                        <?php endforeach; ?>
                    </div>

                    <div class="tarjetaPie">
                        <div class="datoPie">
                            <img class="avatarMini" src="img/aside/logo_usuarios.png" alt="">
                            <span><?= htmlspecialchars($p['autor']); ?></span>
                        </div>

                        <div class="datoPie">
                            <svg viewBox="0 0 24 24" width="30" height="30" aria-hidden="true">
                                <path d="M12 21s-8-5.2-8-11a4.6 4.6 0 0 1 8-3 4.6 4.6 0 0 1 8 3c0 5.8-8 11-8 11z"
                                      fill="<?= $p['me_gusta'] ? '#00A67E' : 'none'; ?>"
                                      stroke="<?= $p['me_gusta'] ? '#00A67E' : '#111820'; ?>"
                                      stroke-width="1.4" stroke-linejoin="round"/>
                            </svg>
                            <span><?= (int)$p['likes']; ?> likes</span>
                        </div>

                        <div class="datoPie">
                            <svg viewBox="0 0 24 24" width="28" height="28" aria-hidden="true">
                                <rect x="3" y="5" width="18" height="16" rx="2" fill="none" stroke="#111820" stroke-width="1.5"/>
                                <path d="M3 10h18M8 3v4M16 3v4M8 14h2M12 14h2M16 14h2M8 17h2M12 17h2M16 17h2"
                                      stroke="#111820" stroke-width="1.5" stroke-linecap="round"/>
                            </svg>
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
                    <img src="img/aside/logo_usuarios.png" alt="">
                    <span><?= htmlspecialchars($nombre); ?></span>
                </div>
            <?php endforeach; ?>
        </section>

    </div>

</main>

</body>
</html>