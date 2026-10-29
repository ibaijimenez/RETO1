<?php
    $pagina_activa = 'usuario';
    $img = 'img/main/';
    $urlDetalle = 'index.php?controller=usuarioController&accion=favoritos';
    $nombre = $datosUsuario['nombre'] ?? ($_SESSION['nombre'] ?? 'Usuario');
    $email  = $datosUsuario['email']  ?? '';
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

    <!-- CABECERA -->
    <section class="cabeceraPerfil">
        <div class="cabeceraFavoritos">
            <div class="iconoFavoritos">
                <img src="img/aside/logo_usuarios.png" alt="Imagen de <?= htmlspecialchars($nombre); ?>">
            </div>
            <h1><?= htmlspecialchars($nombre); ?></h1>
        </div>

        <div class="datosContador">
            <div class="dato">
                <span class="numero"><?= count($mis_preguntas); ?></span>
                <span class="label">Preguntas</span>
            </div>
            <div class="dato">
                <span class="numero"><?= count($preguntas_guardadas); ?></span>
                <span class="label">Guardados</span>
            </div>
        </div>

        <div class="apartados">
            <button type="button" class="pestanaBtn activa" onclick="cambiarPestana('preguntas', this)">Preguntas</button>
            <button type="button" class="pestanaBtn" onclick="cambiarPestana('guardados', this)">Guardados</button>
            <button type="button" class="pestanaBtn" onclick="cambiarPestana('informacion', this)">Información</button>
        </div>
    </section>

    <div class="listaPreguntas">

        <!-- PESTAÑA PREGUNTAS -->
        <div id="preguntas" class="seccionPestana">
            <?php if (empty($mis_preguntas)): ?>
                <p class="sinResultados">Todavía no has publicado ninguna pregunta.</p>
            <?php endif; ?>
            <?php foreach ($mis_preguntas as $p): ?>
                <article class="tarjetaPregunta tarjetaFavorito">
                    <div class="tarjetaCabecera">
                        <h3>
                            <a href="<?= $urlDetalle . $p['id']; ?>"><?= htmlspecialchars($p['titulo']); ?></a>
                        </h3>
                    </div>

                    <div class="etiquetas">
                        <?php if (!empty($p['temas'])): ?>
                            <?php foreach (explode('||', $p['temas']) as $tema): ?>
                                <span class="etiqueta"><?= htmlspecialchars($tema); ?></span>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>

                    <div class="tarjetaPie">
                        <div class="datoPie">
                            <img class="iconoCalendario" src="img/aside/preguntas_verde.png" alt="">
                            <span><?= $p['respuestas']; ?> respuestas</span>
                        </div>
                        <div class="datoPie">
                            <img class="iconoCalendario" src="<?= $img; ?>coraSinRelleno.png" alt="">
                            <span><?= $p['likes']; ?> likes</span>
                        </div>
                        <div class="datoPie">
                            <img class="iconoCalendario" src="<?= $img; ?>calendario 1.png" alt="">
                            <span><?= date('d/m/Y', strtotime($p['fecha_creacion'])); ?></span>
                        </div>
                        <button type="button" class="btnToggle btnEliminar">
                            <img src="<?= $img; ?>papelera.png" alt="Eliminar">
                        </button>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>

        <!-- PESTAÑA GUARDADOS -->
        <div id="guardados" class="seccionPestana" style="display: none;">
            <?php if (empty($preguntas_guardadas)): ?>
                <p class="sinResultados">Todavía no has guardado ninguna pregunta.</p>
            <?php endif; ?>
            <?php foreach ($preguntas_guardadas as $p): ?>
                <article class="tarjetaPregunta tarjetaFavorito">
                    <div class="tarjetaCabecera">
                        <h3>
                            <a href="<?= $urlDetalle . $p['id']; ?>"><?= htmlspecialchars($p['titulo']); ?></a>
                        </h3>
                        <button type="button" class="btnToggle btnGuardar">
                            <img src="<?= $img; ?>guardadoConRelleno.png" alt="Guardada">
                        </button>
                    </div>

                    <div class="etiquetas">
                        <?php if (!empty($p['temas'])): ?>
                            <?php foreach (explode('||', $p['temas']) as $tema): ?>
                                <span class="etiqueta"><?= htmlspecialchars($tema); ?></span>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>

                    <div class="tarjetaPie">
                        <div class="datoPie">
                            <img class="avatarMini" src="<?= $img; ?>iconoPerfil.png" alt="">
                            <span><?= htmlspecialchars($p['autor']); ?></span>
                        </div>
                        <div class="datoPie">
                            <img class="iconoCalendario" src="<?= $img; ?>coraSinRelleno.png" alt="">
                            <span><?= $p['likes']; ?> likes</span>
                        </div>
                        <div class="datoPie">
                            <img class="iconoCalendario" src="<?= $img; ?>calendario 1.png" alt="">
                            <span><?= date('d/m/Y', strtotime($p['fecha_creacion'])); ?></span>
                        </div>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>

        <!-- PESTAÑA INFORMACIÓN -->
        <div id="informacion" class="seccionPestana" style="display: none;">
            <div class="formInformacion">
                <?php foreach ($campos as $titulo => $valor): ?>
                    <div class="campoInfo">
                        <label><?= $titulo; ?></label>
                        <div class="campoValor">
                            <input type="text" value="<?= htmlspecialchars($valor); ?>">
                            <button type="button" class="btnToggle btnGuardar">
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