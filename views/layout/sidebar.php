<?php
if (!isset($pagina_activa)) {
    $pagina_activa = 'inicio';
}

$menu_items = [
    ['id' => 'inicio',        'texto' => 'Inicio',        'url' => '../index.php'],
    ['id' => 'preguntas',     'texto' => 'Preguntas',     'url' => '../preguntas.php'],
    ['id' => 'documentos',    'texto' => 'Documentos',    'url' => '../documentos.php'],
    ['id' => 'favoritos',     'texto' => 'Favoritos',     'url' => '../favoritos.php'],
    ['id' => 'notificaciones','texto' => 'Notificaciones','url' => '../notificaciones.php'],
];
?>

<aside>
    <div class="menu">
        <?php 
        foreach ($menu_items as $item): 
        ?>
            <?php 
                if ($item['id'] === $pagina_activa) {
                $clase = 'activo';
            } else {
                $clase = '';
            }
            ?>
            <a href="<?= $item['url']; ?>" class="<?= $clase; ?>">
            <?= $item['texto']; ?>
            </a>
        <?php endforeach; ?>
    </div>

    <div class="usuario-aside">
        <img src="img/aside/logo_usuarios.png" alt="Usuario">
        <span>Carlos Martín</span>
    </div>
</aside>
</body>
</html>