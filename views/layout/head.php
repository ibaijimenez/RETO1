<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <base href="/RETO1/">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Aergibide SL</title>
    <link rel="icon" type="image/png" href="img/header/favicon/favicon_aergibide.png">
    <link rel="stylesheet" href="styles/styles.css">
</head>
<body>
    <header class="header">

        <div class="logo">
        <a href="index.php?controller=AergibideController&accion=inicio" class="logo link">
            <img src="img/header/aergibide_blanco.png" alt="Aergibide SL">
        </a>
        </div>

        <div class="logoCentro">
            <img src="img/header/logotipo.png" alt="Logotipo de Aergibide">
        </div>

        <div class="acciones">
          
            <?php if (($_SESSION['rol'] ?? '') === 'admin'): ?>
                <a href="index.php?controller=AdminController&accion=usuarios" title="Administración">
                    <img src="img/header/estadisticas.png" alt="Administración">
                </a>
            <?php endif; ?>

            <a href="index.php?pagina=login" class="btnSalir" title="Cerrar sesión">
                <img src="img/header/exit.png" alt="Salir">
            </a>
          
          <a href="index.php?controller=usuarioController&accion=perfil" class="btnPerfil" title="Mi perfil">
                <img src="img/header/logo_user.png" alt="Perfil">
            </a>
        </div>


    </header>
