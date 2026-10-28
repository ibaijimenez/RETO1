<?php
if (!isset($pagina_activa)) {
    $pagina_activa = 'inicio';
}

$ruta = 'index.php?controller=';

// calcular notis
require_once __DIR__ . '/../../models/AergibideModel.php';
$numNotis = isset($_SESSION['usuario_id'])
    ? AergibideModel::contarNoLeidas((int) $_SESSION['usuario_id'])
    : 0;
$menu_items = [
    ['id' => 'inicio',         'texto' => 'Inicio',         'url' => $ruta.'AergibideController&accion=inicio',        'icono_verde'=>'img/aside/avion_verde.png',      'icono_blanco'=>'img/aside/avion_blanco.png'],
    ['id' => 'preguntas',      'texto' => 'Preguntas',      'url' => $ruta.'PreguntasController&accion=preguntas',     'icono_verde'=>'img/aside/preguntas_verde.png',  'icono_blanco'=>'img/aside/preguntas_blanco.png'],
    ['id' => 'documentos',     'texto' => 'Documentos',     'url' => $ruta.'DocumentosController&accion=documentos',   'icono_verde'=>'img/aside/documentos_verde.png', 'icono_blanco'=>'img/aside/documentos_blanco.png'],
    ['id' => 'favoritos',      'texto' => 'Favoritos',      'url' => $ruta.'FavoritosController&accion=favoritos',     'icono_verde'=>'img/aside/favoritos_verde.png',  'icono_blanco'=>'img/aside/favoritos_blanco.png'],
    ['id' => 'notificaciones', 'texto' => 'Notificaciones', 'url' => $ruta.'notificacionesController&accion=notificaciones',                                              'icono_verde'=>'img/aside/notificaciones_verde.png',                               'icono_blanco'=>'img/aside/notificaciones_blanco.png'],
];
?>
   
<aside>
    <div class="menu">
        <?php foreach ($menu_items as $item): ?>
            <?php
                $es_activo = ($item['id'] === $pagina_activa);
                $clase = $es_activo ? 'activo' : '';
                $icono_actual = $es_activo ? $item['icono_verde'] : $item['icono_blanco'];
            ?>
            <a href="<?= $item['url']; ?>" class="<?= $clase; ?>">
                <img src="<?= $icono_actual; ?>" alt="<?= $item['texto']; ?>" class="icono-menu">
                <span><?= $item['texto']; ?></span>
                <?php if ($item['id'] === 'notificaciones' && $numNotis > 0): ?>
                <span style="margin-left:auto; background:#D93025; color:white; font-size:13px; font-weight:700;
                 border-radius:12px; min-width:22px; height:22px; padding:0 6px;
                 display:flex; align-items:center; justify-content:center;">
                <?= $numNotis > 99 ? '99+' : $numNotis ?>
    </span>
<?php endif; ?>
            </a>
        <?php endforeach; ?>
    </div>

    <div class="usuario-aside">
        <a href="index.php?controller=usuarioController&accion=perfil">
            <img src="img/aside/logo_usuarios.png" alt="Usuario">
            <span><?= $_SESSION['nombre'] ?? 'Usuario'; ?></span>
        </a>
    </div>
</aside>
