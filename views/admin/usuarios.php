<?php require __DIR__ . '/../layout/head.php'; ?>

<body>
    <?php require __DIR__ . '/../layout/sidebar.php'; ?>

    <main class="inicio">
        <div class="cabeceraPreguntas">
            <div>
                <h1>Usuarios</h1>
                <p class="subtituloPreguntas">Gestiona los usuarios y consulta sus preguntas</p>
            </div>
            <a class="btnNuevaPregunta" href="index.php?controller=AdminController&accion=crearUsuario">+ Crear usuario</a>
        </div>

        <div class="tablaWrap">
            <table class="tablaDocs tablaAdmin">
                <thead>
                    <tr><th>Nombre</th><th>Email</th><th>Rol</th><th>Registro</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($usuarios as $u): ?>
                    <tr>
                        <td>
                            <a class="enlaceUsuario" href="index.php?controller=AdminController&accion=preguntasUsuario&id=<?= (int) $u['id'] ?>">
                                <?= htmlspecialchars($u['nombre']) ?>
                            </a>
                        </td>
                        <td class="textoGris"><?= htmlspecialchars($u['email']) ?></td>
                        <td>
                            <span class="pillCategoria pillRol <?= $u['rol'] === 'admin' ? 'catMorado' : 'catVerde' ?>">
                                <?= htmlspecialchars($u['rol']) ?>
                            </span>
                        </td>
                        <td class="textoGris"><?= htmlspecialchars(substr($u['fecha_registro'] ?? '', 0, 10)) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </main>
</body>
</html>