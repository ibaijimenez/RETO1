<?php
    $pagina_activa = 'usuario';

    // para pestaña seleccionada
    $pestaña = $_GET['pestaña'] ?? 'preguntas';

    // apartado informacion usuario
    $usuario = [
            'nombre'       => 'Carlos Martín',
            'puesto'       => 'Ingeniera de I+D+i',
            'departamento' => 'Departamento de Análisis Estructural y Materiales',
            'ubicacion'    => 'Vitoria-Gasteiz, Álava',
            'email'        => 'l.garcia@aergibide.com',
            'avatar'       => 'img/usuario/perfil_grande.png'
    ];

    // apartado las preguntas piblicadaas del usuario
    $mis_preguntas = [
        [
            'titulo'     => '¿Alguien ha trabajado con el material Ti6Al4V en procesos de mecanizado?',
            'etiquetas'  => ['Materiales', 'Mecanizado', 'Ti6Al4V'],
            'respuestas' => 7,
            'likes'      => 4,
            'fecha'      => '02/08/2026'
        ],
        [
            'titulo'     => '¿Alguien ha trabajado con el material Ti6Al4V en procesos de mecanizado?',
            'etiquetas'  => ['Materiales', 'Mecanizado', 'Ti6Al4V'],
            'respuestas' => 10,
            'likes'      => 6,
            'fecha'      => '23/04/2026'
        ],
        [
            'titulo'     => '¿Qué bibliotecas recomendáis para el análisis de vibraciones en turbinas?',
            'etiquetas'  => ['Motores', 'Turbinas', 'Carbono'],
            'respuestas' => 2,
            'likes'      => 12,
            'fecha'      => '20/06/2026'
        ],
        [
            'titulo'     => '¿Cómo mejorar la precisión de los sensores de temperatura en ensayos de laboratorio?',
            'etiquetas'  => ['Instrumentación', 'Sensores', 'Ensayos'],
            'respuestas' => 4,
            'likes'      => 23,
            'fecha'      => '22/11/2026'
        ]
    ];


    $preguntas_guardadas = [
        [
            'titulo'    => '¿Cómo mejorar la precisión de los sensores de temperatura en ensayos de laboratorio?',
            'etiquetas' => ['Instrumentación', 'Sensores', 'Ensayos'],
            'autor'     => 'Elena Ruiz',
            'likes'     => 23,
            'fecha'     => '22/11/2026',
            'me_gusta' => true,
            'guardada'  => true,
        ],
        [
            'titulo'    => '¿Cómo podemos reducir el peso de una estructura aeronáutica sin comprometer su resistencia?',
            'etiquetas' => ['Materiales', 'Diseño estructural', 'Aeronáutica'],
            'autor'     => 'Ana Torres',
            'likes'     => 9,
            'fecha'     => '13/10/2026',
            'me_gusta' => true,
            'guardada'  => true,
        ],
        [
            'titulo'    => '¿Alguien ha trabajado con el material Ti6Al4V en procesos de mecanizado?',
            'etiquetas' => ['Materiales', 'Mecanizado', 'Ti6Al4V'],
            'autor'     => 'Pablo López',
            'likes'     => 4,
            'fecha'     => '02/08/2026',
            'me_gusta' => true,
            'guardada'  => true,
        ],
    ];

    $img = 'img/main/';
    require __DIR__ . '/layout/head.php';
    require __DIR__ . '/layout/sidebar.php';
?>


<main class="perfil">

    <section class="cabecera-perfil">
        <!-- foto de perfil y nombre -->
        <div class="foto">
            <img src="img/aside/logo_usuarios.png" alt="Avatar de <?= $usuario['nombre']; ?>">
        </div>
        <h1 class="nombreUser"><?= $usuario['nombre']; ?></h1><!-- nombre de la foto de perfil -->

        <div class="datosContador">
            <div class="dato">
                <span class="numero"><?= (int)$usuario['total_preguntas']; ?></span>
                <span class="label">Preguntas</span>
            </div>
            <div class="dato">
                <span class="numero"><?= (int)$usuario['total_favoritos']; ?></span>
                <span class="label">Favoritos</span>
            </div>
        </div>

        <!-- apartado preguntas guardados informacion del usuario -->
        <div class="apartados">
            <a href="#" class="apartado1">Preguntas</a>
            <a href="#" class="apartado2">Guardados</a>
            <a href="#" class="apartado3">Información</a>
        </div>
    </section>

    <!-- las preguntas -->
    <div class="listaPreguntas">
        <?php foreach ($preguntas_guardadas as $p): ?>
            <article class="tarjetaPregunta">

                <div class="tarjetaCabecera">
                    <h3><?= $p['titulo']; ?></h3>

                    <button type="button" class="btnGuardar" data-tipo="guardar" >
                        <img src="<?= $img; ?>guardadoConRelleno.png" alt="Guardada">
                    </button>
                </div>

                <div class="etiquetas">
                    <?php foreach ($p['etiquetas'] as $etiqueta): ?>
                        <span class="etiqueta"><?= $etiqueta; ?></span>
                    <?php endforeach; ?>
                </div>
                    
                <!-- informacion de abajo de las preguntas, quien la ha echo, los likes, la fecha -->
                <div class="infoPregunta">
                    <div class="autorPregunta">
                        <img class="perfilPequeño" src="<?= $img; ?>iconoPerfil.png" alt="">
                        <span><?= $p['autor']; ?></span>
                    </div>

                    <div class="likePregunta">
                        <button type="button" class="btnToggle btnLike" data-tipo="like" >
                            <img src="<?= $img; ?>coraSinRelleno.png" alt="">
                        </button>
                        <span><span class="numLikes"><?= (int)$p['likes']; ?></span> likes</span>
                    </div>

                    <div class="fechaPregunta">
                        <img class="iconoCalendario" src="<?= $img; ?>calendario 1.png" alt="">
                        <span><?= $p['fecha']; ?></span>
                    </div>
                </div>

            </article>
        <?php endforeach; ?>
    </section>

</main>

</body>
</html>




