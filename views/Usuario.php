<?php
    $pagina_activa = 'usuario';
    $img = 'img/main/';
    $urlDetalle = 'index.php?controller=PreguntasController&accion=detalle&id=';

    $nombre         = $datosUsuario['nombre'] ?? ($_SESSION['nombre'] ?? 'Usuario');
    $apellidos      = $datosUsuario['apellidos'] ?? '';
    $nombreCompleto = trim($nombre . ' ' . $apellidos);
    $rol   = ($datosUsuario['rol'] ?? '') === 'admin' ? 'Administrador' : 'Usuario';
    $desde = !empty($datosUsuario['fecha_registro'])
                ? date('d/m/Y', strtotime($datosUsuario['fecha_registro']))
                : '—';
    $campos = [
        'Nombre:'        => $nombre,
        'Apellidos:'     => $apellidos,
        'Email:'         => $datosUsuario['email'] ?? '',
        'Rol:'           => $rol,
        'Miembro desde:' => $desde,
    ];

    require __DIR__ . '/layout/head.php';
    require __DIR__ . '/layout/sidebar.php';
?>
<main class="inicio perfil">
    <section class="cabeceraPerfil">
        <div class="cabeceraFavoritos">
            <div class="iconoFavoritos">
                <img src="img/usuario/iconoPerfil.png" alt="Imagen de <?= htmlspecialchars($nombreCompleto); ?>">
            </div>
            <h1><?= htmlspecialchars($nombreCompleto); ?></h1>
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
        <div id="preguntas" class="seccionPestana">
            <?php if (empty($mis_preguntas)): ?>
                <p class="sinResultados">Todavía no has publicado ninguna pregunta.</p>
            <?php endif; ?>
            <?php foreach ($mis_preguntas as $p): ?>
                <article class="tarjetaPregunta tarjetaFavorito">
                    <div class="tarjetaCabecera">
                        <h3>
                            <a href="<?= $urlDetalle . (int) $p['id']; ?>"><?= htmlspecialchars($p['titulo']); ?></a>
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
                            <img class="iconoCalendario" src="img/usuario/respuestas.png" alt="">
                            <span><?= (int) $p['respuestas']; ?> respuestas</span>
                        </div>
                        <div class="datoPie">
                            <img class="iconoCalendario" src="<?= $img; ?>coraSinRelleno.png" alt="">
                            <span><?= (int) $p['likes']; ?> likes</span>
                        </div>
                        <div class="datoPie">
                            <img class="iconoCalendario" src="<?= $img; ?>calendario 1.png" alt="">
                            <span><?= date('d/m/Y', strtotime($p['fecha_creacion'])); ?></span>
                        </div>
                        <button type="button" class="btnToggle btnEliminar">
                            <img class="iconoCalendario" src="img/usuario/papelera.png" alt="Eliminar">
                        </button>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
        <div id="guardados" class="seccionPestana" style="display: none;">
            <?php if (empty($preguntas_guardadas)): ?>
                <p class="sinResultados">Todavía no has guardado ninguna pregunta.</p>
            <?php endif; ?>
            <?php foreach ($preguntas_guardadas as $p): ?>
                <article class="tarjetaPregunta tarjetaFavorito">
                    <div class="tarjetaCabecera">
                        <h3>
                            <a href="<?= $urlDetalle . (int) $p['id']; ?>"><?= htmlspecialchars($p['titulo']); ?></a>
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
                            <span><?= (int) $p['likes']; ?> likes</span>
                        </div>
                        <div class="datoPie">
                            <img class="iconoCalendario" src="<?= $img; ?>calendario 1.png" alt="">
                            <span><?= date('d/m/Y', strtotime($p['fecha_creacion'])); ?></span>
                        </div>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
        <div id="informacion" class="seccionPestana" style="display: none;">
            <div class="formInformacion">
                <?php foreach ($campos as $titulo => $valor): ?>
                    <div class="campoInfo">
                        <label><?= $titulo; ?></label>
                        <div class="campoValor">
                            <input type="text" value="<?= htmlspecialchars($valor); ?>" readonly>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <form class="cajaPassword" method="post"
                  action="index.php?controller=UsuarioController&accion=cambiarPassword">
                <h3>Cambiar contraseña</h3>

                <?php if (!empty($mensaje)): ?>
                    <p class="<?= $mensaje['tipo'] === 'ok' ? 'mensajeOk' : 'mensajeError'; ?>" role="alert">
                        <?= htmlspecialchars($mensaje['texto']); ?>
                    </p>
                <?php endif; ?>

                <label for="actual">Contraseña actual</label>
                <input type="password" id="actual" name="actual" autocomplete="current-password" required>

                <label for="nueva">Nueva contraseña</label>
                <input type="password" id="nueva" name="nueva" minlength="6" autocomplete="new-password" required>

                <label for="confirmar">Repetir nueva contraseña</label>
                <input type="password" id="confirmar" name="confirmar" minlength="6" autocomplete="new-password" required>

                <p id="avisoCoincide" class="mensajeError" style="display:none;">Las contraseñas no coinciden.</p>

                <button type="submit">Cambiar contraseña</button>
            </form>
        </div>

    </div>
</main>

<script src="JavaScript/pestanas.js"></script>
<script>
    (function () {
        var nueva = document.getElementById('nueva');
        var conf  = document.getElementById('confirmar');
        var aviso = document.getElementById('avisoCoincide');
        function comprobar() {
            aviso.style.display = (conf.value !== '' && nueva.value !== conf.value) ? 'block' : 'none';
        }
        nueva.addEventListener('input', comprobar);
        conf.addEventListener('input', comprobar);
    })();
</script>
<?php if (!empty($mensaje)): ?>
<script>
    cambiarPestana('informacion', document.querySelectorAll('.pestanaBtn')[2]);
</script>
<?php endif; ?>