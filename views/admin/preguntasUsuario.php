<?php require __DIR__ . '/../layout/head.php'; ?>

<body>
    <?php require __DIR__ . '/../layout/sidebar.php'; ?>

    <main class="inicio">
        <div class="migas">
            <a href="index.php?controller=AdminController&accion=usuarios">Usuarios</a>
            <span>/</span>
            <span><?= htmlspecialchars($usuarioVisto['nombre']) ?></span>
        </div>

        <div class="cabeceraPreguntas">
            <div>
                <h1>Actividad de <?= htmlspecialchars($usuarioVisto['nombre']) ?></h1>
                <p class="subtituloPreguntas"><?= htmlspecialchars($usuarioVisto['email']) ?></p>
            </div>
        </div>

        <h2 class="tituloDocs">Preguntas (<?= count($preguntas) ?>)</h2>

        <?php if (empty($preguntas)): ?>
            <p class="sinResultados">Este usuario todavía no ha hecho ninguna pregunta.</p>
        <?php endif; ?>

        <?php foreach ($preguntas as $p): ?>
            <?php $urlDetalle = 'index.php?controller=PreguntasController&accion=detalle&id=' . (int) $p['id']; ?>

            <div class="filaPregunta">
                <a class="cajaRespuestas" href="<?= $urlDetalle ?>">
                    <strong><?= (int) $p['respuestas'] ?></strong>
                    <span>respuestas</span>
                </a>

                <div class="tarjetaPregunta">
                    <div class="tarjetaCabecera">
                        <h3><a href="<?= $urlDetalle ?>"><?= htmlspecialchars($p['titulo']) ?></a></h3>
                    </div>

                    <?php if (!empty($p['temas'])): ?>
                    <div class="etiquetas">
                        <?php foreach (explode('||', $p['temas']) as $tema): ?>
                            <span class="etiqueta"><?= htmlspecialchars($tema) ?></span>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>

                    <div class="tarjetaPie">
                        <span class="datoPie"><?= htmlspecialchars(substr($p['fecha_creacion'], 0, 10)) ?></span>
                        <span class="datoPie"><?= (int) $p['likes'] ?> likes</span>

                        <form method="POST"
                              action="index.php?controller=AdminController&accion=eliminarPregunta"
                              onsubmit="return confirm('¿Seguro que quieres borrar esta pregunta? Se borrarán también sus respuestas y no se puede deshacer.');"
                              style="margin-left:auto">
                            <input type="hidden" name="id" value="<?= (int) $p['id'] ?>">
                            <button type="submit" class="btnCancelarModal">Eliminar</button>
                        </form>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>

        <h2 class="tituloDocs">Respuestas (<?= count($respuestas) ?>)</h2>

        <?php if (empty($respuestas)): ?>
            <p class="sinResultados">Este usuario todavía no ha respondido a ninguna pregunta.</p>
        <?php endif; ?>

        <?php foreach ($respuestas as $r): ?>
            <?php $urlPregunta = 'index.php?controller=PreguntasController&accion=detalle&id=' . (int) $r['pregunta_id']; ?>

            <div class="tarjetaPregunta tarjetaFavorito">
                <p class="respuestaEn">
                    Respuesta en <a href="<?= $urlPregunta ?>"><?= htmlspecialchars($r['pregunta_titulo']) ?></a>
                </p>

                <p class="respuestaFavTexto"><?= nl2br(htmlspecialchars($r['contenido'])) ?></p>

                <div class="tarjetaPie">
                    <span class="datoPie"><?= htmlspecialchars(substr($r['fecha_creacion'], 0, 10)) ?></span>
                    <span class="datoPie"><?= (int) $r['likes'] ?> likes</span>

                    <form method="POST"
                          action="index.php?controller=AdminController&accion=eliminarRespuesta"
                          onsubmit="return confirm('¿Seguro que quieres borrar esta respuesta? No se puede deshacer.');"
                          style="margin-left:auto">
                        <input type="hidden" name="id" value="<?= (int) $r['id'] ?>">
                        <button type="submit" class="btnCancelarModal">Eliminar</button>
                    </form>
                </div>
            </div>
        <?php endforeach; ?>
    </main>
</body>
</html>