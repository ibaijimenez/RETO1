<?php require __DIR__ . '/../layout/head.php'; ?>

<body>
    <?php require __DIR__ . '/../layout/sidebar.php'; ?>

    <main class="inicio nuevaPregunta">
        <div class="migas">
            <a href="index.php?controller=AdminController&accion=usuarios">Usuarios</a>
            <span>/</span>
            <span>Crear usuario</span>
        </div>

        <h1>Crear usuario</h1>
        <p class="subtituloPreguntas">Elige el rol que tendrá el nuevo usuario</p>

        <form class="formPregunta" method="POST" autocomplete="off">
            <?php if (!empty($error)): ?>
                <div class="erroresForm"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <label for="nombre">Nombre <span class="obligatorio">*</span></label>
            <input type="text" id="nombre" name="nombre" placeholder="Nombre de usuario" autocomplete="off" required>

            <label for="email">Email <span class="obligatorio">*</span></label>
            <input type="text" id="email" name="email" placeholder="correo@ejemplo.com" autocomplete="off" required>

            <label for="passwd">Contraseña <span class="obligatorio">*</span></label>
            <input type="text" id="passwd" name="passwd" placeholder="Contraseña" autocomplete="off"
                   style="-webkit-text-security: disc;" required>

            <label for="rol">Rol <span class="obligatorio">*</span></label>
            <div class="filaTemas" style="margin-bottom:28px">
                <select class="selectTema" id="rol" name="rol">
                    <option value="user">user</option>
                    <option value="admin">admin</option>
                </select>
            </div>

            <div class="pieForm">
                <span class="avisoForm">Los campos con * son obligatorios</span>
                <div class="botonesForm">
                    <a class="btnCancelar" href="index.php?controller=AdminController&accion=usuarios">Cancelar</a>
                    <button type="submit" class="btnPublicar">Crear usuario</button>
                </div>
            </div>
        </form>
    </main>
</body>
</html>