<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Aergibide SL</title>
    <link rel="stylesheet" href="../styles/styles.css">
</head>
<body>
    <header class="header">
    <div class="logo">
        <a href="#" class="logo link">
            <img src="../img/header/aergibide_blanco.png" alt="Aergibide SL">
        </a>
    </div>
    <div class="buscador">
        <img src="../img/header/lupa.png" alt="Buscar" class="lupa">
        <input type="text" placeholder="Buscar preguntas, temas..." class="buscar">
    </div>
    <div class="acciones">
        <img src="../img/header/campana_notificaciones.png" alt="No se ha podido cargar esta funcion">
        <img src="../img/header/logo_user.png" alt="No se ha podidocargar esta funcion">
    </div>
    </header>
    <?php
if (!isset($pagina_activa)) {
    $pagina_activa = 'inicio';
}

$menu_items = [
    ['id' => 'inicio',        'texto' => 'Inicio',        'url' => 'index.php'],
    ['id' => 'preguntas',     'texto' => 'Preguntas',     'url' => 'preguntas.php'],
    ['id' => 'documentos',    'texto' => 'Documentos',    'url' => 'documentos.php'],
    ['id' => 'favoritos',     'texto' => 'Favoritos',     'url' => 'favoritos.php'],
    ['id' => 'notificaciones','texto' => 'Notificaciones','url' => 'notificaciones.php'],
];
?>

<aside>
    <div class="menu">
        <?php foreach ($menu_items as $item): ?>
            <?php $clase = ($item['id'] === $pagina_activa) ? 'activo' : ''; ?>
            <a href="<?= $item['url']; ?>" class="<?= $clase; ?>">
                <?= $item['texto']; ?>
            </a>
        <?php endforeach; ?>
    </div>

    <div class="usuario-aside">
        <img src="img/usuario.png" alt="Usuario">
        <span>Carlos Martín</span>
    </div>
</aside>
</body>
</html>
