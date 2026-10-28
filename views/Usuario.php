<?php
    $pagina_activa = 'usuario';
    $img = 'img/main/';

    // apartado informacion usuario
    $nombre = $datosUsuario['nombre'] ?? ($_SESSION['nombre'] ?? 'Usuario');
    $email  = $datosUsuario['email']  ?? '';
    
    // La BD todavía no guarda puesto / departamento / ubicación: valores de ejemplo
    $puesto       = 'Ingeniera de I+D+i';
    $departamento = 'Departamento de Análisis Estructural y Materiales';
    $ubicacion    = 'Vitoria-Gasteiz, Álava';
    
    $campos = [
        'Nombre y Apellidos:' => $nombre,
        'Puesto:'             => $puesto,
        'Departamento:'       => $departamento,
        'Ubicación:'          => $ubicacion,
        'Email:'              => $email,
    ];
  
    
    require __DIR__ . '/layout/head.php';
    require __DIR__ . '/layout/sidebar.php';
?>


<main class="inicio perfil">
<!-- PARTE SUPERIOR -->
    <!-- cabecera de la pagina: foto perfil, nombre, contador de guardados y preguntas -->
    <section class="cabeceraPerfil">
        <div class="foto">
            <img src="img/aside/logo_usuarios.png" alt="Imagen de <?= $nombre; ?>"><!-- la imagen -->
        </div>
        <h1 class="nombreUser"><?= $nombre; ?></h1><!-- nombre del usuario -->

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
            <button type="button" class="pestanaBtn activa" onclick="cambiarPestana('preguntas', this)">Preguntas</button>
            <button type="button" class="pestanaBtn" onclick="cambiarPestana('guardados', this)">Guardados</button>
            <button type="button" class="pestanaBtn" onclick="cambiarPestana('informacion', this)">Información</button>
        </div>
    </section>

<!-- PARTE DE ABAJO -->
    <div class="listaPreguntas">
        <!-- pestaña preguntas usuario activa -->
        <div id="preguntas" class="seccionPestana">
            <?php if (empty($mis_preguntas)): ?>
                <p class="sinResultados">Todavía no has publicado ninguna pregunta.</p>
            <?php endif; ?>
            <?php foreach ($mis_preguntas as $p): ?>
                <article class="tarjetaPregunta">
                    <div class="tarjetaCabecera">
                        <h3><?=$p['titulo']; ?></h3>
                    </div>

                    <div class="etiquetas">
                        <?php if (!empty($p['temas'])): ?>
                            <?php foreach (explode('||', $p['temas']) as $tema): ?>
                                <span class="etiqueta"><?= $tema; ?></span>
                            <?php endforeach; ?>
                        <?php endif; ?>
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
                            <span><?= date('d/m/Y', strtotime($p['fecha_creacion'])); ?></span>
                        </div>

                        
                        <div class="eliminarPregunta">
                            <button type="button" class="btnEliminar">
                                <img src="<?= $img; ?>papelera.png" alt="Eliminar">
                            </button>
                        </div>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
            
        <!-- cpestaña de guardados -->
        <div id="guardados" class="seccionPestana" style="display: none;">
            <?php if (empty($preguntas_guardadas)): ?>
                <p class="sinResultados">Todavía no has guardado ninguna pregunta.</p>
            <?php endif; ?>
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
        </div>
            
        <!-- pestaña de informacion  -->
        <div id="informacion" class="seccionPestana" style="display: none;">
            <div class="formInformacion">
                <?php foreach ($campos as $titulo => $valor): ?>
                    <div class="campoInfo">
                     <label><?= $titulo; ?></label>
                        <div class="container">
                            <input type="text" value="<?= $valor; ?>">
                            <button type="button" class="btnGuardar">
                                <img src="<?= $img; ?>iconoGuardar.png" alt="Guardar">
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
         </div>

    </div>

</main>

<script src="JavaScript/pestanas.js"></script>

</body>
</html>




