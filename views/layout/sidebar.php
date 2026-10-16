<?php
if (!isset($pagina_activa)) {
    $pagina_activa = 'inicio';
}

$menu_items = [
    ['id' => 'inicio',        'texto' => 'Inicio',        'url' => 'index.php','icono_verde'=>'img/aside/avion_verde.png','icono_blanco'=>'img/aside/avion_blanco.png'],
    ['id' => 'preguntas',     'texto' => 'Preguntas',     'url' => 'preguntas.php','icono_verde'=>'img/aside/preguntas_verde.png','icono_blanco'=>'img/aside/preguntas_blanco.png'],
    ['id' => 'documentos',    'texto' => 'Documentos',    'url' => 'documentos.php','icono_verde'=>'img/aside/documentos_verde.png','icono_blanco'=>'img/aside/documentos_blanco.png'],
    ['id' => 'favoritos',     'texto' => 'Favoritos',     'url' => 'favoritos.php','icono_verde'=>'','icono_blanco'=>'img/aside/favoritos_blanco.png'],
    ['id' => 'notificaciones','texto' => 'Notificaciones','url' => 'notificaciones.php','icono_verde'=>'','icono_blanco'=>'img/aside/notificaciones_blanco.png'],
];
?>

<aside>
    <div class="menu">
        <?php foreach ($menu_items as $item): ?>
            <?php 
                $es_activo = ($item['id'] === $pagina_activa);
                $clase = $es_activo ? 'activo' : '';
                
                if ($es_activo) {
                    $icono_actual = $item['icono_verde'];
                } else {
                    $icono_actual = $item['icono_blanco'];
                }
            ?>
            <a href="<?= $item['url']; ?>" class="<?= $clase; ?>">
                <img src="<?= $icono_actual; ?>" alt="<?= $item['texto']; ?>" class="icono-menu">
                <span><?= $item['texto']; ?></span>
            </a>
        <?php endforeach; ?>
    </div>

    <div class="usuario-aside">
        <img src="img/aside/logo_usuarios.png" alt="Usuario">
        <span>Carlos Martín</span>
    </div>
</aside>
