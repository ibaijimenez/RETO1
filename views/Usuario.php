<?php
    $pagina_activa = 'usuario';

    // para pestaña seleccionada
    $pestana = $_GET['pestana'] ?? 'preguntas';

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

    // apartado de preguntas guardadas del usuario
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
<!-- PARTE SUPERIOR -->
    <!-- cabecera de la pagina: foto perfil, nombre, contador de guardados y preguntas -->
    <section class="cabeceraPerfil">
        <div class="foto">
            <img src="img/aside/logo_usuarios.png" alt="Avatar de <?= ($usuario['nombre']); ?>"><!-- la imagen -->
        </div>
        <h1 class="nombreUser"><?= $usuario['nombre']; ?></h1><!-- nombre del usuario -->

        <!-- para los contadores de pruntas del usuario y gpreguntas guardadas -->
        <div class="datosContador">
            <div class="dato">
                <span class="numero"><?= count($mis_preguntas); ?></span>
                <span class="label">Preguntas</span>
            </div>
            <div class="dato">
                <span class="numero"><?= count($preguntas_guardadas); ?></span>
                <span class="label">Favoritos</span>
            </div>
        </div>

        <!-- los tres apartados: preguntas, favoritos, informacion -->
        <div class="apartados">
            <button type="button" class="pestana-btn activa" onclick="cambiarPestana('preguntas', this)">Preguntas</button>
            <button type="button" class="pestana-btn" onclick="cambiarPestana('guardados', this)">Guardados</button>
            <button type="button" class="pestana-btn" onclick="cambiarPestana('informacion', this)">Información</button>
        </div>
    </section>

<!-- PARTE DE ABAJO -->
    <div class="listaPreguntas">
        <!-- pestaña preguntas usuario activa -->
        <div id="preguntas" class="seccionPestana">
            <?php foreach ($mis_preguntas as $p): ?>
                <article class="tarjetaPregunta">
                    <div class="tarjetaCabecera">
                        <h3><?=$p['titulo']; ?></h3>
                    </div>

                    <div class="etiquetas">
                        <?php foreach ($p['etiquetas'] as $etiqueta): ?>
                            <span class="etiqueta"><?= $etiqueta; ?></span>
                        <?php endforeach; ?>
                    </div>

                    <div class="infoPregunta">
                        <div class="respuestasPregunta">
                            <img class="iconoInfo" src="img/aside/preguntas_verde.png" alt="">
                            <span><?= $p['respuestas']; ?> respuestas</span>
                        </div>

                        <div class="likePregunta">
                            <img class="iconoInfo" src="<?= $img; ?>coraSinRelleno.png" alt="">
                            <span><?= $p['likes']; ?> likes</span>
                        </div>

                        <div class="fechaPregunta">
                            <img class="iconoInfo" src="<?= $img; ?>calendario 1.png" alt="">
                            <span><?= $p['fecha']; ?></span>
                        </div>

                        
                        <div class="eliminarPregunta">
                            <button type="button" class="btnEliminar">
                                <img src="<?= $img; ?>papelera.png" alt="Eliminar">
                            </button>
                        </div>
                    </div>
                </article>
            <?php endforeach; ?>
            
        <!-- cpestaña de guardados -->
        <div id="guardados" class="seccionPestana" style="display: none;">
            <?php foreach ($preguntas_guardadas as $p): ?>
                <article class="tarjetaPregunta">
                    <div class="tarjetaCabecera">
                        <h3><?= $p['titulo']; ?></h3>
                        <button type="button" class="btnGuardar">
                            <img src="<?= $img; ?>guardadoConRelleno.png" alt="Guardada">
                        </button>
                    </div>

                    <div class="etiquetas">
                        <?php foreach ($p['etiquetas'] as $etiqueta): ?>
                            <span class="etiqueta"><?= $etiqueta; ?></span>
                        <?php endforeach; ?>
                    </div>

                    <div class="infoPregunta">
                        <div class="autorPregunta">
                            <img class="perfilPequeño" src="<?= $img; ?>iconoPerfil.png" alt="">
                            <span><?= $p['autor']; ?></span>
                        </div>

                        <div class="likePregunta">
                            <img class="iconoInfo" src="<?= $img; ?>coraSinRelleno.png" alt="">
                            <span><?= $p['likes']; ?> likes</span>
                        </div>

                        <div class="fechaPregunta">
                            <img class="iconoInfo" src="<?= $img; ?>calendario 1.png" alt="">
                            <span><?= $p['fecha']; ?></span>
                        </div>
                    </div>
                </article>
            <?php endforeach; ?>
            
        <!-- pestaña de informacion  -->
        <div id="informacion" class="seccionPestana" style="display: none;">
            <div class="formInformacion">
                <div class="campoInfo">
                    <label>Nombre y Apellidos:</label>
                    <div class="container">
                        <input type="text" value="<?= $usuario['nombre']; ?>">
                        <button type="button" class="btnGuardar"><img src="<?= $img; ?>iconoGuardar.png" alt="Guardar"></button>
                    </div>
                </div>

                <div class="campoInfo">
                    <label>Puesto:</label>
                    <div class="container">
                        <input type="text" value="<?= $usuario['puesto']; ?>">
                        <button type="button" class="btnGuardar"><img src="<?= $img; ?>iconoGuardar.png" alt="Guardar"></button>
                    </div>
                </div>

                <div class="campoInfo">
                    <label>Departamento:</label>
                    <div class="container">
                        <input type="text" value="<?= $usuario['departamento']; ?>">
                        <button type="button" class="btnGuardar"><img src="<?= $img; ?>iconoGuardar.png" alt="Guardar"></button>
                    </div>
                </div>

                <div class="campoInfo">
                    <label>Ubicación:</label>
                    <div class="container">
                        <input type="text" value="<?= $usuario['ubicacion']; ?>">
                        <button type="button" class="btnGuardar"><img src="<?= $img; ?>iconoGuardar.png" alt="Guardar"></button>
                    </div>
                </div>

                <div class="campoInfo">
                    <label>Email:</label>
                    <div class="container">
                        <input type="text" value="<?= $usuario['email']; ?>">
                        <button type="button" class="btnGuardar"><img src="<?= $img; ?>iconoGuardar.png" alt="Guardar"></button>
                    </div>
                </div>
            </div>
        

    </div>

</main>

<script src="JavaScript/pestanas.js"></script>


</body>
</html>




