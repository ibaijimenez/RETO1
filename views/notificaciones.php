<?php
$pagina_activa = 'notificaciones';

// "hace 2 días", "hace 3 h"...
if (!function_exists('haceTiempo')) {
    function haceTiempo(string $fecha): string
    {
        $seg = time() - strtotime($fecha);
        if ($seg < 60)    return 'hace un momento';
        if ($seg < 3600)  return 'hace ' . floor($seg / 60) . ' min';
        if ($seg < 86400) return 'hace ' . floor($seg / 3600) . ' h';
        $dias = (int) floor($seg / 86400);
        return 'hace ' . $dias . ($dias === 1 ? ' día' : ' días');
    }
}

require __DIR__ . '/layout/head.php';
require __DIR__ . '/layout/sidebar.php';
?>

<main class="inicio">
    <div class="cabeceraPreguntas">
        <div>
            <h1>Notificaciones</h1>
            <p class="subtituloPreguntas">Lo que ha pasado con tus preguntas y respuestas.</p>
        </div>
    </div>

    <?php if (empty($notificaciones)): ?>
        <p class="sinResultados">Todavía no tienes notificaciones.</p>
    <?php endif; ?>

    <?php foreach ($notificaciones as $n): ?>
        <?php $url = 'index.php?controller=PreguntasController&accion=detalle&id=' . (int) $n['pregunta_id']; ?>

        <div class="tarjetaPregunta tarjetaFavorito">
            <p class="respuestaFavTexto">
                <?= htmlspecialchars($n['mensaje']) ?>
            </p>

            <div class="tarjetaPie">
                <span class="datoPie"><?= haceTiempo($n['fecha_creacion']) ?></span>

                <?php if (!$n['leida']): ?>
                    <span class="etiqueta" style="font-size:14px; padding:4px 16px;">Nueva</span>
                <?php endif; ?>

                <a class="btnCancelarModal" href="<?= $url ?>"
                   style="margin-left:auto; text-decoration:none;">Ver pregunta</a>
            </div>
        </div>
    <?php endforeach; ?>
</main>
</body>
</html>